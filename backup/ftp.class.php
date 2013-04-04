<?php
class ftp{
    public $conn;
    private $config;

    public function __construct($config){
    	$this->config = $config;
        $this->conn = ftp_connect($config->server);
    }
    public function login(){
		return ftp_login($this->conn, $this->config->user, $this->config->password);
	}
   
    public function __call($func,$a){
        if(strstr($func,'ftp_') !== false && function_exists($func)){
            array_unshift($a,$this->conn);
            return call_user_func_array($func,$a);
        }else{
            // replace with your own error handler.
            die("$func is not a valid FTP function");
        }
    }
    
    public function putLocalFile($file, $remote_dir){
    	if(!$this->conn){
    		echoline("no hay conección");
    		return false;
    	}
		$fp = fopen($file, 'r');
		if(!$fp){
			echoline("no se puede abrir $file");
			return false;
		}
		$remote_file = $remote_dir.'/'.basename($file);
		$return = ftp_fput($this->conn, $remote_file, $fp, FTP_ASCII);
		fclose($fp);
		return $return;
	}
}
