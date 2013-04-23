<?php

/**
 * Copied from Azzimall project. Modified for Amour.
 * This is the core unit test class, all unit test classes will 
 * inheret the functionality behind this class.
 *
 * @author Jason McCarrell <jason.abz@gmail.com>
 * @author Gilles Paquette <gilles@azzimov.com>
 */

class Test 
{
	private $median;
	private $succeeded;
	protected $function;
	protected $assertion;
	protected $assertions;
	protected $failed;
	protected $failure_msg;
	protected $skips_msg;

	/**
	 * @param Testoutput A test output object that states how to display results
	 */
	public function __construct($median)
	{
		$this->median = $median;
	}

	/**
	 * The shell of a before function. This can be extended to run before all
	 * tests, in the specific test class.
	 */
	public function before()
	{
	}

	/**
	 * The shell of an after function. This can be extended to run after all
	 * tests, in the specific test class.
	 */
	public function after()
	{
	}

	/**
	 * allows a message to be attached to either the success or failure of an
	 * assertion
	 *
	 * @param array $messages [0] failure message, [1] success message
	 * @deprecated I'm not going to bother testing this right now.
	 */
	public function msg($messages)
	{
		if($this->succeeded)
			$this->median->output("\t\t\t\t" . $messages[1]);
		else
			$this->median->output("\t\t\t\t" . $messages[0]);
	}

	/**
	 * Skips the rest of this test.
	 *
	 * @param string $msg Message explains why we skiped
	 */
	public function skip($msg)
	{
		$trace = debug_backtrace();
		$this->median->output("\t\t\tSKIPPED " . $trace[1]["function"] . "! Message: " . $msg);
		$trace['msg'] = $msg;
		$this->skips_msg[] = $trace;
	}

	/**
	 * Returns the skips from the controller.
	 *
	 * @return array
	 */
	public function get_skip()
	{
		return $this->skips_msg;
	}

	/**
	 * Get the amount of assertions done by this object
	 * @return int 
	 */
	public function get_assertions()
	{
		return $this->assertions;
	}

	/**
	 * Get the amount of failed assertions by this object
	 * @return int
	 */
	public function get_failed()
	{
		return $this->failed;
	}

	/**
	 * Attempts to get the failures for this unit test
	 *
	 * @return array
	 */
	public function get_failures()
	{
		return $this->failure_msg;	
	}

	/**
	 * A wrapper around assert_success and assert_failed
	 * @param bool $bool used to select success or failed
	 * @param mixed $a
	 * @param mixed $b
	 * @see Assert_failed()
	 * @see Assert_Success()
	 */
	private function Assert($bool, $a, $b = null)
	{
		if($bool)
		{
			$this->Assert_success($a, $b);
		}
		else
		{
			$this->Assert_failed($a, $b);
		}
	}

	/**
	 * Increments the assertions for a failed assertion and logs it.
	 * @param mixed $a
	 * @param mixed $b
	 * @see log_failed()
	 */
	private function Assert_failed($a, $b = null)
	{
		$this->succeeded = false;
		$fulltrace = debug_backtrace();
		$trace = array_merge(
			array("assertion" => array(
				"succeeded" => false,
				"type" => $this->assertion,
				"values" => array($a, $b),
			),),
			$this->get_trace_info()
		);
		$this->median->output_assertion($trace);

		// save the failure
		$this->failure_msg[] = debug_backtrace();

		++$this->assertions;
		++$this->failed;
		$this->log_failed($a, $b);
	}

	/**
	 * Increments the assertions for a successful assertions and logs it.
	 * @param mixed $a
	 * @param mixed $b
	 * @see log_success()
	 */
	private function Assert_success($a, $b = null)
	{
		$this->succeeded = true;
		$trace = array_merge(
			array("assertion" => array(
				"succeeded" => true,
				"type" => $this->assertion,
				"values" => array($a, $b),
			),),
			$this->get_trace_info()
		);
		$this->median->output_assertion($trace);

		++$this->assertions;
		$this->log_success($a, $b);
	}

