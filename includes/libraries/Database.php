<?php

	/*
	 *	Aruna Development Project
	 *	IS NOT FREE SOFTWARE
	 *	Codename: Aruna Personal Site
	 *	Source: Based on Sosiaku Social Networking Software
	 *	Website: https://www.sosiaku.gq
	 *	Website: https://www.aruna-dev.id
	 *	Created and developed by Andhika Adhitia N
	 */

defined('BASEPATH') OR exit('No direct script access allowed');

class ARUNA_Database {

	/**
	 * DEFAULT Variable Connection
	 *
	 * @var	string
	 */

	private $conn = NULL;

	/**
	 * DEFAULT DB TYPE statement string
	 *
	 * @var	string
	 */

	private $dbtype = NULL;

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @param	array	$params
	 * @return	void
	 */

	function __construct(string $active_group = '') 
	{

		if ( ! file_exists($file_path = BASEPATH.'config/database.php')) 
		{
			show_error('The configuration file database.php does not exist.');
		}

		// Include Database Settings
		include(BASEPATH.'config/database.php');

		if ( ! isset($db) OR count($db) === 0) 
		{
			show_error('No database connection settings were found in the database config file.');
		}

		if ( ! isset($active_group)) 
		{
			show_error('You have not specified a database connection group via $active_group in your config/database.php file.');
		}
		elseif ( ! isset($db[$active_group])) 
		{
			show_error('You have specified an invalid database connection group ('.$active_group.') in your config/database.php file.');
		}

		if (empty($db[$active_group]['dbname'])) 
		{
			show_error('No database name selected');
		}

		if (empty($db[$active_group]['dbtype']))
		{
			show_error('The database type is not selected or empty');
		}

		if (empty($db[$active_group]['charset']))
		{
			show_error('The character set for database is empty');
		}

		$this->dbtype = 'PDO_'.$db[$active_group]['dbtype'];
		$this->conn = $this->sql_connect($db[$active_group]['host'], $db[$active_group]['user'], $db[$active_group]['password'], $db[$active_group]['dbname'], $db[$active_group]['charset']);
		
		return $this->conn;
	}
	
