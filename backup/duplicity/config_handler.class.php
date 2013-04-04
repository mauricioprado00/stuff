<?php

class ConfigHandler{
	protected $config;
	private static $instance;
	const PATH_DIR_CONFIG_AVAILABLE = '/config/config_available';
	const PATH_DIR_CONFIG_ENABLED = '/config/config_enabled';
	public function getInstance(){
		if(!isset(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}
	public function __construct(){
		$this->config = Config::read('etc/config.xml');
	}
	public function getDirConfigEnabled(){
		$res = $this->config->xpath(self::PATH_DIR_CONFIG_ENABLED);
		if(!$res){
			return 'config-enabled';
		}
		return (string)$res[0];
	}
	public function getDirConfigAvailable(){
		$res = $this->config->xpath(self::PATH_DIR_CONFIG_AVAILABLE);
		if(!$res){
			return 'config-available';
		}
		return (string)$res[0];
	}
	protected function getBackupConfigs(){
		$base_path = dirname(__FILE__);
		$dir_config_available = $base_path . '/' . $this->getDirConfigAvailable();
		$dir_config_enabled = $base_path . '/' . $this->getDirConfigEnabled();
		$available = array_diff(scandir($dir_config_available), array('.', '..'));
		$assoc = array();
		foreach($available as $file_name){
			$assoc[$file_name] = false;
		}
		$enabled = array_diff(scandir($dir_config_enabled), array('.', '..'));
		foreach($enabled as $file_name){
			$real_path = realpath($dir_config_enabled.'/'.$file_name);
			if(strpos($real_path, $dir_config_available)===0){
				$assoc[$file_name] = true;
			}
			else{
				$assoc['config-enabled/'.$file_name] = 1;
			}
		}
		if(isset($type)&&in_array($type, array('all','enabled','disabled'))&&$type!='all'){
			$show = $type=='enabled'?true:false;
			$new_assoc = array();
			foreach($assoc as $file_name=>$enabled){
				if($enabled==$show){
					$new_assoc[$file_name] = $enabled;
				}
			}
			$assoc = $new_assoc;
		}
		return $assoc;

	}
	public function outputConfigState($config_name, $enabled){
		$pad_size = 30;
		if($enabled===true){
			$state = 'Enabled';
			$state = C::__($state, C::COLOR_GREEN);
		}
		elseif($enabled===false){
			$state = 'Disabled';
			$state = C::__($state, C::COLOR_RED);
		}
		elseif($enabled===1){
			$state = 'Enabled (hardcoded)';
			$state = C::__($state, C::COLOR_BLACK, C::COLOR_GREEN);
		}
		elseif($enabled===2){
			$state = 'Removed';
			$state = C::__($state, C::COLOR_WHITE, C::COLOR_RED);
		}
		echo str_pad($config_name, $pad_size, ' ', STR_PAD_RIGHT).'['.$state."]\n";
	}
	public function list_handle($type='all'){
		$assoc = $this->getBackupConfigs();
		//echo C::_u(str_pad('config', $pad_size, ' ', STR_PAD_RIGHT).'state')."\n";
		foreach($assoc as $file_name=>$enabled){
			$this->outputConfigState($file_name, $enabled);
		}
	}
	public function add_handle($config_name, $local_directory, $ftp_directory, $ftp_domain=null, $ftp_user=null, $ftp_password=null, $ftp_base_directory=null){
		if($config_name==''){
			echo "Debe proveer un nombre de configuracion\n";
			return;
		}
		$file = $this->getDirConfigAvailable().'/'.$config_name;
		if(file_exists($file)){
			echo "El archivo $file ya existe en la configuración, no se ha podido crear\n";
			return;
		}
		$elements_values = func_get_args();
		$elements_values = array_slice($elements_values, 1);
		$elements_names = array('local_directory', 'ftp_directory', 'ftp_domain', 'ftp_user', 'ftp_password', 'ftp_base_directory');
		$xmlwriter = new XMLWriter();
		$xmlwriter->openURI($file);
		$xmlwriter->startDocument("1.0", 'utf8');
		$xmlwriter->startElement('config');
		foreach($elements_values as $idx=>$element_value){
			if(!isset($element_value))
				continue;
			$element_name = $elements_names[$idx];
			$xmlwriter->startElement($element_name);
			$xmlwriter->text(utf8_encode($element_value));
			$xmlwriter->endElement();	
		}
		$xmlwriter->endElement();
		$xmlwriter->flush();
	}
	public function enable_handle($config_name){
		if(empty($config_name)){
			echo "Debe proveer un nombre de configuracion\n";
			return;
		}
		$assoc = $this->getBackupConfigs();
		if(!isset($assoc[$config_name])){
			echo "No existe ninguna configuración con el nombre $config_name\n";
			return;
		}
		$target = '../' .$this->getDirConfigAvailable() . '/' . $config_name;
		$link = dirname(__FILE__) . '/' . $this->getDirConfigEnabled() . '/' . $config_name;
		if(!file_exists($link)){
			`ln -s $target $link`;
		}
		$this->outputConfigState($config_name, true);
	}
	public function disable_handle($config_name){
		if(empty($config_name)){
			echo "Debe proveer un nombre de configuracion\n";
			return;
		}
		$assoc = $this->getBackupConfigs();
		if(!isset($assoc[$config_name])){
			echo "No existe ninguna configuración con el nombre $config_name\n";
			return;
		}
		$link = $this->getDirConfigEnabled() . '/' . $config_name;
		$link_realpath = realpath(dirname(__FILE__) . $link);
		$config_path = realpath($this->getDirConfigAvailable().'/'.$config_name);
		if($config_path!=$link_realpath){
			echo "No se puede eliminar la configuración, el archivo no es un link a la configuración $config_name ($link)\nEliminelo manualmente con rm $link";
		}
		`rm $link`;
		$this->outputConfigState($config_name, false);
	}
	public function remove_handle($config_name){
		if(empty($config_name)){
			echo "Debe proveer un nombre de configuracion\n";
			return;
		}
		$assoc = $this->getBackupConfigs();
		if(!isset($assoc[$config_name])){
			echo "No existe ninguna configuración con el nombre $config_name\n";
			return;
		}
		$this->disable_handle($config_name);
		$config_path = realpath($this->getDirConfigAvailable().'/'.$config_name);
		`rm $config_path`;
		$this->outputConfigState($config_name, 2);
	}
	protected function getConfigBackup($config_name){
		$return = array();
		$config_path = ($this->getDirConfigAvailable().'/'.$config_name);
		$config_backup = Config::read($config_path);
		$return['local_directory'] = $config_backup->local_directory;
		$return['ftp_directory'] = $config_backup->ftp_directory;
		$ftp_config_names = explode(' ', 'ftp_domain ftp_user ftp_password ftp_base_directory');
		foreach($ftp_config_names as $config_name){
			$return[$config_name] = isset($config_backup->$config_name)?$config_backup->$config_name:$this->config->ftp->$config_name;
		}
		return $return;
	}
	public function view_handle($config_name){
		if(empty($config_name)){
			echo "Debe proveer un nombre de configuracion\n";
			return;
		}
		$assoc = $this->getBackupConfigs();
		if(!isset($assoc[$config_name])){
			echo "No existe ninguna configuración con el nombre $config_name\n";
			return;
		}
		extract($this->getConfigBackup($config_name));
		$this->outputConfigState($config_name, $assoc[$config_name]);
		echo <<<out
local_directory: $local_directory
ftp_directory: $ftp_directory
ftp_domain: $ftp_domain
ftp_user: $ftp_user
ftp_password: $ftp_password
ftp_base_directory: $ftp_base_directory

out;
	}
	function help_handle(){
		echo <<<out
Ayuda de configuración de backups

--list[=all|enabled|disabled]
			Lista las configuraciones de backup. Opcionalmente se puede indicar que liste solo activas o inactivas.	

--add="config_name local_directory ftp_directory [ftp_domain ftp_user ftp_password ftp_base_directory]" 
			Agrega una configuración de backup.
			ftp_directory puede ser relativo (si no comienza con /) o absoluto (si comienza con /).
			Se puede configurar un ftp específico para la configuración o usar el ftp por defecto (ftp_config.xml)

			IMPORTANTE: Recuerde utilizar las comillas para encerrar todo el argumento

--remove=config_name
			Elimina una configuración de backup
	
--enable=config_name
			Activa una configuración de backup

--disable=config_name
			Desactiva una configuración de backup

--view=config_name
			Para ver una configuración de backup
out;
	}
}

?>