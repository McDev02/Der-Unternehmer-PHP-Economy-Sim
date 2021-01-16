<?php
include_once('./core/start.php');
include_once('./core/data/staticdata.php');

if(!isset($_SESSION['accid']) && $_SESSION['accid']!=0){
	session_unset();
	redirect('frontpage.php', 0);
}

include_once('./core/GameFactory.php');

$starttime = microtime();

$MyDB = new DBHelper($dbhost, $dbuser, $dbpass, $dbname);
$MyDB -> OpenDB();

$StaticData = new StaticData();

$page = '';
$action = '';
if(isset($_REQUEST['page']))
	$page = validate_input($_REQUEST['page']);
if(isset($_REQUEST['action']))
	$action = validate_input($_REQUEST['action']);

//Document
echo createlayout_header('');
echo createlayout_start();
echo CreateCMSMenu($MyDB, $page);


switch($page){
	case 'vehicles': showPageVehicles($MyDB, $action); break;
}

echo createlayout_footer();
echo createlayout_end();
//End

//Methods
function showPageVehicles($MyDB, $action){

	if($action == 'storeNew'){
	$vehicleData = $MyDB-> QuerySQL("INSERT INTO `data_vehicles` (`uid`, `name`, `travel`, `type`, `tank`, `consume`, `speed`, `storage`, `seats`, `pollution`) VALUES
			(NULL,
			'".$_POST['name']."',
			'".$_POST['travel']."',
			'".$_POST['type']."',
			'".$_POST['tank']."',
			'".$_POST['consume']."',
			'".$_POST['speed']."',
			'".$_POST['storage']."',
			'".$_POST['seats']."',
			'".$_POST['pollution']."');");
	redirect("cms.php?page=vehicles");
	}
	else{
		echo ShowVehicleList($MyDB);
	}
}

function ShowVehicleList($MyDB){
	$vehicleData = $MyDB-> QuerySQL("SELECT * FROM `data_vehicles`");

	$content = '';
	$out ='';

	$content .= '<form action="cms.php?page=vehicles&action=storeNew" method="post">';
	$content .= '<input type="submit" value="Create new Vehicle">';
	$content .= '<table>';
	$content .= '
	<tr>
    <th>ID</th>
    <th>name</th>
    <th>Travel</th>
    <th>Type</th>
    <th>Tank</th>
    <th>Consumption</th>
    <th>Speed</th>
    <th>Storage</th>
    <th>Seats</th>
    <th>Pollution</th>
 	</tr>
	';
	$tdTag = ' style="padding:0 4px;"';
	$inputTag = ' style="width:100%;"';
	$content .= '
		<tr>
	    <td'.$tdTag.'>New</td>
	    <td'.$tdTag.'><input '.$inputTag.' type="text" name="name"></td>
	    <td style="padding:0 4px; min-width:70px;">'.GetTravelDropdown('travel', $inputTag).'</td>
	    <td'.$tdTag.'><input '.$inputTag.' type="text" name="type"></td>
	    <td'.$tdTag.'><input '.$inputTag.' type="text" name="tank"></td>
	    <td'.$tdTag.'><input '.$inputTag.' type="text" name="consume"></td>
	    <td'.$tdTag.'><input '.$inputTag.' type="text" name="speed"></td>
	    <td'.$tdTag.'><input '.$inputTag.' type="text" name="storage"></td>
	    <td'.$tdTag.'><input '.$inputTag.' type="text" name="seats"></td>
	    <td'.$tdTag.'><input '.$inputTag.' type="text" name="pollution"></td>
			</tr>
		';
	if($vehicleData!=null){
		for($i=0;$i<count($vehicleData);$i++){
			$data = $vehicleData[$i];
			$content .= '
			<tr>
		    <td>'.$data['uid'].'</td>
		    <td>'.$data['name'].'</td>
		    <td>'.$data['travel'].'</td>
		    <td>'.$data['type'].'</td>
		    <td>'.$data['tank'].'</td>
		    <td>'.$data['consume'].'</td>
		    <td>'.$data['speed'].'</td>
		    <td>'.$data['storage'].'</td>
		    <td>'.$data['seats'].'</td>
		    <td>'.$data['pollution'].'</td>
		 	</tr>
			';
		}
	}
	$content .= '</table>';
	$content .= '</form>';

	$out = showWindow($content, 'Vehicles');
	return $out;
}

function GetTravelDropdown($name, $tag=''){
	return '<select '.$tag.' name="'.$name.'">
      <option value="land">Land</option>
      <option value="sea">See</option>
      <option value="air">Luft</option>
    </select>';
}
