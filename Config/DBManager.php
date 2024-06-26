<?php

define('ENVIRONMENT', 'development');

date_default_timezone_set('Asia/Kolkata');
/*
*
* Author : Kumari-S.
* Class : DBManager Class,
* This class is used for making a database connection,
* This class returns a connection object.
*
*/
class DBManager
{

	/*
	*
	* Database connection parameters.
	* The following parameters are used for making a database connection,
	*
	*/

	private $host     = 'localhost';
	private $dbname   = 'testcrud';
	private $username = 'root';
	private $password = '';


	public $dbconn = '';

	/*
	*
	* Method : Constructor.
	*
	*/

	function __construct()
	{
		$this->connect();
	}

	function connect()
	{

		try {

			if (defined('ENVIRONMENT')) {
				switch (ENVIRONMENT) {
					case 'development':
						error_reporting(E_ALL);
						break;

					case 'testing':
					case 'production':
						error_reporting(0);
						break;

					default:
						exit('The application environment is not set correctly.');
				}
			}
			$this->dbconn = new PDO(
				"mysql:host=$this->host;port=3306;dbname=$this->dbname",
				$this->username,
				$this->password
			);
			$this->dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			//echo 'Successfully connected to the database!';

		} catch (PDOException $e) {

			echo 'We\'re sorry but there was an error while trying to connect to the database';
			file_put_contents('connection.errors.txt', $e->getMessage() . PHP_EOL, FILE_APPEND);
		}
	}
}
