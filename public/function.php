<?php
require_once __DIR__.'/casesuf.inc';
require_once __DIR__.'/config.php';
require_once __DIR__.'/../path.php';
$_book_index=null; //书的列表

/*
$mode:
0 
*/
function getPaliWordBase($word,$mode=0){
	global $case;
	//去除尾查
	$newWord=array();
	for ($row = 0; $row < count($case); $row++) {
		$len=mb_strlen($case[$row][1],"UTF-8");
		$end=mb_substr($word, 0-$len,NULL,"UTF-8");
		if($end==$case[$row][1]){
			$base=mb_substr($word, 0,mb_strlen($word,"UTF-8")-$len,"UTF-8").$case[$row][0];
			if($base!=$word){
				$type=".n.";
				$gramma=$case[$row][2];
				$parts="{$base}+{$case[$row][1]}";
				if(isset($newWord[$base])){
					array_push($newWord[$base],array("type"=>$type,
										 "gramma"=>$gramma,
										 "parts"=>$parts
										 ));
				}
				else{
					$newWord[$base] = array(array("type"=>$type,
										 "gramma"=>$gramma,
										 "parts"=>$parts
										 ));
				}
			}
		}
	}
	return($newWord);
}

function _load_book_index(){
	global $_book_index,$_dir_lang,$currLanguage,$_dir_book_index;
	if(file_exists($_dir_lang.$currLanguage.".json")){
		$_book_index=json_decode(file_get_contents($_dir_book_index."a/".$currLanguage.".json"));
	}
	else{
		$_book_index=json_decode(file_get_contents($_dir_book_index."a/default.json"));
	}
	//print_r($_book_index);
}

function _get_book_info($index){
	global $_book_index;
	foreach($_book_index as $book){
		if($book->row==$index){
			return($book);
		}
	}
	return(null);
}

function _get_book_path($index){
	global $_book_index;
}

function _get_para_path($book,$paragraph){
	
	$dictFileName=_FILE_DB_PALITEXT_;
	PDO_Connect("sqlite:{$dictFileName}");	
	$path="";
	$parent = $paragraph;
	$deep=0;
	$sFirstParentTitle="";
	//循环查找父标题 得到整条路径
	while($parent>-1){
		$query = "select * from pali_text where \"book\" = '{$book}' and \"paragraph\" = '{$parent}' limit 0,1";
		$FetParent = PDO_FetchAll($query);
		$toc="<chapter book=\"{$book}\" para=\"{$parent}\">{$FetParent[0]["toc"]}</chapter>";
		
		if($path==""){
			$path="({$paragraph})";
		}
		else{
			$path="{$toc}>{$path}";
		}
		if($sFirstParentTitle==""){
			$sFirstParentTitle = $FetParent[0]["toc"];
		}						
		$parent = $FetParent[0]["parent"];
		$deep++;
		if($deep>5){
			break;
		}
	}
	
	return("——".$path);
}
?>