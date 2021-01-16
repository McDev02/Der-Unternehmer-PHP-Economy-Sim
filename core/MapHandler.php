<?php
class MapTileData {
	public $TileID;
	public $FieldType;
	public $Content;
	public $X;
	public $Y;

	function MapTileData($tileID, $fieldType, $content, $x, $y) {
		$this -> TileID = $tileID;
		$this -> FieldType = $fieldType;
		$this -> Content = $content;
		$this -> X = $x;
		$this -> Y = $y;
	}
}

class MapHandler{
	//Data Fields
	public $worldmap_DataFields = array (
			'none',
			//Water
			'ocean',
			'coast',
			'river',
			//Land
			'grass',
			'sand',
			'rocky'
	);
	//Data Content
	public $worldmap_DataContent = array (
			'empty',
			'city',
			'offshore'
	);

	public $staticdata_world_map = [
			1 => [
					'width' => 72,	//72
					'length' => 42,	//42
			]
	];

	public $tileWidth = 48;
	public $tileHeight = 32;
	public $tileHalfHeight;
	public $tileWidthOffset;

	function MapHandler(){
		$this -> tileHalfHeight = $this ->tileHeight/2;
		$this -> tileWidthOffset =$this ->tileWidth*(3/4);
	}

	/*
	 * TileID is "odd-q offset" coordinates related to
	 * http://www.redblobgames.com/grids/hexagons/
	*/
	public $directions = [
		'north' => ['x' => 0,'y' => 1,'z' => -1],
		'n-east' => ['x' => 1,'y' => 0,'z' => -1],
		's-east' => ['x' => 1,'y' => -1,'z' => 0],
		'south' => ['x' => 0,'y' => -1,'z' => 1],
		's-west' => ['x' => -1,'y' => 0,'z' => -1],
		'n-west' => ['x' => -1,'y' => 1,'z' => 0]
	];

	public function GetDistance($a, $b){
		return ( abs($a['x'] - $b['x']) + abs($a['y'] - $b['y']) + abs($a['z'] - $b['z'])) / 2;
	}

	public function ConvertCubeToOffset($x, $y, $z){
		$pos = array();
		$pos['x'] = $x;
		$pos['y'] = $z + ($x - ($x&1)) / 2;
		return $pos;
	}

	public function ConvertOffsetToCube($x, $y){
		$pos = array();
		$pos['x'] = $x;
		$pos['z'] = $y - ($x - ($x&1)) / 2;
		$pos['y'] = -$pos['x']-$pos['z'];
		return $pos;
	}

	public function GetMapTileID($gamid, $tilex, $tiley){
		$tileid = 0;
		if(isset($this->staticdata_world_map[$gamid]))
		$tileid = $tiley * $this->staticdata_world_map[$gamid]['width'] + $tilex;
		return $gamid*100000 + $tileid;
	}

	public function RevertMapTileID($maptileid){
		$tileid = 0;
		$gamid = floor($maptileid / 100000);
		if(isset($this->staticdata_world_map[$gamid])){
			$tileid = $maptileid - $gamid * 100000;
			$y = floor($tileid/ $this->staticdata_world_map[$gamid]['width']);
			$x = $tileid - $y * $this->staticdata_world_map[$gamid]['width'];
		}
		return ['x' => $x, 'y' => $y];
	}

	public function ShowMap($target, $DataArray, $tilex, $tiley, $width, $length,$mapcontent=''){
		global $MyDB;
		$out = '';

		$offset = 6;
		$boxwidth = $this->tileWidth+($width-1)*$this->tileWidthOffset;
		$boxlength = (32+$this->tileHalfHeight+$length*$this->tileHeight);
		$col = ceil( $boxwidth / 92.5);
		$out .= '<div class="col-sm-12 col-md-'.$col.' nopadding" style="margin-bottom: 20px;">';
		$out .= $mapcontent;
		$out .= '<div class="map-tile-container" style="margin: 0 auto; width: '.$boxwidth.'px; height: '.$boxlength.'px;">';

		$startWithEven=$tilex % 2 == 0;
		for ($y = 0; $y< $length; $y++){
			//Even
			for ($x = $startWithEven?0:1; $x< $width; $x+=2){
				$tiledata = $DataArray[$y*$width+$x];
				$title = '';
				if($tiledata-> Content == 'city')
					$title = $this->GetCityName($MyDB, $tiledata);

				$out .= $this -> CreateTile($startWithEven,$x, $y, $tiledata, $title);
			}
			//Odd
			for ($x = $startWithEven?1:0; $x< $width; $x+=2){
				$tiledata = $DataArray[$y*$width+$x];
				$title = '';
				if($tiledata-> Content == 'city')
					$title = $this->GetCityName($MyDB, $tiledata);

				$out .= $this -> CreateTile($startWithEven,$x, $y, $tiledata, $title);
			}
		}
		$out .= '<a href="'.$target.'&tilex='.($tilex).'&tiley='.($tiley-$offset).'" class="ui-arrow-up" style="top: 0px; left:'.($boxwidth/2-21).'px"></a>';
		$out .= '<a href="'.$target.'&tilex='.($tilex+$offset).'&tiley='.($tiley).'" class="ui-arrow-right" style="right: -10px; top:'.($boxlength/2).'px"></a>';
		$out .= '<a href="'.$target.'&tilex='.($tilex).'&tiley='.($tiley+$offset).'" class="ui-arrow-down" style="bottom: -20px; left:'.($boxwidth/2-21).'px"></a>';
		$out .= '<a href="'.$target.'&tilex='.($tilex-$offset).'&tiley='.($tiley).'" class="ui-arrow-left" style="left: -10px; top:'.($boxlength/2).'px"></a>';
		$out .= '</div>';
		$out .= '</div>';	//Block
		return $out;
	}

