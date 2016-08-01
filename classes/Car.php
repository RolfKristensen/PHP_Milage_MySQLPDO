<?php
namespace dk\lightsaber\milage;

	Class Car extends Persistent {
		public $name;
		public $user_id;
		public $make;
		public $model;
		public $model_specific;
		public $fuel_type;
		public $milage_mixed;
		
		protected $user;
		
		protected $table = 'car';
/*		protected $meta_mapping = array(
			'id' => 'id',
			'name' => 'name',
			'userId' => 'user_id',
			'make' => 'make',
			'model' => 'model',
			'modelSpecific' => 'model_specific',
			'fuelType' => 'fuel_type',
			'milageMixed' => 'milage_mixed'
		);
		
*/
		protected $meta_mapping_type = array(
			'id' => PersistentConst::NUMERIC,
			'name' => PersistentConst::STRING,
			'user_id' => PersistentConst::NUMERIC,
			'make' => PersistentConst::STRING,
			'model' => PersistentConst::STRING,
			'model_specific' => PersistentConst::STRING,
			'fuel_type' => PersistentConst::STRING,
			'milage_mixed' => PersistentConst::NUMERIC
		);

		public function getBinding() {
			return array('id' => $this->getId(),
					'name' => $this->getName(),
					'user_id' => $this->getUserId(),
					'make' => $this->getMake(),
					'model' => $this->getModel(),
					'model_specific' => $this->getModelSpecific(),
					'fuel_type' => $this->getFuelType(),
					'milageMixed' => $this->getMilageMixed()
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
			if($this->getUser() != NULL) {
				$this->user = User::load($this->getUserId());
			}
			return $this->user;
		}

		public function clearUser() {
			$this->users = null;
		}
		
		/***********
		 * toString method
		 ***********/
		public function __toString() {
			$string = '<h1>Car</h1>';
			$string .= 'ID: ' . $this->getId() . '<br/>';
			$string .= 'Car name: ' . $this->getName() . '<br/>';
			$string .= 'Make: ' . $this->getMake() . '<br/>';
			$string .= 'Model: ' . $this->getModel() . '<br/>';
			$string .= 'Model Specific: ' . $this->getModelSpecific() . '<br/>';
			$string .= 'Fuel type: ' . $this->getFuelType() . '<br/>';
			$string .= 'Milage mixed: ' . $this->getMilageMixed() . ' km/l<br/>';
			if(PersistentLog::$debug) {
				$string .= 'Persistent: ' . ($this->persist ? 'TRUE' : 'FALSE') . '<br/>';
			}
			$string .= '<h2>User</h2>';
			if($this->getUser() != null) {
				$string .= $this->getUser()->__toString();
			} else {
				$string .= 'N/A <br/>';
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
		
		public function getUserId() {
			return $this->user_id;
		}
		public function setUserId($userId) {
			$this->user_id=$userId;
			return $this;
		}
		
		public function getMake() {
			return $this->make;
		}
		public function setMake($make) {
			$this->make=$make;
			return $this;
		}
		
		public function getModel() {
			return $this->model;
		}
		public function setModel($model) {
			$this->model=$model;
			return $this;
		}
		
		public function getModelSpecific() {
			return $this->model_specific;
		}
		public function setModelSpecific($modelSpecific) {
			$this->model_specific=$modelSpecific;
			return $this;
		}
		
		public function getFuelType() {
			return $this->fuel_type;
		}
		public function setFuelType($fuelType) {
			$this->fuel_type=$fuelType;
			return $this;
		}
		
		public function getMilageMixed() {
			return $this->milage_mixed;
		}
		public function setMilageMixed($milageMixed) {
			$this->milage_mixed=$milageMixed;
			return $this;
		}

	}

?>