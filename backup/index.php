<?php
include('messages.function.php');
include('isold.function.php');
include('ftp.class.php');
include('config.class.php');
include('mysqldump.class.php');
//echo Config::getInstance()->getConnectionConfig('server');
$config = Config::getInstance();

header('content-type:text');
//$time = strtotime('20120120 181516');
//var_dump(date('d/m/Y H:i:s', $time));
//die();
$files = array();
foreach($config->backup_settings->dbs->children() as $db){
	$dump = new Mysqldump($db->getName(), $config->db_connection, $db->db_connection);
	$filename = $dump->backup($config->local_temp, $db->db_name);
	if($filename)
		$files[] = $filename;
	echo "\n";
}

echoline("\nconectando a servidor ftp");
//// Example
$ftp = new ftp($config->connection);
$connection = $ftp->login();
if(!$connection){
	echoline("no se pudo conectar al servidor ftp");
}
else{
	echoline("conección correcta");
}
$temp_dir = dirname(__FILE__).'/'.$config->local_temp;
$files = array_diff(scandir($temp_dir), array('.','..'));
//var_dump($files);
//die();
foreach($files as $file){
	$file = $temp_dir.'/'.$file;
	echoline("subiendo archivo a ftp: $file");
	$subido = $ftp->putLocalFile($file, (string)$config->backup_settings->target_ftp_folder);
	if($subido){
		echoline("archivo subido correctamente");
		exec("rm $file");
	}
	else echoline("hubo un error subiendo el archivo $file");
}

echoline("\nborrando backups antiguos en servidor FTP");
echoline("listando archivos de backup en servidor FTP");
$archivos = $ftp->ftp_nlist('backups');
//var_dump($archivos);
$current_date = time();
foreach($archivos as $archivo){
	//var_dump($archivo);
	$re = '/[_](?P<date_time>(?P<year>[0-9]{4})(?P<month>[0-9]{2})(?P<day>[0-9]{2})_(?P<hour>[0-9]{2})(?P<minute>[0-9]{2})(?P<second>[0-9]{2}))[.]sql/';
	if(preg_match($re, $archivo, $matches)){
		$time = strtotime(str_replace('_',' ', $matches['date_time']));
		//var_dump(date('d/m/Y H:i:s', $time));
		if(isold($time, $config->preserve_files)){
			echoline("borrando archivo antiguo $archivo");
			if($ftp->ftp_delete($archivo)){
				echoline("archivo borrado correctamente");
			}
			else echoline("no se pudo borrar archivo");
		}
		else{
			echoline("se preserva archivo $archivo");
		}
	}
}