	public function sql_connect($host, $user, $password, $db, $charset) 
	{
		switch ($this->dbtype) 
		{
			case "PDO_MySQL":
				try 
				{
					$get_charset = ! empty($charset) ? ';charset='.$charset : ''; 

					$dbi = new PDO("mysql:host=$host;dbname=$db$get_charset", $user, $password);
					$dbi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$dbi->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
				}
				catch (PDOExecption $e) 
				{
					show_error($e->getMessage());
				}
				return $dbi;
				break;

			case "PDO_SQLSRV":
				try 
				{
					$dbi = new PDO("sqlsrv:Server=$host;database=$db", $user, $password);
					$dbi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

					if ( ! empty($charset) && preg_match('/utf[^8]*8/i', $charset))
					{
						$dbi->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_UTF8);
					}
				}
				catch (PDOExecption $e) 
				{
					show_error($e->getMessage());
				}
				return $dbi;
				break;

			case "PDO_PostgreSQL":
				try 
				{
					$get_charset = ! empty($charset) ? ";options=\'--client_encoding=".$charset."\'" : ''; 

					$dbi = new PDO("pgsql:host=$host;dbname=$db$get_charset", $user, $password);
					$dbi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					$dbi->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
				}
				catch (PDOExecption $e) 
				{
					show_error($e->getMessage());
				}
				return $dbi;
				break;

			default:
				break;
		}
	}

	public function sql_prepare(string $query, string $type_query = NULL) 
	{
		switch ($this->dbtype) 
		{
			case 'PDO_MySQL':
				try 
				{
					if ( ! $this->conn) 
					{
						show_error('Cannot connect to MySQL Server, please check your connection database.');
					}
					$result = $this->conn->prepare($query);

					if ($type_query == 'select') 
					{
						$result->execute();
					}
				}
				catch (PDOException $e) 
				{
					show_error("SQL query error!<br><b>Error infomation:</b> <div style='padding:1rem'><hr><br>[SQL]<br>".$e->getMessage()."<br><br>[Error]<br><font color=red>".$query."</font><br><br><hr>You can get help by sending this infomation to us: <a href='https://www.aruna-dev.id' target='_blank'>https://www.aruna-dev.id</a></div>");
				}
				return $result;
			 	break;

			case 'PDO_SQLSRV':
				try 
				{
					if ( ! $this->conn) 
					{
						show_error('Cannot connect to SQL Server, please check your connection database.');
					}
					$result = $this->conn->prepare($query);

					if ($type_query == 'select') 
					{
						$result->execute();
					}
				}
				catch (PDOException $e) 
				{
					show_error("SQL query error!<br><b>Error infomation:</b> <div style='padding:1rem'><hr><br>[SQL]<br>".$e->getMessage()."<br><br>[Error]<br><font color=red>".$query."</font><br><br><hr>You can get help by sending this infomation to us: <a href='https://www.aruna-dev.id' target='_blank'>https://www.aruna-dev.id</a></div>");
				}
				return $result;
			 	break;

			case 'PDO_PostgreSQL':
				try 
				{
					if ( ! $this->conn) 
					{
						show_error('Cannot connect to PostgreSQL Server, please check your connection database.');
					}
					$result = $this->conn->prepare($query);

					if ($type_query == 'select') 
					{
						$result->execute();
					}
				}
				catch (PDOException $e) 
				{
					show_error("SQL query error!<br><b>Error infomation:</b> <div style='padding:1rem'><hr><br>[SQL]<br>".$e->getMessage()."<br><br>[Error]<br><font color=red>".$query."</font><br><br><hr>You can get help by sending this infomation to us: <a href='https://www.aruna-dev.id' target='_blank'>https://www.aruna-dev.id</a></div>");
				}
				return $result;
			 	break;

			default:
				break;
		}
	}

	public function sql_bindParam(array $att, $stmt) 
	{
		switch ($this->dbtype) 
		{
			case "PDO_MySQL":
				if (is_array($att)) 
				{
					foreach ($att as $key => &$value) 
					{
						if (is_int($value)) 
						{
							$stmt->bindParam(":".$key."", $value, PDO::PARAM_INT);
						}
						else 
						{
	  						$stmt->bindParam(":".$key."", $value, PDO::PARAM_STR);
	  					}
	  				}
	  			}
				else
				{
					show_error('Your data must be an array.');
				}

	  			$stmt->execute();

				return $stmt;
				break;

			case "PDO_SQLSRV":
				if (is_array($att)) 
				{
					foreach ($att as $key => &$value) 
					{
						if (is_int($value)) 
						{
							$stmt->bindParam(":".$key."", $value, PDO::PARAM_INT);
						}
						else 
						{
	  						$stmt->bindParam(":".$key."", $value, PDO::PARAM_STR);
	  					}
	  				}
				}
				else
				{
					show_error('Your data must be an array.');
				}

	  			$stmt->execute();

				return $stmt;
				break;

			case "PDO_PostgreSQL":
				if (is_array($att)) 
				{
					foreach ($att as $key => &$value) 
					{
						if (is_int($value)) 
						{
							$stmt->bindParam(":".$key."", $value, PDO::PARAM_INT);
						}
						else 
						{
	  						$stmt->bindParam(":".$key."", $value, PDO::PARAM_STR);
	  					}
	  				}
				}
				else
				{
					show_error('Your data must be an array.');
				}

	  			$stmt->execute();

				return $stmt;
				break;

			default:
				break;
		}
	}

	public function sql_fetch_array(&$res, $fetch_style = PDO::FETCH_ASSOC) 
	{
		switch ($this->dbtype) 
		{
			case "PDO_MySQL":
				$row = $res->fetch($fetch_style);
				return $row;
				break;

			case "PDO_SQLSRV":
				$row = $res->fetch($fetch_style);
				return $row;
				break;

			case "PDO_PostgreSQL":
				$row = $res->fetch($fetch_style);
				return $row;
				break;
		
			default:
				break;
		}
	}

	/**
	 * Memeriksa data di table database dan juga bisa
	 * menampilkan jumlah baris data di table database, tetapi
	 * saya merekomendasikan menggunakan fungsi ini untuk memerika data di table database
	 * gunakan fungsi num_rows() untuk menampilkan jumlah baris data di table database
	 *
	 * @return int or boolean
	 */

	public function sql_counts($res) 
	{
		switch ($this->dbtype) 
		{
			case "PDO_MySQL":
				$rows = $res->rowCount();
				return $rows;
				break;

			case "PDO_SQLSRV":
				$rows = $res->rowCount();
				return $rows;
				break;

			case "PDO_PostgreSQL":
				$rows = $res->rowCount();
				return $rows;
				break;

			default;
				break;
		}
	}

	/**
	 * Number of rows in the result set
	 *
	 * @return	int
	 */

	public function num_rows(string $table = NULL, string $alias = NULL, array $params = [])
	{
		if (empty($table))
		{
			show_error('Cannot get table name database');
		}

		if ( ! empty($alias))
		{
			if ( ! empty($params) && is_array($params))
			{
				$target = NULL;

				foreach ($params as $key => $val) 
				{
					$target .= $target ? " and $key = :$key" : "$key = :$key";
				}

				$res = $this->sql_prepare("select count(*) as $alias from $table where $target");
				$bindParam = $this->sql_bindParam($params, $res);
				$row = $this->sql_fetch_array($bindParam);

				return $row;
			}
			else
			{
				$res = $this->sql_prepare("select count(*) as $alias from $table", "select");
				$row = $this->sql_fetch_array($res);

				return $row;
			}
		}
		elseif ( ! empty($params) && is_array($params))
		{
			$target = NULL;
			
			foreach ($params as $key => $val) 
			{
				$target .= $target ? " and $key = :$key" : "$key = :$key";
			}

			$res = $this->sql_prepare("select * from $table where $target");
			$bindParam = $this->sql_bindParam($params, $res);
			$row = $this->sql_counts($bindParam);

			return $row;
		}
		else 
		{
			$res = $this->sql_prepare("select * from $table", "select");
			$row = $this->sql_counts($res);

			return $row;
		}
	}

	public function insert_id() 
	{
		switch ($this->dbtype) 
		{
			case "PDO_MySQL":
				if ( ! $this->conn) 
				{
					show_error('Cannot connect to MySQL Server, please check your connection database.');
				}

				$rows = $this->conn->lastInsertId();
				return $rows;
				break;

			case "PDO_SQLSRV":
				if ( ! $this->conn) 
				{
					show_error('Cannot connect to Microsoft SQL Server, please check your connection database.');
				}

				$rows = $this->conn->lastInsertId();
				return $rows;
				break;

			case "PDO_PostgreSQL":
				if ( ! $this->conn) 
				{
					show_error('Cannot connect to PostgreSQL Server, please check your connection database.');
				}

				$rows = $this->conn->lastInsertId();
				return $rows;
				break;
		
			default:
				break;
		}
	}

	public function sql_insert(array $att, string $table_name) 
	{
		switch ($this->dbtype) 
		{
			case "PDO_MySQL":
				try 
				{
					if ( ! $this->conn) 
					{
						show_error('Cannot connect to MySQL Server, please check your connection database.');
					}

					$sql = "INSERT INTO $table_name (".$this->bindFields($att).") VALUES (".$this->bindVals($att).")";
					$stmt = $this->conn->prepare($sql);

					if (is_array($att))
					{
						foreach ($att as $key => &$value) 
						{
							if (is_int($value)) 
							{
								$stmt->bindParam(":".$key."", $value, PDO::PARAM_INT);
							}
							else 
							{
								$stmt->bindParam(":".$key, $value, PDO::PARAM_STR);
							}
						}
					}
					else
					{
						show_error('Your data must be an array.');
					}
	
					$stmt->execute();
				}
				catch (PDOExecption $e) 
				{
					show_error($e->getMessage());
				}

				return $stmt;
				break;

			case "PDO_SQLSRV":
				try 
				{
					if ( ! $this->conn) 
					{
						show_error('Cannot connect to Microsoft SQL Server, please check your connection database.');
					}

					$sql = "INSERT INTO $table_name (".$this->bindFields($att).") VALUES (".$this->bindVals($att).")";
					$stmt = $this->conn->prepare($sql);

					if (is_array($att))
					{
						foreach ($att as $key => &$value) 
						{
							if (is_int($value)) 
							{
								$stmt->bindParam(":".$key."", $value, PDO::PARAM_INT);
							}
							else 
							{
								$stmt->bindParam(":".$key, $value, PDO::PARAM_STR);
							}
						}
					}
					else
					{
						show_error('Your data must be an array.');
					}

					$stmt->execute();
				}
				catch (PDOExecption $e) 
				{
					show_error($e->getMessage());
				}

				return $stmt;
				break;

			case "PDO_PostgreSQL":
				try 
				{
					if ( ! $this->conn) 
					{
						show_error('Cannot connect to PostgreSQL Server, please check your connection database.');
					}

					$sql = "INSERT INTO $table_name (".$this->bindFields($att).") VALUES (".$this->bindVals($att).")";
					$stmt = $this->conn->prepare($sql);

					if (is_array($att))
					{
						foreach ($att as $key => &$value) 
						{
							if (is_int($value)) 
							{
								$stmt->bindParam(":".$key."", $value, PDO::PARAM_INT);
							}
							else 
							{
								$stmt->bindParam(":".$key, $value, PDO::PARAM_STR);
							}
						}
					}
					else
					{
						show_error('Your data must be an array.');
					}

					$stmt->execute();
				}
				catch (PDOExecption $e) 
				{
					show_error($e->getMessage());
				}

				return $stmt;
				break;

			default:
				break;
		}
	}

	public function sql_update($att, $table_name, $arg = 0) 
	{
		switch ($this->dbtype) 
		{
			case 'PDO_MySQL':
				try 
				{
					if ( ! $this->conn) 
					{
						show_error('Cannot connect to MySQL Server, please check your connection database.');
					}

					$target = NULL;
		
					if (is_array($arg)) 
					{
						if ($this->array_equal($arg, $att))
						{
							$getres = array_intersect_key($arg, $att);

							foreach ($getres as $key => $val) 
							{
								$keym = $key."2";
								$target .= $target ? " and $key = :$keym" : "$key = :$keym";
							}
						}
						else
						{
							foreach ($arg as $key => $val) 
							{
								$target .= $target ? " and $key = :$key" : "$key = :$key";
							}
						}
					}

					$sql = "UPDATE $table_name SET ".$this->bindField_Update($att)." where $target";
					$stmt = $this->conn->prepare($sql);

					if (is_array($arg)) 
					{
						if ($this->array_equal($arg, $att))
						{
							$getres2 = array_intersect_key($arg, $att);

							foreach ($getres2 as $key => &$value) 
							{
								$keym2 = $key."2";
								
								if (is_int($value)) 
								{
									$stmt->bindParam(":".$keym2."", $value, PDO::PARAM_INT);
								}
								else 
								{
									$stmt->bindParam(":".$keym2, $value, PDO::PARAM_STR);
								}
							}
						}
						else
						{
							foreach ($arg as $key => &$value) 
							{
								if (is_int($value)) 
								{
									$stmt->bindParam(":".$key."", $value, PDO::PARAM_INT);
								}
								else 
								{
									$stmt->bindParam(":".$key, $value, PDO::PARAM_STR);
								}
							}
						}
					}
					else
					{	
						show_error('Your data must be an array.');
					}

					if (is_array($att)) 
					{
						foreach ($att as $key => &$value) 
						{
							if (is_int($value)) 
							{
								$stmt->bindParam(":".$key."", $value, PDO::PARAM_INT);
							}
							else 
							{
								$stmt->bindParam(":".$key, $value, PDO::PARAM_STR);
							}
						}
					}
					else
					{	
						show_error('Your data must be an array.');
					}

					$stmt->execute();
				}
				catch (PDOExecption $e) 
				{
					show_error($e->getMessage());
				}
				return $stmt;
				break;

			case 'PDO_SQLSRV':
				try 
				{
					if ( ! $this->conn) 
					{
						show_error('Cannot connect to Microsoft SQL Server, please check your connection database.');
					}

					$target = NULL;
		
					if (is_array($arg)) 
					{
						if ($this->array_equal($arg, $att))
						{
							$getres = array_intersect_key($arg, $att);

							foreach ($getres as $key => $val) 
							{
								$keym = $key."2";
								$target .= $target ? " and $key = :$keym" : "$key = :$keym";
							}
						}
						else
						{
							foreach ($arg as $key => $val) 
							{
								$target .= $target ? " and $key = :$key" : "$key = :$key";
							}
						}
					}

					$sql = "UPDATE $table_name SET ".$this->bindField_Update($att)." where $target";
					$stmt = $this->conn->prepare($sql);

					if (is_array($arg)) 
					{		
						if ($this->array_equal($arg, $att))
						{
							$getres2 = array_intersect_key($arg, $att);

							foreach ($getres2 as $key => &$value) 
							{
								$keym2 = $key."2";
								
								if (is_int($value)) 
								{
									$stmt->bindParam(":".$keym2."", $value, PDO::PARAM_INT);
								}
								else 
								{
									$stmt->bindParam(":".$keym2, $value, PDO::PARAM_STR);
								}
							}
						}
						else
						{
							foreach ($arg as $key => &$value) 
							{
								if (is_int($value)) 
								{
									$stmt->bindParam(":".$key."", $value, PDO::PARAM_INT);
								}
								else 
								{
									$stmt->bindParam(":".$key, $value, PDO::PARAM_STR);
								}
							}
						}
					}
					else
					{	
						show_error('Your data must be an array.');
					}

					if (is_array($att)) 
					{
						foreach ($att as $key => &$value) 
						{
							if (is_int($value)) 
							{
								$stmt->bindParam(":".$key."", $value, PDO::PARAM_INT);
							}
							else 
							{
								$stmt->bindParam(":".$key, $value, PDO::PARAM_STR);
							}
						}
					}
					else
					{	
						show_error('Your data must be an array.');
					}

					$stmt->execute();
				}
				catch (PDOExecption $e) 
				{
					show_error($e->getMessage());
				}
				return $stmt;
				break;

			case 'PDO_PostgreSQL':
				try 
				{
					if ( ! $this->conn) 
					{
						show_error('Cannot connect to PostgreSQL Server, please check your connection database.');
					}

					$target = NULL;
		
					if (is_array($arg)) 
					{
						if ($this->array_equal($arg, $att))
						{
							$getres = array_intersect_key($arg, $att);

							foreach ($getres as $key => $val) 
							{
								$keym = $key."2";
								$target .= $target ? " and $key = :$keym" : "$key = :$keym";
							}
						}
						else
						{
							foreach ($arg as $key => $val) 
							{
								$target .= $target ? " and $key = :$key" : "$key = :$key";
							}
						}
					}

					$sql = "UPDATE $table_name SET ".$this->bindField_Update($att)." where $target";
					$stmt = $this->conn->prepare($sql);

					if (is_array($arg)) 
					{		
						if ($this->array_equal($arg, $att))
						{
							$getres2 = array_intersect_key($arg, $att);

							foreach ($getres2 as $key => &$value) 
							{
								$keym2 = $key."2";
								
								if (is_int($value)) 
								{
									$stmt->bindParam(":".$keym2."", $value, PDO::PARAM_INT);
								}
								else 
								{
									$stmt->bindParam(":".$keym2, $value, PDO::PARAM_STR);
								}
							}
						}
						else
						{
							foreach ($arg as $key => &$value) 
							{
								if (is_int($value)) 
								{
									$stmt->bindParam(":".$key."", $value, PDO::PARAM_INT);
								}
								else 
								{
									$stmt->bindParam(":".$key, $value, PDO::PARAM_STR);
								}
							}
						}
					}
					else
					{	
						show_error('Your data must be an array.');
					}

					if (is_array($att)) 
					{
						foreach ($att as $key => &$value) 
						{
							if (is_int($value)) 
							{
								$stmt->bindParam(":".$key."", $value, PDO::PARAM_INT);
							}
							else 
							{
								$stmt->bindParam(":".$key, $value, PDO::PARAM_STR);
							}
						}
					}
					else
					{	
						show_error('Your data must be an array.');
					}

					$stmt->execute();
				}
				catch (PDOExecption $e) 
				{
					show_error($e->getMessage());
				}
				return $stmt;
				break;

			default:
				break;
		}
	}

	public function sql_delete($table_name, array $arg = []) 
	{
		switch ($this->dbtype) 
		{
			case 'PDO_MySQL':
				try 
				{
					if ( ! $this->conn) 
					{
						show_error('Cannot connect to MySQL Server, please check your connection database.');
					}

					$target = NULL;
		
					if (is_array($arg)) 
					{
						foreach ($arg as $key => $val) 
						{
							$target .= $target ? " and $key = :$key" : "$key = :$key";
						}
					}
					else
					{	
						show_error('Your data must be an array.');
					}

					$sql = "DELETE FROM $table_name WHERE $target";
					$stmt = $this->conn->prepare($sql);

					if (is_array($arg)) 
					{		
						foreach ($arg as $key => &$value) 
						{
							if (is_numeric($value)) 
							{
								$stmt->bindParam(":".$key."", $value, PDO::PARAM_INT);
							}
							else 
							{
								$stmt->bindParam(":".$key, $value, PDO::PARAM_STR);
							}
						}
					}
					else
					{	
						show_error('Your data must be an array.');
					}

					$stmt->execute();
				}
				catch (PDOExecption $e) 
				{
					show_error($e->getMessage());
				}
				return $stmt;
				break;

			case 'PDO_SQLSRV':
				try 
				{
					if ( ! $this->conn) 
					{
						show_error('Cannot connect to Microsoft SQL Server, please check your connection database.');
					}

					$target = NULL;
		
					if (is_array($arg)) 
					{
						foreach ($arg as $key => $val) 
						{
							$target .= $target ? " and $key = :$key" : "$key = :$key";
						}
					}
					else
					{	
						show_error('Your data must be an array.');
					}

					$sql = "DELETE FROM $table_name WHERE $target";
					$stmt = $this->conn->prepare($sql);

					if (is_array($arg)) 
					{		
						foreach ($arg as $key => &$value) 
						{
							if (is_int($value)) 
							{
								$stmt->bindParam(":".$key."", $value, PDO::PARAM_INT);
							}
							else 
							{
								$stmt->bindParam(":".$key, $value, PDO::PARAM_STR);
							}
						}
					}
					else
					{	
						show_error('Your data must be an array.');
					}

					$stmt->execute();
				}
				catch (PDOExecption $e) 
				{
					show_error($e->getMessage());
				}
				return $stmt;
				break;

			case 'PDO_PostgreSQL':
				try 
				{
					if ( ! $this->conn) 
					{
						show_error('Cannot connect to PostgreSQL Server, please check your connection database.');
					}

					$target = NULL;
		
					if (is_array($arg)) 
					{
						foreach ($arg as $key => $val) 
						{
							$target .= $target ? " and $key = :$key" : "$key = :$key";
						}
					}
					else
					{	
						show_error('Your data must be an array.');
					}

					$sql = "DELETE FROM $table_name WHERE $target";
					$stmt = $this->conn->prepare($sql);

					if (is_array($arg)) 
					{		
						foreach ($arg as $key => &$value) 
						{
							if (is_int($value)) 
							{
								$stmt->bindParam(":".$key."", $value, PDO::PARAM_INT);
							}
							else 
							{
								$stmt->bindParam(":".$key, $value, PDO::PARAM_STR);
							}
						}
					}
					else
					{	
						show_error('Your data must be an array.');
					}

					$stmt->execute();
				}
				catch (PDOExecption $e) 
				{
					show_error($e->getMessage());
				}
				return $stmt;
				break;

			default:
				break;
		}
	}

	private function bindFields($fields) 
	{
		end($fields); $lastField = key($fields);
		$bindString = ' ';

		foreach ($fields as $field => $data) 
		{
	        $bindString .= $field;
	        $bindString .= ($field === $lastField ? ' ' : ', ');
		}

		return $bindString;
	}

	private function bindVals($fields) 
	{
		end($fields); $lastField = key($fields);
		$bindString = ' ';

		foreach ($fields as $field => $data) 
		{
	        $bindString .= ':'.$field;
	        $bindString .= ($field === $lastField ? ' ' : ', ');
		}

		return $bindString;
	}

	private function bindField_Update($fields) 
	{
		end($fields); $lastField = key($fields);
		$bindString = ' ';

		foreach ($fields as $field => $data) 
		{
	        $bindString .= $field.' = :'.$field;
	        $bindString .= ($field === $lastField ? ' ' : ', ');
		}
		
		return $bindString;
	}

	private function array_equal($a, $b) 
	{
		if (array_intersect_key($a, $b)) 
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

?>