<?php
$style = 'default';
$cssPath = './src/style.css';
$finalPath = '../core/style/'.$style.'/style.css';

$keywords = [
		//'$main-font-color$' => '#0079c4',
		'$main-font-color$' => '#3d6279',
		'$blue-color$' => '#15a4ff',
		'$dark-blue$' => '#3d6279',
		'$bright-blue-color$' => '#c6e9ff',
		'$orange-color$' => '#ff6b08',
];

if(isset($_GET['generatecss'])){
	$myfile = fopen($cssPath, "r") or die("Unable to open file! ".$cssPath);
	$text = fread($myfile,filesize($cssPath));
	fclose($myfile);
	
	$text = strtr($text, $keywords);
	
	
	$newcssfile = fopen($finalPath, "w") or die("Unable to open file! ".$finalPath);
	fwrite($newcssfile, $text);
	fclose($newcssfile);
	
	
	echo '<p>CSS File: '.$finalPath.' generated successfully</p>';
	echo $text;
}
else{
	echo '<p><a href="?generatecss">Generate CSS File: '.$finalPath.'</a></p>';
}