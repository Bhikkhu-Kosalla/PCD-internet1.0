<?php

require_once "../path.php";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>PCD Sync</title>

		<script src="../public/js/jquery.js"></script>
		<script src="../public/js/comm.js"></script>
	</head>	
	
	
	<body class="sync_body" onload="sync_index_init()">
		<div class="fun_block">
			<h2>SERVER ADDRESS</h2>
			<div id="wiki_search" style="width:100%;">
				<div>
					<input id="wiki_search_input" type="input" placeholder="server" style="width:100%" />
					<button onclick="sync_start()">Start</button>
				</div>

			</div>
			<div id="sync_result">

			</div>
		</div>
		

	</body>
</html>