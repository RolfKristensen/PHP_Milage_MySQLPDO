<?php
namespace dk\lightsaber\milage;

use dk\lightsaber\milage\PersistentConst;

Class AuthKeys extends Persistent {
	public $user_id;
	public $auth_key;
	public $from_date;
	public $to_date;
	public $create_date;

	protected $user;

	protected $table = 'auth_keys';

	protected $meta_mapping_type = array(
			'id' => PersistentConst::NUMERIC,
			'user_id' => PersistentConst::NUMERIC,
			'auth_key' => PersistentConst::STRING,
			'from_date' => PersistentConst::DATE_TIME,
			'to_date' => PersistentConst::DATE_TIME,
			'create_date' => PersistentConst::DATE_TIME
	);

	public function getBinding() {
		return array('id' => $this->getId(),
				'user_id' => $this->getUserId(),
				'auth_key' => $this->getAuthKey(),
				'from_date' => $this->getFromDate(),
				'to_date' => $this->getToDate(),
				'create_date' => $this->getCreateDate()
		);
	}
	/***********
	 * Standard Persistency methods for retrieving objects from the database
	 ***********/
	public static function load($pdo, $id) {
		return parent::_loadInstance($pdo, __CLASS__, $id);
	}

	public static function loadInstanceWhere($pdo, $where, $binding) {
		return parent::_loadInstanceWhere($pdo, __CLASS__, $where, $binding);
	}

	public static function loadList($pdo, $where, $binding) {
		return parent::_loadList($pdo, __CLASS__, $where, $binding);
	}

	public static function loadAll($pdo) {
		return parent::_loadAll($pdo, __CLASS__);
	}

	/*
	 * Custom methods
	 */
	public function getUser() {
		if(!isset($this->user)) {
			$where = "WHERE account_id=" . $this->getId();
			$this->user = User::load($this->getPdo(), $this->getUserId());
		}
		return $this->user;
	}

	public function clearUser() {
		$this->user = NULL;
	}
	
	public static function getValidUser(\PDO $pdo, $authKey) {
		$where = 'WHERE auth_key=:auth_key AND from_date < now() AND (to_date is null OR to_date > now())';
		$binding = array('auth_key' => $authKey);
		$authKeyInstance = AuthKeys::loadInstanceWhere($pdo, $where, $binding);
		if ($authKeyInstance != NULL) {
			return $authKeyInstance->getUser();
		} else {
			return NULL;
		}
	}

	/***********
	 * toString method
	 ***********/
	public function __toString() {
		$string = 'ID: ' . $this->getId() . '<br/>';
		$string .= 'User_id: ' . $this->getUserId() . '<br/>';
		$string .= 'Auth key: ' . $this->getAuthKey() . '<br/>';
		if(PersistentLog::$debug) {
			$string .= 'Persistent: ' . ($this->persist ? 'TRUE' : 'FALSE') . '<br/>';
		}
		return $string;
	}

	/***********
	 * Getter/Setters
	 ***********/
	public function getUserId() {
		return $this->user_id;
	}
	public function setUserId($userId) {
		$this->user_id=$userId;
		return $this;
	}

	public function getAuthKey() {
		return $this->auth_key;
	}
	public function setAuthKey($authKey) {
		$this->auth_key=$authKey;
		return $this;
	}
	public function getFromDate() {
		return $this->from_date;
	}
	public function setFromDate($date) {
		$this->from_date=$date;
		return $this;
	}
	public function getToDate() {
		return $this->to_date;
	}
	public function setToDate($date) {
		$this->to_date=$date;
		return $this;
	}
	public function getCreateDate() {
		return $this->create_date;
	}
	public function setCreateDate($date) {
		$this->create_date=$date;
		return $this;
	}

}

?>