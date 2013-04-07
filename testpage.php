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
	var_dump($db->select("article"));
?>
</pre>
</body>
</html>
