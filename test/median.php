<?php

abstract class Median
{
	protected $tests;

	abstract public function output($message);
	abstract public function output_assertion($test_trace);
	public function get_all_tests($path="test")
	{
		$this->tests = array();
		$files = dir(__DIR__ . "/" . $path);
		while(false !== ($file = $files->read()))
			//file isn't hidden or a directory.
			if(substr($file, 0, 1) !== ".")
			$this->tests[] = str_replace("/", "_", $path) . "_" . substr($file, 0, -4);
	}

	public function __get($key)
	{
		return $this->$key;
	}
}
