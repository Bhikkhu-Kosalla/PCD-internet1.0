<?php
//查询参考字典
require_once '../public/casesuf.inc';
require_once '../public/union.inc';
require_once "../public/config.php";
require_once "../public/_pdo.php";
require_once "../public/load_lang.php";//语言文件
require_once "../public/function.php";

_load_book_index();


$op=$_GET["op"];
$word=mb_strtolower($_GET["word"],'UTF-8');
$org_word=$word;

$count_return=0;
$dict_list=array();


global $PDO;
function isExsit($word){
global $PDO;
		$query = "select count(*) as co from dict where \"word\" = ".$PDO->quote($word);
		$row=PDO_FetchOne($query);
		/*
		$Fetch = PDO_FetchAll($query);
		$iFetch=count($Fetch);
		$count_return+=$iFetch;
		if($iFetch>0){
		
		}
		*/
		if($row[0]==0){
			return false;
		}
		else{
			return true;
		}
}

function myfunction($v1,$v2)
{
return $v1 . "+" . $v2;
}

function mySplit($strWord){
	//echo("<br>".$strWord."<br>");
	$doubleword="kkggccjjṭṭḍḍttddppbb";
	$len=mb_strlen($strWord,"UTF-8");
	if($len>5){
		for($i=$len-1;$i>3;$i--){
			$str1=mb_substr($strWord,0,$i,"UTF-8");
			$str2=mb_substr($strWord,$i,NULL,"UTF-8");
			//echo "$str1 + $str2 = ";
			if(isExsit($str1)){
				//echo "match";
				$left2=mb_substr($str2,0,2,"UTF-8");
				if(mb_strpos($doubleword,$left2,0,"UTF-8")!==FALSE){
					$str2=mb_substr($str2,1,NULL,"UTF-8");
				}
				return array($str1,$str2);
			}
			else{
				$str1=$str1."a";
				if(isExsit($str1)){
					//echo "match";
					$left2=mb_substr($str2,0,2,"UTF-8");
					if(mb_strpos($doubleword,$left2,0,"UTF-8")!==FALSE){
						$str2=mb_substr($str2,1,NULL,"UTF-8");
					}
					return array($str1,$str2);
				}
			}
		}
		//not found
		if(mb_substr($strWord,0,1,"UTF-8")=="ā"){
			$strWord='a'.mb_substr($strWord,1,NULL,"UTF-8");
			for($i=$len-1;$i>3;$i--){
				$str1=mb_substr($strWord,0,$i,"UTF-8");
				$str2=mb_substr($strWord,$i,NULL,"UTF-8");
				//echo "$str1 + $str2 = ";
				if(isExsit($str1)){
					//echo "match";
					$left2=mb_substr($str2,0,2,"UTF-8");
					if(mb_strpos($doubleword,$left2,0,"UTF-8")!==FALSE){
						$str2=mb_substr($str2,1,NULL,"UTF-8");
					}
					return array($str1,$str2);
				}
				else{
					$str1=$str1."a";
					if(isExsit($str1)){
						//echo "match";
						$left2=mb_substr($str2,0,2,"UTF-8");
						if(mb_strpos($doubleword,$left2,0,"UTF-8")!==FALSE){
							$str2=mb_substr($str2,1,NULL,"UTF-8");
						}
						return array($str1,$str2);
					}
				}
			}			
		}
		//not found
		if(mb_substr($strWord,0,1,"UTF-8")=="e"){
			$strWord='i'.mb_substr($strWord,1,NULL,"UTF-8");
			for($i=$len-1;$i>3;$i--){
				$str1=mb_substr($strWord,0,$i,"UTF-8");
				$str2=mb_substr($strWord,$i,NULL,"UTF-8");
				//echo "$str1 + $str2 = ";
				if(isExsit($str1)){
					//echo "match";
					$left2=mb_substr($str2,0,2,"UTF-8");
					if(mb_strpos($doubleword,$left2,0,"UTF-8")!==FALSE){
						$str2=mb_substr($str2,1,NULL,"UTF-8");
					}
					return array($str1,$str2);
				}
				else{
					$str1=$str1."a";
					if(isExsit($str1)){
						//echo "match";
						$left2=mb_substr($str2,0,2,"UTF-8");
						if(mb_strpos($doubleword,$left2,0,"UTF-8")!==FALSE){
							$str2=mb_substr($str2,1,NULL,"UTF-8");
						}
						return array($str1,$str2);
					}
				}
			}			
		}		
	}
	return(FALSE);
}


