<?php

class App extends ConfigHandler{
	private static $instance;
	public function getInstance(){
		if(!isset(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}
	public function run(){
		//@mkdir('temp');
		//`rm temp/run.sh`;
		$configs = $this->getBackupConfigs();
		foreach($configs as $config_name=>$enabled){
			$this->outputConfigState($config_name, $enabled);
			if(!$enabled){
				echo "skipping\n";
				continue;
			}
			echo "actualizando en ftp\n";
			$config = (object)$this->getConfigBackup($config_name);
			//var_dump($config);
			$target_directory = $config->ftp_base_directory . '/' . $config->ftp_directory;
			
			$sh = "export PASSPHRASE=SomeLongGeneratedHardToCrackKey\nexport FTP_PASSWORD={$config->ftp_password}\nduplicity {$config->local_directory} ftp://{$config->ftp_user}@{$config->ftp_domain}{$target_directory}\nunset PASSPHRASE\nunset FTP_PASSWORD\n";
			//echo $sh;
			echo system($sh);
			//ECHO "Agregado para actualización\n";
			//file_put_contents('temp/run.sh', $sh);
			//chmod('temp/run.sh', 0777);
			//system('temp/run.sh');
			echo "\n";
		}
		echo "\n";
	}
	public function verify(){
		//@mkdir('temp');
		//`rm temp/run.sh`;
		$configs = $this->getBackupConfigs();
		foreach($configs as $config_name=>$enabled){
			$this->outputConfigState($config_name, $enabled);
			if(!$enabled){
				echo "skipping\n";
				continue;
			}
			echo "buscando cambios en archivos locales\n";
			$config = (object)$this->getConfigBackup($config_name);
			//var_dump($config);
			$target_directory = $config->ftp_base_directory . '/' . $config->ftp_directory;
			
			$sh = "export PASSPHRASE=SomeLongGeneratedHardToCrackKey\nexport FTP_PASSWORD={$config->ftp_password}\nduplicity verify ftp://{$config->ftp_user}@{$config->ftp_domain}{$target_directory} {$config->local_directory}\nunset PASSPHRASE\nunset FTP_PASSWORD\n";
			//echo $sh;
			echo system($sh);
			//ECHO "Agregado para actualización\n";
			//file_put_contents('temp/run.sh', $sh);
			//chmod('temp/run.sh', 0777);
			//system('temp/run.sh');
			echo "\n";
		}
		echo "\n";
	}
	
	function help_handle(){
		echo <<<out
Ayuda de configuración de backups

--list[=all|enabled|disabled]
			Lista las configuraciones de backup. Opcionalmente se puede indicar que liste solo activas o inactivas.	

--add=config_name local_directory ftp_directory [ftp_domain ftp_user ftp_password ftp_base_directory] 
			Agrega una configuración de backup.
			ftp_directory puede ser relativo (si no comienza con /) o absoluto (si comienza con /).
			Se puede configurar un ftp específico para la configuración o usar el ftp por defecto (ftp_config.xml)

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