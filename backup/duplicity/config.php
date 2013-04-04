#!/usr/bin/php5-cgi -q
<?php
#!/usr/bin/php5 -f
#!/usr/bin/php5-cgi -q
//echo `env`;
//var_dump($GLOBALS);
//die(__FILE__.__LINE__);

include('c.class.php');
include('config.class.php');
include('config_handler.class.php');
$config_handler = ConfigHandler::getInstance();

if(isset($_GET['--list'])){
	$config_handler->list_handle($_GET['--list']);
}
elseif(isset($_GET['--add'])){
//	$params = array_keys($_GET);
//	$params = array_slice($params, 2);
//	array_unshift($params, $_GET['--add']);
	$params = explode(' ', $_GET['--add']);
	call_user_func_array(array($config_handler, 'add_handle'), $params);
}
elseif(isset($_GET['--enable'])){
	$config_handler->enable_handle($_GET['--enable']);
}
elseif(isset($_GET['--disable'])){
	$config_handler->disable_handle($_GET['--disable']);
}
elseif(isset($_GET['--remove'])){
	$config_handler->remove_handle($_GET['--remove']);
}
elseif(isset($_GET['--view'])){
	$config_handler->view_handle($_GET['--view']);
}
else{
	$config_handler->help_handle();
}


?>
