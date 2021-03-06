<?php

require_once '../../Cmd.php';
require_once 'unit_test/Test.php';

try{
	$Test = new \Utils\Cmd\Test\Unit;
	$Test->proc('echo this-test', '', 'this-test');
	$Test->proc_stream('php procs/proc.php', 'An error occurred!', "First message\nSecond message\nThird message\nFinal message");
	$Test->proc_stream('php procs/proc_exit.php', 'An fatal error occurred!', 'First message', 1);
}
catch(\Utils\Cmd\Test\Error $e){
	echo "\n\tTEST FAILED ON COMMAND: ".$e->getMessage()."\n\n";
}