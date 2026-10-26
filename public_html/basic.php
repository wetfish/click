<?php

session_start();
require('include.php');

$ID = db_escape(replace_input($_GET['id'] ?? ''));

$Query = db_query("Select `Page`,`Time`,`Data`,`Deleted`
						from `Pages`
						where `ID`='$ID'
						limit 1");

list($Page, $Time, $Data, $Deleted) = mysqli_fetch_array($Query);

if($Time and (!$Deleted))
{
    echo replace_output($Data);
}

?>