	function GetCityName($MyDB,$tiledata){
		if($tiledata-> Content == 'city'){
			$query = $MyDB->QuerySQLSingle("SELECT `name` FROM `world_city` WHERE `maptileid` = '".($this->GetMapTileID($_SESSION['gamid'], $tiledata-> X, $tiledata-> Y))."'");
			if($query!=null)
			return $query['name'];
		}
		return 'City';
	}

	public function LoadMapPatch($gamid, $x, $y, $width, $length){
		global $MyDB;
		$data = array();
		$maxX = $x + $width;
		$maxY = $y + $length;

		$sql = "SELECT * FROM `world_map_tile` WHERE
				`gamid` = '".$gamid."' AND
				`tilex` >= '".$x."' AND
				`tiley` >= '".$y."' AND
				`tilex` < '".$maxX."' AND
				`tiley` < '".$maxY."' ";
		$query = $MyDB -> QuerySQL($sql);

		for ($i = 0; $i< count($query); $i++){
			$tile = $query[$i];
			$id = ($tile['tiley']-$y)*$width+($tile['tilex']-$x);
			$data[$id] = new MapTileData(0, $this -> worldmap_DataFields[$tile['field']],$this -> worldmap_DataContent[$tile['content']], $tile['tilex'], $tile['tiley']);
		}

		return $data;
	}

	public function CreateTile($startWithEven, $posX, $posY, $data, $title){
		$out = '';
		$odd =  $posX % 2 !=0;
		if(!$startWithEven)
			$odd = !$odd;
		$yOff = 22;
		$clickable=false;
		$obj = 'empty';
		if($odd)
			$yOff +=  $this->tileHalfHeight;
		$out .= '<div class="map-tile-'.($data->FieldType=='ocean'?'ocean':'base').'" style="
				left: '.($this->tileWidthOffset*$posX).'px;
				top: '.($yOff+$this->tileHeight*$posY).'px;
				">';
		$obj = $data-> Content;
		$veh = ($data->FieldType=='ocean'&&rand(0,100)==1)?'ship':'empty';
		$clickable = $obj != 'empty';

		$out .= '<span class="map-tile-'.($clickable?'clickable':'span').'"></span>';
		if($obj!='empty')
			$out .= '<span class="map-tile-obj" style="
					background: url(./core/style/default/img/map/obj_'.$obj.'.png) no-repeat;"></span>';
		if($veh!='empty'){
			$title = 'MSS';
			$out .= '<span class="map-tile-veh" style="
					background: url(./core/style/default/img/map/veh_'.$veh.'.png) no-repeat;"></span>';
		}
		//$out .= '<span class="map-tile-info">'.$data->  X .'|'. $data->  Y.'</span>';

		if($title!=null)
			$out .= '<span class="map-tile-title">'.$title.'</span>';

		$out .= '</div>';
		return $out;
	}

	function WriteTileToDatabase($gamid, $tilex,$tiley, $fieldtype, $content){
		global $MyDB;
		$maptileid = $this ->GetMapTileID($gamid, $tilex, $tiley);
		$MyDB -> CallSQL("INSERT INTO `world_map_tile` (`maptileid`, `gamid`, `tilex`, `tiley`, `field`, `content`) VALUES ('".$maptileid."', '".$gamid."', '".$tilex."', '".$tiley."', '".$fieldtype."', '".$content."');");
	}

