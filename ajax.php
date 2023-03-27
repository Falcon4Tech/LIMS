<?php
header('Content-type: application/json');

	function __autoload($class) {
		include_once("../class/class.{$class}.php");
		if (file_exists("../class/style.{$class}.php")) include("../class/style.{$class}.php");
	}

	include_once("globals.php");

	$sql_user = "lims_".$_SERVER['REMOTE_USER'];
	$sql_pass = "dB".$_SERVER['REMOTE_USER']."!#";

	$mysqli = new log_mysqli(host, $sql_user, $sql_pass, db);
	$mysqli->debug(true, "lims.ajax");
	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}
?>