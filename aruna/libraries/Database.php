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

	/**
	 * Result Fetch
	 *
	 * @var	result_fetch[]
	 */

	public $result_fetch = array();

	/**
	 * QB key
	 *
	 * @var	array
	 */

	protected $qb_key = array();

	/**
	 * QB keys
	 *
	 * @var	array
	 */

	protected $qb_keys = array();

	/**
	 * QB data sets
	 *
	 * @var	array
	 */
	
	protected $qb_set = array();

	/**
	 * Transaction enabled flag
	 *
	 * @var	bool
	 */

	protected $_is_transact = TRUE;

	/**
	 * Transaction status flag
	 *
	 * Used with transactions to determine if a rollback should occur.
	 *
	 * @var	bool
	 */
	
	protected $_trans_status = TRUE;

	protected $_is_already_active = FALSE;

	protected $_is_already_executed = FALSE;

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @param	array	$params
	 * @return	void
	 */

	function __construct(string $active_group = '') 
	{
		if ( ! file_exists($file_path = APPPATH.'config/database.php')) 
		{
			$this->display_error('The configuration file database.php does not exist.');
		}

		// Include Database Settings
		include(APPPATH.'config/database.php');

		if ( ! isset($db) OR count($db) === 0) 
		{
			$this->display_error('No database connection settings were found in the database config file.');
		}

		if ( ! isset($active_group)) 
		{
			$this->display_error('You have not specified a database connection group via $active_group in your config/database.php file.');
		}
		elseif ( ! isset($db[$active_group])) 
		{
			$this->display_error('You have specified an invalid database connection group ('.$active_group.') in your config/database.php file.');
		}

		if (empty($db[$active_group]['dbname'])) 
		{
			$this->display_error('No database name selected');
		}

		if (empty($db[$active_group]['dbtype']))
		{
			$this->display_error('The database type is not selected or empty');
		}

		if (empty($db[$active_group]['charset']))
		{
			$this->display_error('The character set for database is empty');
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
					// $dbi->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, TRUE);
				}
				catch (PDOExecption $e) 
				{
					$this->display_error($e->getMessage());
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
					$this->display_error($e->getMessage());
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
					$this->display_error($e->getMessage());
				}
				return $dbi;
				break;

			default:
				break;
		}
	}

	public function sql_prepare(string $query) 
	{
		switch ($this->dbtype) 
		{
			case 'PDO_MySQL':
				try 
				{
					if ( ! $this->conn) 
					{
						$this->display_error('Cannot connect to MySQL Server, please check your connection database.');
					}

					$result = $this->conn->prepare($query);

					if ($this->_is_transact)
					{
						$this->_trans_status = TRUE;
					}
				}
				catch (PDOException $e) 
				{
					if ($this->_is_transact)
					{
						$this->_trans_status = FALSE;

						log_message('debug', 'DB Transaction Failure');
					}

					$this->display_error("SQL query error!<br><b>Error infomation:</b> <div style='padding:1rem'><hr><br>[SQL]<br>".$e->getMessage()."<br><br>[Error]<br><font color=red>".$query."</font><br><br><hr>You can get help by sending this infomation to us: <a href='https://www.aruna-dev.id' target='_blank'>https://www.aruna-dev.id</a></div>");
				}
				return $result;
			 	break;

			case 'PDO_SQLSRV':
				try 
				{
					if ( ! $this->conn) 
					{
						$this->display_error('Cannot connect to SQL Server, please check your connection database.');
					}

					$result = $this->conn->prepare($query);
				}
				catch (PDOException $e) 
				{
					$this->display_error("SQL query error!<br><b>Error infomation:</b> <div style='padding:1rem'><hr><br>[SQL]<br>".$e->getMessage()."<br><br>[Error]<br><font color=red>".$query."</font><br><br><hr>You can get help by sending this infomation to us: <a href='https://www.aruna-dev.id' target='_blank'>https://www.aruna-dev.id</a></div>");
				}
				return $result;
			 	break;

			case 'PDO_PostgreSQL':
				try 
				{
					if ( ! $this->conn) 
					{
						$this->display_error('Cannot connect to PostgreSQL Server, please check your connection database.');
					}

					$result = $this->conn->prepare($query);
				}
				catch (PDOException $e) 
				{
					$this->display_error("SQL query error!<br><b>Error infomation:</b> <div style='padding:1rem'><hr><br>[SQL]<br>".$e->getMessage()."<br><br>[Error]<br><font color=red>".$query."</font><br><br><hr>You can get help by sending this infomation to us: <a href='https://www.aruna-dev.id' target='_blank'>https://www.aruna-dev.id</a></div>");
				}
				return $result;
			 	break;

			default:
				break;
		}
	}

	public function sql_select(string $query) 
	{
		switch ($this->dbtype) 
		{
			case 'PDO_MySQL':
				try 
				{
					if ( ! $this->conn) 
					{
						$this->display_error('Cannot connect to MySQL Server, please check your connection database.');
					}

					$result = $this->conn->prepare($query);
					
					$result->execute();

					// Tell the system the function executed() has been running
					$this->_is_already_executed = TRUE;
					
					if ($this->_is_transact)
					{
						$this->_trans_status = TRUE;
					}
				}
				catch (PDOException $e) 
				{
					if ($this->_is_transact)
					{
						$this->_trans_status = FALSE;

						log_message('debug', 'DB Transaction Failure');
					}

					$this->display_error("SQL query error!<br><b>Error infomation:</b> <div style='padding:1rem'><hr><br>[SQL]<br>".$e->getMessage()."<br><br>[Error]<br><font color=red>".$query."</font><br><br><hr>You can get help by sending this infomation to us: <a href='https://www.aruna-dev.id' target='_blank'>https://www.aruna-dev.id</a></div>");
				}
				return $result;
			 	break;

			case 'PDO_SQLSRV':
				try 
				{
					if ( ! $this->conn) 
					{
						$this->display_error('Cannot connect to SQL Server, please check your connection database.');
					}

					$result = $this->conn->prepare($query);

					$result->execute();

					// Tell the system the function executed() has been running
					$this->_is_already_executed = TRUE;
				}
				catch (PDOException $e) 
				{
					$this->display_error("SQL query error!<br><b>Error infomation:</b> <div style='padding:1rem'><hr><br>[SQL]<br>".$e->getMessage()."<br><br>[Error]<br><font color=red>".$query."</font><br><br><hr>You can get help by sending this infomation to us: <a href='https://www.aruna-dev.id' target='_blank'>https://www.aruna-dev.id</a></div>");
				}
				return $result;
			 	break;

			case 'PDO_PostgreSQL':
				try 
				{
					if ( ! $this->conn) 
					{
						$this->display_error('Cannot connect to PostgreSQL Server, please check your connection database.');
					}

					$result = $this->conn->prepare($query);
					
					$result->execute();

					// Tell the system the function executed() has been running
					$this->_is_already_executed = TRUE;
				}
				catch (PDOException $e) 
				{
					$this->display_error("SQL query error!<br><b>Error infomation:</b> <div style='padding:1rem'><hr><br>[SQL]<br>".$e->getMessage()."<br><br>[Error]<br><font color=red>".$query."</font><br><br><hr>You can get help by sending this infomation to us: <a href='https://www.aruna-dev.id' target='_blank'>https://www.aruna-dev.id</a></div>");
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
						// if (is_array($value))
						// {
						// 	foreach ($value as $k => &$id) 
						// 	{
						// 		log_message('error', $key.' from array value');

						// 		$stmt->bindParam(":".$key."", $id, PDO::PARAM_INT);
						// 		// $stmt->bindValue(":".$key.$k."", $id, PDO::PARAM_INT);
						// 	}
						// }
						// else
						// {
							if (is_int($value)) 
							{
								$stmt->bindParam(":".$key."", $value, PDO::PARAM_INT);
							}
							else 
							{
		  						$stmt->bindParam(":".$key."", $value, PDO::PARAM_STR);
		  					}
		  				// }
	  				}
	  			}
				else
				{
					$this->display_error('Your data must be an array.');
				}

				$stmt->execute();
				
				// Tell the system the function executed() has been running
				$this->_is_already_executed = TRUE;

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
					$this->display_error('Your data must be an array.');
				}

				$stmt->execute();
				
				// Tell the system the function executed() has been running
				$this->_is_already_executed = TRUE;

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
					$this->display_error('Your data must be an array.');
				}

				$stmt->execute();
				
				// Tell the system the function executed() has been running
				$this->_is_already_executed = TRUE;

				return $stmt;
				break;

			default:
				break;
		}
	}

	public function sql_chunk(array $data, int $batchSize, Closure $userFunc)
	{
		$batch 	= [];

		foreach ($data as $i) 
		{
			$batch[] = $i;
		
			// See if we have the right amount in the batch
			if (count($batch) === $batchSize) 
			{
				// Pass the batch into the Closure
				$userFunc($batch);
		
				// Reset the batch
				$batch = [];
			}
		}
		
		// See if we have any leftover data to process
		if (count($batch)) $userFunc($batch);
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

	public function sql_result(&$res, $fetch_style = PDO::FETCH_ASSOC) 
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

	public function sql_result_all(&$res, $fetch_style = PDO::FETCH_ASSOC) 
	{
		switch ($this->dbtype) 
		{
			case "PDO_MySQL":
				$row = $res->fetchAll($fetch_style);
				return $row;
				break;

			case "PDO_SQLSRV":
				$row = $res->fetchAll($fetch_style);
				return $row;
				break;

			case "PDO_PostgreSQL":
				$row = $res->fetchAll($fetch_style);
				return $row;
				break;
		
			default:
				break;
		}
	}

	/**
	 * Fungsi ini berguna untuk mendapatkan dan menampilkan data dari database berupa array
	 * namun untuk mengakses data array dari database harus dipecah terlebih dahulu menggunakan
	 * fungsi foreach 
	 * 
	 * Contoh:
	 * 
	 * $row = $this->db->sql_fetch($bindParam);
	 * 
	 * foreach ($row as $key => $value)
	 * {
	 * 	 echo $value['username'];
	 * }
	 * 
	 * @return	array
	 */

	public function sql_fetch(&$res, $type = 'array')
	{
		switch ($type) 
		{
			case "array":
				$i = 0;
				while ($row = $this->sql_result_all($res))
				{
					$this->result_fetch[$i] = $row;

					$i++;
				}

				return array_shift($this->result_fetch);
				break;

			case "object":
				$i = 0;
				while ($row = $this->sql_result_all($res, PDO::FETCH_OBJ))
				{
					$this->result_fetch[$i] = $row;
					$i++;
				}

				return array_shift($this->result_fetch);
				break;

			default;
				break;
		}
	}

	/**
	 * Fungsi ini berguna untuk mendapatkan dan menampilkan data dari database berupa array
	 * namun untuk mengakses data array dari database tidak harus dipecah terlebih dahulu menggunakan
	 * fungsi foreach seperti pada fungsi sql_fetch() langsung bisa didefiniskan
	 * 
	 * Contoh:
	 * 
	 * $row = $this->db->sql_fetch_single($bindParam);
	 * 
	 * echo $row['username'];
	 * 
	 * @return	array
	 */

	public function sql_fetch_single($res, $type = 'array')
	{
		switch ($type) 
		{
			case "array":
				return $row = $this->sql_result($res);
				break;

			case "object":
				return $row = $this->sql_result($res, PDO::FETCH_OBJ);
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
			$this->display_error('Cannot get table name database');
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
				$row = $this->sql_fetch_single($bindParam);

				return $row;
			}
			else
			{
				$res = $this->sql_select("select count(*) as $alias from $table");
				$row = $this->sql_fetch($res);

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
					$this->display_error('Cannot connect to MySQL Server, please check your connection database.');
				}

				$rows = $this->conn->lastInsertId();
				return $rows;
				break;

			case "PDO_SQLSRV":
				if ( ! $this->conn) 
				{
					$this->display_error('Cannot connect to Microsoft SQL Server, please check your connection database.');
				}

				$rows = $this->conn->lastInsertId();
				return $rows;
				break;

			case "PDO_PostgreSQL":
				if ( ! $this->conn) 
				{
					$this->display_error('Cannot connect to PostgreSQL Server, please check your connection database.');
				}

				$rows = $this->conn->lastInsertId();
				return $rows;
				break;
		
			default:
				break;
		}
	}

	public function sql_insert(array $att, string $table_name, $with_db_trans = FALSE) 
	{
		switch ($this->dbtype) 
		{
			case "PDO_MySQL":
				try 
				{
					if ($this->_is_already_active == TRUE && $with_db_trans == TRUE)
					{
						$this->display_error('You cannot use DB Transaction at the sametime.');
					}

					if ( ! $this->conn) 
					{
						$this->display_error('Cannot connect to MySQL Server, please check your connection database.');
					}

					if ($with_db_trans == TRUE)
					{
						// From this point and until the transaction is being committed every change to the database can be reverted
						$this->conn->beginTransaction(); 
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
						$this->display_error('Your data must be an array.');
					}
	
					$stmt->execute();

					if ($with_db_trans == TRUE)
					{
						// Make the changes to the database permanent
						$this->conn->commit();
					}
				}
				catch (PDOExecption $e) 
				{
					if ($with_db_trans == TRUE)
					{
						// Failed to insert the order into the database so we rollback any changes
						$this->conn->rollback();
					}

					$this->display_error($e->getMessage());
				}

				return $stmt;
				break;

			case "PDO_SQLSRV":
				try 
				{
					if ( ! $this->conn) 
					{
						$this->display_error('Cannot connect to Microsoft SQL Server, please check your connection database.');
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
						$this->display_error('Your data must be an array.');
					}

					$stmt->execute();
				}
				catch (PDOExecption $e) 
				{
					$this->display_error($e->getMessage());
				}

				return $stmt;
				break;

			case "PDO_PostgreSQL":
				try 
				{
					if ( ! $this->conn) 
					{
						$this->display_error('Cannot connect to PostgreSQL Server, please check your connection database.');
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
						$this->display_error('Your data must be an array.');
					}

					$stmt->execute();
				}
				catch (PDOExecption $e) 
				{
					$this->display_error($e->getMessage());
				}

				return $stmt;
				break;

			default:
				break;
		}
	}

	public function sql_update($att, $table_name, $arg = 0, $with_db_trans = FALSE) 
	{
		switch ($this->dbtype) 
		{
			case 'PDO_MySQL':
				try 
				{
					if ($this->_is_already_active == TRUE && $with_db_trans == TRUE)
					{
						$this->display_error('You cannot use DB Transaction at the sametime.');
					}

					if ( ! $this->conn) 
					{
						$this->display_error('Cannot connect to MySQL Server, please check your connection database.');
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

					if ($with_db_trans == TRUE)
					{
						// From this point and until the transaction is being committed every change to the database can be reverted
						$this->conn->beginTransaction(); 
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
						$this->display_error('Your data must be an array.');
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
						$this->display_error('Your data must be an array.');
					}

					$stmt->execute();

					if ($with_db_trans == TRUE)
					{
						// Make the changes to the database permanent
						$this->conn->commit();
					}
				}
				catch (PDOExecption $e) 
				{
					if ($with_db_trans == TRUE)
					{
						// Failed to insert the order into the database so we rollback any changes
						$this->conn->rollback();
					}

					$this->display_error($e->getMessage());
				}
				return $stmt;
				break;

			case 'PDO_SQLSRV':
				try 
				{
					if ( ! $this->conn) 
					{
						$this->display_error('Cannot connect to Microsoft SQL Server, please check your connection database.');
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
						$this->display_error('Your data must be an array.');
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
						$this->display_error('Your data must be an array.');
					}

					$stmt->execute();
				}
				catch (PDOExecption $e) 
				{
					$this->display_error($e->getMessage());
				}
				return $stmt;
				break;

			case 'PDO_PostgreSQL':
				try 
				{
					if ( ! $this->conn) 
					{
						$this->display_error('Cannot connect to PostgreSQL Server, please check your connection database.');
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
						$this->display_error('Your data must be an array.');
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
						$this->display_error('Your data must be an array.');
					}

					$stmt->execute();
				}
				catch (PDOExecption $e) 
				{
					$this->display_error($e->getMessage());
				}
				return $stmt;
				break;

			default:
				break;
		}
	}

	public function sql_delete($table_name, array $arg = [], $with_db_trans = FALSE) 
	{
		switch ($this->dbtype) 
		{
			case 'PDO_MySQL':
				try 
				{
					if ($this->_is_already_active == TRUE && $with_db_trans == TRUE)
					{
						$this->display_error('You cannot use DB Transaction at the sametime.');
					}

					if ( ! $this->conn) 
					{
						$this->display_error('Cannot connect to MySQL Server, please check your connection database.');
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
						$this->display_error('Your data must be an array.');
					}

					if ($with_db_trans == TRUE)
					{
						// From this point and until the transaction is being committed every change to the database can be reverted
						$this->conn->beginTransaction(); 
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
						$this->display_error('Your data must be an array.');
					}

					$stmt->execute();

					if ($with_db_trans == TRUE)
					{
						// Make the changes to the database permanent
						$this->conn->commit();
					}
				}
				catch (PDOExecption $e) 
				{
					if ($with_db_trans == TRUE)
					{
						// Failed to insert the order into the database so we rollback any changes
						$this->conn->rollback();
					}

					$this->display_error($e->getMessage());
				}
				return $stmt;
				break;

			case 'PDO_SQLSRV':
				try 
				{
					if ( ! $this->conn) 
					{
						$this->display_error('Cannot connect to Microsoft SQL Server, please check your connection database.');
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
						$this->display_error('Your data must be an array.');
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
						$this->display_error('Your data must be an array.');
					}

					$stmt->execute();
				}
				catch (PDOExecption $e) 
				{
					$this->display_error($e->getMessage());
				}
				return $stmt;
				break;

			case 'PDO_PostgreSQL':
				try 
				{
					if ( ! $this->conn) 
					{
						$this->display_error('Cannot connect to PostgreSQL Server, please check your connection database.');
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
						$this->display_error('Your data must be an array.');
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
						$this->display_error('Your data must be an array.');
					}

					$stmt->execute();
				}
				catch (PDOExecption $e) 
				{
					$this->display_error($e->getMessage());
				}
				return $stmt;
				break;

			default:
				break;
		}
	}

	public function sql_insert_batch(array $att, string $table_name, $batch_size = 100, $with_db_trans = FALSE) 
	{
		switch ($this->dbtype) 
		{
			case "PDO_MySQL":
				try 
				{
					if ($this->_is_already_active == TRUE && $with_db_trans == TRUE)
					{
						$this->display_error('You cannot use DB Transaction at the sametime.');
					}

					if ( ! $this->conn) 
					{
						$this->display_error('Cannot connect to MySQL Server, please check your connection database.');
					}

					$this->set_insert_batch($att, '');
					
					if (is_array($att))
					{
						$gett = array();

						for ($i = 0, $total = count($this->qb_keys); $i < $total; $i+= $batch_size)
						{
							foreach ($att as $key) 
							{
								$init_increment = $i++;
								$gets = "(:".implode(", :", array_keys($key)).")";
								$gets2 = str_replace(",", $init_increment.",", $gets);
								$gett[] = str_replace(")", $init_increment.")", $gets2);
							}
						}
					}
					else
					{
						$this->display_error('Your data must be an array.');
					}

					if ($with_db_trans == TRUE)
					{
						// From this point and until the transaction is being committed every change to the database can be reverted
						$this->conn->beginTransaction(); 
					}

					$sql = "INSERT INTO $table_name (".$this->qb_key.") VALUES ".implode(", ", $gett)."";
					$stmt = $this->conn->prepare($sql);

					if (is_array($att))
					{
						for ($i = 0, $total = count($this->qb_keys); $i < $total; $i++)
						{
							foreach ($att[$i] as $key => &$value) 
							{
								if (is_int($value)) 
								{
									$stmt->bindParam(":".$key.$i."", $value, PDO::PARAM_INT);
								}
								else 
								{
									$stmt->bindParam(":".$key.$i, $value, PDO::PARAM_STR);
								}
							}
						}
					}
					else
					{
						$this->display_error('Your data must be an array.');
					}
	
					$stmt->execute();

					if ($with_db_trans == TRUE)
					{
						// Make the changes to the database permanent
						$this->conn->commit();
					}
				}
				catch (PDOExecption $e) 
				{
					if ($with_db_trans == TRUE)
					{
						// Failed to insert the order into the database so we rollback any changes
						$this->conn->rollback();
					}

					$this->display_error($e->getMessage());
				}

				return $stmt;
				break;

			case "PDO_SQLSRV":
				try 
				{
					if ( ! $this->conn) 
					{
						$this->display_error('Cannot connect to Microsoft SQL Server, please check your connection database.');
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
						$this->display_error('Your data must be an array.');
					}

					$stmt->execute();
				}
				catch (PDOExecption $e) 
				{
					$this->display_error($e->getMessage());
				}

				return $stmt;
				break;

			case "PDO_PostgreSQL":
				try 
				{
					if ( ! $this->conn) 
					{
						$this->display_error('Cannot connect to PostgreSQL Server, please check your connection database.');
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
						$this->display_error('Your data must be an array.');
					}

					$stmt->execute();
				}
				catch (PDOExecption $e) 
				{
					$this->display_error($e->getMessage());
				}

				return $stmt;
				break;

			default:
				break;
		}
	}

	public function startTransaction()
	{
		$this->_is_already_active = TRUE;

		return $this->conn->beginTransaction();
	}
	
	public function disableTransaction()
	{
		$this->_is_transact = FALSE;
	}

	public function runTransaction()
	{
		if ( ! $this->_is_transact)
		{
			return FALSE;
		}
		else
		{
			if ($this->_trans_status == FALSE)
			{
				$this->conn->rollback();

				log_message('debug', 'DB Transaction Failure');
				return FALSE;
			}
			else
			{
				return $this->conn->commit();
			}
		}
	}

	public function transStatus()
	{
		return $this->_trans_status;
	}

	public function transCommit()
	{
		if ( ! $this->_is_transact)
		{
			return FALSE;
		}
		else
		{
			return $this->conn->commit();
		}
	}

	public function transRollback()
	{
		if ( ! $this->_is_transact)
		{
			return FALSE;
		}
		else
		{		
			$this->conn->rollback();

			log_message('debug', 'DB Transaction Failure');
			return FALSE;
		}
	}

	public function display_error($message = '')
	{
		$error =& load_class('Exceptions', 'system');
		echo $error->show_error('A Database Error Occurred', $message, 'error_db');
		exit(8); // EXIT_DATABASE		
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

	private function bindVals_batch($fields) 
	{
		end($fields); $lastField = key($fields);
		$bindString = ' ';

		foreach ($fields as $field => $data) 
		{
			$bindString .= $data;
			$bindString .= ($data === $lastField ? ' ' : ', ');
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

	// --------------------------------------------------------------------

	/**
	 * The "set_insert_batch" function.  Allows key/value pairs to be set for batch inserts
	 *
	 * @param	mixed
	 * @param	string
	 * @param	bool
	 * @return	CI_DB_query_builder
	 */
	
	public function set_insert_batch($key, $value = '', $escape = NULL)
	{
		$key = $this->_object_to_array_batch($key);

		if ( ! is_array($key))
		{
			$key = array($key => $value);
		}

		$keys = array_keys($this->_object_to_array(reset($key)));
		sort($keys);

		foreach ($key as $row)
		{
			$row = $this->_object_to_array($row);
			if (count(array_diff($keys, array_keys($row))) > 0 OR count(array_diff(array_keys($row), $keys)) > 0)
			{
				// batch function above returns an error on an empty array
				$this->qb_set[] = array();
				return;
			}

			ksort($row); // puts $row in the same order as our keys

			$this->qb_set[] = '('.implode(',', $row).')';
		}

		foreach ($key as $k)
		{
			$this->qb_key = implode(', ', array_keys($k));
		}

		foreach ($key as $k)
		{
			$ks = array_keys($k);
			$this->qb_keys[] = '(:'.implode(', :', $ks).')';
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Object to Array
	 *
	 * Takes an object as input and converts the class variables to array key/vals
	 *
	 * @param	object
	 * @return	array
	 */
	
	protected function _object_to_array($object)
	{
		if ( ! is_object($object))
		{
			return $object;
		}

		$array = array();
		foreach (get_object_vars($object) as $key => $val)
		{
			// There are some built in keys we need to ignore for this conversion
			if ( ! is_object($val) && ! is_array($val) && $key !== '_parent_name')
			{
				$array[$key] = $val;
			}
		}

		return $array;
	}

	// --------------------------------------------------------------------

	/**
	 * Object to Array
	 *
	 * Takes an object as input and converts the class variables to array key/vals
	 *
	 * @param	object
	 * @return	array
	 */
	
	protected function _object_to_array_batch($object)
	{
		if ( ! is_object($object))
		{
			return $object;
		}

		$array = array();
		$out = get_object_vars($object);
		$fields = array_keys($out);

		foreach ($fields as $val)
		{
			// There are some built in keys we need to ignore for this conversion
			if ($val !== '_parent_name')
			{
				$i = 0;
				foreach ($out[$val] as $data)
				{
					$array[$i++][$val] = $data;
				}
			}
		}

		return $array;
	}
}

?>