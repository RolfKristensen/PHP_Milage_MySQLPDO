<?php
	require 'classes/Persistent.php';
	require 'classes/PersistentConst.php';
	require 'classes/PersistentLog.php';
	require 'classes/Account.php';
	require 'classes/Car.php';
	require 'classes/User.php';
	require 'classes/Milage.php';
	
	use dk\lightsaber\milage\Account;
use dk\lightsaber\milage\PersistentLog;
use dk\lightsaber\milage\Car;
use dk\lightsaber\milage\User;
use dk\lightsaber\milage\Milage;
	$username = 'milage';
	$password = 'egalim';
	$dbname = 'lightsaber_dk';
	$host = 'localhost';

	try {
		$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		PersistentLog::$debug=TRUE;
/*
		echo "load list<br/>";
		$data = Account::loadList($pdo, "where id > :id", array('id'=>'12'));
		$data = Account::loadAll($pdo);
		
		foreach($data as $obj) {
			PersistentLog::info(json_encode($obj));
			PersistentLog::warn("Persisted? " . $obj->isPersisted());
		}
*/
/*
		echo "<br/>load instance<br/>";
		$obj = Account::load($pdo, 12);
		if (isset($obj)) {
			PersistentLog::info(json_encode($obj));
			PersistentLog::warn($obj->__toString());
		}
		$obj->setAccountName('ACC1');
		PersistentLog::warn("change values");
		PersistentLog::warn($obj->__toString());
		$obj->save();
*/		
/*
		$newObj = (new Account())->
			setPdo($pdo)->
			setAccountName("ACC4")->
			setStatus(0);
		PersistentLog::warn($newObj->__toString());
		
		
		PersistentLog::warn("[databaseTest] binding: " . json_encode($newObj->getBinding()));
		$newObj->save();
		
		PersistentLog::warn("[databaseTest] : " . $newObj->__toString());
		
*/
/*
		$data = Car::loadAll($pdo);
		foreach($data as $obj) {
			PersistentLog::info("[databaseTest] : " . json_encode($obj));
		}
	*/
/*
		$user = User::logIn($pdo, 'rolf@lightsaber.dk', 'Password1');
		PersistentLog::info("[databaseTest] : " . json_encode($user));
		PersistentLog::info("[databaseTest] : pass: " . $user->getPassword());
*/
		
/*
		$milage = Milage::load($pdo, 230);
		PersistentLog::info("[databaseTest] : " . json_encode($milage));
*/
		$user = User::logIn($pdo, 'rolf@lightsaber.dk', 'Password1');
		$list = $user->getMilagesFromTo('2015-01-01','2016-01-01');
		$list = $user->getMilages();
		$list = $user->getMilagesLastXMonths(10);
		$list = $user->getCars();
		PersistentLog::info("[databaseTest] : <br/>" . json_encode($list));
		
	} catch(PDOException $e) {
		echo 'ERROR: ' . $e->getMessage();
	}
?>