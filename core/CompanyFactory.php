<?php
include_once('GameFactory.php');

function GenerateCompanyContent($MyDB, $comid, $ctyid){
	$sql = "SELECT * FROM `game_company` WHERE `comid` = '".$comid."'";
	$company = $MyDB-> QuerySQLSingle($sql);

	if($company!=null)
		switch ($company['isic']) {
			case 'a':
				SetupIndustry_A($MyDB, $ctyid, $comid);
				break;
			case 'b':
				SetupIndustry_B($MyDB, $ctyid, $comid);
				break;
			case 'c':
				SetupIndustry_C($MyDB, $ctyid, $comid);
				break;
			case 'd':
				SetupIndustry_D($MyDB, $ctyid, $comid);
				break;
			case 'e':
				SetupIndustry_E($MyDB, $ctyid, $comid);
				break;
			case 'm':
				SetupIndustry_M($MyDB, $ctyid, $comid);
				break;

			default:
				SetupDefaultIndustry($MyDB, $ctyid, $comid);
				break;
		}
}

//Default
function SetupDefaultIndustry($MyDB, $ctyid, $comid){
	CreateBusiness($MyDB, $ctyid, $comid, '100', 'B&uuml;ro', 'operation');
}

//Landwirtschaft
function SetupIndustry_A($MyDB, $ctyid, $comid){
SetupDefaultIndustry($MyDB, $ctyid, $comid);
}

//Bergbau
function SetupIndustry_B($MyDB, $ctyid, $comid){
SetupDefaultIndustry($MyDB, $ctyid, $comid);

}

//Bergbau
function SetupIndustry_C($MyDB, $ctyid, $comid){
SetupDefaultIndustry($MyDB, $ctyid, $comid);

}

//Bergbau
function SetupIndustry_D($MyDB, $ctyid, $comid){
SetupDefaultIndustry($MyDB, $ctyid, $comid);

}

//Bergbau
function SetupIndustry_E($MyDB, $ctyid, $comid){
SetupDefaultIndustry($MyDB, $ctyid, $comid);

}

//Bergbau
function SetupIndustry_F($MyDB, $ctyid, $comid){
SetupDefaultIndustry($MyDB, $ctyid, $comid);

}

//Bergbau
function SetupIndustry_G($MyDB, $ctyid, $comid){
SetupDefaultIndustry($MyDB, $ctyid, $comid);

}

//Bergbau
function SetupIndustry_H($MyDB, $ctyid, $comid){
SetupDefaultIndustry($MyDB, $ctyid, $comid);

}

//Bergbau
function SetupIndustry_I($MyDB, $ctyid, $comid){
SetupDefaultIndustry($MyDB, $ctyid, $comid);

}

//Bergbau
function SetupIndustry_J($MyDB, $ctyid, $comid){
SetupDefaultIndustry($MyDB, $ctyid, $comid);

}

//Bergbau
function SetupIndustry_K($MyDB, $ctyid, $comid){
SetupDefaultIndustry($MyDB, $ctyid, $comid);

}

//Bergbau
function SetupIndustry_L($MyDB, $ctyid, $comid){
SetupDefaultIndustry($MyDB, $ctyid, $comid);

}

//Individual
function SetupIndustry_M($MyDB, $ctyid, $comid){
	CreateBusiness($MyDB, $ctyid, $comid, '30', 'Heimb&uuml;ro', 'operation');

}

//Bergbau
function SetupIndustry_N($MyDB, $ctyid, $comid){
SetupDefaultIndustry($MyDB, $ctyid, $comid);

}

//Bergbau
function SetupIndustry_O($MyDB, $ctyid, $comid){
SetupDefaultIndustry($MyDB, $ctyid, $comid);

}

//Bergbau
function SetupIndustry_P($MyDB, $ctyid, $comid){
SetupDefaultIndustry($MyDB, $ctyid, $comid);

}

//Bergbau
function SetupIndustry_Q($MyDB, $ctyid, $comid){
SetupDefaultIndustry($MyDB, $ctyid, $comid);

}

//Bergbau
function SetupIndustry_R($MyDB, $ctyid, $comid){
SetupDefaultIndustry($MyDB, $ctyid, $comid);

}

//Bergbau
function SetupIndustry_S($MyDB, $ctyid, $comid){
SetupDefaultIndustry($MyDB, $ctyid, $comid);

}
