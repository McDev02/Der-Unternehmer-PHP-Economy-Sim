<?php
include_once('./core/start.php');
include_once('./core/data/staticdata.php');

if(!isset($_SESSION['accid'])){
	session_unset();
	redirect('frontpage.php', 0);
}

include_once('./core/GameFactory.php');

$starttime = microtime();

$MyDB = new DBHelper($dbhost, $dbuser, $dbpass, $dbname);
$MyDB -> OpenDB();

$StaticData = new StaticData();

//Start
$action = '';
$page = '';
$pid = '';
if(isset($_GET['action']))
	$action = validate_input($_GET['action']);
if(isset($_REQUEST['page']))
	$page = validate_input($_REQUEST['page']);
if(isset($_REQUEST['pid']))
	$pid = validate_input($_REQUEST['pid']);

	if($page=='')
		$page = 'company';

if(!isset($_SESSION['usr_comid']))
{
	$ownID = GetOwnerID($_SESSION['usrid'], 'user');
	$query = $MyDB->QuerySQLSingle("SELECT * FROM `game_company_owners` WHERE `ownid` = '".$ownID."'");
	if($query!=null){
		$query = $MyDB->QuerySQLSingle("SELECT * FROM `game_company` WHERE `comid` = '".$query['comid']."'");
		if($query!=null){
			$_SESSION['usr_comid'] = $query['comid'];
			$_SESSION['usr_company_name'] = $query['name']." ".$query['firm'];
		}
	}
}

//Document
echo createlayout_start();
echo CreateMainMenu($MyDB, $page);

if($page=='logout')
	$page = 'logout';
else if(!checkIfUserReady())
	$page = 'setupgame';

switch($page){
	case 'setupgame': showPageTutorial($MyDB, $pid, $action); break;
	case 'overview': showPageOverview($action); break;
	case 'map': showPageMap($action); break;
	case 'company': showPageCompany($action); break;
	case 'market': showPageMarket($action); break;
	case 'profile': showPageProfile($action); break;
	case 'logout': showPageLogout($action); break;
}

echo createlayout_footer();
echo createlayout_end();
//End

//Methods
function showPageOverview($action){
}
function showPageMap($action){
	include('./core/pages/pageMap.php');
	$mapPage = new MapPage();
	echo $mapPage -> ShowPage();
}
function showPageCompany($action){
	include('./core/pages/pageCompany.php');
	$companyPage = new CompanyPage();
	echo $companyPage -> ShowPage($action);
}
function showPageMarket($action){
}
function showPageProfile($action){
}

function showPageLogout($action){
session_unset();
//echo '<p>You are logged out</p><a href="frontpage.php"><p>Continue</a> Redirect missing.</p>';
redirect('frontpage.php', 0);
}
function checkIfUserReady(){
	if($_SESSION['usr_tutstate']<1) return true;
	return false;
}

function showPageTutorial($MyDB, $pid, $action){
	$user = $MyDB -> QuerySQLSingle("SELECT * FROM `game_users` WHERE `accid` = '".$_SESSION['accid']."'");
	if($user == '')
		die("ERROR User not found in database.");
		if($user['tutstate']<1) return true;

	$tut_id = $user['tutstate'];
	if($pid!='' && $pid>0)
		$tut_id = $pid<($user['tutstate']+1)?$pid:$user['tutstate'];

	include('./core/tutorial.php');

	if($tut_id == 1) $tut_id = showTut1($user, $action);
	if($tut_id == 2) $tut_id = showTut2($user, $action);
	if($tut_id == 3) $tut_id = showTut3($user, $action);
	if($tut_id == 4) $tut_id = showTut4($user, $action);

}
?>
