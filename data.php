<?php
class Config{
	public static $config=[
	'objects'=>[

	],
	
	'options'=>[
//		'tel'=>'8 (84366) 3-11-25',
		]
];
	public static function get($object){
		return self::$config[$object];
		
	}
}
