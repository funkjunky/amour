<?php
/**
 *	Notes:
 * There are two ways to identify the table, either name the class Model_<table>
 * or set the table_name variable in the extended class.
 * the primary key is id by default, but it is encouraged to name your own key
 * set it using id_key.
 */
class Model implements serializable
{
	public $dbh;
	public $values;

	private $id;

	protected $table_name;
	protected $id_key;

	public function __constructor($id=null)
	{
		if(!isset($this->table_name))
			$this->table_name = strtolower(substr(get_class($this), 6));

		$this->values = array();

		if($id !== null)
			$this->id = $this->values[$this->id_key] = $id;
		//set the default id key to be 'id'
		if(!isset($this->id_key))
			$this->id_key = 'id';

		$this->dbh = AmourDB::instance();
	}

	public function __get($key)
	{
		if(isset($this->values[$key]))
			return $this->values[$key];

		if(!isset($this->id))
			die('JOO NEED AN ID TO GET A VALUE!'); //obviously need better error ha

		$result = $this->dbh->select($this->table_name, array($key), array(
			array($this->id_key, '=', $this->id),
		));

		return $this->values[$key] = $result[0][$key];
	}

	public function __set($key, $value)
	{
		$this->values[$key];
	}

	public function set($values)
	{
		$this->values = array_merge($values, $this->values);
	}

	//we just use replace, because it's a nice function...
	public function save()
	{
		$this->dbh->replace($this->table_name, $this->values);
	}

	//remove the item from the db and clears the values.
	public function delete()
	{
		if(isset($this->id))
			$this->dbh->delete($this->table_name, array(
				array($this->id_key, '=', $this->id),
			));

		$this->values = array();
	}

	public static function instance($id = null)
	{
		return new self($id);
	}

	//we always get the id, because the model needs it.
	public static function Find_all($columns = array('*'), $filters)
	{
		//add the id to our select list, so every model has a ref.
		if($columns != array('*'))
			$columns = array_merge($columns, array($this->id_key));

		$results = $this->dbh->select(
			$this->table_name,
			implode_kvp($columns, ", ", " = "),
			$filters
		);

		//iterate through the rows and create model objects to return
		$models = array();
		foreach($results as $row)
			$models[$row[$this->id_key]] = self::instance()->set($row);

		return $models;
	}
}
