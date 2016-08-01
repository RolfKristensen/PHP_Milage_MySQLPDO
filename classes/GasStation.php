<?php
namespace dk\lightsaber\milage;

	Class GasStation extends Persistent {
		public $name;
	
		protected $table = 'gas_station';
/*
		protected $meta_mapping = array(
			'id' => 'id',
			'name' => 'name'
		);
*/
		protected $meta_mapping_type = array(
			'id' => PersistentConst::NUMERIC,
			'name' => PersistentConst::STRING
		);

		public function getBinding() {
			return array('id' => $this->getId(),
					'name' => $this->getName()
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
		
		/***********
		 * toString method
		 ***********/
		public function __toString() {
			$string = $this->name. "<br/>" . $this->id . "<br/>";
			if(PersistentLog::$debug) {
				$string .= 'Persistent: ' . ($this->persist ? 'TRUE' : 'FALSE') . '<br/>';
			}
			
			return $string;
		}
	
		/***********
		 * Getter/Setters
		 ***********/		
		public function getName() {
			return $this->name;
		}
		public function setName($name) {
			$this->name=$name;
			return $this;
		}
		
	}

?>