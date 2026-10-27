<?php

session_start();
require('include.php');

if(can_moderate())
{
	$ID = stripslashes($_GET['id'] ?? '');
	$ID = db_escape(filter_var($ID, FILTER_SANITIZE_SPECIAL_CHARS));

	db_query("Update `Pages` set `Deleted`='1' where `ID`='$ID'");

	echo "Post deleted.";
}

?>