	public function GenerateNewMap($gamid){
		global $MyDB;

		include_once('/GameFactory.php');
		include_once('/perlin.php');


		$Perlin = new Perlin();
		//$Perlin -> seed = 239;

		$width = $this -> staticdata_world_map[$gamid]['width'];
		$length = $this -> staticdata_world_map[$gamid]['length'];

		debugEcho('GenerateMap');

		$worldmap_DataFieldsReverse = array ();
		$worldmap_DataContentReverse = array ();
		$data = array();

		for ($i = 0; $i< count($this->worldmap_DataFields); $i++){
			$worldmap_DataFieldsReverse[$this->worldmap_DataFields[$i]] = $i;
		}
		for ($i = 0; $i< count($this->worldmap_DataContent); $i++){
			$worldmap_DataContentReverse[$this->worldmap_DataContent[$i]] = $i;
		}

		$halfhight = $length/2;
		for ($y = 0; $y< $length; $y++){
			for ($x = 0; $x< $width; $x++){
				$id = $y*$width+$x;

				$v = 1-abs($y-$halfhight)/$halfhight;

				//$height = (1+ $Perlin->noise($x, $y,0,24))/2;
				$height = $v + 2.5*$Perlin->noise($x, $y,0,24);
				$height /= 2.5;
				$height += 0.4*$Perlin->noise($x, $y,0,8);

				$field = $worldmap_DataFieldsReverse['grass'];
				if($height <= 0.4)
					$field = $worldmap_DataFieldsReverse['ocean'];

				$content= 0;
				$data[$id] = new MapTileData($id, $field, $content,$x,$y);
			}
		}
		$cityList =array();
		$offshoreList =array();

		//Generate Content
		for ($y = 0; $y< $length; $y++){
			for ($x = 0; $x< $width; $x++){
				$id = $y*$width+$x;

				if($data[$id]->FieldType == $worldmap_DataFieldsReverse['grass']){
					if(rand(0,10)==1 && $this->CanPlaceCity($cityList, $x, $y)){
						$cityList[count($cityList)] = ['x'=>$x,'y'=>$y];
						$data[$id]->Content = $worldmap_DataContentReverse['city'];
					}
				}
				else if($data[$id]->FieldType == $worldmap_DataFieldsReverse['ocean']){
					if(rand(0,100)==1 && $this->CanPlaceOffshore($data, $x, $y)){
						$offshoreList[count($offshoreList)] = ['x'=>$x,'y'=>$y];
						$data[$id]->Content = $worldmap_DataContentReverse['offshore'];
					}
				}
			}
		}

		//Clear Map
		$MyDB -> CallSQL("DELETE FROM `world_map_tile` WHERE `world_map_tile`.`gamid` = '".$gamid."'");
		$this -> DeleteAllCities($MyDB, $gamid);
		//Write Map
		$tile;
		$citylist = array();
		for ($i = 0; $i< count($data); $i++){
			$tile = $data[$i];
			$this->WriteTileToDatabase($gamid, $tile->X, $tile->Y, $tile->FieldType, $tile->Content);
			switch ($tile->Content){
				case $worldmap_DataContentReverse['city']:{
					$name = $this -> GetNewCityName($citylist);
					$citylist[count($citylist)] = $name;
					$residents = rand(100000,5000000);
					CreateCity($MyDB, $gamid, $this->GetMapTileID($gamid, $tile->X, $tile->Y), $name, $residents); break;
				}
			}
		}
		debugEcho('Map Generated');
	}

	function GetNewCityName($citylist){
		global $citynames;
		for ($i = 0; $i< 100; $i++){
			$name = $citynames[rand(0,count($citynames)-1)];
			$found = true;
			for ($c = 0; $c < count($citylist); $c++){
				if($name == $citylist[$c]){
					$found=false;
					break;
				}
			}
			if($found)
				return $name;
		}
		return 'Newcity';
	}

	function DeleteAllCities($MyDB, $gamid){
		$MyDB -> CallSQL("DELETE FROM `world_city` WHERE `world_city`.`maptileid` >= '".($this->GetMapTileID($gamid, 0,0))."' AND `world_city`.`maptileid` < '".($this->GetMapTileID($gamid+1, 0,0))."'");
	}

	function CanPlaceCity($cityList, $tilex, $tiley){
		$r = 3;
		$a = $this->ConvertOffsetToCube($tilex, $tiley);
		for ($i = 0; $i< count($cityList); $i++){
			$b = $this->ConvertOffsetToCube($cityList[$i]['x'],$cityList[$i]['y']);
			if($this->GetDistance($a, $b)<= $r)
				return false;
		}
		return true;
	}

	function CanPlaceOffshore($data, $tilex, $tiley){
		return true;
	}
}
