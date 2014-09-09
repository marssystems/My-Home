<?php
//error_reporting(0);

$dbhost = 'mysql.sectorsieteg.com';
$dbuser = 'gusmena';
$dbpass = 'sector7g';
$dbname = 'gmsoft';

//Connect
$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if (mysqli_connect_errno()) {
	printf("mysqli connection failed: ", mysqli_connect_error());
	exit();
}
?>