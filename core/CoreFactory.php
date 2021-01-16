<?php

function CreateCoreAccount($MyDB, $uname, $hashpass, $email){
	$sql = "INSERT INTO `core_accounts` (`accid`, `uname`, `upass`, `email`) VALUES (NULL, '".$uname."', '".$hashpass."', '".$email."');";
	$MyDB -> CallSQL($sql);
	$accountID = $MyDB -> GetLastID();
	return $accountID;
}

function CreateGameUser($MyDB, $accid, $gamid){
	$sql = "INSERT INTO `game_users` (`usrid`, `accid`, `gamid`, `lastlogin`, `tutstate`) VALUES (NULL, '".$accid."', '".$gamid."', 'CURRENT_TIMESTAMP', '1');";
	$MyDB -> CallSQL($sql);
	$userID = $MyDB -> GetLastID();
	return $userID;
}
