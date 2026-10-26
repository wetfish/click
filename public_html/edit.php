<?php

require('include.php');

if($_POST)
{
    $Page = replace_input($_GET['page'] ?? '');
	$Time = time();
	$IP = db_escape($_SERVER['REMOTE_ADDR']);
	$Data = stripslashes($_POST['data'] ?? '');
	$Data = db_escape($Data);
	$ID = db_escape($_GET['id'] ?? '');

	if(in_array(trim(str_replace('&nbsp;', ' ', $Data)), array("", "<br>", "<br />", "Care to write something?")))
	{
		# Useless content.
	}
	else
	{
		db_query("Update `Pages` set `Data`='$Data', `Time`='$Time', `IP`='$IP' where `ID`='$ID'");
	}
}


?>
