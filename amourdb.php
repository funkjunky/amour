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

	public function select($table, $columns = array("*"))
	{
		$sql = "SELECT ".implode(",", $columns)." FROM $table";
		$statement = $this->dbh->query($sql);

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public function insert($table, $values)
	{
		$sql = "INSERT INTO $table SET " . self::binding_string($values);

		$statement = $this->dbh->prepare($sql);

		$statement->execute(self::binding_array($values));
		var_dump($this->dbh->errorInfo());
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

		return new PDO($connection_string, $user, $pass);
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
