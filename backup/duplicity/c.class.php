<?php

class C{
	const COLOR_BLACK = 0;
	const COLOR_RED = 1;
	const COLOR_GREEN = 2;
	const COLOR_BROWN = 3;
	const COLOR_BLUE = 4;
	const COLOR_MAGENTA = 5;
	const COLOR_CYAN = 6;
	const COLOR_WHITE = 7;
	/*
	0 reset all attributes
	1 bold
	2 half-bright
	4 underscore
	5 blink
	7 reverse colours
	*/
	private static function colorize($text, $fg_color=null, $bg_color=null, $format=null){
		if(!isset($fg_color)&&!isset($bg_color)&&!isset($format))
			return $text;
		if(!isset($fg_color)){
			$fg_color = 7;
		}
		if(!isset($bg_color)){
			$bg_color = 0;
		}
		if(!isset($format)){
			$format = 0;
		}
		return "\033[".$format.';3'.$fg_color.';4'.$bg_color.'m' . $text . "\033[0m";
	}
	function __($text, $fg_color=null, $bg_color=null, $format=null){
		return self::colorize($text, $fg_color, $bg_color, $format);
	}
	function _u($text, $fg_color=null, $bg_color=null){
		return self::colorize($text, $fg_color, $bg_color, 4);
	}
}

?>