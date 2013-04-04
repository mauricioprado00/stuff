<?php

function isold($file_time, $max){
	$time_expresions  = 'years|months|days|hours|minutes';
	$re = '/(?P<amount>[0-9]+) (?P<time_expresion>'.$time_expresions.')/';
	$time_expresions = explode('|', $time_expresions);
	$file_time = time() - $file_time;
	if(preg_match($re, $max, $matches)){
		$time = time();
		$time_expresion = $matches['time_expresion'];
		$amount = $matches['amount'];
		switch($time_expresion){
			case 'years':{
				$file_time /= 360;
			}
			case 'months':{
				$file_time /= 30;
			}
			case 'days':{
				$file_time /= 24;
			}
			case 'hours':{
				$file_time /= 60;
			}
			case 'minutes':{
				$file_time /= 60;
			}
		}
		$file_time = floor($file_time);
		if($file_time>$amount){
			return true;
		}
	}
	return false;
	
}

?>

