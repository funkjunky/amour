<?php
require_once "extra.php";

/**
 * THis is the class that does the basic DB stuff.
 * Use it by initially getting an instance. I choose using singleton over
 * static, because singleton has an easier transition to using multiple
 * instances, which I could see potentially becoming a thing.
 */
class AmourDB
{
	const CONFIG_DB = "config/db.php";
	private static $singleton;

	protected $dbh;
	protected $settings;

	/**
	 * loads the settings for this instance of amourDB, including PDO object.
	 */
	public function __construct($settings = null)
	{
		if($settings === null)
			$settings = require self::CONFIG_DB;

		$this->dbh = self::buildPDO($settings);
	}

	public function select($table, $columns = array("*"), $filters = null)
	{
		$sql = "SELECT ".implode(",", $columns)." FROM $table";

		//add any filters that exist
		if(!is_array($filters))
			$statement = $this->dbh->query($sql);
		else
		{
			$statement = $this->get_filtered_statement($sql, $filters);
			$statement->execute();
		}

		//return rows
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function insert($table, $values)
	{
		$sql = "INSERT INTO $table SET " . self::binding_string($values);

		$statement = $this->dbh->prepare($sql);

		$statement->execute(self::binding_array($values));
	}

	/**
	 * @param array $filters an array of arrays
	 */
	public function update($table, $values, $filters)
	{
		$sql = "UPDATE $table SET " . self::binding_string($values);

		//add any filters
		if(count($filters) > 0)
			$statement = $this->get_filtered_statement($sql, $filters);

		//bind the set variables.
		foreach(self::binding_array($values) as $k => $v)
			$statement->bindValue($k, $v);

		//execute the update while applying additional bindings.
		$statement->execute();
	}

	/**
	 * @param array $filters an array of arrays
	 */
	public function delete($table, $filters)
	{
		$sql = "DELETE FROM $table";

		//add any filters
		if(count($filters) > 0)
			$statement = $this->get_filtered_statement($sql, $filters);

		//execute the update while applying additional bindings.
		$statement->execute();
	}

	private function get_filtered_statement($sql, $filters)
	{
		//build the where clause
		$sql .= " WHERE ";
		foreach($filters as $filter)
			$sql .= $filter[0] . " " . $filter[1] . " :" . $filter[0] . " ";

		//prepare the sql with parameter binding
		$statement = $this->dbh->prepare($sql);
		foreach($filters as $filter)
			$statement->bindValue(":".$filter[0], $filter[2]);

		return $statement;
	}

	//~~STATIC~~//

	public static function instance($settings = null)
	{
		if(isset(self::$singleton))
			return self::$singleton;
		else
			return self::$singleton = new AmourDB($settings);
	}

	private static function buildPDO($settings)
	{
		$db = $settings['db']; unset($settings['db']);
		$user = $settings['user']; unset($settings['user']);
		$pass = $settings['pass']; unset($settings['pass']);

		$connection_string = $db . ":" . implode_kvp($settings, ";", "=");

		return new PDO($connection_string, $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	}

	private static function binding_string($params)
	{
		$keys = array_keys($params);
		$ret = ""; $i = 0;
		foreach($params as $k => $v)
			$ret .= "$k = :$k" . ((++$i < count($params)) ? ", " : "");

		return $ret;
	}

	private static function binding_array($params)
	{
		$colonized_keys = array_keys($params);
		array_walk($colonized_keys, function(&$item, $k) {
			$item = ":" . $item;
		});

		return array_combine($colonized_keys, array_values($params));
	}
}
