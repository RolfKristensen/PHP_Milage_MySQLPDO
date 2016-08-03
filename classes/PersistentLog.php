<?php
namespace dk\lightsaber\milage;

	Class PersistentLog {
		public static $debug;
		public static $logger;
		/**
		 * Method used to send log messages
		 * @param $msg
		 * @return void
		 */
		public static function log($msg)
		{
			if (PersistentLog::$debug) echo "<font color=#009900>== LOG ==<br/>".$msg."</font><br>\n";
		}
		
		/**
		 * Method used to send info messages
		 * @param $msg
		 * @return void
		 */
		public static function info($msg)
		{
			if (PersistentLog::$debug) echo "<font color=#0000FF>INFO: ".$msg."</font><br>\n";
		}
		
		/**
		 * Method used to send warnings messages
		 * @param $msg
		 * @return void
		 */
		public static function warn($msg)
		{
			if (PersistentLog::$debug) echo "<font color=#999900>WARN: ".$msg."</font><br>\n";
		}
		
		/**
		 * Method used to send error messages
		 * @param $msg
		 * @return void
		 */
		public static function error($msg)
		{
			if (PersistentLog::$debug) echo "<font color=#FF0000>ERROR: ".$msg."</font><br>\n";
		}
		
	}

?>