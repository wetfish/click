<?php

require('include.php');

$Page = db_escape(replace_input($_GET['page'] ?? ''));

$Query = db_query("Select `Data` from `Pages` where `Page`='$Page'");
list($Data) = mysqli_fetch_array($Query);

if($Data)
{
    echo replace_output($Data);
}
else
{
	echo "There's nothing here yet.";
}

?>
