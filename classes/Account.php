<?php
namespace dk\lightsaber\milage;

	Class Account extends Persistent {
		public $account_name;
		public $status;

		public $accountOwner;
		public $users;
		
		protected $table = 'account';
		
		protected $meta_mapping = array(
			'id' => 'id',
			'account_name' => 'account_name',
			'status' => 'status'
		);
		protected $meta_mapping_type = array(
			'id' => PersistentConst::NUMERIC,
			'account_name' => PersistentConst::STRING,
			'status' => PersistentConst::NUMERIC
		);

		public function getBinding() {
			return array('id' => $this->getId(), 
					'account_name' => $this->getAccountName(), 
					'status' => $this->getStatus()
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
		public function getUsers() {
			if(!isset($this->users)) {
				$where = "WHERE account_id=:id";
				$binding = array('id' => $this->getId());
				$this->users = User::loadList($this->getPdo(), $where, $binding);
			}
			return $this->users;
		}

		public function clearUsers() {
			$this->users = null;
		}
		
		/***********
		 * toString method
		 ***********/
		public function __toString() {
			$string = 'ID: ' . $this->getId() . '<br/>';
			$string .= 'Account name: ' . $this->getAccountName() . '<br/>';
			$string .= 'Status: ' . $this->getStatus() . '<br/>';
			if(PersistentLog::$debug) {
				$string .= 'Persistent: ' . ($this->persist ? 'TRUE' : 'FALSE') . '<br/>';
			}
			return $string;
		}	

		/***********
		 * Getter/Setters
		 ***********/		
		public function getAccountName() {
			return $this->account_name;
		}
		public function setAccountName($accountName) {
			$this->account_name=$accountName;
			return $this;
		}
		
		public function getStatus() {
			return $this->status;
		}
		public function setStatus($status) {
			$this->status=$status;
			return $this;
		}


	}

?>