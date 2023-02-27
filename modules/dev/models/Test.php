<?php

class Test extends Aruna_Model
{
	public function getListOfUsers()
	{
		$res =  $this->db->sql_select("select * from ml_accounts order by id desc");
		$row =  $this->db->sql_fetch($res);

		// $res =  $this->db->get("ml_accounts", 10);
		// $row =  $res->result();

		return $row;
	}
}

?>