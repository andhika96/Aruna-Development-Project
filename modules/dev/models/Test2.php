<?php

class Test2 extends Aruna_Model
{
	public function getListOfUsers()
	{
		$res =  $this->db_alt->get("ml_accounts", 10);
		$row =  $res->result();

		return $row;
	}
}

?>