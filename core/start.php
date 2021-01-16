<?php
session_start();
//session_unset ( );

include('config.php');
include('DBHelper.php');

include('utilities.php');
include('defaults.php');
include('layout.php');

$langfile = getLanguage();
if(!file_exists($langfile))
	die("We miss the language file for: ".$_SESSION['lang']." | ".$langfile);
include_once $langfile;

function getLanguage()
{
	$path = 'core/pages/';
	if(isset($_SESSION['lang']) && $_SESSION['lang'] != '')
		return $path.$_SESSION['lang'].'/language.php';
	
	$lang = '';	
	$acceptedLanguages = @explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
	$maxWeight = 0.0;
	
	foreach((array)$acceptedLanguages as $acceptedLanguage){	
		$weight = (float)@substr(explode(';', $acceptedLanguage)[1], 2);
		if(!$weight){
			continue;
			//$weight = 1.0;
		}

		$tmp = substr($acceptedLanguage, 0, 2);
		
		if(file_exists($path.$tmp.'/language.php') && $weight > $maxWeight){
			$lang =  $tmp;
			$maxWeight = $weight;
		}
	}
	$_SESSION['lang'] = $lang;
	setcookie('lang', $lang, time() + (3600 * 24 * 30));

	return $path.$lang.'/language.php';
}
?>