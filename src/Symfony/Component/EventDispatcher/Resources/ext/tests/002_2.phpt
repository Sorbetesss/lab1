--TEST--
EventDispatcher dispatch() with custom doDispatch()
--SKIPIF--
<?php
    if (!extension_loaded("symfony_eventdispatcher")) die("skip symfony_eventdispatcher is not loaded");
?>
--FILE--
<?php
class MyDispatcher extends Symfony\Component\EventDispatcher\EventDispatcher
{
    protected function doDispatch($listeners, $eventName, Symfony\Component\EventDispatcher\Event $event)
    {
        var_dump('dodispatch');
        parent::dodispatch($listeners, $eventName, $event);
    }
}

class MyListener
{
	public function __invoke()
	{
		var_dump('invoked');
	}
}

$d = new MyDispatcher;

$d->addListener('test-event', new MyListener, 1);

$d->dispatch('test-event');
?>
--EXPECTF--
string(10) "dodispatch"
string(7) "invoked"