	/**
	 * Asserts that the first argument is an array.
	 * @param array $a
	 */
	public function Assert_IsArray($a)
	{
		$this->assertion = "IsArray";

		$this->Assert(is_array($a), $a);
	}

	/**
	 * Asserts that the second argument is a key for the first argument.
	 * @param array $a
	 * @param string $b
	 */
	public function Assert_ArrayHasKey($a, $b)
	{
		$this->assertion = "ArrayHasKey";

		if(is_array($a))
		{
			$this->Assert(array_key_exists($b, $a), $a, $b);
		}
		else
		{
			$this->Assert_failed($a, $b);
		}
	}

	/**
	 * Asserts that the second argument is a value in the first argument.
	 * @param array $a
	 * @param mixed $b
	 */
	public function Assert_Contains($a, $b)
	{
		$this->assertion = "Contains";

		if(is_array($a))
		{
			$this->Assert(in_array($b, $a), $a, $b);
		}
		else
		{
			$this->Assert_failed($a, $b);
		}
	}

	/**
	 * Asserts that the first argument has only the second argument as a value.
	 * @param array $a
	 * @param mixed $b
	 */
	public function Assert_ContainsOnly($a, $b)
	{
		$this->assertion = "ContainsOnly";

		if(is_array($a))
		{
			$this->Assert(in_array($b, $a) && (count($a) == 1), $a, $b);
		}
		else
		{
			$this->Assert_failed($a, $b);
		}
	}

	/**
	 * Asserts that the amount of elements in the first argument is equal to the second argument.
	 * @param array $a
	 * @param int $b
	 */
	public function Assert_Count($a, $b)
	{
		$this->assertion = "Count";

		if(is_array($a))
		{
			$this->Assert(count($a) == $b, $a, $b);
		}
		else
		{
			$this->Assert_failed($a, $b);
		}
	}

	/**
	 * Asserts that the first argument is empty.
	 * @param mixed $a
	 */
	public function Assert_Empty($a)
	{
		$this->assertion = "Empty";

		$this->Assert(empty($a), $a);
	}

	/**
	 * Asserts that the two paramaters are equal (==).
	 * @param mixed $a
	 * @param mixed $b
	 */
	public function Assert_Equals($a, $b)
	{
		$this->assertion = "Equals";

		$this->Assert($a == $b, $a, $b);
	}

	/**
	 * Asserts that the two paramaters are equal (===).
	 * @param mixed $a
	 * @param mixed $b
	 */
	public function Assert_TypeEquals($a, $b)
	{
		$this->assertion = "TypeEquals";

		$this->Assert($a === $b, $a, $b);
	}

	/**
	 * Asserts that a file exists at the location in the first paramater.
	 * @param string $a
	 */
	public function Assert_FileExists($a)
	{
		$this->assertion = "FileExists";

		$this->Assert(file_exists($a), $a);
	}

	/**
	 * Asserts that the first argument is greater than the second one.
	 * @param int|float $a
	 * @param int|float $b
	 */
	public function Assert_GreaterThan($a, $b)
	{
		$this->assertion = "GreaterThan";

		$this->Assert($a > $b, $a, $b);
	}

	/**
	 * Asserts that the first argument is greater than or equal to the second one.
	 * @param int|float $a
	 * @param int|float $b
	 */
	public function Assert_GreaterThanOrEqual($a, $b)
	{
		$this->assertion = "GreaterThanOrEqual";

		$this->Assert($a >= $b, $a, $b);
	}

	/**
	 * Asserts that the first argument is less than the second one.
	 * @param int|float $a
	 * @param int|float $b
	 */
	public function Assert_LessThan($a, $b)
	{
		$this->assertion = "LessThan";

		$this->Assert($a < $b, $a, $b);
	}

	/**
	 * Asserts that the first argument is less than or equal to the second one.
	 * @param int|float $a
	 * @param int|float $b
	 */
	public function Assert_LessThanOrEqual($a, $b)
	{
		$this->assertion = "LessThanOrEqual";

		$this->Assert($a <= $b, $a, $b);
	}

