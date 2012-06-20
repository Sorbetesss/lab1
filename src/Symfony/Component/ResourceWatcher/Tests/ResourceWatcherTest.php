<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\ResourceWatcher\Tests;

use Symfony\Component\ResourceWatcher\ResourceWatcher;
use Symfony\Component\ResourceWatcher\Event\Event;

class ResourceWatcherTest extends \PHPUnit_Framework_TestCase
{
    public function testUntrackedResourceTrack()
    {
        $tracker = $this
            ->getMockBuilder('Symfony\Component\ResourceWatcher\Tracker\TrackerInterface')
            ->getMock();

        $resource = $this
            ->getMockBuilder('Symfony\Component\Config\Resource\ResourceInterface')
            ->getMock();

        $tracker
            ->expects($this->once())
            ->method('isResourceTracked')
            ->with($resource)
            ->will($this->returnValue(false));
        $tracker
            ->expects($this->once())
            ->method('track')
            ->with($resource)
            ->will($this->returnValue(null));

        $resource
            ->expects($this->once())
            ->method('getId');

        $watcher = new ResourceWatcher($tracker);
        $watcher->track($resource, function(){});
    }

    public function testTrackedResourceTrack()
    {
        $tracker = $this
            ->getMockBuilder('Symfony\Component\ResourceWatcher\Tracker\TrackerInterface')
            ->getMock();

        $resource = $this
            ->getMockBuilder('Symfony\Component\Config\Resource\ResourceInterface')
            ->getMock();

        $tracker
            ->expects($this->once())
            ->method('isResourceTracked')
            ->with($resource)
            ->will($this->returnValue(true));
        $tracker
            ->expects($this->never())
            ->method('track');

        $resource
            ->expects($this->once())
            ->method('getId');

        $watcher = new ResourceWatcher($tracker);
        $watcher->track($resource, function(){});
    }

    public function testWatching()
    {
        $tracker = $this
            ->getMockBuilder('Symfony\Component\ResourceWatcher\Tracker\TrackerInterface')
            ->getMock();

        $resourceMockBuilder = $this
            ->getMockBuilder('Symfony\Component\Config\Resource\ResourceInterface');

        $resource1 = $resourceMockBuilder->getMock();
        $resource2 = $resourceMockBuilder->getMock();

        $listenerMockBuilder = $this
            ->getMockBuilder('Symfony\Component\ResourceWatcher\Event\EventListenerInterface');

        $listener1 = $listenerMockBuilder->getMock();
        $listener2 = $listenerMockBuilder->getMock();
        $listener3 = $listenerMockBuilder->getMock();

        $listener1
            ->expects($this->exactly(3))
            ->method('getResource')
            ->will($this->returnValue($resource1));
        $listener2
            ->expects($this->exactly(3))
            ->method('getResource')
            ->will($this->returnValue($resource2));
        $listener3
            ->expects($this->exactly(2))
            ->method('getResource')
            ->will($this->returnValue($resource2));

        $tracker
            ->expects($this->exactly(3))
            ->method('isResourceTracked')
            ->will($this->onConsecutiveCalls(false, false, true));
        $tracker
            ->expects($this->exactly(2))
            ->method('track');

        $resource1
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $resource2
            ->expects($this->exactly(2))
            ->method('getId')
            ->will($this->onConsecutiveCalls(2, 2));

        $watcher = new ResourceWatcher($tracker);
        $watcher->addListener($listener1);
        $watcher->addListener($listener2);
        $watcher->addListener($listener3);

        $listener1
            ->expects($this->once())
            ->method('supports')
            ->will($this->returnValue(true));
        $listener2
            ->expects($this->exactly(2))
            ->method('supports')
            ->will($this->onConsecutiveCalls(false, false));
        $listener3
            ->expects($this->exactly(2))
            ->method('supports')
            ->will($this->onConsecutiveCalls(true, true));

        $listener1
            ->expects($this->once())
            ->method('getCallback')
            ->will($this->returnValue(function($e){}));
        $listener2
            ->expects($this->never())
            ->method('getCallback');
        $listener3
            ->expects($this->exactly(2))
            ->method('getCallback')
            ->will($this->returnValue(function($e){}));

        $tracker
            ->expects($this->once())
            ->method('getEvents')
            ->will($this->returnValue(array(
                new Event(1, $resource1, 1),
                new Event(2, $resource2, 1),
                new Event(2, $resource2, 1),
            )));

        $watcher->start(1, 1);
    }
}
