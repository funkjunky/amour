<?php

//I use this to test tests... ensure testing is working.
class Test_Test extends Test
{
	public function test_simple()
	{
		$this->Assert_True(true);
		$this->Assert_True(false);
		$this->skip("skippy skip skip");
	}
}
