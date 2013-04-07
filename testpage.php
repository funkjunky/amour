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
	include "amourdb.php";
	$db = AmourDB::instance();
	print "Before:\n";
	var_dump($db->select("article"));
	print "After:\n";
	$id = $db->insert("article", array(
		"title" => "a THIRD article",
		"body" => "the third body of text",
		"added" => gmdate("Y-m-d H:i:s"),
	));
	var_dump($db->select("article"));
?>
</pre>
</body>
</html>
