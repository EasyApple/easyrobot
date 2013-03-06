<?php
/*
 * @package Easybot
 * @author Jack Zheng
 * @copyright (c) www.easyapple.net
 * @license GNU Affero General Public License
 * @link http://easyrobot.sinaapp.com
 */

// Easybot backend logic:
class Easybot {

	var $db;
	var $_config;
	
	function Easybot() {
		$this->initialize();
	}

	function initialize() {
		// Initialize configuration settings:
		$this->initConfig();

		// Initialize the DataBase connection:
		$this->initDataBaseConnection();		
	}

	function initConfig() {
		/*
		$config = null;
		if (!include(EASYBOT_PATH.'lib/config.php')) {
			echo('<strong>Error:</strong> Could not find a config.php file in "'.EASYBOT_PATH.'"lib/". Check to make sure the file exists.');
			die();
		}
		$this->_config = &$config;

		// Initialize custom configuration settings:
		$this->initCustomConfig();
		*/
	}
	
	function initDataBaseConnection() {
		/*
		// Create a new database object:
		$this->db = new EasybotDataBase(
			$this->_config['dbConnection']
		);
		// Use a new database connection if no existing is given:
		if(!$this->_config['dbConnection']['link']) {
			// Connect to the database server:
			$this->db->connect($this->_config['dbConnection']);
			if($this->db->error()) {
				echo $this->db->getError();
				die();
			}
			// Select the database:
			$this->db->select($this->_config['dbConnection']['name']);
			if($this->db->error()) {
				echo $this->db->getError();
				die();
			}
		}
		// Unset the dbConnection array for safety purposes:
		unset($this->_config['dbConnection']);		
		*/
	}
	
	function getDataBaseTable($table) {
		/*
		return ($this->db->getName() ? '`'.$this->db->getName().'`.'.$this->getConfig('dbTableNames',$table) : $this->getConfig('dbTableNames',$table));
		*/
	}

	function getTestReply($msg) {
		return $msg;
	}
}

?>