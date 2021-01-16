<?php

function GetOwnerID($id, $owntype){
	$sufix = 0;
	if($owntype=='user') $sufix = 1;
	else if($owntype=='comp') $sufix = 2;
	return $id.$sufix;
}

//Creation
function CreateCompany($MyDB, $gamid, $ctyid, $name, $firm, $isic, $allshares=10000){
	$compid = '';
	$sql = "INSERT INTO `game_company` (`comid`, `gamid`, `name`, `firm`, `isic`, `allshares`) VALUES (NULL, '".$gamid."', '".$name."', '".$firm."', '".$isic."', '".$allshares."')";
	$MyDB -> CallSQL($sql);
	$compid = $MyDB -> GetLastID();
	$sql = "INSERT INTO `game_subsidiary` (`comid`, `ctyid`) VALUES ('".$compid."', '".$ctyid."')";
	$MyDB -> CallSQL($sql);
	return $compid;
}

function CreateBusiness($MyDB, $ctyid, $comid, $size, $name, $type){
	$compid = '';
	if(!isset($type)||$type=="") {
		print("<p>Type not set</p>");
		return "";
	}
	$bustype = $MyDB -> QuerySQLSingle("SELECT `busid` FROM `data_businesstypes` WHERE `type` = '".$type."'");
	if(!isset($bustype)) print("<p>Wrong Business type</p>");

	$sql = "INSERT INTO `game_business` (`busid`, `comid`, `ctyid`, `bustype`, `name`, `type`, `size`, `builddate`) VALUES (NULL, '".$comid."', '".$ctyid."', '".$bustype['busid']."',  '".$name."','".$type."', '".$size."', CURRENT_TIMESTAMP);";
	$MyDB -> CallSQL($sql);
	$busid = $MyDB -> GetLastID();
	return $busid;
}

function SetCompanyOwnership($MyDB, $compid, $ownerid, $ownertype, $shares){
	$ownership = $MyDB -> QuerySQLSingle("SELECT * FROM `game_company_owners` WHERE `comid` = '".$compid."' AND `ownid` = '".$ownerid."'");
	if($ownership == ''){
		//Create New
		$MyDB -> CallSQL("INSERT INTO `game_company_owners` (`comid`, `ownid`, `shares`) VALUES ('".$compid."', '".$ownerid."', '".$shares."');");
	}
	else{
		//Update
		$MyDB -> CallSQL("UPDATE `game_company_owners` SET `shares` = '".$shares."' WHERE `game_company_owners`.`comid` = '".$compid."' AND `game_company_owners`.`ownid` = '".$ownerid."';");
	}
}

function CreateCity($MyDB, $gamid, $maptileid, $name, $residents){
	$ctyid = '';

	$sql = "INSERT INTO `world_city` (`ctyid`, `maptileid`, `gamid`, `name`, `residents`) VALUES (NULL, '".$maptileid."', '".$gamid."', '".$name."', '".$residents."');";
	$MyDB -> CallSQL($sql);
	$ctyid = $MyDB -> GetLastID();
	if($ctyid==null)
		die("<p>Error: City was not correctly written for ".($maptileid).'</p>'.
				'<p>'.$sql.'</p>');
	return $ctyid;
}

//Delete

function DeleteCompany($MyDB, $comid){
	$company = $MyDB -> QuerySQLSingle("SELECT * FROM `game_company` WHERE `comid` = '".$comid."'");
		if($company!=null){
			print("Account found");
			DeleteCompanyOwners($MyDB, $comid);
			DeleteSubsidiariy($MyDB, $comid);
			DeleteBusinesses($MyDB, $comid);

			$ownid = GetOwnerID($comid, 'comp');
			DeleteBankAccounts($MyDB, $ownid);

			$MyDB -> CallSQL("DELETE FROM `game_company` WHERE `comid` = '".$comid."'");
		}else
			print("No Account found");
}
function DeleteCompanyOwners($MyDB, $comid){
				$MyDB -> CallSQL("DELETE FROM `game_company_owners` WHERE `comid` = '".$comid."'");
}
function DeleteSubsidiariy($MyDB, $comid){
				$MyDB -> CallSQL("DELETE FROM `game_subsidiary` WHERE `comid` = '".$comid."'");
}
function DeleteBusinesses($MyDB, $comid){
				$MyDB -> CallSQL("DELETE FROM `game_business` WHERE `comid` = '".$comid."'");
}
function DeleteBankAccounts($MyDB, $ownid){
				$MyDB -> CallSQL("DELETE FROM `game_bank_account` WHERE `ownid` = '".$ownid."'");
}
