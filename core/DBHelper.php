<?php
class DBHelper{
	private $dbhost;
	private $dbuser;
	private $dbpass;
	private $dbname;
	private $mysqli;

	function __construct($dbhost, $dbuser, $dbpass, $dbname){
      $this->dbhost=$dbhost;
      $this->dbuser=$dbuser;
      $this->dbpass=$dbpass;
      $this->dbname=$dbname;
    }

	public function OpenDB(){
		$this->mysqli = new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
		if ($this->mysqli->connect_errno) {
		echo "Failed to connect to Database: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error;
		}
	}

	public function CloseDB(){
	$this->mysqli->Close();
	}

	public function GetLastID(){
		return $this->mysqli->insert_id;
	}

	public function CallSQL($sql){
	$res = $this->mysqli->query($sql);
	if($this->mysqli->error != '')
	{
		die('<tt>'.$sql.'</tt><br />caused an error:<br />'.$this->mysqli->error);
	}
	return $res;
	}

	public function QuerySQL($sql){
	$res = $this->mysqli->query($sql);
	if($this->mysqli->error != '')
	{
		die('<tt>'.$sql.'</tt><br />caused an error:<br />'.$this->mysqli->error);
	}
	if($res->num_rows<=0)
		return "";

	$res->data_seek(0);
	$fetchedArray;
	$i=0;
	while ($row = $res->fetch_assoc()) {
    $fetchedArray[$i] = $row;
	$i++;
	}
	return $fetchedArray;
	}

	public function QuerySQLSingle($sql){
	$res = $this->mysqli->query($sql);
	if($this->mysqli->error != '')
	{
		die('<tt>'.$sql.'</tt><br />caused an error:<br />'.$this->mysqli->error);
	}
	if($res == "")
		return false;
	if($res->num_rows<=0)
		return "";

	$res->data_seek(0);
	return $row = $res->fetch_assoc();
	}
}

?>
