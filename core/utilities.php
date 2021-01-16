<?php
function debugEcho($msg){
	echo $msg.'</br>';
}

function validate_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

function GetCompanyName($company){
	return $company['name'].' '.$company['firm'];
}

function GetPriceNumber($value, $decimals=2){
	return '&sect;'.number_format($value,$decimals,',','.');
}
function GetNumber($value, $decimals=2){
	return number_format($value,$decimals,',','.');
}
function GetArea($value){
	return $value.'m<sup>2</sup>';
}

function CalcualtePerformance($last, $current){
	$value = 0;
	$value = (($current - $last) / ($current>0?$current:0.001)) * 100;
	//echo ("<p>((".$current." - ".$last.") / ".$current.") * 100 = ".$value."</p>");
	return $value;
}

function GetPercentageText($perf){
	$perfsign = ($perf>0?'+':'');
	$perfHighlight = 'highlight-'.($perf>0?'positive':($perf<0?'negative':'neutral'));
	return "<span class='".$perfHighlight."'>".$perfsign.$perf.'%'."</span>";
}

function GetPercentageString($perf){
	$perfsign = ($perf>0?'+':'');
	$perfHighlight = 'highlight-'.($perf>0?'positive':($perf<0?'negative':'neutral'));
	return $perfsign.$perf.'%';
}

function SendMail($recipient, $subject, $text, $from = "noreply@marketobserver.com"){
	mail($recipient, $subject, $text, "From: ".$from);
}
function redirect($target, $time = 0, $message=''){
	if($message == '')
		$message = 'You should be redirected to <a href = "'.$target.'">'.$target.'</a>.';
	
die('<html><head><meta http-equiv="refresh" content="'.$time.'; url='.$target.'" />
  <link rel="stylesheet" type="text/css" href="core/style/default/style.css" /></head>
			<body><p>'.$message.'</p></body>
			</html>');
}
?>