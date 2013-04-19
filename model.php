<?php
/**
 *	Notes:
 * There are two ways to identify the table, either name the class Model_<table>
 * or set the table_name variable in the extended class.
 */
class Model implements serializable
{
	public $dbh;
	public $cache;

	private $table_name;

	private $id;

	public function __constructor($id)
	{
		if(!isset($this->table_name))
			$this->table_name = strtolower(substr(get_class($this), 6));

		$this->id = $id;

		$this->dbh = AmourDB::instance();
	}

	public function __get($key)
	{
		if(isset($this->cache[$key]))
			return $this->cache[$key];

		$this->dbh->select($this->table_name);
	}
}
