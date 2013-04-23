<?php
require_once("autoloader.php");

//I use this to test tests... ensure testing is working.
class Test_Model extends Test
{
	public function test_basics()
	{
		//TODO: if the article table doesn't exist, then skip the test.
		$article = Model::Factory("article");
		$article->title = "test";
		$article->body = "the test body";
		$article->added = date("Y-m-d H:i:s", time());
		$article->save();
		$article2 = Model::Factory("article", $article->article_id);
		
		//assert the article we get back is the same as the one we made.
		$this->Assert_Equals($article->title, $article2->title);
		$this->Assert_Equals($article->body, $article2->body);
		$this->Assert_Equals($article->added, $article2->added);

		$new_values = array("title" => "new test", "body" => "new body");
		$article->set($new_values);
		$article->save();
		$article2 = Model::Factory("article", $article->article_id);

		$this->Assert_Equals($new_values["title"], $article2->title);
		$this->Assert_Equals($new_values["body"], $article2->body);
	}
}
