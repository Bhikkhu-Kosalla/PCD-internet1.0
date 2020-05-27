<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link type="text/css" rel="stylesheet" href="css/style.css"/>
	<link type="text/css" rel="stylesheet" href="css/color_day.css" id="colorchange" />
	<link type="text/css" rel="stylesheet" href="css/style_mobile.css" media="screen and (max-width:767px)">

</head>
<body>
<?php
//工程文件操作
//建立，
require 'checklogin.inc';
require '../public/config.php';
require "../public/_pdo.php";
require "./public.inc";
require "./book_list_en.inc";

$sLang["1"]="pali";
$sLang["2"]="en";
$sLang["3"]="sc";
$sLang["4"]="tc";

if(isset($_POST["op"])){
	$op=$_POST["op"];
}
if(isset($_GET["op"])){
	$op=$_GET["op"];
}
if(isset($_POST["data"])){
	$data=$_POST["data"];
}
else if(isset($_GET["data"])){
	$data=$_GET["data"];
}
if($_COOKIE["uid"]){
	$uid = $_COOKIE["uid"];
	$USER_ID = $_COOKIE["userid"];
}
else{
	echo "尚未登录";
	exit;
}
switch($op){
	case "create":
	{
		if(!isset($data)){
			$dl_file_name=$dir_user_base.$USER_ID."/dl.json";
			$data=file_get_contents($dl_file_name);
		}
		$res=json_decode($data);
		
		$title=$res[0]->title;
		$title_en=pali2english($title);
		$book=$res[0]->book;
		if(substr($book,0,1)=="p"){
			$book=substr($book,1);
		}
		$paragraph=$res[0]->parNum;
		$tag="[$title]";
		$paraList=$res[0]->parlist;
		$paraList=rtrim($paraList,",");
		$strQueryParaList=str_replace(",","','",$paraList);
		$strQueryParaList="('".$strQueryParaList."')";
		$aParaList=str_getcsv($paraList);
		echo $strQueryParaList;
		echo "<textarea>";
		print_r($res);
		echo "</textarea>";
		if(!isset($_POST["title"])){
			$thisFileName=basename(__FILE__);
			echo "<div class='fun_block'>";
			echo "<h2>新建工程</h2>";
			echo "<form action=\"{$thisFileName}\" method=\"post\">";
			echo "<input type='hidden' name='op' value='{$op}'/>";
			echo "<input type='hidden' name='data' value='{$data}'/>";
			echo "Project Title:<input type='input' name='title' value='{$title}'/><br>";
			echo "<input type=\"checkbox\" name='new_tran' />自动新建译文<br>";
			echo "<input type=\"submit\" value='Load'>";
			echo "</form>";
			echo "</div>";
			echo "</body>";
			exit;
		}
		$user_title=$_POST["title"];
		$create_para=$paragraph;
		$FileName = $book."_".$paragraph."_".time().".pcs";
		$sFullFileName=$dir_user_base.$userid.$dir_mydocument.$FileName;
		$myfile = fopen($sFullFileName, "w") or die("Unable to open file!");
		
		$strXml="<set>\n";
		$strXml.="    <head>\n";
		$strXml.="        <type>pcdsset</type>\n";
		$strXml.="        <mode>package</mode>\n";
		$strXml.="        <ver>1</ver>\n";
		$strXml.="        <toc></toc>\n";
		$strXml.="        <style></style>\n";
		$strXml.="        <doc_title>$user_title</doc_title>\n";
		$strXml.="        <tag>$tag</tag>\n";
		$strXml.="        <book>$book</book>\n";
		$strXml.="        <paragraph>$paragraph</paragraph>\n";
		$strXml.="    </head>\n";
		$strXml.="    <dict></dict>\n";
		$strXml.="    <message></message>\n";
		$strXml.="    <body>\n";
		fwrite($myfile, $strXml);
		echo "count res:".count($res)."<br>";
		for($iRes=0;$iRes<count($res);$iRes++){
			$get_res_type=$res[$iRes]->type;
			echo "iRes: $iRes,type:$get_res_type<br/>";
			$res_album_id=$res[$iRes]->album_id;
			$res_book=$res[$iRes]->book;
			$get_par_begin=$res[$iRes]->parNum;
			$language=$res[$iRes]->language;
			$author=$res[$iRes]->author;
			
			$db_file = $dir_palicanon.'res.db3';
			PDO_Connect("sqlite:$db_file");
			$query = "select guid,owner from 'album' where id='{$res_album_id}'";
			$Fetch = PDO_FetchAll($query);
			if(count($Fetch)>0){
				$res_album_guid=$Fetch[0]["guid"];
				$res_album_owner=$Fetch[0]["owner"];
			}
			else{
				$res_album_guid=UUID();
				$res_album_owner=0;
			}
			
			switch($get_res_type){
				case "1"://pali text
					PDO_Connect("sqlite:$_file_db_pali_text");
					$query="SELECT * FROM pali_text WHERE \"book\" = ".$PDO->quote($res_book)." AND (\"paragraph\" in {$strQueryParaList} ) ";

					$sth = $PDO->prepare($query);
					$sth->execute();
					echo $query."<br/>";
					{

						while($result = $sth->fetch(PDO::FETCH_ASSOC))
						{							
							$text = $result["text"];
							$paragraph = $result["paragraph"];
								$strXml = "<block>
	<info>
		<type>palitext</type>
		<book>{$book}</book>		
		<paragraph>{$result["paragraph"]}</paragraph>
		<album_id>{$result["album_index"]}</album_id>
		<album_guid>{$res_album_guid}</album_guid>
		<author>VRI</author>
		<language>pali</language>
		<version>4</version>
		<edition>CSCD4</edition>
		<level>{$result["level"]}</level>
		<id>".GUIDv4()."</id>
	</info>
	<data><text>{$result["text"]}</text></data>
</block>";
							fwrite($myfile, $strXml);
							
						if($result["level"]>0 && $result["level"]<9){

							$strXml="<block>
	<info>
		<type>heading</type>
		<book>{$book}</book>		
		<paragraph>{$result["paragraph"]}</paragraph>
		<album_id>{$result["album_index"]}</album_id>
		<album_guid>{$res_album_guid}</album_guid>
		<author>VRI</author>
		<language>pali</language>
		<version>4</version>
		<edition>CSCD4</edition>
		<level>{$result["level"]}</level>
		<id>".GUIDv4()."</id>
	</info>
	<data><text>{$result["text"]}</text></data>
</block>";
								fwrite($myfile, $strXml);
							}
							else{
								
							}
						}
					}
					break;
				case "2"://逐词解析
				break;
				case "3"://translate
					//打开翻译数据文件
					$db_file = "../".DIR_PALICANON_TRANSLATION."p{$book}_translate.db3";
					PDO_Connect("sqlite:$db_file");
					$table="p{$book}_translate_info";
					//部分段落
					$query="SELECT * FROM {$table} WHERE paragraph in {$strQueryParaList}  and album_id=$res_album_id";
					echo $query."<br/>";
					//查询翻译经文内容
					$FetchText = PDO_FetchAll($query);
					$iFetchText = count($FetchText);
					echo "iFetchText:{$iFetchText}<br/>";
					if($iFetchText>0){
						for($i=0;$i<$iFetchText;$i++){
							$currParNo = $FetchText[$i]["paragraph"];
							$language = $FetchText[$i]["language"];
							$language = $sLang["{$language}"];
							if($res_album_owner == $UID){
								$power = "write";
							}
							else{
								$power = "read";
							}
							//输出数据头
					
$strXml="<block>
	<info>
		<type>translate</type>
		<book>{$res_book}</book>
		<paragraph>{$currParNo}</paragraph>
		<album_id>{$res_album_id}</album_id>
		<album_guid>{$res_album_guid}</album_guid>
		<author>{$FetchText[$i]["author"]}</author>
		<editor>{$FetchText[$i]["editor"]}</editor>
		<language>{$language}</language>
		<version>{$FetchText[$i]["version"]}</version>		
		<edition>{$FetchText[$i]["edition"]}</edition>
		<level>{$FetchText[$i]["level"]}</level>
		<readonly>0</readonly>
		<power>{$power}</power>
		<id>".GUIDv4()."</id>
	</info>
	<data>";
					fwrite($myfile, $strXml);
					//查另一个表，获取段落文本。一句一条记录。有些是一段一条记录
					$table_data = "p{$book}_translate_data";
					$query = "SELECT * FROM '{$table_data}' WHERE info_id={$FetchText[$i]["id"]}";
					$aParaText = PDO_FetchAll($query);
					
					//输出数据内容					
					$par_text="";
					foreach($aParaText as $sent){
						$par_text .= "<sen><begin>{$sent["begin"]}</begin>
									<end>{$sent["end"]}</end>
									<text>{$sent["text"]}</text></sen>";
					}
					fwrite($myfile, $par_text);
					//段落块结束
					$strXml = "</data></block>";
					fwrite($myfile, $strXml);
					//获取段落文本结束。
				}
			}
					break;
				case "4"://note
					break;
				case "5":
					break;
				case "6"://逐词译模板
					$album_guid = UUID();
					$album_title = "title";
					$album_author = "VRI";
					$album_type=$get_res_type;
					//获取段落层级和标题
					$para_title=array();
					PDO_Connect("sqlite:$_file_db_pali_text");
					$query="SELECT * FROM pali_text WHERE \"book\" = ".$PDO->quote($res_book)." AND (\"paragraph\" in {$strQueryParaList} ) AND level>0 AND level<9";
					$sth = $PDO->prepare($query);
					$sth->execute();
					while($result = $sth->fetch(PDO::FETCH_ASSOC))
					{
						$paragraph = $result["paragraph"];
						$para_title["{$paragraph}"][0]=$result["level"];
						$para_title["{$paragraph}"][1]=$result["text"];
					}
						
					$db_file = "{$dir_palicanon}templet/p".$res_book."_tpl.db3";					
					PDO_Connect("sqlite:$db_file");
					foreach($aParaList as $iPar){
						$query="SELECT * FROM 'main' WHERE (\"paragraph\" = ".$PDO->quote($iPar)." ) ";
						
						$sth = $PDO->prepare($query);
						$sth->execute();
						{
							if(isset($para_title["{$iPar}"])){
								$level=$para_title["{$iPar}"][0];
								$title=$para_title["{$iPar}"][1];
							}
							else{
								$level=100;
								$title="";
							}
							$strXml="
	<block>
		<info>
			<type>wbw</type>
			<book>{$book}</book>		
			<paragraph>{$iPar}</paragraph>
			<level>{$level}</level>
			<title>{$title}</title>
			<album_id>-1</album_id>
			<album_guid>{$album_guid}</album_guid>			
			<author>{$USER_NAME}</author>
			<editor>{$USER_NAME}</editor>
			<language>en</language>
			<version>1</version>
			<edition></edition>
			<splited>0</splited>
			<id>".GUIDv4()."</id>
		</info>\n
		<data>";
						fwrite($myfile, $strXml);
						while($result = $sth->fetch(PDO::FETCH_ASSOC))
						{
							if($result["gramma"]=="?"){
								$wGrammar="";
							}
							else{
								$wGrammar=$result["gramma"];
							}
							$strXml="<word>
<pali>{$result["word"]}</pali>
<real>{$result["real"]}</real>
<id>{$result["wid"]}</id>
<type status=\"0\">{$result["type"]}</type>
<gramma status=\"0\">{$wGrammar}</gramma>
<mean status=\"0\">?</mean>
<org status=\"0\">".mb_strtolower($result["part"], 'UTF-8')."</org>
<om status=\"0\">?</om>
<case status=\"0\">{$result["type"]}#{$wGrammar}</case>
<style>{$result["style"]}</style>
<status>0</status>
</word>";
							fwrite($myfile, $strXml);
						}
							
							$strXml="</data>
									</block>";
							fwrite($myfile, $strXml);
						}
							
					}
					break;
				case "2"://wbw
					//$res_album_id;
					$album_title = "title";
					$album_author = $author;
					$album_type=$get_res_type;	
					$db_file = $dir_palicannon_wbw."p{$res_book}_wbw.db3";
					$table_info="p{$res_book}_wbw_info";
					$table_data="p{$res_book}_wbw_data";
					PDO_Connect("sqlite:$db_file");
					foreach($aParaList as $iPar){
						$query="SELECT * FROM '{$table_info}' WHERE paragraph = ".$PDO->quote($iPar)." and album_id=".$PDO->quote($res_album_id);
						//$FetchInfo = PDO_FetchAll($query);
						echo $query."<br>";
						$sth = $PDO->prepare($query);
						$sth->execute();
						echo "para:{$iPar} row:".$sth->rowCount();
						
						if($result = $sth->fetch(PDO::FETCH_ASSOC))
						{
							$lang=$sLang["{$result["language"]}"];
							$info_id=$result["id"];
							$strXml="
	<block>
		<info>
			<type>wbw</type>
			<book>{$res_book}</book>
			<paragraph>{$iPar}</paragraph>
			<level>{$result["level"]}</level>
			<title>{$result["title"]}</title>
			<album_id>{$res_album_id}</album_id>
			<album_guid>{$res_album_guid}</album_guid>	
			<author>{$result["author"]}</author>
			<language>{$lang}</language>
			<version>{$result["version"]}</version>
			<edition>{$result["edition"]}</edition>
			<id>".GUIDv4()."</id>
		</info>\n
		<data>";
							fwrite($myfile, $strXml);
							$query="SELECT * FROM \"{$table_data}\" WHERE info_id=".$PDO->quote($info_id);
							$sth = $PDO->prepare($query);
							$sth->execute();
							while($result = $sth->fetch(PDO::FETCH_ASSOC))
							{
								$wid="p{$res_book}-{$iPar}-{$result["sn"]}";
								$strXml="<word>
<pali>{$result["word"]}</pali>
<real>{$result["real"]}</real>
<id>{$wid}</id>
<type>{$result["type"]}</type>
<gramma>{$result["gramma"]}</gramma>
<mean>{$result["mean"]}</mean>
<note>{$result["note"]}</note>
<org>{$result["part"]}</org>
<om>{$result["partmean"]}</om>
<case>{$result["type"]}#{$result["gramma"]}</case>
<style>{$result["style"]}</style>
<enter>{$result["enter"]}</enter>
<status>0</status>
</word>";
								fwrite($myfile, $strXml);
							}
							
							$strXml="</data>
									</block>";
							fwrite($myfile, $strXml);
						}
							
					}
					break;
			}
			/*查询结束*/
		}
		/*
		自动新建译文
		*/
		if(isset($_POST["new_tran"])){
			$new_tran=$_POST["new_tran"];
			if($new_tran=="on"){
				$album_guid = UUID();
				foreach($aParaList as $iPar){
				$strXml="
	<block>
		<info>
			<album_id>-1</album_id>
			<album_guid>{$album_guid}</album_guid>
			<type>translate</type>
			<paragraph>{$iPar}</paragraph>
			<book>{$book}</book>
			<author>{$author}</author>
			<language>en</language>
			<version>0</version>
			<edition>0</edition>
			<id>".UUID()."</id>
		</info>
		<data>
			<sen><begin></begin><end></end><text>new translate</text></sen>
		</data>
	</block>
		";
		fwrite($myfile, $strXml);
				}
			}
		}
		$strXml="    </body>\n";
		$strXml.="</set>\n";
		fwrite($myfile, $strXml);
		fclose($myfile);
		echo "<p>save ok</p>";
		$filesize=filesize($sFullFileName);
		//服务器端文件列表
		$db_file = $_file_db_fileindex;
		PDO_Connect("sqlite:$db_file");
		$query="INSERT INTO fileindex ('id','userid','doc_id','book','paragraph','file_name','title','tag','create_time','modify_time','accese_time','file_size') 
						VALUES (NULL,?,?,?,?,?,?,?,?,?,?,?)";
		$stmt = $PDO->prepare($query);
		$newData=array($uid,GUIDv4(),$book,$create_para,$FileName,$user_title,$tag,time(),time(),time(),$filesize);
		$stmt->execute($newData);
		if (!$stmt || ($stmt && $stmt->errorCode() != 0)) {
			$error = PDO_ErrorInfo();
			echo "error - $error[2] <br>";
		}
		else{
			//获取刚刚插入的索引号
			$file_index=$PDO->lastInsertId();
			echo "updata 1 recorders.";
		}
		echo "<a href=\"editor.php?op=open&fileid={$file_index}&filename={$FileName}\">正在跳转</a>";
		echo "<script>";
		echo "window.location.assign(\"editor.php?op=open&fileid={$file_index}&filename=$FileName\");";
		echo "</script>";
		break;
	}
	case "open":
	{
	/*打开工程文件
	三种情况
	1.自己的文档
	2.别人的共享文档，自己以前没有打开过。复制到自己的空间，再打开。
	3.别人的共享文档，自己以前打开过。直接打开
	*/
		if($_COOKIE["uid"]){
			$uid=$_COOKIE["uid"];
		}
		else{
			echo "尚未登录";
			exit;
		}	
		$db_file = $_file_db_fileindex;
		PDO_Connect("sqlite:$db_file");
		if(isset($_GET["doc_id"])){
			$doc_id=$_GET["doc_id"];
			$query = "select * from fileindex where doc_id='$doc_id' ";
			$Fetch = PDO_FetchAll($query);
			$iFetch=count($Fetch);
			if($iFetch>0){
				//文档信息
				$owner=$Fetch[0]["userid"];
				$filename=$Fetch[0]["file_name"];
				$title=$Fetch[0]["title"];
				$tag=$Fetch[0]["tag"];
				$mbook=$Fetch[0]["book"];
				$paragraph=$Fetch[0]["paragraph"];
				
				if($owner==$uid){
					//自己的文档
					echo "自己的文档，正在打开...";
					$my_doc_id=$doc_id;
					$my_file_name=$Fetch[0]["file_name"];
				}
				else{
					//别人的文档
					//查询自己是否以前打开过
					$query = "select * from fileindex where parent_id='$doc_id' and userid='$uid' ";
					$FetchSelf = PDO_FetchAll($query);
					$iFetchSelf=count($FetchSelf);
					if($iFetchSelf>0){
						//以前打开过
						echo "共享的文档，正在打开...";
						$my_doc_id=$FetchSelf[0]["doc_id"];
						$my_file_name=$FetchSelf[0]["file_name"];
					}
					else{
						//以前没打开过
						//询问是否打开
						if(isset($_GET["openin"])){
							$open_in=$_GET["openin"];
						}
						else{
						?>
						<p>这是PCS共享文档，您是否要打开？</p>
						<div>
						文档信息：
						<ul>
						<?php
						$book_name=$book["p".$mbook];
						echo "<li>文档主人：{$owner}</li>";
						echo "<li>文档标题：{$title}</li>";
						echo "<li>书名：{$book_name}</li>";
					?>				
						</ul>
						</div>
						<p>打开方式：</p>
						<ul>
						<li><a href="../pcdl/reader.php?file=<?php echo $doc_id;?>">阅读器中打开（只读）</a></li>
						<li><a href="../studio/project.php?op=open&doc_id=<?php echo $doc_id;?>&openin=editor">复制到我的空间用编辑器打开</a></li>
						</ul>
						<?php
						exit;
						}
						if($open_in=="editor"){
							//获取文件路径
							echo "共享的文档，复制并打开...";
							$db_file = $_file_db_userinfo;
							PDO_Connect("sqlite:$db_file");
							$query = "select userid from user where id='$owner'";
							$FetchUid = PDO_FetchOne($query);
							if($FetchUid){
								$source=$dir_user_base.$FetchUid.$dir_mydocument.$filename;
								$dest=$dir_user_base.$userid.$dir_mydocument.$filename;
							}						
							if(copy($source,$dest)){
								echo "复制文件成功";
								$my_file_name=$filename;
								//插入记录到文件索引
								$filesize=filesize($dest);
								//服务器端文件列表
								$db_file = $_file_db_fileindex;
								PDO_Connect("sqlite:$db_file");
								$query="INSERT INTO fileindex ('id','userid','parent_id','doc_id','book','paragraph','file_name','title','tag','create_time','modify_time','accese_time','file_size') 
												VALUES (NULL,?,?,?,?,?,?,?,?,?,?,?,?)";
								$stmt = $PDO->prepare($query);
								$newData=array($uid,$doc_id,GUIDv4(),$mbook,$paragraph,$filename,$title,$tag,time(),time(),time(),$filesize);
								$stmt->execute($newData);
								if (!$stmt || ($stmt && $stmt->errorCode() != 0)) {
									$error = PDO_ErrorInfo();
									echo "error - $error[2] <br>";
									$my_file_name="";
								}
								else{
									//获取刚刚插入的索引号
									$file_index=$PDO->lastInsertId();
									echo "updata 1 recorders.";
								}

							}
							else{
								echo "复制文件失败";
								$my_file_name="";
							}
						}
						else{
							echo "错误-无法识别的操作：open in:{$open_in}";
							$my_file_name="";
						}
					}
					
				}
				if($my_file_name!=""){
					echo "<script>";
					echo "window.location.assign(\"editor.php?op=open&fileid={$file_index}&filename=$my_file_name\");";
					echo "</script>";
					
				}
			}
			else{
				echo "未知的文档。可能该文件已经被删除。";
			}
		}
	}
	break;
	case "openfile":
	break;
	case "save":
	break;
}
?>
</body>