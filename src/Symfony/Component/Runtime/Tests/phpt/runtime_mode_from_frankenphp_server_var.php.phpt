--TEST--
Test set ENV variable APP_RUNTIME_MODE with FRANKENPHP_WORKER $_SERVER variable set
--INI--
display_errors=1
--FILE--
<?php

require $_SERVER['SCRIPT_FILENAME'] = __DIR__.'/runtime_mode_from_frankenphp_server_var.php';

?>
--EXPECTF--
From context worker, from $_ENV worker
