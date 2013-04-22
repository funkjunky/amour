<?php
function test_autoloader($classname)
{
	include_once strtolower(__DIR__ . "/" . str_replace("_", "/", $classname)) . ".php";
}

spl_autoload_register("test_autoloader");
