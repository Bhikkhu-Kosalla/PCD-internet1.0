<?php

require_once "../path.php";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>PCD Encyclopedia</title>
	<style>

	</style>
	<script src="../public/js/jquery.js"></script>
	<script src="../public/js/comm.js"></script>
	<script src="../term/term.js"></script>
	<script src="../term/note.js"></script>
	<script src="wiki.js"></script>
	
	<style>
	.term_link,.term_link_new{
		color: blue;
		padding-left: 2px;
		padding-right: 2px;
	}
	.term_link_new{
		color:red;
	}
	#search_result{
    position: absolute;
    background: wheat;
    max-width: 95%;
    width: 24em;
	}
	chapter{
		color: blue;
		text-decoration: none;
		cursor: pointer;
	}
	chapter:hover{
		color: blue;
		text-decoration: underline;
	}
	.fun_block {
    color: var(--tool-color);
    width: 95%;
	margin-top:3em;
	margin-left:auto;
	margin-right:auto;
    max-width: 30em;
    margin-bottom: 20px;
    box-shadow: 2px 2px 10px 2px var(--shadow-color);
    border-radius: 8px;
}
	.wiki_body{
		align-items: center;
	}
	</style>
	<body class="wiki_body" onload="wiki_index_init()">
		<div class="fun_block">
			<h2>wikipali Encyclopedia<br>圣典百科</h2>
			<div id="wiki_search" style="width:100%;">
				<div><input id="wiki_search_input" type="input" placeholder="search" style="width:100%" onkeyup="wiki_search_keyup(event,this)"/></div>
				<div id="search_result">
				</div>
			</div>
			<div id="wiki_contents">
				<ul>
					<li>人人皆可编辑</li>
					<li>引用圣典原文</li>
					<li>内容翔实可信</li>
					<li>新建我的词条</li>
					<li>新手指南</li>
				</ul>
			</div>
		</div>
		

	</body>
</html>