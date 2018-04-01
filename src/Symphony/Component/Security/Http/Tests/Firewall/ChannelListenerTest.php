<?php

/*
 * This file is part of the Symphony package.
 *
 * (c) Fabien Potencier <fabien@symphony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphony\Component\Security\Http\Tests\Firewall;

use PHPUnit\Framework\TestCase;
use Symphony\Component\Security\Http\Firewall\ChannelListener;
use Symphony\Component\HttpFoundation\Response;

class ChannelListenerTest extends TestCase
{
    public function testHandleWithNotSecuredRequestAndHttpChannel()
    {
        $request = $this->getMockBuilder('Symphony\Component\HttpFoundation\Request')->disableOriginalConstructor()->disableOriginalClone()->getMock();
        $request
            ->expects($this->any())
            ->method('isSecure')
            ->will($this->returnValue(false))
        ;

        $accessMap = $this->getMockBuilder('Symphony\Component\Security\Http\AccessMapInterface')->getMock();
        $accessMap
            ->expects($this->any())
            ->method('getPatterns')
            ->with($this->equalTo($request))
            ->will($this->returnValue(array(array(), 'http')))
        ;

        $entryPoint = $this->getMockBuilder('Symphony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface')->getMock();
        $entryPoint
            ->expects($this->never())
            ->method('start')
        ;

        $event = $this->getMockBuilder('Symphony\Component\HttpKernel\Event\GetResponseEvent')->disableOriginalConstructor()->getMock();
        $event
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;
        $event
            ->expects($this->never())
            ->method('setResponse')
        ;

        $listener = new ChannelListener($accessMap, $entryPoint);
        $listener->handle($event);
    }

    public function testHandleWithSecuredRequestAndHttpsChannel()
    {
        $request = $this->getMockBuilder('Symphony\Component\HttpFoundation\Request')->disableOriginalConstructor()->disableOriginalClone()->getMock();
        $request
            ->expects($this->any())
            ->method('isSecure')
            ->will($this->returnValue(true))
        ;

        $accessMap = $this->getMockBuilder('Symphony\Component\Security\Http\AccessMapInterface')->getMock();
        $accessMap
            ->expects($this->any())
            ->method('getPatterns')
            ->with($this->equalTo($request))
            ->will($this->returnValue(array(array(), 'https')))
        ;

        $entryPoint = $this->getMockBuilder('Symphony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface')->getMock();
        $entryPoint
            ->expects($this->never())
            ->method('start')
        ;

        $event = $this->getMockBuilder('Symphony\Component\HttpKernel\Event\GetResponseEvent')->disableOriginalConstructor()->getMock();
        $event
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;
        $event
            ->expects($this->never())
            ->method('setResponse')
        ;

        $listener = new ChannelListener($accessMap, $entryPoint);
        $listener->handle($event);
    }

    public function testHandleWithNotSecuredRequestAndHttpsChannel()
    {
        $request = $this->getMockBuilder('Symphony\Component\HttpFoundation\Request')->disableOriginalConstructor()->disableOriginalClone()->getMock();
        $request
            ->expects($this->any())
            ->method('isSecure')
            ->will($this->returnValue(false))
        ;

        $response = new Response();

        $accessMap = $this->getMockBuilder('Symphony\Component\Security\Http\AccessMapInterface')->getMock();
        $accessMap
            ->expects($this->any())
            ->method('getPatterns')
            ->with($this->equalTo($request))
            ->will($this->returnValue(array(array(), 'https')))
        ;

        $entryPoint = $this->getMockBuilder('Symphony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface')->getMock();
        $entryPoint
            ->expects($this->once())
            ->method('start')
            ->with($this->equalTo($request))
            ->will($this->returnValue($response))
        ;

        $event = $this->getMockBuilder('Symphony\Component\HttpKernel\Event\GetResponseEvent')->disableOriginalConstructor()->getMock();
        $event
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;
        $event
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->equalTo($response))
        ;

        $listener = new ChannelListener($accessMap, $entryPoint);
        $listener->handle($event);
    }

    public function testHandleWithSecuredRequestAndHttpChannel()
    {
        $request = $this->getMockBuilder('Symphony\Component\HttpFoundation\Request')->disableOriginalConstructor()->disableOriginalClone()->getMock();
        $request
            ->expects($this->any())
            ->method('isSecure')
            ->will($this->returnValue(true))
        ;

        $response = new Response();

        $accessMap = $this->getMockBuilder('Symphony\Component\Security\Http\AccessMapInterface')->getMock();
        $accessMap
            ->expects($this->any())
            ->method('getPatterns')
            ->with($this->equalTo($request))
            ->will($this->returnValue(array(array(), 'http')))
        ;

        $entryPoint = $this->getMockBuilder('Symphony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface')->getMock();
        $entryPoint
            ->expects($this->once())
            ->method('start')
            ->with($this->equalTo($request))
            ->will($this->returnValue($response))
        ;

        $event = $this->getMockBuilder('Symphony\Component\HttpKernel\Event\GetResponseEvent')->disableOriginalConstructor()->getMock();
        $event
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request))
        ;
        $event
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->equalTo($response))
        ;

        $listener = new ChannelListener($accessMap, $entryPoint);
        $listener->handle($event);
    }
}
