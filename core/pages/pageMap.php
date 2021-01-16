<?php
include(dirname(__DIR__).'./MapHandler.php');

class MapPage{
	public function ShowPage(){
		global $lang, $MyDB;
		$width = 20;
		$length= 14;
		$x = 0;
		$y= 0;
		$gamid = 1;
		$size = 'medium';

		$mapHandler = new MapHandler();

		if(isset($_GET['setmapsize'])){
			switch ($_GET['setmapsize']){
				case 'small': $_COOKIE['set_mapsize'] = $_SESSION['set_mapsize'] = 'small'; break;
				case 'medium': $_COOKIE['set_mapsize'] = $_SESSION['set_mapsize'] = 'medium'; break;
				case 'big': $_COOKIE['set_mapsize'] = $_SESSION['set_mapsize'] = 'big'; break;
				case 'full': $_COOKIE['set_mapsize'] = $_SESSION['set_mapsize'] = 'full'; break;
			}
		}
		if(isset($_COOKIE['set_mapsize']))
			$size = $_COOKIE['set_mapsize'];
		if(isset($_SESSION['set_mapsize']))
			$size = $_SESSION['set_mapsize'];
		if(isset($_GET['size']))
			$size = $_GET['size'];

		switch ($size){
			case 'small': $width = 10; $length=10; break;
			case 'medium': $width = 18; $length=14; break;
			case 'big': $width = 28; $length=22; break;
			case 'full': $width = 999; $length=999; break;
		}

		if(isset($_SESSION['usr_comid'])){
			$query = $MyDB-> QuerySQLSingle("SELECT `ctyid` FROM `game_subsidiary` WHERE `comid` = '".$_SESSION['usr_comid']."'");
			$query = $MyDB-> QuerySQLSingle("SELECT `maptileid` FROM `world_city` WHERE `ctyid` = '".$query['ctyid']."'");
			$cords = $mapHandler -> RevertMapTileID($query['maptileid']);
			$x = $cords['x']-$width/2;
			$y = $cords['y']-$length/2;
		}

		if(isset($_GET['tilex']))
			$x = $_GET['tilex'];
		if(isset($_GET['tiley']))
			$y = $_GET['tiley'];


		if(isset($_GET['showall'])){
			$width = 500;
			$length= 500;
		}

		$maxwidth = $mapHandler -> staticdata_world_map[$gamid]['width'];
		$maxlength = $mapHandler -> staticdata_world_map[$gamid]['length'];

		if($width>$maxwidth) $width=$maxwidth;
		if($length>$maxlength) $length=$maxlength;
		if($x + $width>=$maxwidth) $x = $maxwidth-$width;
		if($y + $length>=$maxlength) $y = $maxlength-$length;
		if($x < 0) $x = 0;
		if($y < 0) $y = 0;

		$data = $mapHandler -> LoadMapPatch($gamid, $x, $y, $width, $length);

		$out = '';
		$content = '';

		$mapcontent = $this->ShowSizeMenu($x, $y);
		$content .= '<div class="block">';
		//$content .= '<div class="col-xs-2 nopadding" style="padding-left:4px;"><div class="page-sidebar">Side</div></div>';
		$content .= $mapHandler -> ShowMap('game.php?page=map&size='.$size,$data, $x, $y, $width, $length,$mapcontent);
		$content .= '<div class="col-sm-6 col-md-4" style="padding-right:4px;"><div class="page-sidebar">Sidebar</div></div>';
		$content .= '</div>';

		$out .= showWindow($content, $lang['map'], false);

		return $out;
	}
	function ShowSizeMenu($tilex, $tiley){
		global $lang;
		$mapurl = 'game.php?page=map&tilex='.$tilex.'&tiley='.$tiley;
		$out = '<ul class="menu-container">';
		$out .= '<li class="menu-entry"><span>'.$lang['mapsize'].'</span></li>';
		$out .= '<li class="menu-entry"><a href="'.$mapurl.'&setmapsize=small">'.$lang['small'].'</a></li>';
		$out .= '<li class="menu-entry"><a href="'.$mapurl.'&setmapsize=medium">'.$lang['medium'].'</a></li>';
		$out .= '<li class="menu-entry"><a href="'.$mapurl.'&setmapsize=big">'.$lang['big'].'</a></li>';
		$out .= '</ul>';
		return  $out;
	}
}
