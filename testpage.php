<html>
<head><title>Amour Test Page</title></head>
<body>
<h1>Amour</h1>
<pre>
I'll be testing the functionality of Amour things here.
Eventually this should be replaced with a test suite, then it should be brought back as a demo. :)
</pre>
<hr />
<pre>
<?php
	require_once "autoloader.php";
	$db = AmourDB::instance();
	print "Before:\n";
	var_dump($db->select("article"));
	print "After:\n";
	try{
	$db->replace("article", array(
		"title" => "a THIRD article",
		"body" => "the third body of text",
		"added" => gmdate("Y-m-d H:i:s"),
	));
	} catch(Exception $e) {
		print "Error: " . $e->getMessage();
	}
	var_dump($db->select("article", array("body"), array(array("title", "LIKE", "%article"))));
	$db->update("article", array(
		"body" => "new body using update! VERY UPDATED!",
	), array(array("title", "LIKE", "%THIRD%")));
	$db->delete("article", array(array("title", "LIKE", "%NEW%")));
	print "VERY AFTER:\n";
	var_dump($db->select("article", array("body"), array(array("title", "LIKE", "%article"))));
?>
</pre>
</body>
</html>