switch($op){
	case "pre"://预查询
		//$dictFileName=$dir_dict_system."index.db3";
		$dictFileName=$dir_dict_system."ref1.db";
		PDO_Connect("sqlite:$dictFileName");
		echo "<div>";
		//$query = "select word,count from wordindex where \"word_en\" like ".$PDO->quote($word.'%')." OR \"word\" like ".$PDO->quote($word.'%')." order by len limit 0,30";
		$query = "select word,count from dict where \"eword\" like ".$PDO->quote($word.'%')." OR \"word\" like ".$PDO->quote($word.'%')."  limit 0,100";

		$Fetch = PDO_FetchAll($query);
		$iFetch=count($Fetch);
		if($iFetch>0){
			for($i=0;$i<$iFetch;$i++){
				$word=$Fetch[$i]["word"];
				$count=$Fetch[$i]["count"];
				echo  "<div class='dict_word_list'>";
				echo  "<a onclick='dict_pre_word_click(\"$word\")'>$word-$count</a>";
				echo  "</div>";
			}
		}
		echo "</div>";
		break;
	case "search":
		echo "<div id=\"dict_ref\">";	
		
		PDO_Connect("sqlite:$_file_db_ref");
		//直接查询
		$query = "select dict.dict_id,dict.mean,info.shortname from dict LEFT JOIN info ON dict.dict_id = info.id where \"word\" = ".$PDO->quote($word)." limit 0,100";
		
		$Fetch = PDO_FetchAll($query);
		$iFetch=count($Fetch);
		$count_return+=$iFetch;
		if($iFetch>0){
			for($i=0;$i<$iFetch;$i++){
				$mean=$Fetch[$i]["mean"];
				$dictid=$Fetch[$i]["dict_id"];
				$dict_list[$dictid]=$Fetch[$i]["shortname"];
				$outXml = "<div class='dict_word'>";
				$outXml = $outXml."<a name='ref_dict_$dictid'></a>";
				$outXml = $outXml."<div class='dict'>".$Fetch[$i]["shortname"]."</div>";
				$outXml = $outXml."<div class='mean'>".$mean."</div>";
				$outXml = $outXml."</div>";
				echo $outXml;
			}
		}
		//去除尾查
		$newWord=array();
		for ($row = 0; $row < count($case); $row++) {
			$len=mb_strlen($case[$row][1],"UTF-8");
			$end=mb_substr($word, 0-$len,NULL,"UTF-8");
			if($end==$case[$row][1]){
				$base=mb_substr($word, 0,mb_strlen($word,"UTF-8")-$len,"UTF-8").$case[$row][0];
				if($base!=$word){
					if(isset($newWord[$base])){
						$newWord[$base] .= "<br />".$case[$row][2];
					}
					else{
						$newWord[$base] = $case[$row][2];
					}
				}
			}
		}

		if(count($newWord)>0){
			foreach($newWord as $x=>$x_value) {
				$query = "select dict.dict_id,dict.mean,info.shortname from dict LEFT JOIN info ON dict.dict_id = info.id where \"word\" = ".$PDO->quote($x)." limit 0,30";
				$Fetch = PDO_FetchAll($query);
				$iFetch=count($Fetch);
				$count_return+=$iFetch;
				if($iFetch>0){
					echo $x . ":<div class='dict_find_gramma'>" . $x_value . "</div>";
					for($i=0;$i<$iFetch;$i++){
						$mean=$Fetch[$i]["mean"];
						$dictid=$Fetch[$i]["dict_id"];
						$dict_list[$dictid]=$Fetch[$i]["shortname"];
						echo "<div class='dict_word'>";
						echo "<a name='ref_dict_$dictid'></a>";
						echo "<div class='dict'>".$Fetch[$i]["shortname"]."</div>";
						echo "<div class='mean'>".$mean."</div>";
						echo "</div>";
					}
				}			  
			}
		}
		//去除尾查结束
		
		//模糊查
		//模糊查结束
		//查连读词
		if($count_return<2){
			echo "Junction:<br />";
			$newWord=array();
			for ($row = 0; $row < count($un); $row++) {
				$len=mb_strlen($un[$row][1],"UTF-8");
				$end=mb_substr($word, 0-$len,NULL,"UTF-8");
				if($end==$un[$row][1]){
					$base=mb_substr($word, 0,mb_strlen($word,"UTF-8")-$len,"UTF-8").$un[$row][0];
						$arr_un=explode("+",$base);
						foreach ($arr_un as $oneword)
						{
						  echo "<a onclick='dict_pre_word_click(\"$oneword\")'>$oneword</a> + ";
						}
						echo "<br />";
				}
			}		
		}
		
		//查内容
		if($count_return<2){
			$word1=$org_word;
			$wordInMean="%$org_word%";
			echo "include $org_word:<br />";
			$query = "select dict.dict_id,dict.word,dict.mean,info.shortname from dict LEFT JOIN info ON dict.dict_id = info.id where \"mean\" like ".$PDO->quote($wordInMean)." limit 0,30";
			$Fetch = PDO_FetchAll($query);
			$iFetch=count($Fetch);
			$count_return+=$iFetch;
			if($iFetch>0){
				for($i=0;$i<$iFetch;$i++){
					$mean=$Fetch[$i]["mean"];
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
					echo "<div class='dict_word'>";
					echo "<div class='pali'>".$Fetch[$i]["word"]."</div>";
					echo "<div class='dict'>".$Fetch[$i]["shortname"]."</div>";
					echo "<div class='mean'>".$heigh_light_mean."</div>";
					echo "</div>";
				}
			}		
		}
		
		//拆复合词
		
		$splitWord=$word;
		$part=array();
		if($count_return<2){
			echo "Try to split comp:<br>";
			while(($split=mySplit($splitWord))!==FALSE){
				array_push($part,$split[0]);
				$splitWord=$split[1];
			}
			if(count($part)>0){
				array_push($part,$splitWord);
				$newPart=ltrim(array_reduce($part,"myfunction"),"+");
				echo $newPart;
			}
		}


		echo "<div id='dictlist'>";
		foreach($dict_list as $x=>$x_value) {
		  echo "<a href='#ref_dict_$x'>$x_value</a>";
		}
		echo "</div>";
		
		echo "</div>";
		//参考字典查询结束
		
		$strDictTab="<li id=\"dt_dict\" class=\"act\" onclick=\"tab_click('dict_ref','dt_dict')\">{$_local->gui->dict}</li>";
		
		//查黑体字开始
		$arrBookName=json_decode(file_get_contents("../public/book_name/sc.json"));
		echo "<div id=\"dict_bold\" style='display:none'>";
		
		//加语尾
		$arrNewWord=array();
		for ($row = 0; $row < count($case); $row++) {
			$len=mb_strlen($case[$row][0],"UTF-8");
			$end=mb_substr($word, 0-$len,NULL,"UTF-8");
			if($end==$case[$row][0]){
				$newWord=mb_substr($word, 0,mb_strlen($word,"UTF-8")-$len,"UTF-8").$case[$row][1];
				$arrNewWord[$newWord]=1;
			}
		}
		//加连读词尾
		$arrUnWord=array();
		for ($row = 0; $row < count($union); $row++) {
			$len=mb_strlen($union[$row][0],"UTF-8");
			foreach($arrNewWord as $x=>$x_value){
				$end=mb_substr($x, 0-$len,NULL,"UTF-8");
				if($end==$union[$row][0]){
					$newWord=mb_substr($x, 0,mb_strlen($x,"UTF-8")-$len,"UTF-8").$union[$row][1];
					$arrUnWord[$newWord]=1;
				}
			}
		}
		//将连读词和$arrNewWord混合
		foreach($arrUnWord as $x=>$x_value){
			$arrNewWord[$x]=1;
		}
		if(count($arrNewWord)>0){
			$strQueryWord="(";
			foreach($arrNewWord as $x=>$x_value) {
			  $strQueryWord.="'{$x}',";
			}
			$strQueryWord=mb_substr($strQueryWord, 0,mb_strlen($strQueryWord,"UTF-8")-1,"UTF-8");
			$strQueryWord.=")";
		}
		else{
			$strQueryWord="('{$word}')";
		}

		$dictFileName=$dir_palicanon."bold.db3";
		PDO_Connect("sqlite:$dictFileName");
		//查询符合的记录数
		$query = "select count(*) as co from bold where \"word2\" in  $strQueryWord";
		$Fetch = PDO_FetchOne($query);
		if($Fetch>0){
			$strDictTab.="<li id=\"dt_bold\"  onclick=\"tab_click('dict_bold','dt_bold')\">{$_local->gui->vannana}({$Fetch})</li>";
		
			echo "查询：$word 共{$Fetch}条记录<br />";
		
			//黑体字主显示区开始
			echo "<div style='display:flex;'>";
		
			//黑体字主显示区左侧开始
			echo "<div style='flex:3;max-width: 17em;min-width: 10em;'>";
			echo "<button onclick=\"dict_update_bold(0)\">筛选</button>";
			/*查找实际出现的拼写
			$strQueryWord中是所有可能的拼写
			*/
			$realQueryWord="(";
			$query = "select word2,count(word) as co from bold where \"word2\" in $strQueryWord group by word2 order by co DESC";
			$Fetch = PDO_FetchAll($query);
			$iFetch=count($Fetch);
			if($iFetch>0){
				echo "<div>";
				echo "<input id='bold_all_word' type='checkbox' checked='true' value='' onclick=\"dict_bold_word_all_select()\"/>全选<br />";
				for($i=0;$i<$iFetch;$i++){
					$realQueryWord.="'{$Fetch[$i]["word2"]}',";
					echo "<input id='bold_word_{$i}' type='checkbox' checked value='{$Fetch[$i]["word2"]}' />";
					echo "<a onclick=\"dict_bold_word_select({$i})\">";
					echo $Fetch[$i]["word2"].":".$Fetch[$i]["co"]."<br />";
					echo "</a>";
				}
				$realQueryWord=mb_substr($realQueryWord, 0,mb_strlen($realQueryWord,"UTF-8")-1,"UTF-8");
				$realQueryWord.=")";
				echo "<input id='bold_word_count' type='hidden' value='{$iFetch}' />";
				echo "</div>";
				
			}
		
			//查找这些词出现在哪些书中
			$query = "select book,count(word) as co from bold where \"word2\" in $realQueryWord group by book order by co DESC";
			$Fetch = PDO_FetchAll($query);
			$iFetch=count($Fetch);
			if($iFetch>0){
				echo "<div id='bold_book_list'>";
				echo "出现在{$iFetch}本书中：<br />";
				echo "<input type='checkbox' checked='true' value='' />全选<br />";
				for($i=0;$i<$iFetch;$i++){
					$book=$Fetch[$i]["book"];
					$bookname=_get_book_info($book)->title;
					echo "<input id='bold_book_{$i}' type='checkbox' checked value='{$book}'/>";
					echo "<a onclick=\"dict_bold_book_select({$i})\">";
					echo "《{$bookname}》:{$Fetch[$i]["co"]}次<br />";
					echo "</a>";
				}
				echo "<input id='bold_book_count' type='hidden' value='{$iFetch}' />";
				echo "</div>";
			}
			//查找这些词出现在哪些书中结束
			echo "</div>";
			//黑体字主显示区左侧结束
			
			//黑体字主显示区右侧开始
			echo "<div id=\"dict_bold_right\" style='flex:7;'>";
			//前20条记录
			$query = "select * from bold where \"word2\" in $realQueryWord limit 0,20";
			$Fetch = PDO_FetchAll($query);
			$iFetch=count($Fetch);
			if($iFetch>0){
				$dictFileName=$_file_db_pali_text;
				PDO_Connect("sqlite:$dictFileName");
				for($i=0;$i<$iFetch;$i++){
					$paliword=$Fetch[$i]["word"];
					$book=$Fetch[$i]["book"];
					$bookInfo=_get_book_info($book);
					$bookname=$bookInfo->title;
					$bookPath=$bookInfo->c1.">".$bookInfo->c2.">".$bookInfo->c3;
					$paragraph=$Fetch[$i]["paragraph"];
					$base=$Fetch[$i]["base"];
					$pali=$Fetch[$i]["pali"];
					echo "<div class='dict_word'>";
					echo  "<div class='dict'><b>《{$bookname}》</b> {$bookPath}</div>";
					echo  "<div class='mean'>$paliword</div>";
					

								
					if(strlen($pali)>1){
						echo  "<div class='mean'>$pali</div>";
					}
					else{
						$dictFileName=$_file_db_pali_text;
						PDO_Connect("sqlite:$dictFileName");
						$query = "select * from pali_text where \"book\" = '{$book}' and \"paragraph\" = '{$paragraph}' limit 0,20";
						//$query = "select * from data where \"book\" = 'p{$book}' and \"paragraph\" = '{$paragraph}' limit 0,20";
						$FetchPaliText = PDO_FetchAll($query);
						$countPaliText=count($FetchPaliText);
						if($countPaliText>0){
							
							for($iPali=0;$iPali<$countPaliText;$iPali++){
					$path="";
					$parent = $FetchPaliText[0]["parent"];
					$deep=0;
					$sFirstParentTitle="";
					while($parent>-1){
						$query = "select * from pali_text where \"book\" = '{$book}' and \"paragraph\" = '{$parent}' limit 0,1";
						$FetParent = PDO_FetchAll($query);
						if($sFirstParentTitle==""){
							$sFirstParentTitle = $FetParent[0]["toc"];
						}	
						$path="{$FetParent[0]["toc"]}>{$path}";
						$parent = $FetParent[0]["parent"];
						$deep++;
						if($deep>5){
							break;
						}
					}
					$path=$bookPath.$path."No. ".$paragraph;
					echo  "<div class='mean'>$path</div>";
					
								if(substr($paliword,-1)=="n"){
									$paliword=substr($paliword,0,-1);
								}
								$htmlPara=str_replace(".0","。0",$FetchPaliText[$iPali]["html"]);
								$aSent=str_getcsv($htmlPara,".");
								echo count($aSent);
								
								$aSentInfo=array();
								$aBold=array();
								echo  "<div class='wizard_par_div'>";
								foreach($aSent as $sent){
									//array_push($aSentInfo,false);
									//array_push($aBold,false);
									
									if(stristr($sent,$paliword)){
										echo "<span>{$sent}.</span><br>";
									//	$aSent[$i]=str_replace($paliword,"<hl>{$paliword}</hl>",$aSent[$i]);
										//$aSentInfo[$i]=true;
									}
									//if(stristr($aSent[$i],"bld")){
										//$aBold[$i]=true;
									//}
								}
								echo "</div>";
								/*
								$output="";
								$bold_on=false;
								for($i=0;$i<count($aSent);$i++){
									if($aBold[$i]){
										if($aSentInfo[$i]){
										$output.=$aSent[$i]."<br>";
										$bold_on=true;
										}
										else{
											echo "<div>{$output}</div>";
											$output="";
											$bold_on=false;
										}
									}
									else{
										if($bold_on){
											echo "<div>{$aBold[$i]}</div>";
										}
									}
								}
								*/
								//$light_text=str_replace($paliword,"<hl>{$paliword}</hl>",$FetchPaliText[$iPali]["vri_text"]);
								//$light_text=str_replace(".",".<br />",$light_text);
								//echo  "<div class='wizard_par_div'>{$light_text}</div>";
							}
						}
					}
					echo  "<div class='search_para_tools'><button onclick=\"dict_edit_now('{$book}','{$paragraph}','{$sFirstParentTitle}')\">Edit</button></div>";		
					echo  "</div>";
				}
			}
			echo "</div>";
			//黑体字主显示区右侧结束
			echo "</div>";
			//黑体字主显示区结束
		}
		echo "</div>";
		//查黑体字结束
		
		//查术语
		echo "<div id=\"dict_term\"  style='display:none;'>";	
		PDO_Connect("sqlite:$_file_db_term");
		$query = "select * from term  where \"word\" = ".$PDO->quote($word)." limit 0,30";
		
		$Fetch = PDO_FetchAll($query);
		$iFetch=count($Fetch);
		$count_return+=$iFetch;
		if($iFetch>0){
			$strDictTab.="<li id=\"dt_term\"  onclick=\"tab_click('dict_term','dt_term')\">{$_local->gui->adhivacana}({$iFetch})</li>";
			for($i=0;$i<$iFetch;$i++){
				echo "<div class='dict_word'>";
				echo "<div class='pali'>{$word}</div>";
				echo "<div class='dict'>{$Fetch[$i]["owner"]}</div>";
				echo "<div class='mean'>{$Fetch[$i]["meaning"]}</div>";
				echo "<div class='other_mean'>{$Fetch[$i]["other_meaning"]}</div>";
				echo "<div class='note'>{$Fetch[$i]["note"]}</div>";
				echo "</div>";
			}
		}		
		echo "</div>";
		//查术语结束	

		//查词源
		echo "<div id=\"dict_wordmap\">";	
		echo "</div>";
		//查词源结束	

		//查用户词典
		echo "<div id=\"dict_user\" style='display:none;'>";	
		$db_file = $_file_db_wbw;
		PDO_Connect("sqlite:$db_file");
		$query = "select *  from dict where \"pali\"= ".$PDO->quote($word)." limit 0,100";
		$Fetch = PDO_FetchAll($query);
		$iFetch=count($Fetch);
		$count_return+=$iFetch;
		$strDictTab.="<li id=\"dt_user\" onclick=\"tab_click('dict_user','dt_user')\">{$_local->gui->user}({$iFetch})</li>";
		echo "<div class='dict_word'>";
		echo "<div class='pali'>{$word}</div>";
		echo "<div class='' onclick=\"dict_show_edit()\">编辑并收藏</div>";
		if($iFetch>0){
			echo "<div id='user_word_edit' style='display:none'>";
		}
		else{
			echo "<div id='user_word_edit'>";
		}
		echo "<div class=''>Type：";
		echo "<select id=\"id_type\" name=\"type\" >";
		foreach($_local->type_str as $type){
			echo "<option value=\"{$type->id}\" >{$type->value}</option>";
		}
		echo "</select>";
		echo "</div>";
		echo "<div class=''>Gramma:<input type='input' value=''/></div>";
		echo "<div class=''>语基：<input type='input' value=''/></div>";
		echo "<div class=''>意思：<input type='input' value=''/></div>";
		echo "<div class=''>注解：<textarea></textarea></div>";
		echo "<div class=''>组分：<input type='input' value=''/></div>";
		echo "<div class=''>组分意思：<input type='input' value=''/></div>";
		echo "<div class=''><button>收藏</button></div>";
		echo "</div>";
		echo "</div>";
		
		if($iFetch>0){
			for($i=0;$i<$iFetch;$i++){
				$mean=$Fetch[$i]["mean"];
				echo "<div class='dict_word'>";
				echo "<div class='pali'>{$word}</div>";
				echo "<div class=''>语法：{$Fetch[$i]["type"]}-{$Fetch[$i]["gramma"]}</div>";
				if(strlen($Fetch[$i]["parent"])>0){
					echo "<div class=''>语基：{$Fetch[$i]["parent"]}</div>";
				}
				echo "<div class=''>意思：{$Fetch[$i]["mean"]}</div>";
				if(strlen($Fetch[$i]["note"]>0)){
					echo "<div class=''>注解：{$Fetch[$i]["note"]}</div>";
				}
				echo "<div class=''>组分：{$Fetch[$i]["factors"]}</div>";
				echo "<div class=''>组分意思：{$Fetch[$i]["factormean"]}</div>";
				echo "<div class=''>贡献者：{$Fetch[$i]["creator"]}</div>";
				echo "<div class=''>收藏：{$Fetch[$i]["ref_counter"]}次</div>";
				echo "</div>";
			}
		}
		echo "</div>";
		//查用户词典结束	

		echo "<div id='real_dict_tab' style='display:none;'>$strDictTab</div>";
		break;	
	case "update":
		$target=$_GET["target"];
		switch($target){
			case "bold";
				$arrBookName=json_decode(file_get_contents("../public/book_name/sc.json"));
				$dictFileName=$dir_palicanon."bold.db3";
				PDO_Connect("sqlite:$dictFileName");
				$wordlist=$_GET["wordlist"];
				$booklist=$_GET["booklist"];
				$aBookList=ltrim($booklist,"(");
				$aBookList=rtrim($aBookList,")");
				$aBookList=str_replace("'","",$aBookList);
				$aBookList=str_getcsv($aBookList);
				foreach($aBookList as $oneBook){
					$aInputBook["{$oneBook}"]=1;
				}
				
				//查找这些词出现在哪些书中
				$query = "select book,count(word) as co from bold where \"word2\" in $wordlist group by book order by co DESC";
				$Fetch = PDO_FetchAll($query);
				$iFetch=count($Fetch);
				if($iFetch>0){
					echo "<div id='bold_book_list_new'>";
					echo "出现在{$iFetch}本书中：<br />";
					echo "<input id='bold_all_book' type='checkbox' checked onclick=\"dict_bold_book_all_select()\" />全选<br />";
					for($i=0;$i<$iFetch;$i++){
						$book=$Fetch[$i]["book"];
						$bookname=$arrBookName[$book-1]->title;	
						if(isset($aInputBook["{$book}"])){
							$bookcheck="checked";
						}
						else{
							$bookcheck="";
						}
						echo "<input id='bold_book_{$i}' type='checkbox' $bookcheck value='{$book}'/>";
						echo "<a onclick=\"dict_bold_book_select({$i})\">";
						echo "《{$bookname}》({$Fetch[$i]["co"]})<br />";
						echo "</a>";
					}
					echo "<input id='bold_book_count' type='hidden' value='{$iFetch}' />";
					echo "</div>";
				}
				//查找这些词出现在哪些书中结束
				//前20条记录
				$query = "select * from bold where \"word2\" in $wordlist and \"book\" in $booklist  limit 0,20";
				$Fetch = PDO_FetchAll($query);
				$iFetch=count($Fetch);
				if($iFetch>0){
					for($i=0;$i<$iFetch;$i++){
						$paliword=$Fetch[$i]["word"];
						$book=$Fetch[$i]["book"];
						$bookname=$arrBookName[$book-1]->title;
						$c1=$arrBookName[$book-1]->c1;
						$c2=$arrBookName[$book-1]->c2;
						$bookPath = "$c1>$c2";
						$paragraph=$Fetch[$i]["paragraph"];
						$base=$Fetch[$i]["base"];
						$pali=$Fetch[$i]["pali"];
						echo "<div class='dict_word'>";
						echo  "<div class='dict'>《{$bookname}》 $c1 $c2 </div>";
						echo  "<div class='mean'>$paliword</div>";
						
						if(strlen($pali)>1){
							echo  "<div class='mean'>$pali</div>";
						}
						else{
							$dictFileName=$_file_db_pali_text;
						PDO_Connect("sqlite:$dictFileName");
						$query = "select * from pali_text where \"book\" = '{$book}' and \"paragraph\" = '{$paragraph}' limit 0,20";
						$FetchPaliText = PDO_FetchAll($query);
							$countPaliText=count($FetchPaliText);
							if($countPaliText>0){
								for($iPali=0;$iPali<$countPaliText;$iPali++){
									
														$path="";
					$parent = $FetchPaliText[0]["parent"];
					$deep=0;
					$sFirstParentTitle="";
					while($parent>-1){
						$query = "select * from pali_text where \"book\" = '{$book}' and \"paragraph\" = '{$parent}' limit 0,1";
						$FetParent = PDO_FetchAll($query);
						if($sFirstParentTitle==""){
							$sFirstParentTitle = $FetParent[0]["toc"];
						}	
						$path="{$FetParent[0]["toc"]}>{$path}";
						$parent = $FetParent[0]["parent"];
						$deep++;
						if($deep>5){
							break;
						}
					}
					$path="<div>{$bookPath}>{$path} No. {$paragraph}</div>";
					echo  "<div class='mean'>$path</div>";
					
									if(substr($paliword,-1)=="n"){
										$paliword=substr($paliword,0,-1);
									}
									$light_text=str_replace($paliword,"<hl>{$paliword}</hl>",$FetchPaliText[$iPali]["html"]);
									$light_text=str_replace(".",".<br /><br />",$light_text);
									echo  "<div class='wizard_par_div'>{$light_text}</div>";
								}
							}
						}
						echo  "<div class='search_para_tools'><button onclick=\"dict_edit_now('{$book}','{$paragraph}','{$sFirstParentTitle}')\">Edit</button></div>";
						echo  "</div>";
					}
				}		
				break;
		}
		break;
}


?>