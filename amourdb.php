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
		var_dump($this->dbh->errorInfo());

		return $statement->fetchAll(PDO::FETCH_ASSOC);
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
		var_dump($connection_string);
		return new PDO($connection_string, $user, $pass);
	}
}
