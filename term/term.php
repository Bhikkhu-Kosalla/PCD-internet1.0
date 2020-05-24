<?php
//查询term字典
require_once "../path.php";
require_once "../public/_pdo.php";

//is login
if(isset($_COOKIE["username"]) && !empty($_COOKIE["username"])){
	$username = $_COOKIE["username"];
}
else{
	$username = "";
}

if(isset($_GET["op"])){
	$op=$_GET["op"];
}
else if(isset($_POST["op"])){
	$op=$_POST["op"];
}
if(isset($_GET["word"])){
	$word=mb_strtolower($_GET["word"],'UTF-8');
	$org_word=$word;
}

if(isset($_GET["guid"])){
	$_guid=$_GET["guid"];
}

global $PDO;
PDO_Connect("sqlite:"._FILE_DB_TERM_);
switch($op){
	case "pre"://预查询
	{
		$query = "select word,meaning from term where \"eword\" like ".$PDO->quote($word.'%')." OR \"word\" like ".$PDO->quote($word.'%')." group by word limit 0,10";
		$Fetch = PDO_FetchAll($query);
		if(count($Fetch)<5){
			$query = "select word,meaning from term where \"eword\" like ".$PDO->quote('%'.$word.'%')." OR \"word\" like ".$PDO->quote('%'.$word.'%')." group by word limit 0,10";
			$Fetch2 = PDO_FetchAll($query);
			//去掉重复的
			foreach($Fetch2 as $onerow){
				$found=false;
				foreach($Fetch as $oldArray){
					if($onerow["word"]==$oldArray["word"]){
						$found=true;
						break;
					}
				}
				if($found==false){
					array_push($Fetch,$onerow);
				}
			}
			if(count($Fetch)<8){
				$query = "select word,meaning from term where \"meaning\" like ".$PDO->quote($word.'%')." OR \"other_meaning\" like ".$PDO->quote($word.'%')." group by word limit 0,10";
				$Fetch3 = PDO_FetchAll($query);
				
				$Fetch = array_merge($Fetch,$Fetch3);
				if(count($Fetch)<8){
					$query = "select word,meaning from term where \"meaning\" like ".$PDO->quote('%'.$word.'%')." OR \"other_meaning\" like ".$PDO->quote('%'.$word.'%')." group by word limit 0,10";
					$Fetch4 = PDO_FetchAll($query);
					//去掉重复的
					foreach($Fetch4 as $onerow){
						$found=false;
						foreach($Fetch as $oldArray){
							if($onerow["word"]==$oldArray["word"]){
								$found=true;
								break;
							}
						}
						if($found==false){
							array_push($Fetch,$onerow);
						}
					}
				}
			}
		}
		echo json_encode($Fetch, JSON_UNESCAPED_UNICODE);
		break;
	}
	case "my":
	{
		$query = "select guid,word,meaning,other_meaning from term  where owner= ".$PDO->quote($username);
		$Fetch = PDO_FetchAll($query);
		$iFetch=count($Fetch);
		if($iFetch>0){
			echo json_encode($Fetch, JSON_UNESCAPED_UNICODE);
		}
		break;
	}
	case "allpali":
	{
		$query = "select word from term  where 1 group by word";
		$Fetch = PDO_FetchAll($query);
		$iFetch=count($Fetch);
		if($iFetch>0){
			echo json_encode($Fetch, JSON_UNESCAPED_UNICODE);
		}
		break;
	}
	case "allmean":
	{
		$query = "select meaning from term  where \"word\" = ".$PDO->quote($word)." group by meaning";
		$Fetch = PDO_FetchAll($query);
		foreach($Fetch as $one){
			echo "<a>".$one["meaning"]."</a> ";
		}
		//echo json_encode($Fetch, JSON_UNESCAPED_UNICODE);
		break;
	}
	case "load_id":
	{
		if(isset($_GET["id"])){
			$id=$_GET["id"];
			$query = "select * from term  where \"guid\" = ".$PDO->quote($id);
			$Fetch = PDO_FetchAll($query);
			echo json_encode($Fetch, JSON_UNESCAPED_UNICODE);			
		}
		else{
			echo "{}";
		}
		break;
	}
	case "search":
	{
		//查本人数据
		echo "<div></div>";//My Term
		$query = "select * from term  where \"word\" = ".$PDO->quote($word)." AND \"owner\"= ".$PDO->quote($username)." limit 0,30";
		echo $query;
		$Fetch = PDO_FetchAll($query);
		$iFetch=count($Fetch);
		$count_return+=$iFetch;
		if($iFetch>0){
			for($i=0;$i<$iFetch;$i++){
				$mean=$Fetch[$i]["meaning"];
				$guid=$Fetch[$i]["guid"];
				$dict_list[$guid]=$Fetch[$i]["owner"];
				echo "<div class='dict_word'>";
				echo "<a name='ref_dict_$guid'></a>";
				echo"<div class='dict'>$word</div>";
				echo "<div id='term_dict_my_$guid'>";
				echo "<div class='mean'>".$mean."</div>";
				echo "<div class='other_mean'>".$Fetch[$i]["other_meaning"]."</div>";
				echo "<div class='term_note' status=0>".$Fetch[$i]["note"]."</div>";
				echo "</div>";
				//编辑词条表单
				echo "<div id='term_dict_my_edit_$guid' style='display:none'>";
				echo "<input type='hidden' id='term_edit_word_$guid' value='$word' />";
				echo "<div class='mean'><input type='input' id='term_edit_mean_$guid'  placeholder='".$module_gui_str['editor']['1010']."'value='$mean' /></div>";//'意思'
				echo "<div class='other_mean'><input type='input' id='term_edit_mean2_$guid'  placeholder=".$module_gui_str['editor']['1120']." value='".$Fetch[$i]["other_meaning"]."' /></div>";//'备选意思（可选项）'
				echo "<div class='note'><textarea  id='term_edit_note_$guid'  placeholder='".$module_gui_str['editor']['1043']."'>".$Fetch[$i]["note"]."</textarea></div>";//'注解'
				echo "</div>";
				echo "<div id='term_edit_btn1_$guid'>";
				echo "<button onclick=\"term_apply('$guid')\">".$module_gui_str['editor_layout']['1003']."</button>";//Apply
				echo "<button onclick=\"term_edit('$guid')\">".$module_gui_str['editor']['1002']."</button>";//Edit
				echo "</div>";
				echo "<div id='term_edit_btn2_$guid'  style='display:none'>";
				echo "<button onclick=\"term_data_esc_edit('$guid')\">".$module_gui_str['editor']['1028']."</button>";//Cancel
				echo "<button onclick=\"term_data_save('$guid')\">".$module_gui_str['editor']['1017']."</button>";//保存
				echo "</div>";
				echo "</div>";
			}
		}
		//新建词条
		echo "<div class='dict_word'>";
		echo "<div class='dict'>".$module_gui_str['editor']['1121']."</div>";//New Techinc Term
		echo "<div class='mean'>巴利拼写：<input type='input' placeholder=".$module_gui_str['editor']['1119']." id='term_new_word' value='{$word}' /></div>";//'拼写'
		echo "<div class='mean'>意思<input type='input' placeholder='".$module_gui_str["editor"]["1010"]."' id='term_new_mean'/></div>";//'意思'
		echo "<div class='other_mean'>第二意思：<input type='input'  placeholder='".$module_gui_str['editor']['1120']."' id='term_new_mean2'/></div>";//'备选意思（可选项）'
		echo "<div class='tag'>分类：<input type='input'  placeholder='".$module_gui_str['tools']['1005']."' id='term_new_tag'/></div>";//'标签'
		echo "<div class='note'>注解：<textarea width='100%' height='3em'  placeholder='".$module_gui_str['editor']['1043']."' id='term_new_note'></textarea></div>";//'注解'
		echo "<button onclick=\"term_data_save('')\">".$module_gui_str['editor']['1017']."</button>";//保存
		echo "</div>";

		
		//查他人数据
		$query = "select * from term  where \"word\" = ".$PDO->quote($word)."AND \"owner\" <> ".$PDO->quote($username)." limit 0,30";
		
		$Fetch = PDO_FetchAll($query);
		$iFetch=count($Fetch);
		$count_return+=$iFetch;
		if($iFetch>0){
			for($i=0;$i<$iFetch;$i++){
				$mean=$Fetch[$i]["meaning"];
				$guid=$Fetch[$i]["guid"];
				$dict_list[$guid]=$Fetch[$i]["owner"];
				echo "<div class='dict_word'>";
				echo "<a name='ref_dict_$guid'></a>";
				echo"<div class='dict'>".$Fetch[$i]["owner"]."</div>";
				echo "<div class='mean'>".$mean."</div>";
				echo "<div class='other_mean'>".$Fetch[$i]["other_meaning"]."</div>";
				echo "<div class='term_note'>".$Fetch[$i]["note"]."</div>";
				echo "<button onclick=\"term_data_copy_to_me($guid)\">".$module_gui_str['editor']['1123']."</button>";//复制
				echo "</div>";
			}
		}

	
		//查内容
		if($count_return<2){
			$word1=$org_word;
			$wordInMean="%$org_word%";
			echo $module_gui_str['editor']['1124']."：$org_word<br />";
			$query = "select * from term  where \"meaning\" like ".$PDO->quote($word)." limit 0,30";
			$Fetch = PDO_FetchAll($query);
			$iFetch=count($Fetch);
			$count_return+=$iFetch;
			if($iFetch>0){
				for($i=0;$i<$iFetch;$i++){
					$mean=$Fetch[$i]["meaning"];
					$pos=mb_stripos($mean,$word,0,"UTF-8");
					if($pos){
						if($pos>20){
							$start=$pos-20;
						}
						else{
							$start=0;
						}
						$newmean=mb_substr($mean,$start,100,"UTF-8");
					}
					else{
						$newmean=$mean;
					}
					$pos=mb_stripos($newmean,$word1,0,"UTF-8");
					$head=mb_substr($newmean,0,$pos,"UTF-8");
					$mid=mb_substr($newmean,$pos,mb_strlen($word1,"UTF-8"),"UTF-8");
					$end=mb_substr($newmean,$pos+mb_strlen($word1,"UTF-8"),NULL,"UTF-8");
					$heigh_light_mean="$head<hl>$mid</hl>$end";
					$outXml = "<div class='dict_word'>";
					$outXml = $outXml."<div class='dict'>".$Fetch[$i]["owner"]."</div>";					
					$outXml = $outXml."<div class='pali'>".$Fetch[$i]["word"]."</div>";
					$outXml = $outXml."<div class='mean'>".$heigh_light_mean."</div>";
					$outXml = $outXml."<div class='note'>{$Fetch[$i]["note"]}</div>";
					$outXml = $outXml."</div>";
					echo $outXml;
				}
			}		
		}
		//查内容结束

		echo "<div id='dictlist'>";
		foreach($dict_list as $x=>$x_value) {
		  echo "<a href='#ref_dict_$x'>$x_value</a><br/>";
		}
		echo "</div>";
		
		break;
	}
	case "save":
	{
		if($_GET["guid"]!=""){
			$mean=$_GET["mean"];
			$query="UPDATE term SET meaning='$mean' ,
									other_meaning='".$_GET["mean2"]."' ,
									note='".$_GET["note"]."' 
							where guid='".$_GET["guid"]."'";
		}
		else{
			$newGuid=GUIDv4();
			$newGuid=str_replace("-","",$newGuid);
			$word=$_GET["word"];
			$worden=pali2english($word);
			$mean=$_GET["mean"];
			$mean2=$_GET["mean2"];
			$note=$_GET["note"];
			$tag=$_GET["tag"];
			$time=time();
			$query="INSERT INTO term VALUES (NULL, 
											'$newGuid', 
											'$word', 
											'$worden', 
											'$mean', 
											'$mean2', 
											'$note', 
											'$tag', 
											'$time', 
											'$username', 
											'1',
											'zh',
											'0',
											'0',
											'0')";		
		}
			$stmt = @PDO_Execute($query);
			$respond=array("status"=>0,"message"=>"");
			if (!$stmt || ($stmt && $stmt->errorCode() != 0)) {
				$error = PDO_ErrorInfo();
				$respond['status']=1;
				$respond['message']=$error[2].$query;
			}
			else{
				$respond['status']=0;
				$respond['message']=$word;
			}		
			echo json_encode($respond, JSON_UNESCAPED_UNICODE);
		break;
	}
	case "copy"://拷贝到我的字典
	{
		$query = "select * from term  where \"guid\" = ".$PDO->quote($_GET["wordid"]);
		
		$Fetch = PDO_FetchAll($query);
		$iFetch=count($Fetch);
		if($iFetch>0){
			/* 开始一个事务，关闭自动提交 */
			$PDO->beginTransaction();
			$query="INSERT INTO term ('id','guid','word','word_en','meaning','other_meaning','note','tag','create_time','owner','hit') VALUES (null,?,?,?,?,?,?,?,".time().",'$username',1)";
			$stmt = $PDO->prepare($query);
			{
			$stmt->execute(array(GUIDv4(false),
								$Fetch[0]["word"],
								$Fetch[0]["word_en"],
								$Fetch[0]["meaning"],
								$Fetch[0]["other_meaning"],
								$Fetch[0]["note"],
								$Fetch[0]["tag"],
								));
			}
			/* 提交更改 */
			$PDO->commit();
			if (!$stmt || ($stmt && $stmt->errorCode() != 0)) {
				$error = PDO_ErrorInfo();
				echo "error - $error[2] <br>";
			}
			else{
				echo "updata ok.";
			}			
		}
		break;
	}
	case "extract":
	{
		if(isset($_POST["words"])){
			$words=$_POST["words"];
		}
		if(isset($_POST["authors"])){
			$authors=str_getcsv($_POST["authors"]);
		}		
		$query = "select * from term  where \"word\" in {$words}  limit 0,1000";
		$Fetch = PDO_FetchAll($query);
		$iFetch=count($Fetch);
		if($iFetch>0){
			echo json_encode($Fetch, JSON_UNESCAPED_UNICODE);
		}	
		else{
			echo "[]";
		}			
		break;
	}
	case "sync":
	{
		$time=$_GET["time"];
		$query = "select guid,modify_time from term  where receive_time>'{$time}' limit 0,1000";
		$Fetch = PDO_FetchAll($query);
		$iFetch=count($Fetch);
		if($iFetch>0){
			echo json_encode($Fetch, JSON_UNESCAPED_UNICODE);
		}	
		else{
			echo "[]";
		}
		break;
	}
	case "get":
	{
		if(isset($guid)){
			$query = "select * from term  where \"guid\" = '{$guid}'";
		}
		else if(isset($word)){
			$query = "select * from term  where \"word\" = '{$word}'";
		}
		else{
			echo "[]";
			return;
		}
		$Fetch = PDO_FetchAll($query);
		$iFetch=count($Fetch);
		if($iFetch>0){
			echo json_encode($Fetch, JSON_UNESCAPED_UNICODE);
		}	
		else{
			echo "[]";
		}		
		break;
	}
	
}


?>