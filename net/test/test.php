<?php

require_once '../../Net.php';

try{
	$Net = new \Utils\Net\Net;
	$Net->request('https://www.google.com');
	
	/*$Test = new \Utils\Cmd\Test\Unit;
	$Test->proc('echo this-test', '', 'this-test');
	$Test->proc_stream('php procs/proc.php', 'An error occurred!', "First message\nSecond message\nThird message\nFinal message");
	$Test->proc_stream('php procs/proc_exit.php', 'An fatal error occurred!', 'First message', 1);*/
}
catch(\Utils\Net\Error $e){
	echo "\n\tTEST FAILED ON URL: ".$e->getMessage()."\n\n";
}