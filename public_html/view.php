<?php

session_start();
require('include.php');

?>

<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script src='/node_modules/wetfish-basic/dist/basic.js'></script>
		<script src='/click.js'></script>

		<style>
			#click-header { font-family: Tahoma,Helvetica,Sans-serif; font-size:10pt; }
			#click-submit { display:none }
			#click-submit.editing { display:inline; animation: click-fade-in 0.4s; }
			@keyframes click-fade-in { from { opacity:0; } to { opacity:1; } }
		</style>

		<script>
			function Render()
			{
				var HTML = $('#click-content').el[0].innerHTML;

				HTML = HTML.replace(/&amp;/g, '&');
				HTML = HTML.replace(/&lt;/g, '<');
				HTML = HTML.replace(/&gt;/g, '>');

				$('#click-content').html(HTML);
			}

			function UnRender()
			{
				var HTML = $('#click-content').el[0].innerHTML;

				HTML = HTML.replace(/&/g, '&amp;');
				HTML = HTML.replace(/</g, '&lt;');
				HTML = HTML.replace(/>/g, '&gt;');

				$('#click-content').html(HTML);
			}

			function Submit()
			{
				$('#click-content').style({'background-image': "url(/super.gif)"});

				Post('edit.php?id=<?php echo rawurlencode($_GET['id'] ?? ''); ?>', {data: $('#click-content').el[0].innerHTML }, function()
				{
					// /me farts
					$('#click-content').style({'background-image': "none"});
				});
			}

			$(document).ready(function()
			{
				$('#click-edit').on('click', function(event)
				{
					$('#click-content').attr('contenteditable', 'true');
					$('#click-submit').addClass('editing');

				});

				$('#click-submit').on('click', function(event)
				{
					Submit();
				});
			});
		</script>
	</head>

	<body>
		<div id='click-header'>
			<a href='/'>Click Home</a> |
			<a href='/recent.php'>Recent Clicks</a> |
			<a href='javascript:Render()' style='font-weight:bold'>Render HTML</a> |
			<a href='javascript:UnRender()' style='font-weight:bold'>UnRender HTML</a>

<?php

$ID = replace_input($_GET['id'] ?? '');

$Query = db_query("Select `Page`,`Time`,`Data`,`Deleted`
						from `Pages`
						where `ID`='" . db_escape($ID) . "'
						limit 1");

list($Page, $Time, $Data, $Deleted) = mysqli_fetch_array($Query);

if($Time and (!$Deleted or !empty($_SESSION['Click']['User'])))
{
	$Time = date("G:i:s T", $Time);

	echo "<!-- Start Click Nav --> <div style='float:right' id='click-nav'>";

	if(in_array($_SESSION['Click']['User'] ?? null, array('click', 'rachel', 'wetfish', 'guthbrandr')))
	{
		echo "	<a href='#Submit' id='click-submit'>Submit</a>
				&emsp;
				<a href='#Edit' id='click-edit'>Edit</a>
				&emsp;
				<a href='delete.php?id=$ID'>Delete</a>
			";
	}
	else
	{
		echo "&emsp;<a href='/login.php' id='click-login'>Login</a>";
	}

    $Data = replace_output($Data);
	echo "&emsp;<a href='/$Page' id='click-thread'>View Thread</a>";
	echo "<!-- Close Click Nav --> </div>";
	echo "<!-- Close Click Header --> <hr /></div>";
	echo "<div id='click-content'>$Data</div>";
}

?>
	<hr />

	<center>
		<iframe id='leader-friend'
				src='https://ads.wetfish.net/friendship/leader.html'
				style='width:750px; height:115px; border:0; outline:0; overflow:hidden;'
				scrolling="no"></iframe>
	</center>

	</body>
</html>
