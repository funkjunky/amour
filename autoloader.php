<?php
function psr_autoloader($classname)
{
	include_once strtolower(str_replace("_", "/", $classname)) . ".php";
}

spl_autoload_register("psr_autoloader");
