<?php
namespace dk\lightsaber\milage;

	Class Milage extends Persistent {
		// database columns
		public $date;
		public $litre;
		public $km;
		public $price_l;
		public $gas_type_id;
		public $gas_station_id;
		public $km_l;
		public $price_km;
		public $price_sum;
		public $user_id;
		public $car_id;
		
		public $car;
	
		protected $table = 'milage';
/*
		protected $meta_mapping = array(
			'id' => 'id',
			'date' => 'date',
			'litre' => 'litre',
			'km' => 'km',
			'priceL' => 'price_l',
			'gasTypeId' => 'gas_type_id',
			'gasStationId' => 'gas_station_id',
			'kmL' => 'km_l',
			'priceKm' => 'price_km',
			'priceSum' => 'price_sum',
			'userId' => 'user_id',
			'carId' => 'car_id'
		);
*/
		protected $meta_mapping_type = array(
			'id' => PersistentConst::NUMERIC,
			'date' => PersistentConst::DATE,
			'litre' => PersistentConst::NUMERIC,
			'km' => PersistentConst::NUMERIC,
			'price_l' => PersistentConst::NUMERIC,
			'gas_type_id' => PersistentConst::NUMERIC,
			'gas_station_id' => PersistentConst::NUMERIC,
			'km_l' => PersistentConst::NUMERIC,
			'price_km' => PersistentConst::NUMERIC,
			'price_sum' => PersistentConst::NUMERIC,
			'user_id' => PersistentConst::NUMERIC,
			'car_id' => PersistentConst::NUMERIC
		);

		public function getBinding() {
			return array('id' => $this->getId(),
					'date' => $this->getDateStr(),
					'litre' => $this->getLitre(),
					'km' => $this->getKm(),
					'price_l' => $this->getPriceL(),
					'gas_type_id' => $this->getGasTypeId(),
					'gas_station_id' => $this->getGasStationId(),
					'km_l' => $this->getKmL(),
					'price_km' => $this->getPriceKm(),
					'price_sum' => $this->getPriceSum(),
					'user_id' => $this->getUserId(),
					'car_id' => $this->getCarId()
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
		public function getCar() {
			if(!isset($this->car)) {
				$this->setCar(Car::load($this->getCarId()));
			}
			return $this->car;
		}
		
		public function setCar($car) {
			$this->car=$car;
			if(!isset($this->carId)) {
				$this->carId=$car->getId();
			}
		}
		
		public function getDateStr() {
			return date('d-m-Y', strtotime($this->date));
		}

		/***********
		 * toString method
		 ***********/
		public function __toString() {
			$string = "<h1>Milage</h1> ";
			$string .= $this->id . "\t" . $this->getDateStr() . "\t" . $this->litre . "\t";
			$string .= $this->km . "\t" . $this->priceL . "\t" . $this->gasTypeId . "\t";
			$string .= $this->gasStationId . "\t" . $this->kmL . "\t";
			$string .= $this->priceKm;
			$string .= $this->getCar()->__toString();
			if(PersistentLog::$debug) {
				$string .= "\tPersistent: " . ($this->persist ? 'TRUE' : 'FALSE') . '<br/>';
			}

			return $string;
		}
		
		// Update methods of dynamic content
		public function calculateDynamicValues() {
			$this->calculateKmL();
			$this->calculatePriceKm();
			$this->calculatePriceSum();
		}
		
		protected function calculateKmL() {
			if($this->litre != 0) {
				$this->kmL = $this->km/$this->litre;
			}
		}
		
		protected function calculatePriceKm() {
			if($this->km != 0) {
				$this->priceKm = ($this->priceL * $this->litre)/$this->km;
			}
		}

		protected function calculatePriceSum() {
			$this->priceSum = $this->priceL*$this->litre;
		}
		
		
		
		// Getter/Setters

		public function getDate() {
			return $this->date;
		}
		public function setDate($date) {
			$this->date=$date;
			return $this;
		}
		
		public function getLitre() {
			return $this->litre;
		}
		public function setLitre($litre) {
			$this->litre=$litre;
			return $this;
		}
		
		public function getKm() {
			return $this->km;
		}
		public function setKm($km) {
			$this->km=$km;
			return $this;
		}
		
		public function getPriceL() {
			return $this->priceL;
		}
		public function setPriceL($priceL) {
			$this->priceL=$priceL;
			return $this;
		}
		
		public function getGasTypeId() {
			return $this->gasTypeId;
		}
		public function setGasTypeId($gasTypeId) {
			$this->gasTypeId=$gasTypeId;
			return $this;
		}
		
		public function getGasStationId() {
			return $this->gasStationId;
		}
		public function setGasStationId($gasStationId) {
			$this->gasStationId=$gasStationId;
			return $this;
		}
		
		public function getKmL() {
			return $this->kmL;
		}
		public function setKmL($kmL) {
			$this->kmL=$kmL;
			return $this;
		}
		
		public function getPriceKm() {
			return $this->priceKm;
		}
		public function setPriceKm($priceKm) {
			$this->priceKm=$priceKm;
			return $this;
		}

		public function getPriceSum() {
			return $this->priceSum;
		}
		public function setPriceSum($priceSum) {
			$this->priceSum=$priceSum;
			return $this;
		}
		
		public function getUserId() {
			return $this->userId;
		}
		public function setUserId($userId) {
			$this->userId=$userId;
			return $this;
		}
		
		public function getCarId() {
			return $this->carId;
		}
		public function setCarId($carId) {
			$this->carId=$carId;
			return $this;
		}
	}

?>