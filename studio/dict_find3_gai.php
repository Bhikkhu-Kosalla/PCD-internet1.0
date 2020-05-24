<?php
//查询参考字典
require_once '../public/casesuf.inc';
require_once 'dict_find_un.inc';
require_once 'sandhi.php';
require_once "../public/config.php";
require_once "../public/_pdo.php";

require_once '../public/load_lang.php';

if(isset($_GET["op"])){
	$op=$_GET["op"];
}
else{
	$op="";
}
$word=mb_strtolower($_GET["word"],'UTF-8');
$org_word=$word;

/*
//预处理
$search  = array('aa', 'ae', 'ai', 'ao', 'au', 'aā', 'aī', 'aū', 'ea', 'ee', 'ei', 'eo', 'eu', 'eā', 'eī', 'eū', 'ia', 'ie', 'ii', 'io', 'iu', 'iā', 'iī', 'iū', 'oa', 'oe', 'oi', 'oo', 'ou', 'oā', 'oī', 'oū', 'ua', 'ue', 'ui', 'uo', 'uu', 'uā', 'uī', 'uū', 'āa', 'āe', 'āi', 'āo', 'āu', 'āā', 'āī', 'āū', 'īa', 'īe', 'īi', 'īo', 'īu', 'īā', 'īī', 'īū', 'ūa', 'ūe', 'ūi', 'ūo', 'ūu', 'ūā', 'ūī', 'ūū');
$replace = array('a-a', 'a-e', 'a-i', 'a-o', 'a-u', 'a-ā', 'a-ī', 'a-ū', 'e-a', 'e-e', 'e-i', 'e-o', 'e-u', 'e-ā', 'e-ī', 'e-ū', 'i-a', 'i-e', 'i-i', 'i-o', 'i-u', 'i-ā', 'i-ī', 'i-ū', 'o-a', 'o-e', 'o-i', 'o-o', 'o-u', 'o-ā', 'o-ī', 'o-ū', 'u-a', 'u-e', 'u-i', 'u-o', 'u-u', 'u-ā', 'u-ī', 'u-ū', 'ā-a', 'ā-e', 'ā-i', 'ā-o', 'ā-u', 'ā-ā', 'ā-ī', 'ā-ū', 'ī-a', 'ī-e', 'ī-i', 'ī-o', 'ī-u', 'ī-ā', 'ī-ī', 'ī-ū', 'ū-a', 'ū-e', 'ū-i', 'ū-o', 'ū-u', 'ū-ā', 'ū-ī', 'ū-ū');

$word = str_replace($search, $replace, $word);

$ending[]=array("len"=>6,"e1"=>"yevāti","e2"=>"-eva-iti");
$len = mb_strlen($word,"UTF-8");
foreach($ending as $end){
	$head=mb_substr($word,0,$len-$end["len"],"UTF-8");
	
	if(mb_substr($word,$len-$end["len"],NULL,"UTF-8")==$end["e1"]){
		$word=$head.$end["e2"];
		break;
	}
}
echo "整理：{$word}<br>";
		
$arrword = str_getcsv($word,"-");


$dictFileName=$dir_dict_system."compindex.db3";
PDO_Connect("sqlite:{$dictFileName}");
global $auto_split_deep;
global $auto_split_times;
$auto_split_deep=0;
$auto_split_times=0;

$t1=time();
foreach($arrword as $oneword){
	echo mySplit2($oneword);
	
	//后处理
	//-ssāpi=-[ssa]-api
	
	echo "-";
}
echo "<br>数据库查询【{$auto_split_times}】次";
echo "time:".(time()-$t1);
return;
*/
$count_return=0;
$dict_list=array();

global $PDO;
function myfunction($v1,$v2)
{
return $v1 . "+" . $v2;
}

