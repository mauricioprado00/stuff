<?php
class Mysqldump{
	private $db_name;
	private $default_connection;
	private $specific_connection;
	public function __construct($db_name, $default_connection, $specific_connection){
		$this->db_name = $db_name;
		$this->default_connection = $default_connection;
		$this->specific_connection = $specific_connection;
	}
	public function backup($directory, $file_prefix){
		$user = $this->specific_connection?$this->specific_connection->user:$this->default_connection->user;
		$password = $this->specific_connection?$this->specific_connection->password:$this->default_connection->password;
		$host = $this->specific_connection?$this->specific_connection->host:$this->default_connection->host;
		$binario = 'mysqldump';
		$schema = $this->db_name;
		$filename = dirname(__FILE__).$directory.'/'.$file_prefix.date('_Ymd_His').'.sql';
		$action =  "$binario -h$host --skip-opt --quick --add-drop-table --add-locks --create-options --disable-keys --extended-insert --lock-tables --set-charset -u$user -p$password $schema > $filename";
		echoline("creando backup de db '$schema'\n$action");
		exec($action);
		if(file_exists($filename)){
			$action = "tar -zcf $filename.tar.gz $filename";
			echoline("comprimiendo $filename\n$action");
			exec($action);
			if(!file_exists($filename.'.tar.gz')){
				echoline("no se pudo comprimir");
			}
			else{
				echoline("comprimido correctametne $filename.tar.gz");
				echoline("eliminando archivo sql $filename");
				exec("rm $filename");
				$filename = $filename.'.tar.gz';
			}
			echoline("backup creado correctamente $filename");
			return $filename;
		}
		else echoline("no se pudo crear backup");
	}
}

