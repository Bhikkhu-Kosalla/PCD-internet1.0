<?php
//查询term字典
include "../../public/_pdo.php";
include "../public.inc";
require '../../vendor/autoloader.php';
require '../../path.php';

$username = "";

$op=$_POST["op"];

if(isset($_POST["uid"])){
	$UID=$_POST["uid"];
}
else{
	if(isset($_COOKIE["username"]) && !empty($_COOKIE["username"])){
		$UID = $_COOKIE["uid"];
	}
}


if(isset($_POST["book"])){
	$_book=$_POST["book"];
}

if(isset($_POST["para"])){
	$_para=$_POST["para"];
}

if(isset($_POST["begin"])){
	$_begin=$_POST["begin"];
}

if(isset($_POST["end"])){
	$_end=$_POST["end"];
}

if(isset($_POST["style"])){
	$_style=$_POST["style"];
}

if(isset($_POST["text"])){
	$_text=$_POST["text"];
}

if(isset($_POST["author"])){
	$_author=$_POST["author"];
}

if(isset($_POST["lang"])){
	$_lang=$_POST["lang"];
}

if(isset($_POST["stage"])){
	$_stage=$_POST["stage"];
}

if(isset($_POST["id"])){
	$_id=$_POST["id"];
}

if(isset($_POST["block_id"])){
	$_block_id=$_POST["block_id"];
}

if(isset($_POST["parent_id"])){
	$_parent_id=$_POST["parent_id"];
}

global $PDO;
$db_file=_FILE_DB_SENTENCE_;
PDO_Connect("sqlite:$db_file");

switch($op){
	case "save":
	/*
	$client = new \GuzzleHttp\Client();
	$parm = ['form_params'=>['op'=>$op,
			'id'=>$_id,
			'block_id'=>$_block_id,
			'parent_id'=>$_parent_id,
			'uid'=>$UID,
			'book'=>$_book,
			'paragraph'=>$_para,
			'begin'=>$_begin,
			'end'=>$_end,
			'style'=>$_style,
			'text'=>$_text,
			'stage'=>$_stage,
			'author'=>$_author,
			'lang'=>$_lang
			]];
	
	$response = $client->request('POST', 'http://10.10.1.100/wikipalipro/app/studio/sent/sent.php',$parm);

	$status = $response->getStatusCode();
	$head_type = $response->getHeaderLine('content-type');
	//echo $response->getBody();
		*/
		if($_id==0){
			$query="select * from sentence where 
						book='{$_book}' and 
						paragraph='{$_para}' and 
						begin='{$_begin}' and 
						end='{$_end}'  and 
						style='{$_style}'  and 
						author='{$_author}'  and 
						editor='{$UID}'  and 
						language='{$_lang}'
						";
			$Fetch = PDO_FetchAll($query);
			$iFetch=count($Fetch);
			if($iFetch>0){
				$_id = $Fetch[0]["id"];
				$_parent_id = $Fetch[0]["parent_id"];
			}	
		}
		$recodeId=$_id;
		$time=time();
		if($_id>0){//修改旧记录
			$query="UPDATE sentence SET text='$_text' ,
									time='{$time}'  
							where id=".$PDO->quote($_id);
		}
		else{//新建记录
		
			$query="INSERT INTO sentence VALUES (NULL, 
											'$_parent_id', 
											'$_book', 
											'$_para', 
											'$_begin', 
											'$_end',
											'$_style', 
											'$_author', 
											'$UID',
											'$_text',
											'$_lang',
											'1',
											'1',
											'$time',
											'1')";		
		}
		$stmt = @PDO_Execute($query);
		$respond=array("status"=>0,"message"=>"");
		if (!$stmt || ($stmt && $stmt->errorCode() != 0)) {
			$error = PDO_ErrorInfo();
			$respond['status']=1;
			$respond['error']=$error[2];
		}
		else{
			$respond['status']=0;
			$respond['error']="";
			if($recodeId==0){
				$respond['id']=$PDO->lastInsertId();
			}
			else{
				$respond['id']=$recodeId;
			}
			
			$respond['block_id']=$_block_id;
			$respond['parent_id']=$_parent_id;
			$respond['begin']=$_begin;
			$respond['end']=$_end;
		}
		echo json_encode($respond, JSON_UNESCAPED_UNICODE);
		break;

}

?>