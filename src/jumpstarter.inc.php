<?php
/******************
 * PHP Library for Jumpstarter.io projects
 * Copyright (C) 2014 Alexander Forselius <alex@artistconnector.com>
 * License: MIT
 **/

/***
 * Jumpstarter.IO class
 * @class
 * @extends {SQLite3}
 ***/
class Jumpstarter extends SQLite3 {
	/**
	 * Creates a new Jumpstarter class
	 */
	public function __construct($name = 'database')
    {
    	// Check if we are in development or production environment
    	if ($this->isProduction()) {
    		
			// Check if the database exists or create it.
			$database_path = $this->getStateDirectory() . $name . '.sqlite';
    		if (file_exists($database_path)) {
    			// Open as usual
	    	 	$this->open($database_path);
			} else {
				// Create new production database
    	 		copy($this->getAppDirectory() . $name . DIRECTORY_SEPARATOR . $name . '.sqlite', $database_path);
				// Clean tables from test data
				$this->open($database_path);
			}		
		} else {
			// Development build
    	 	$this->open(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'database.sqlite');
		}
	}
	
	/**
	 * Returns if you are in production mode
	 */
	public function isProduction() {
		return is_dir('/app') && is_readable('/app');
	}
	
	public function getStateDirectory() {
		if ($this->isProduction()) {
			return $thi->getAppDirectory() . "state/";
		}
	}
	
	/**
	 * Gets the app directory depending on the environment (dev or production)
	 */
	
	public function getAppDirectory($development_path = '') {
		if ($this->isProduction()) {
			// We are in production environment
			return "/app";
		} else {
			return $development_path;
		}
	}
	
	public function initSessions() {
		if ($this->isProduction()) {
			$session_dir = $this->getStateDirectory() . "/session";
			if (!is_dir($session_dir)) {
				mkdir($session_dir);
			}
			ini_set('session.save_path', $session_dir);
		}
	}
	
	/**
	 * Load file from app
	 * The path provided in the argument is basing from /app/
	 */
	public function loadFile($path) {
		$path = $this->getAppDirectory() . $path;
		$file = fopen($path, 'rb');
		$data = fread($file, filesize($path));
		fclose($file);
		return $data;
	}
	
	/**
	 * Load file from app
	 * The path provided in the argument is basing from /app/state
	 */
	public function saveFile($path, $str) {
		$path = $this->getStateDirectory() . $path;
		$file = fopen($path, 'wb');
		$data = fwrite($file, $str);
		fclose($file);
	}
	
	/**
	 * Loads /app/state/env.json into memory and returns it as JSON
	 */
	public function loadEnvConfig() {
		$file = $this->loadFile('/env.json');
		$data = json_decode($file, TRUE);
		return $data;
	}
	
	public function loadSettings() {
		$envjson = $this->loadEnvConfig();
		return $envjson['app']['settings'];
	}
}