/*
查找某个单词是否在现有词典出现
*/
function isExsit($word){
	global $PDO;
	global $auto_split_times;
	$confidence=0;
	$auto_split_times++;
	$query = "select base from dict where \"word\" = ".$PDO->quote($word);
	$row=PDO_FetchAll($query);

	
	if($row[0]==0){
		return false;
	}
	else{
		return true;
	}
	
}

/*
*自动拆分复合词
*功能：将一个单词拆分为两个部分
*输入：想要拆的词
*输出：数组，第一个为前半部分，第二个为后半部分，前半部分是在现有字典里搜索到的。
*范例：
while(($split=mySplit($splitWord))!==FALSE){
	array_push($part,$split[0]);
	$splitWord=$split[1];
}
循环结束后$part里放的就是拆分结果

算法：从最后一个字母开始，一次去掉一个字母，然后在现有字典里搜索剩余的部分（前半部分）
如果搜索到，就返回。第二次，将剩余的部分，也就是后半部分应用相同的算法。
直到单词长度小于5
中间考虑了连音规则：
~a+i~=~i~
在拆分的时候要补上前面的元音
有时后面的词第一个辅音会重复
word+tha~=wordttha~
需要去掉后面的单词的一个辅音

*/
function mySplit($strWord){
	
	$doubleword="kkggccjjṭṭḍḍttddppbb";
	$len=mb_strlen($strWord,"UTF-8");
	if($len>5){
		for($i=$len;$i>3;$i--){
			$str1=mb_substr($strWord,0,$i,"UTF-8");
			$str2=mb_substr($strWord,$i,NULL,"UTF-8");
			if(isExsit($str1)){
				//如果字典里存在，返回拆分结果
				//如果第二个部分有双辅音，去掉第一个辅音。因为巴利语中没有以双辅音开头的单词。
				if(mb_strlen($str2,"UTF-8")>2){
					$left2=mb_substr($str2,0,2,"UTF-8");
					if(mb_strpos($doubleword,$left2,0,"UTF-8")!==FALSE){
						$str2=mb_substr($str2,1,NULL,"UTF-8");
					}
				}
				return array($str1,$str2);
			}
			else{
				//补上结尾的a再次查找
				$str1=$str1."a";
				if(isExsit($str1)){

					$left2=mb_substr($str2,0,2,"UTF-8");
					if(mb_strpos($doubleword,$left2,0,"UTF-8")!==FALSE){
						$str2=mb_substr($str2,1,NULL,"UTF-8");
					}
					return array($str1,$str2);
				}
			}
		}
		//如果没找到。将ā变为a后再找。因为两个a复合后会变成ā
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
		//如果没找到将开头的e变为i再次查找
		if(mb_substr($strWord,0,1,"UTF-8")=="e"){
			$strWord='i'.mb_substr($strWord,1,NULL,"UTF-8");
			for($i=$len-1;$i>3;$i--){
				$str1=mb_substr($strWord,0,$i,"UTF-8");
				$str2=mb_substr($strWord,$i,NULL,"UTF-8");
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

function mySplit2($strWord){
	global $auto_split_deep;
	$auto_split_deep++;
	
	if($auto_split_deep>=16){
		$auto_split_deep=0;
		return(array($strWord."(dp);<br>");
	}

	if(isExsit($strWord)){
		$auto_split_deep=0;
		return($strWord.";");
	}
	//如果开头有双辅音，去掉第一个辅音。因为巴利语中没有以双辅音开头的单词。
	$doubleword="kkggccjjṭṭḍḍttddppbb";
	if(mb_strlen($strWord,"UTF-8")>2){
		$left2=mb_substr($strWord,0,2,"UTF-8");
		if(mb_strpos($doubleword,$left2,0,"UTF-8")!==FALSE){
			$strWord=mb_substr($strWord,1,NULL,"UTF-8");
		}
	}
				
	$sandhi[0]=array("a"=>"","b"=>"","c"=>"","len"=>0);
	$sandhi[1]=array("a"=>"a","b"=>"a","c"=>"ā","len"=>1);
	$sandhi[2]=array("a"=>"a","b"=>"ā","c"=>"ā","len"=>1);
	$sandhi[3]=array("a"=>"a","b"=>"e","c"=>"e","len"=>1);
	$sandhi[4]=array("a"=>"a","b"=>"i","c"=>"i","len"=>1);
	$sandhi[5]=array("a"=>"a","b"=>"o","c"=>"o","len"=>1);
	$sandhi[6]=array("a"=>"a","b"=>"u","c"=>"o","len"=>1);
	$sandhi[7]=array("a"=>"u","b"=>"a","c"=>"o","len"=>1);
	$sandhi[8]=array("a"=>"a","b"=>"u","c"=>"u","len"=>1);
	$sandhi[9]=array("a"=>"a","b"=>"ī","c"=>"ī","len"=>1);
	$sandhi[10]=array("a"=>"a","b"=>"ū","c"=>"ū","len"=>1);
	$sandhi[11]=array("a"=>"a","b"=>"i","c"=>"e","len"=>1);
	$sandhi[12]=array("a"=>"a","b"=>"atth","c"=>"atth","len"=>4);
	$sandhi[13]=array("a"=>"ṃ","b"=>"api","c"=>"mpi","len"=>3);
	$sandhi[14]=array("a"=>"ṃ","b"=>"eva","c"=>"meva","len"=>4);
	$sandhi[15]=array("a"=>"a","b"=>"ādi","c"=>"ādi","len"=>3);
	$sandhi[16]=array("a"=>"a","b"=>"a","c"=>"ānama","len"=>5);
	$sandhi[17]=array("a"=>"ati","b"=>"a","c"=>"ānama","len"=>5);
	$sandhi[18]=array("a"=>"a","b"=>"iti","c"=>"āti","len"=>3);

	//$sandhi[16]=array("a"=>"ānaṃ","b"=>"a","c"=>"ānama","len"=>3);
	//$sandhi[12]=array("a"=>"ṃ","b"=>"a","c"=>"ma","len"=>2);

	$len=mb_strlen($strWord,"UTF-8");
	if($len>2){
		for($i=$len;$i>1;$i--){
			foreach($sandhi as $row){
				if(mb_substr($strWord,$i-$row["len"],$row["len"],"UTF-8")==$row["c"]){
					$str1=mb_substr($strWord,0,$i-$row["len"],"UTF-8").$row["a"];
					$str2=$row["b"].mb_substr($strWord,$i,NULL,"UTF-8");
					if(isExsit($str1)){
						return("{$str1}+".mySplit2($str2));
					}
				}
			}
		}
	}
	$auto_split_deep=0;
	return($strWord.";<br>");
}


switch($op){
	case "pre"://预查询
		$dictFileName=$dir_dict_system."ref1.db";
		PDO_Connect("sqlite:{$dictFileName}");
		echo "<wordlist>";
		$query = "select word,count from dict where \"eword\" like ".$PDO->quote($word.'%')." OR \"word\" like ".$PDO->quote($word.'%')."  limit 0,30";
		$Fetch = PDO_FetchAll($query);
		$iFetch=count($Fetch);
		if($iFetch>0){
			for($i=0;$i<$iFetch;$i++){
				$outXml = "<word>";
				$word=$Fetch[$i]["word"];
				$outXml = $outXml."<pali>$word</pali>";
				$outXml = $outXml."<count>".$Fetch[$i]["count"]."</count>";
				$outXml = $outXml."</word>";
				echo $outXml;
			}
		}
		echo "</wordlist>";
		break;
	case "search":
		$dictFileName=$dir_dict_system."ref.db";
		PDO_Connect("sqlite:$dictFileName");

		$t1=time();
		//直接查询
		$query = "select dict.dict_id,dict.mean,info.shortname from dict LEFT JOIN info ON dict.dict_id = info.id where \"word\" = ".$PDO->quote($word)." limit 0,30";
		$Fetch = PDO_FetchAll($query);
		$iFetch=count($Fetch);
		$count_return+=$iFetch;
		if($iFetch>0){
			for($i=0;$i<$iFetch;$i++){
				$mean=$Fetch[$i]["mean"];
				$mean = str_replace("[[","<a onclick=\"dict_jump(this)\">",$mean);
				$mean = str_replace("]]","</a>",$mean);
				$dictid=$Fetch[$i]["dict_id"];
				$dict_list[$dictid]=$Fetch[$i]["shortname"];
				$outXml = "<div class='dict_word'>";
				$outXml = $outXml."<a name='ref_dict_$dictid'></a>";
				$outXml = $outXml."<div class='dict'>".$Fetch[$i]["shortname"]."</div>";
				$outXml = $outXml."<div class='mean'>{$mean}</div>";
				$outXml = $outXml."</div>";
				echo $outXml;
			}
		}
		if(substr($word,0,1)=="_" && substr($word,-1,1)=="_"){
			echo "<div id='dictlist'>";
			foreach($dict_list as $x=>$x_value) {
			  echo "<a href='#ref_dict_$x'>$x_value</a>";
			}
			echo "</div>";
			break;
		}
		$t2=time();
		//去除尾查
		$newWord=array();
		for ($row = 0; $row < count($case); $row++) {
			$len=mb_strlen($case[$row][1],"UTF-8");
			$end=mb_substr($word, 0-$len,NULL,"UTF-8");
			if($end==$case[$row][1]){
				$base=mb_substr($word, 0,mb_strlen($word,"UTF-8")-$len,"UTF-8").$case[$row][0];
				if($base!=$word){
					$gr="<a onclick=\"dict_jump(this)\">".str_replace("$","</a> &nbsp;&nbsp;<a  onclick=\"dict_jump(this)\">",$case[$row][2])."</a>";
					if(isset($newWord[$base])){
						$newWord[$base] .= "<br />".$gr;
					}
					else{
						$newWord[$base] = $gr;
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
					//语法信息
					foreach($_local->grammastr as $gr){
						$x_value = str_replace($gr->id,$gr->value,$x_value);
					}
					echo $x . ":<div class='dict_find_gramma'>" . $x_value . "</div>";
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
			}
		}
		//去除尾查结束
		$t3=time();
		
		
		//查连读词
		$junction = "";
		if($count_return<2){
			$newWord=array();
			for ($row = 0; $row < count($un); $row++) {
				$len=mb_strlen($un[$row][1],"UTF-8");
				$end=mb_substr($word, 0-$len,NULL,"UTF-8");
				if($end==$un[$row][1]){
					$junction=mb_substr($word, 0,mb_strlen($word,"UTF-8")-$len,"UTF-8").$un[$row][0];
				}
			}		
		}
		$t4=time();
		
		$ending[0]=array("len"=>2,"e1"=>"+e","e2"=>"+[e]");
		$ending[1]=array("len"=>2,"e1"=>"+o","e2"=>"+[o]");
		$ending[2]=array("len"=>4,"e1"=>"+eti","e2"=>"e+iti");
		$ending[3]=array("len"=>4,"e1"=>"e+na","e2"=>"ena");
		$ending[4]=array("len"=>8,"e1"=>"+ādīna+ṃ","e2"=>"+ādīnaṃ");
		$ending[5]=array("len"=>4,"e1"=>"+mpi","e2"=>"ṃ+api");
		$ending[6]=array("len"=>5,"e1"=>"a+esu","e2"=>"esu");
		$ending[7]=array("len"=>4,"e1"=>"o+ti","e2"=>"o+iti");
		$ending[8]=array("len"=>6,"e1"=>"a+ānaṃ","e2"=>"ānaṃ");
		
		//拆复合词
		$dictFileName=$dir_dict_system."compindex.db3";
		PDO_Connect("sqlite:{$dictFileName}");
		echo "<div id=\"auto_splite\" >";		
		$splitWord=$word;
		$part=array();
		if($count_return<2)
		{
			while(($split=mySplit($splitWord))!==FALSE){
				array_push($part,$split[0]);
				$splitWord=$split[1];
			}
			if(count($part)>0){
				$newComp="";
				if(mb_strlen($splitWord,"UTF-8")>0){
					array_push($part,$splitWord);
				}
				//print_r($part);
				$newPart=ltrim(array_reduce($part,"myfunction"),"+");

				$len= mb_strlen($newPart,"UTF-8");
				
				foreach($ending as $end){
					$head=mb_substr($newPart,0,$len-$end["len"],"UTF-8");
					
					if(mb_substr($newPart,$len-$end["len"],NULL,"UTF-8")==$end["e1"]){
						$newComp=$head.$end["e2"];
						break;
					}
				}
				
				if(empty($newComp)){
					$partLink = "<a onclick=\"part_click(this)\">".str_replace("+","</a> + <a onclick=\"part_click(this)\">",$newPart)."</a>";
					echo "<div>{$partLink} ";
					echo " <a onclick='add_part(\"{$newPart}\")'>[√]</a> ";
					echo " <a onclick='add_part_to_input(\"{$newPart}\")'>[▲]</a>";
					echo "</div>";
				}
				else{
					$partLink = "<a onclick=\"part_click(this)\">".str_replace("+","</a> + <a onclick=\"part_click(this)\">",$newComp)."</a>";
					echo "<div>{$partLink}";
					echo " <a onclick='add_part(\"{$newComp}\")'>[√]</a> ";
					echo " <a onclick='add_part_to_input(\"{$newComp}\")'>[▲]</a>";
					echo "</div>";		
				}
				if(!empty($junction)){
					$junctionLink = "<a onclick=\"part_click(this)\">".str_replace("+","</a> + <a onclick=\"part_click(this)\">",$junction)."</a>";
					echo "<div>{$junctionLink} ";
					echo "<a onclick='add_part(\"{$junction}\")'>[√]</a>";
					echo " <a onclick='add_part_to_input(\"{$junction}\")'>[▲]</a>";
					echo "</div>";
				}
				//echo "<div><a onclick='add_part(\"{$newPart}\")'>{$newPart}</a></div>";
				
			}
		}
		else{
			echo "<button>Compone Splite</button>";
		}
		echo "</div>";
		//拆复合词结束
		$t5=time();
		
		//查内容
		if($count_return<3){
			$word1=$org_word;
			$wordInMean="%$org_word%";
			
			$query = "select dict.dict_id,dict.word,dict.mean,info.shortname from dict LEFT JOIN info ON dict.dict_id = info.id where \"mean\" like ".$PDO->quote($wordInMean)." limit 0,30";
			$Fetch = PDO_FetchAll($query);
			$iFetch=count($Fetch);
			echo "include $org_word ({$iFetch})<br />";
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
					$outXml = "<div class='dict_word'>";
					$outXml = $outXml."<div class='word'>".$Fetch[$i]["word"]."</div>";
					$outXml = $outXml."<div class='dict'>".$Fetch[$i]["shortname"]."</div>";
					$outXml = $outXml."<div class='mean'>".$heigh_light_mean."</div>";
					$outXml = $outXml."</div>";
					echo $outXml;
				}
			}		
		}
		
		$t6 = time();
		echo "<p>直接查：".($t2-$t1)." 去尾插：".($t3-$t2)." 连读：".($t4-$t3)." 复合：".($t5-$t4)." 内容：".($t6-$t5)."</p>";

		echo "<div id='dictlist'>";
		foreach($dict_list as $x=>$x_value) {
		  echo "<a href='#ref_dict_$x'>$x_value</a>";
		}
		echo "</div>";
		
		break;		
}


?>