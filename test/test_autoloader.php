<?php
function test_autoloader($classname)
{
	$test_class = strtolower(__DIR__ . "/" . str_replace("_", "/", $classname)) . ".php";
	if(file_exists($test_class))
		require_once $test_class;
}

spl_autoload_register("test_autoloader");
