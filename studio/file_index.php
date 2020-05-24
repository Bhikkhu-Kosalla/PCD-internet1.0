<?php
require 'checklogin.inc';
require "../public/config.php";
require "../public/_pdo.php";

if(isset($_POST["op"])){
	$op=$_POST["op"];
}
if(isset($_POST["id"])){
	$id=$_POST["id"];
}
if(isset($_POST["filename"])){
	$filename=$_POST["filename"];
}
if(isset($_POST["field"])){
	$field=$_POST["field"];
}
if(isset($_POST["value"])){
	$value=$_POST["value"];
}
if($_COOKIE["uid"]){
	$uid=$_COOKIE["uid"];
}
else{
	echo "尚未登录";
	exit;
}

//$db_file = $_file_db_fileindex;
$db_file = "{$dir_user_base}fileindex.db";
PDO_Connect("sqlite:$db_file");

switch($op){
	case "list":
	break;
	case "get";
	break;
	case "getall";
		//
		$time=time();
		$query="select * from fileindex where userid='$uid' AND  file_name='$filename'";
		$Fetch = PDO_FetchAll($query);
		$iFetch=count($Fetch);
		if($iFetch>0){
			echo json_encode($Fetch[0], JSON_UNESCAPED_UNICODE);
		}

	break;
	case "set";
		//修改文件索引数据库
		if($field=="accese_time"){
			$value=time();
		}
		$query="UPDATE fileindex SET $field='$value' where userid='$uid' AND   file_name='$filename'";
		$stmt = @PDO_Execute($query);
		if (!$stmt || ($stmt && $stmt->errorCode() != 0)) {
			$error = PDO_ErrorInfo();
			echo "{\"error\":\"".$error[2]."\"}";
		}
		else{
			echo "{\"error\":\"0\"}";
		}
	break;
	case "share":
		//修改文件索引数据库
		if(isset($_POST["file"])){
			if(isset($_POST["share"])){
				$share=$_POST["share"];
			}
			else{
				$share=0;
			}
			$fileList=$_POST["file"];
			$aFileList=str_getcsv($fileList);
			if(count($aFileList)>0){
				$strFileList="(";
				foreach($aFileList as $file) {
				  $strFileList.="'{$file}',";
				}
				$strFileList=mb_substr($strFileList, 0,mb_strlen($strFileList,"UTF-8")-1,"UTF-8");
				$strFileList.=")";
				$query="UPDATE fileindex SET share='$share' where userid='$uid' AND   id in $strFileList";
				$stmt = @PDO_Execute($query);
				if (!$stmt || ($stmt && $stmt->errorCode() != 0)) {
					$error = PDO_ErrorInfo();
					echo "error:{$error[2]}";
				}
				else{
					echo "ok";
				}			
			}		
		}	
		break;	
	
	case "delete"://移到回收站
	{
		if(isset($_POST["file"])){
			$fileList=$_POST["file"];
			$aFileList=str_getcsv($fileList);
			if(count($aFileList)>0){
				$strFileList="(";
				foreach($aFileList as $file) {
				  $strFileList.="'{$file}',";
				}
				$strFileList=mb_substr($strFileList, 0,mb_strlen($strFileList,"UTF-8")-1,"UTF-8");
				$strFileList.=")";
				
				$query="UPDATE fileindex SET status='0',share='0' where userid='$uid' AND  id in $strFileList";
				$stmt = @PDO_Execute($query);
				if (!$stmt || ($stmt && $stmt->errorCode() != 0)) {
					$error = PDO_ErrorInfo();
					echo "error:{$error[2]}";
				}
				else{
					echo "ok";
				}			
			}		
		}	
		break;	
	}
	case "restore"://从回收站中恢复
	{
		if(isset($_POST["file"])){
			$fileList=$_POST["file"];
			$aFileList=str_getcsv($fileList);
			if(count($aFileList)>0){
				$strFileList="(";
				foreach($aFileList as $file) {
				  $strFileList.="'{$file}',";
				}
				$strFileList=mb_substr($strFileList, 0,mb_strlen($strFileList,"UTF-8")-1,"UTF-8");
				$strFileList.=")";
				
				$query="UPDATE fileindex SET status='1' where userid='$uid' AND  id in $strFileList";
				$stmt = @PDO_Execute($query);
				if (!$stmt || ($stmt && $stmt->errorCode() != 0)) {
					$error = PDO_ErrorInfo();
					echo "error:{$error[2]}";
				}
				else{
					echo "ok";
				}			
			}		
		}	
		break;	
	}	
	case "remove":
		//彻底删除文件
		if(isset($_POST["file"])){
			$fileList=$_POST["file"];
			$aFileList=str_getcsv($fileList);
			if(count($aFileList)>0){
				$strFileList="(";
				//删除文件
				foreach($aFileList as $file) {
					if(!unlink($dir.$file)){
						 echo("Error deleting $file");
					}
				  $strFileList.="'{$file}',";
				}
				$strFileList=mb_substr($strFileList, 0,mb_strlen($strFileList,"UTF-8")-1,"UTF-8");
				$strFileList.=")";
				//删除记录
				$query="DELETE FROM fileindex WHERE userid='$uid' AND   id in $strFileList";
				$stmt = @PDO_Execute($query);
				if (!$stmt || ($stmt && $stmt->errorCode() != 0)) {
					$error = PDO_ErrorInfo();
					echo "error:{$error[2]}";
				}
				else{
					echo "删除".count($aFileList)."个文件。";
				}			
			}		
		}	
		break;
	case "remove_all":
		//	清空回收站
	break;
}

?>