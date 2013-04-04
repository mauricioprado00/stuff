<?php
class Config extends SimpleXMLElement{
	const CONFIG_FILE = 'config.xml';
	private $config;
	static $instance;
	public static function getInstance(){
		if(!self::$instance){
			//self::$instance = new self();
			self::$instance = simplexml_load_file(dirname(__FILE__).'/'.self::CONFIG_FILE, 'Config');
			//self::$instance->load();
		}
		return self::$instance;
	}
//	public function load(){
//		$this->config = simplexml_load_file(dirname(__FILE__).'/'.self::CONFIG_FILE);
//	}
//	public function getConfig($config){
//		$items = $this->config->xpath($config);
//		if(!$items)
//			return null;
//		return array_pop($items);
//	}
//	public function getConnectionConfig($config){
//		return $this->getConfig('/config/connection/'.$config);
//	}
}

