<?php

/**
 * implodes an array including the entire pair by index_del, and
 * seperating the pair by pair_del.
 */
function implode_kvp($array, $index_del, $pair_del)
{
	$ret = ""; $i = 0;
	foreach($array as $k => $v)
		$ret .= $k . $pair_del . $v . ((++$i < count($array)) ? $index_del : "");

	return $ret;
}
