<?php

class Test extends Aruna_Model
{	
	public function userlist()
	{		
		$res = $this->db->sql_select("select * from ml_accounts order by id asc");
		$row = $this->db->sql_fetch($res);
	
		return $row;
	}

	public function getuser($id)
	{		
		$res = $this->db->sql_prepare("select * from ml_accounts where id = :id");
		$bindParam = $this->db->sql_bindParam(['id' => $id], $res);
		$row = $this->db->sql_fetch($bindParam);
	
		return $row;
	}

	public function asd()
	{
		return 'Hello World! This is a Model';
	}
}

?>