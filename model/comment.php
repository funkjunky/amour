<?php
class Model_Comment extends Model
{
	protected $id_key = "comment_id";

	protected $belongs_to = array(
		'article' => array('foreign_key' => 'article_id'),
	);
}
