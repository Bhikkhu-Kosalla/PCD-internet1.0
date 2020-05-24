<?php
/*
获取我的文档 文件列表
*/
include "../path.php";
include "../public/_pdo.php";

if(isset($_GET["keyword"])){
	$keyword = $_GET["keyword"];
}
else{
	$keyword = "";
}
if(isset($_GET["status"])){
	$status = $_GET["status"];
}
else{
	$status = "all";
}
if(isset($_GET["currLanguage"])){
	$currLanguage = $_GET["currLanguage"];
}

if(isset($_GET["orderby"])){
	$order_by = $_GET["orderby"];
}
else{
	$order_by = "accese_time";
}
if(isset($_GET["order"])){
	$order = $_GET["order"];
}
else{
	$order = "desc";
}



if($_COOKIE["uid"]){
	$uid=$_COOKIE["uid"];
}
else{
	echo "尚未登录";
	exit;
}


PDO_Connect("sqlite:"._FILE_DB_FILEINDEX_);

	switch($order_by){
		case "accese_time":
		case "create_time":
		case "modify_time":
			$time_show=$order_by;
			break;
		default:
			$time_show="accese_time";
			break;
	}
	
	switch($status){
		case "all":
			$query = "select * from fileindex where userid='$uid' AND title like '%$keyword%' and status>0 order by $order_by $order";
			break;
		case "share":
			$query = "select * from fileindex where userid='$uid' AND  title like '%$keyword%' and status>0 and share=1 order by $order_by $order";
			break;
		case "recycle":
			$query = "select * from fileindex where userid='$uid' AND  title like '%$keyword%' and status=0 order by $order_by $order";
			break;	
	}
	$Fetch = PDO_FetchAll($query);
	echo json_encode($Fetch, JSON_UNESCAPED_UNICODE);
	
	/*
	$iFetch=count($Fetch);
	if($iFetch>0){
		date_default_timezone_set("Asia/Colombo");
		for($i=0;$i<$iFetch;$i++){
			//$tag
		}
		if($keyword!=""){
			echo $module_gui_str['editor_palicannon']['1011'].": <strong>$keyword</strong> ";
		}
		echo $module_gui_str['editor']['1109']."$iFetch".$module_gui_str['editor']['1107'];
		echo "<input id='file_count' type='hidden' value='{$iFetch}' />";
		echo "<table style='width:100%;'><tr>";
		echo "<td width='70%'>".$module_gui_str['editor_project']['1027']."</td>";
		echo "<td width='15%' >".$module_gui_str['editor']['1112']."</td>";
		echo "<td width='15%' style='text-align: center;'>".$module_gui_str['editor']['1113']."</td>";
		echo "</tr></table>";			
		for($i=0;$i<$iFetch;$i++){
			echo "<div class='file_list_shell'>";
			echo "<table style='width:100%;'><tr>";
			$filename=$Fetch[$i]["file_name"];
			$title=$Fetch[$i]["title"];
			$link="<a href='editor.php?op=open&fileid=".$Fetch[$i]["id"]."&filename=$filename"."&language=$currLanguage' target='_blank'>";
			if($Fetch[$i]["share"]==1){
				$share="[share]";
			}
			else if(!empty($Fetch[$i]["parent_id"])){
				$share="[▼]";
			}
			else{
				$share="";
			}
			echo "<td width='70%'>";
			echo "<input id='file_check_$i' type='checkbox' class='file_select_checkbox' />";
			echo "{$share}{$link}<input type='hidden' id='file_name_{$i}' value='$filename'>$title";
			echo "</a></td>";
			if($Fetch[$i]["file_size"]<102){
				$str_size=$Fetch[$i]["file_size"]."B";
			}
			else if($Fetch[$i]["file_size"]<(1024*902)){
				$str_size=sprintf("%.0f KB",$Fetch[$i]["file_size"]/1024);
			}
			else{
				$str_size=sprintf("%.1f MB",$Fetch[$i]["file_size"]/(1024*1024));
			}
			echo "<td width='15%'>$str_size</td>";
			
			echo "<td width='15%' style='text-align: right;'>".date("Y-m-d h:i:sa", $Fetch[$i][$time_show])."</td>";
			echo "</tr></table>";
			echo "</div>";
		}
	}
	else{
		echo $module_gui_str['editor']['1108'];
	}
	*/
?>