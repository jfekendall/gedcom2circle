<?php
$username = "";
$password = "";
$hostname = "Server_Name";
$db_name = "Database_Name";
$dbh = mysql_connect($hostname, $username, $password)
	or die("Unable to connect to MySQL");
    $selected = mysql_select_db("$db_name",$dbh)
	or die("Could not select $db_name");
?>
