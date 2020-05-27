<?php 
require "./_pdo.php";
require "../public/config.php";


if(isset($_POST["filename"])){
	$filename = $_POST["filename"];
}
else{
	$filename = "";
	echo "no file name";
	return;
}
if(isset($_POST["fileid"])){
	$fileid = $_POST["fileid"];
}
else{
	$fileid = "";
	echo "no file id";
	return;
}
if(isset($_POST["xmldata"])){
	$xmldata = $_POST["xmldata"];
}
else{
	$xmldata = "desc";
	echo "no file data";
	return;
}
$purefilename = basename($filename);

if($_COOKIE["uid"]){
	$uid=$_COOKIE["uid"];
}
else{
	echo "尚未登录";
	exit;
}	

$dir= $dir_user_base.$_COOKIE["userid"].$dir_mydocument."/";

//save data file
$save_filename=$dir.$purefilename;
$myfile = fopen($save_filename, "w") or die("Unable to open file!");
echo $save_filename;
fwrite($myfile, $xmldata);
fclose($myfile);


$db_file = "{$dir_user_base}fileindex.db";//數據庫遷移至user文件夾，此為非公共數據
PDO_Connect("sqlite:$db_file");
	$time=time();
	$filesize=filesize($save_filename);
if($fileid==0){//插入新的数据
	$filesize=filesize($filename);
	$query="INSERT INTO fileindex ('id','userid','doc_id','book','paragraph','file_name','title','tag','create_time','modify_time','accese_time','file_size') 
						VALUES (NULL,'$uid',?,?,?,'$purefilename','$purefilename',?,'$time','$time','$time','$filesize')";

//$query="INSERT INTO fileindex ('id','userid','doc_id','book','paragraph','file_name','title','tag','create_time','modify_time','accese_time','file_size') 
//						VALUES (NULL,'$purefilename','$purefilename','$time','$time','$time','$filesize')";
    $stmt = @PDO_Execute($query);
    if (!$stmt || ($stmt && $stmt->errorCode() != 0)) {
        $error = PDO_ErrorInfo();
		echo "error".$error[2]."<br>";
    }	

}
else{

	$query="UPDATE fileindex SET modify_time='$time' where file_name=".$PDO->quote($purefilename);
    $stmt = @PDO_Execute($query);
    if (!$stmt || ($stmt && $stmt->errorCode() != 0)) {
        $error = PDO_ErrorInfo();
		echo "error".$error[2]."<br>";
    }	
}

		/*$query="INSERT INTO fileindex ('id','userid','doc_id','book','paragraph','file_name','title','tag','create_time','modify_time','accese_time','file_size') 
						VALUES (NULL,?,?,?,?,?,?,?,?,?,?,?)";
		$stmt = $PDO->prepare($query);
		$newData=array($uid,GUIDv4(),?,?,$purefilename,$purefilename,?,time(),time(),time(),$filesize);
		$stmt->execute($newData);
		if (!$stmt || ($stmt && $stmt->errorCode() != 0)) {
			$error = PDO_ErrorInfo();
			echo "error - $error[2] <br>";
		}
		else{
			echo "updata 1 recorders.";*/



echo("Successful");
?>

