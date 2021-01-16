<?php

include(dirname(__DIR__).'../core/config.php');
include(dirname(__DIR__).'../core/DBHelper.php');

include(dirname(__DIR__).'../core/utilities.php');
include(dirname(__DIR__).'../core/MapHandler.php');

include(dirname(__DIR__).'../core/data/staticdata.php');

$MyDB = new DBHelper($dbhost, $dbuser, $dbpass, $dbname);
$MyDB -> OpenDB();

$StaticData = new StaticData();

$mapHandler = new MapHandler();
$mapHandler -> GenerateNewMap('1');