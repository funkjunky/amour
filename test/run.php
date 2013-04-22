<?php
require_once('test_autoloader.php');

if(php_sapi_name() == "cli")
{
	$median = new Median_Cli();
} else
{
	$median = new Median_Html();
}

$median->output("---Beginning tests---");
foreach($median->tests as $file)
{
	$median->output("\t--Opening file $file--");
	$testclass = new $file($median);
	$testmethods = get_class_methods($testclass);
	foreach($testmethods as $testmethod)
		if(strpos($testmethod, "test_") !== false)
		{
			$median->output("\t\t-Running test $testmethod-");
			$testclass->$testmethod();
			$median->output("\t\t-Done test $testmethod.-");
		}
	$median->output("\t--Closed file $file--");
}
$median->output("---Done running tests---");
