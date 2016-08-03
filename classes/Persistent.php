<?php
namespace dk\lightsaber\milage;

	Abstract Class Persistent {
		public $id=-1;
		
		/*
		 * PDO object for connections to DB
		 */
		protected $pdo;
		
		/* 
		 * true/false defining if the object is persistent.
		 * This must only be changed by the persistency layer!
		 */
		protected $persist = false; 

		/*
		 * defines the tablename of the object
		 */
		protected $table;
		/*
		 * Meta mapping used for mapping a variable name to a column name in the database.
		 * array('class_variable_name1' => 'database_column_name1',
		 *		 'class_variable_name2' => 'database_column_name2', ...);
		 */
//		protected $meta_mapping;

		protected $meta_mapping_type;
		
		/*
		 * This method must be overridden for all classes exetending the Persistent Class.
		 * array('class_veriable/DB_column_name' => getter method)
		 */
		public function getBinding() {
			return array(
					'id' => $this->getId()
			);
		}
		
		/*
		 * Loads an instance of a given class with the specified id
		 * @param $class Class name of the object that should be instantiated
		 * @param $id The id of the record that should be used for instantiating the object
		 * @return and object of type $class, if the record exists in the database
		 */
		public static function _loadInstance(\PDO $pdo, $class, $id) {
			$obj = new $class();
			$where = 'WHERE ID = :id';
			$binding = array('id' => $id);
			
			return Persistent::_loadInstanceWhere($pdo, $class, $where, $binding);
		}
		
		public static function _loadInstanceWhere(\PDO $pdo, $class, $where, $binding) {
			$obj = NULL;
			$sql = "SELECT * FROM " . (new $class)->table . " $where";
			if($binding != NULL) { 
				PersistentLog::$logger->addDebug("[" . __CLASS__ . "." . __METHOD__ . "] SQL: " . $sql, $binding);
			} else {
				PersistentLog::$logger->addDebug("[" . __CLASS__ . "." . __METHOD__ . "] SQL: " . $sql);
			}
			$stmt = $pdo->prepare($sql);
			$stmt->execute($binding);
			$rowCount = $stmt->rowCount();
			$stmt->setFetchMode(\PDO::FETCH_CLASS, $class);
				
			if($rowCount == 1) {
				$obj = $stmt->fetch(\PDO::FETCH_CLASS);
			} else if ($rowCount == 0) {
				PersistentLog::$logger->addDebug("[" . __CLASS__ . "." . __METHOD__ . "] Query didn't return any result");
			} else if ($rowCount > 1) {
				$obj = $stmt->fetch(\PDO::FETCH_CLASS);
				PersistentLog::$logger->addInfo("[" . __CLASS__ . "." . __METHOD__ . "] Instance error... $rowCount records found in the database.");
			}
			if($obj != NULL)
				return $obj->setPersisted()->setPdo($pdo);
			else 
				return NULL;
		}
		
		/*
		 * Loads a list of objects based on the object type this method is called on.
		 * @param $class The class name that the list should contain objects of
		 * @param $where The where clause (user must write 'where' as well
		 * @return A list of the given object (if any results are available)
		 */
		public static function _loadList(\PDO $pdo, $class, $where, $binding) {
			$list = array();
			$sql = "SELECT * FROM " . (new $class)->table . " $where";
			if($binding != NULL) {
				PersistentLog::$logger->addDebug("[" . __CLASS__ . "." . __METHOD__ . "] SQL: " . $sql, $binding);
			} else {
				PersistentLog::$logger->addDebug("[" . __CLASS__ . "." . __METHOD__ . "] SQL: " . $sql);
			}
			$stmt = $pdo->prepare($sql);
			$stmt->execute($binding);
			if($stmt->rowCount() > 0) {
				$list = $stmt->fetchAll(\PDO::FETCH_CLASS, $class);
			} else {
				PersistentLog::$logger->addDebug("[" . __CLASS__ . "." . __METHOD__ . "] Query didn't return any result");
			}
			if($list != NULL)
				foreach($list as $element) {
					$element->setPersisted()->setPdo($pdo);
				}
			return $list;
		}
		
		public static function _loadAll(\PDO $pdo, $class) {
			$where = "WHERE 1=1";
			return Persistent::_loadList($pdo, $class, $where, null);
		}
		
		public function save() {
			$binding = $this->getBinding();
			foreach(array_keys($this->getBinding()) as $column) {
				if(!isset($this->$column)) {
					unset($binding[$column]);
				}	
			}
			if($this->persist) {
				// update
				$sql = $this->makeUpdateSQL();
				if($binding != NULL) {
					PersistentLog::$logger->addDebug("[" . __CLASS__ . "." . __METHOD__ . "] SQL: " . $sql, $binding);
				} else {
					PersistentLog::$logger->addDebug("[" . __CLASS__ . "." . __METHOD__ . "] SQL: " . $sql);
				}
				if(PersistentConst::$doInserts) {
					$stmt = $this->getPdo()->prepare($sql);
					$stmt->execute(binding);
					PersistentLog::error("[Persistent.save] Number of records changed: " . $stmt->rowCount());
				} else {
					PersistentLog::$logger->addInfo("[" . __CLASS__ . "." . __METHOD__ . "] Inserts/updates are disabled!");
				}
			} else {
				// insert
				$sql = $this->makeInsertSQL();
				unset($binding['id']);
				if($binding != NULL) {
					PersistentLog::$logger->addDebug("[" . __CLASS__ . "." . __METHOD__ . "] SQL: " . $sql, $binding);
				} else {
					PersistentLog::$logger->addDebug("[" . __CLASS__ . "." . __METHOD__ . "] SQL: " . $sql);
				}
				if(PersistentConst::$doInserts) {
					$stmt = $this->getPdo()->prepare($sql);
					$stmt->execute($binding);
					PersistentLog::$logger->addDebug("[" . __CLASS__ . "." . __METHOD__ . "] Number of records changed: " . $stmt->rowCount());
						
					if($stmt->rowCount()) {
						$this->id = $this->getPdo()->lastInsertId();
						$this->setPersisted();
					} else {
						PersistentLog::$logger->addDebug("[" . __CLASS__ . "." . __METHOD__ . "] Error saving the object: $this->table to database.", $this);
					}
				} else {
					PersistentLog::$logger->addInfo("[" . __CLASS__ . "." . __METHOD__ . "] Inserts/updates are disabled!");
				}
			}
		}
		
		PUBLIC function makeUpdateSQL() {
			$sql = "UPDATE $this->table ";
			$where;
			$count = 0;

			foreach(array_keys($this->getBinding()) as $column) {
				if($column != 'id') {
					if($count == 0) {
						$sql .= 'SET ';
					} else {
						$sql .= ', ';
					}
					if($this->meta_mapping_type[$column] == PersistentConst::NUMERIC) {
						$sql .= "$column = :$column";
					} else if($this->meta_mapping_type[$column] == PersistentConst::STRING) {
						$sql .= "$column = :$column";
					} else if($this->meta_mapping_type[$column] == PersistentConst::DATE) {
						$sql .= "$column = STR_TO_DATE(:$column,'" . PersistentConst::DATE_FORMAT . "')";
					} else if($this->meta_mapping_type[$column] == PersistentConst::TIME) {
						$sql .= "$column = STR_TO_DATE(:$column,'" . PersistentConst::TIME_FORMAT . "')";
					} else if($this->meta_mapping_type[$column] == PersistentConst::DATE_TIME) {
						$sql .= "$column = STR_TO_DATE(:$column,'" . PersistentConst::DATE_TIME_FORMAT . "')";
					} else {
						PersistentLog::$logger->addError("[" . __CLASS__ . "." . __METHOD__ . "] Could not detect $column type... please check the type definition in meta_mapping_type", $this->getBinding());
					}
					$count++;
				} else {
					$where = " WHERE $column = :$column";
				}
			}
			$sql .= $where;

			return $sql;
		}
		
		private function makeInsertSQL() {
			$sql = 'INSERT INTO ' . $this->table;
			$sql_into = '(';
			$sql_values= ' VALUES(';
			$count = 0;
			foreach(array_keys($this->getBinding()) as $column) {
				if($column != 'id') {
					if(isset($this->$column)) {
						if($count!=0) {
							$sql_into .= ',';
							$sql_values .= ',';
						}
						$sql_into .= " $column";
						if($this->meta_mapping_type[$column] == PersistentConst::NUMERIC) {
							$sql_values .= ":" . $column;
						} else if($this->meta_mapping_type[$column] == PersistentConst::STRING) {
							$sql_values .= ":" . $column;
						} else if($this->meta_mapping_type[$column] == PersistentConst::DATE) {
							$sql_values .= "STR_TO_DATE(:$column,'" . PersistentConst::DATE_FORMAT . "')";
						} else if($this->meta_mapping_type[$column] == PersistentConst::TIME) {
							$sql_values .= "STR_TO_DATE(:$column,'" . PersistentConst::TIME_FORMAT . "')";
						} else if($this->meta_mapping_type[$column] == PersistentConst::DATE_TIME) {
							$sql_values .= "STR_TO_DATE(:$column,'" . PersistentConst::DATE_TIME_FORMAT . "')";
						} else {
							PersistentLog::$logger->addError("[" . __CLASS__ . "." . __METHOD__ . "] Could not detect $column type... please check the type definition in meta_mapping_type", $this->getBinding());
						}
						$count++;
					}
				}
			}
			$sql_into .= ')';
			$sql_values .= ')';
			$sql .= $sql_into . $sql_values;

			return $sql;
		}

		public function isDateTime() {
			if( $this->meta_mapping_type[$variable] == PersistentConst::DATE ||
				$this->meta_mapping_type[$variable] == PersistentConst::TIME ||
				$this->meta_mapping_type[$variable] == PersistentConst::DATE_TIME) {
				return true;
			}
			return false;

		
		}
		
		/**
		 * Magic method used to set the value and check if it's valid
		 * 
		 * @param string $name name of the value
		 * @param string $value value itself
		 * @return bool 
		 */
		public function __set($name, $value)
		{
			try
			{
				$this->$name = trim($value);
				return true;
			}
			catch (Exception $e)
			{
				echo "Error setting ".$name.": ".$e->getMessage();
			}
		}

		/**
		 * Magic method used to get the value of an attribute
		 * 
		 * @param string $name name of the value
		 * @return mixed 
		 */
		public function __get($name)
		{
			return $this->$name;
		}

		/***********
		 * Getter/Setters
		 ***********/		
		public function getId() {
			return $this->id;
		}
		public function setId($id) {
			$this->id=$id;
			return $this;
		}
		/*
		 * @return \PDO
		 */
		public function getPdo() {
			return $this->pdo;
		}
		public function setPdo($pdo) {
			$this->pdo=$pdo;
			return $this;
		}
		private function setPersisted() {
			$this->persist=TRUE;
			return $this;
		}
		public function isPersisted() {
			return $this->persist;
		}
	}




/*
---
source: http://php.net/manual/en/pdostatement.execute.php
--

simplified $placeholder form 

<?php

$data = ['a'=>'foo','b'=>'bar'];

$keys = array_keys($data);
$fields = '`'.implode('`, `',$keys).'`';

#here is my way 
$placeholder = substr(str_repeat('?,',count($keys),0,-1));

$pdo->prepare("INSERT INTO `baz`($fields) VALUES($placeholder)")->execute(array_values($data));
*/

?>