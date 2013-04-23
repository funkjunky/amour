<?php

class Median_Cli extends Median
{
	public function __construct()
	{
		$this->get_all_tests();
		//get arguments
		//TODO: add in argument parsing... for now do all tests.
	}

	public function output($message)
	{
		print $message . "\n";
	}

	public function output_assertion($test_trace)
	{
		$message = "\t\t\t";
		if($test_trace['assertion']['succeeded'])
			$message .= "SUCCESS. " . $test_trace['assertion']['type'] . " assertion.";
		else
			$message .= "FAILURE! " . $test_trace['assertion']['type'] . " assertion. "
				. $test_trace['class'] . "->" . $test_trace['function'] . "()"
				. " line #" . $test_trace['line'] 
				. " { " . implode(" [".$test_trace['assertion']['type']."] ", $test_trace['assertion']['values']) . " => FALSE }";

		$this->output($message);
	}
}
