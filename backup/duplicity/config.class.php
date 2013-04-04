<?php

class Config extends SimpleXMLElement{
	public static function read($config_file){
		return simplexml_load_file(dirname(__FILE__).'/'.$config_file, 'Config');
	}

}

?>