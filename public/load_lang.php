<?php
require_once '../public/config.php';

/*
load language file
范例
echo $_local->gui->welcome;
*/
if(isset($_GET["language"])){
	$currLanguage=$_GET["language"];
	$_COOKIE["language"]=$currLanguage;
}
else{
	if(isset($_COOKIE["language"])){
		$currLanguage=$_COOKIE["language"];
	}
	else{
		$currLanguage="en";
		$_COOKIE["language"]=$currLanguage;
	}
}
if(file_exists($_dir_lang.$currLanguage.".json")){
	$_local=json_decode(file_get_contents($_dir_lang.$currLanguage.".json"));
}
else{
	$_local=json_decode(file_get_contents($_dir_lang."default.json"));
}
?>