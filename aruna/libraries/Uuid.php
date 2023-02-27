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

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include BASEPATH.'libraries/Uuid/vendor/autoload.php';

use Ramsey\Uuid\Uuid;

class ARUNA_Uuid {

	public function v1()
	{
		return Uuid::uuid1();
	}

	public function v2($localDomain, $localIdentifier, $node, $clockSeq)
	{
		return Uuid::uuid2($localDomain, $localIdentifier, $node, $clockSeq);
	}

	public function v3($ns, $name)
	{
		return Uuid::uuid3($ns, $name);
	}

	public function v4()
	{
		return Uuid::uuid4();
	}

	public function v5($ns, $name)
	{
		return Uuid::uuid5($ns, $name);
	}

	public function v6()
	{
		return Uuid::uuid6();
	}

	public function isValid($uuid)
	{
		return Uuid::isValid($uuid);
	}

	public function fromString($uuid)
	{
		return Uuid::fromString($uuid);
	}

	public function fromBytes($bytes)
	{
		return Uuid::fromBytes($bytes);
	}

	public function fromInteger($integer)
	{
		return Uuid::fromInteger($integer);
	}

	public function fromDateTime($dateTime, $node, $clockSeq)
	{
		return Uuid::fromDateTime($dateTime, $node, $clockSeq);
	}

	public function toString()
	{
		return Uuid::toString();
	}

	public function getFields()
	{
		return Uuid::getFields();
	}

	public function getVersion()
	{
		return Uuid::getVersion();
	}

	public function setFactory($factory)
	{
		return Uuid::setFactory($factory);
	}
}

?>