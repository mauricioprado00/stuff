#!/usr/bin/php5 -f
<?php
//#!/usr/bin/php5 -f
//#!/usr/bin/php5-cgi -q
include('c.class.php');
include('config.class.php');
include('config_handler.class.php');
include('app.class.php');
$app = App::getInstance();
$app->run();

?>
