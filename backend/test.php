<?php
include('../core/layout.php');
echo createlayout_header();

die(hash('sha256', "0". md5("K4m572NDaQBYkZhN")));

echo '<style>
.treebox{
background-color: burlywood;
float:left; 
}
.treebox:hover{
background-color: blue;
}
</style>';
//border: 3px solid white;

$boxsize = 400;
$fullsize = 3000;
$halfsize = sqrt($fullsize);
$boxes = array(600,420,603,850,80,60,236);
rsort($boxes);

$tilesize = 5;
$out = '';

$out .= '<div style="background-color: gray; width:100%; max-width:'.($boxsize).'px; height:'.($boxsize).'px;">';
for ($i=0;$i<count($boxes);$i++){
	$s = sqrt($boxes[$i])/$halfsize*100;
	$out .= CreateBox($s, $s);
}
$out .= '</div>';

echo $out;

function  CreateBox($width, $height) {
	global $tilesize;
	return '<div class="treebox" style="width:'.($width).'%; height:'.($height).'%;">
			bla
			</div>';	
}