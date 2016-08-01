<?php
namespace dk\lightsaber\milage;

	Class User extends Persistent{
		public $first_name;
		public $last_name;
		public $email;
		protected $password;
		protected $account_id = -1;
		public $access_rights;
		
		protected $account;
	
		protected $table = 'user';
/*		protected $meta_mapping = array(
			'id' => 'id',
			'firstName' => 'first_name',
			'lastName' => 'last_name',
			'email' => 'email',
			'password' => 'password',
			'accessRights' => 'access_rights',
			'accountId' => 'account_id'
		);
*/
		protected $meta_mapping_type = array(
			'id' => PersistentConst::NUMERIC,
			'firstName' => PersistentConst::STRING,
			'lastName' => PersistentConst::STRING,
			'email' => PersistentConst::STRING,
			'password' => PersistentConst::STRING,
			'accountId' => PersistentConst::NUMERIC,
			'accessRights' => PersistentConst::NUMERIC
		);

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

		/***********
		 * custom methods
		 ***********/
		public function getAccount() {
			if($this->getAccount()!= NULL) {
				if($this->accountId != -1){
					$this->setAccount(Account::load($this->accountId));
				} else {
					PersistentLog::warn("User does not have an account association.");
					return null;
				}
			}
			return $this->account;
		}
		
		public function setAccount($account) {
			if($account) {
				$this->account = $account;
				$this->accountId = $account->getId();
				if(!$account->persist) {
					PersistentLog::warn("Account object has not yet been persisted! Relations might be incorrect if not saved correctly.");
				}
			} else {
				$this->account = null;
			}
		}
		
		public static function logIn($pdo, $email, $password) {
			$where = "WHERE email=:email AND password=:password";
			$bindings = array('email' => $email, 'password' => $password);
			
			return User::loadInstanceWhere($pdo, $where, $bindings);
		}
		
		/*
		 * Methods for retreiving Milage records for this user.
		 */
		public function getMilages() {
			$where = "WHERE user_id = :id ORDER BY date DESC";
			$bindings = array('id' => $this->getId());
			
			return Milage::loadList($this->getPdo(), $where, $bindings);
		}
		
		public function getMilagesFromTo($from, $to) {
			$where = "WHERE date BETWEEN ";
			$where .= "STR_TO_DATE(:from_date,:date_format) AND ";
			$where .= "STR_TO_DATE(:to_date,:date_format) AND ";
			$where .= "user_id = :user_id ";
			$where .= "ORDER BY date DESC";
			$bindings = array('from_date' => $from, 
					'to_date' => $to,
					'user_id' => $this->getId(),
					'date_format' => PersistentConst::DATE_FORMAT
			);
			
			return Milage::loadList($this->getPdo(), $where, $bindings);
		}
		
		public function getMilagesLastXMonths($months) {
			$where = "WHERE date >= DATE_SUB(now(), INTERVAL :months MONTH) ";
			$where .= "AND user_id = :id ORDER BY date DESC";
			$bindings = array('months' => $months,
					'id' => $this->getId()
			);
			
			return Milage::loadList($this->getPdo(), $where, $bindings);
		}
		
		/*
		 * Methods relating to the users car(s)
		 */
		public function getCars() {
			$where = "WHERE user_id = :id";
			$bindings = array('id' => $this->getId());
			
			return Car::loadList($this->getPdo(), $where, $bindings);
		}
		
		/***********
		 * toString method
		 ***********/
		public function __toString() {
			$string = 'Name: ' . $this->firstName . ' ' . $this->lastName . '<br/>';
			$string .= 'Email: ' . $this->email . '<br/>';
			$string .= 'Access Rights: ' . $this->accessRights . '<br/>';
			$string .= 'Password: ' . $this->password . '<br/>';
			$string .= 'ID: ' . $this->id . '<br/>';
			$string .= 'Account id: ' . $this->accountId . '<br/>';
			if(PersistentLog::$debug) {
				$string .= 'Persistent: ' . ($this->persist ? 'TRUE' : 'FALSE') . '<br/>';
			}
			
			return $string;
		
		}

		/***********
		 * Getter/Setters
		 ***********/		
		public function getFirstName() {
			return $this->firstName;
		}
		public function setFirstName($firstName) {
			$this->firstName=$firstName;
			return $this;
		}
		
		public function getLastName() {
			return $this->lastName;
		}
		public function setLastName($lastName) {
			$this->lastName=$lastName;
			return $this;
		}
		
		public function getEmail() {
			return $this->email;
		}
		public function setEmail($email) {
			$this->email=$email;
			return $this;
		}
		
		public function getPassword() {
			return $this->password;
		}
		public function setPassword($password) {
			$this->password=$password;
			return $this;
		}
		
		public function getAccessRights() {
			return $this->accessRights;
		}
		public function setAccessRights($accessRights) {
			$this->accessRigts=$accessRights;
			return $this;
		}


	}

?>