	/**
	 * Asserts that the given argument is null.
	 * @param null $a
	 */
	public function Assert_Null($a)
	{
		$this->assertion = "Null";

		$this->Assert(is_null($a), $a);
	}

	/**
	 * Asserts that the given argument is an object.
	 * @param object $a
	 */
	public function Assert_IsObject($a)
	{
		$this->assertion = "IsObject";

		$this->Assert(is_object($a), $a);
	}

	/**
	 * Asserts that the first argument has the second argument as an attribute.
	 * @param object $a
	 * @param string $b
	 */
	public function Assert_ObjectHasAttribute($a, $b)
	{
		$this->assertion = "ObjectHasAttribute";

		if(is_object($a))
		{
			$this->Assert(property_exists($a, $b), $a, $b);
		}
	}

	/**
	 * Asserts that the given argument is a string.
	 * @param string $a
	 */
	public function Assert_IsString($a)
	{
		$this->assertion = "IsString";
		$this->Assert(is_string($a), $a);
	}

	/**
	 * Asserts that the first argument ends with the second argument.
	 * @param string $a
	 * @param string $b
	 */
	public function Assert_StringEndsWith($a, $b)
	{
		$this->assertion = "StringEndsWith";

		if(is_string($a))
		{
			$this->assert(preg_match("/$b$/", $a), $a, $b);
		}
		else
		{
			$this->Assert_failed($a, $b);
		}
	}

	/**
	 * Asserts that the first argument starts with the second argument.
	 * @param string $a
	 * @param string $b
	 */
	public function Assert_StringStartsWith($a, $b)
	{
		$this->assertion = "StringStartsWith";

		if(is_string($a))
		{
			$this->assert(preg_match("/^$b/", $a), $a, $b);
		}
		else
		{
			$this->Assert_failed($a, $b);
		}
	}

	/**
	 * Asserts that the given argument is true
	 * @param bool $a
	 */
	public function Assert_True($a)
	{
		$this->assertion = "True";

		$this->Assert($a, $a);
	}

	/**
	 * Asserts that the given argument is false
	 * @param bool $a
	 */
	public function Assert_False($a)
	{
		$this->assertion = "False";

		$this->Assert( ! $a, $a);
	}

	/**
	 * Logs the transation as a failed transation, more information is logged
	 * @param mixed $a
	 * @param mixed $b
	 */
	public function log_failed($a, $b)
	{
		global $class, $method;

		$fp = fopen('results.log', 'a');
		$msg =  "Failed Assertion\n";
		$msg .= "Class: $class";
		$msg .= " | Method: $method";
		$msg .= " | Assertion: ".$this->assertion."\n";
		$msg .= "Value 1:\t".serialize($a)."\n";
		$msg .= "Value 2:\t".serialize($b)."\n";
		$msg .= "\n";
		fwrite($fp, $msg);
		fclose($fp);
	}

	/**
	 * Logs the transation as a successful transation
	 * @param mixed $a
	 * @param mixed $b
	 */
	public function log_success($a, $b)
	{
		global $class, $method;

		$fp = fopen('results.log', 'a');
		$msg =  "Successful Assertion\n";
		$msg .= "Class: $class";
		$msg .= " | Method: $method";
		$msg .= " | Assertion: ".$this->assertion."\n";
		$msg .= "\n";
		fwrite($fp, $msg);
		fclose($fp);
	}

	//the 5th element should be the test function.
	//get_trace_info->assert_failed/succeeded->assert->assert_x->test_fnc
	private function get_trace_info($test_deepness = 4)
	{
		$trace = debug_backtrace();
		$assert_trace = array(
			"class" => $trace[$test_deepness]["class"],
			"function" => $trace[$test_deepness]["function"],
			"line" => $trace[$test_deepness]["line"],
			"file" => $trace[$test_deepness]["file"],
			"stacktrace" => array_slice($trace, $test_deepness),
		);

		return $assert_trace;
	}
}
