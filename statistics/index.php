<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>
		.toc_1{
			padding-left:0;
		}
		.toc_2{
			padding-left:1em;
		}
		.toc_3{
			padding-left:2em;
		}
		.toc_4{
			padding-left:3em;
		}

	</style>
</head>	
<body>
<?php
require_once "../public/_pdo.php";
require_once "../path.php";
require_once "../public/load_lang.php";

if(isset($_GET["language"])){
	$language=$_GET["language"];
}
else{ 
	$language="en";
}

include "./language/".$language.".php";
echo "{$language}<br>";
	$toc=array();
	
	if(($handle=fopen("./book_index_".$language.".csv",'r'))!==FALSE){
		while(($data=fgetcsv($handle,0,','))!==FALSE){
			array_push($toc,$data);
			if($data[2]>0){
				$bookid=$data[2];
				$toc_book["$bookid"]=$data[1];
			}
		}
	}

echo "<h2>";
echo $gui['word_analysis'];
echo "</h2>";

if(isset($_GET["spell"])){
	$spell=$_GET["spell"];
}
else{
	$spell="";
}
if(isset($_GET["wordop"])){
	$wordop=$_GET["wordop"];
}
else{
	$wordop="=";
}
if(!isset($_GET["groupby"])){

?>
<form action="index.php" method="get">
	<input type="hidden" name="language" value="<?php echo $language ?>" />
<input type="submit" value="<?php echo $gui['submit'] ?>"><br>
<?php echo $gui['spell'] ?>：
<select name="wordop">
	<option value="like" ><?php echo $gui['like'] ?></option>
</select>
<input type="text" name="spell" value="" /><br>
<?php echo $gui['group_by'] ?>：
<select name="groupby">
	<option value="base" ><?php echo $gui['base'] ?></option>
	<option value="word" ><?php echo $gui['spell'] ?></option>
	<option value="end1" ><?php echo $gui['ending_1'] ?></option>
	<option value="end2" ><?php echo $gui['ending_2'] ?></option>
</select><br>
<?php
$arrlength=count($toc);

echo "<ul>";
for($x=0;$x<$arrlength;$x++) {
  echo "<li class='toc_".$toc[$x][0]."'><input type='checkbox' name='".$toc[$x][2]."' onchange=\"chk_change(this)\" checked />".$toc[$x][1]."</li>";
}
echo "</ul>";
?>
<input type="submit">
</form>
<?php
return;
}

$groupby=$_GET["groupby"];

echo "<a href='index.php?language=$language'>".$gui['home']."</a><br>";
echo $gui['your_choice']."：</br>";

$boolList=array();
for($i=1;$i<218;$i++){
	if(isset($_GET["$i"])){
		if($_GET["$i"]=="on"){
			array_push($boolList,"$i");
			echo $toc_book["$i"]."; ";
		}
	}
}
echo "</br>";

$countInsert=0;
$wordlist=array();


$db_file = _FILE_DB_STATISTICS_;
$bookstring="";
	for($i=0;$i<count($boolList);$i++)
	{
		$bookstring.="'".$boolList[$i]."'";
		if($i<count($boolList)-1){
			$bookstring.=",";
		}
	}

		//open database
	PDO_Connect("sqlite:$db_file");
	if($spell==""){
		echo("<h3>".$gui['group_by']."：$groupby</h3>");
		$query = "SELECT count(*) FROM \"word\" WHERE (bookid in (".$bookstring.")) ";/*查總數*/
		$count_word=PDO_FetchOne($query);
		$query = "SELECT sum(count) FROM \"word\" WHERE (bookid in (".$bookstring.")) ";/*查總數，并分類匯總*/
		$sum_word=PDO_FetchOne($query);
		$query = "select count(*) from (SELECT count() FROM \"word\" WHERE (bookid in (".$bookstring.") and $groupby<>'') group by $groupby )";/*查總數，并分類匯總*/
		$count_parent=PDO_FetchOne($query);

		$query = "select sum(length) from (SELECT * FROM \"word\" WHERE (bookid in (".$bookstring.") and $groupby<>'') group by $groupby )";/*查總數，并分類匯總*/
		$count_parent1=PDO_FetchOne($query);

		$query = "SELECT sum(count) FROM \"word\" WHERE (bookid in (".$bookstring.") and  $groupby<>'') ";/*查總數，并分類匯總*/
		$sum_parent=PDO_FetchOne($query);
		$format=number_format($count_word);
		echo $gui['vacab']."：$format<br>";
		echo $gui['word_count']."：".number_format($sum_word)."<br> ";
		echo "<b>$groupby</b>".$gui['statistics']."：".number_format($count_parent)."<br> ";
		echo "<b>$groupby</b>字母".$gui['statistics']."：".number_format($count_parent1)."<br> ";
		echo $gui['effective']."：".number_format($sum_parent)." <br>";
		$query = "select * from (SELECT $groupby,sum(count) as wordsum FROM \"word\" WHERE (bookid in (".$bookstring.") and $groupby<>'') group by $groupby) order by wordsum DESC limit 0 ,4000";/*查總數，并分類匯總*/
		$Fetch = PDO_FetchAll($query);
		$iFetch=count($Fetch);
		echo "<table>";
		echo "<th>".$gui['serial_num']."</th><th>$groupby</th><th>".$gui['count']."</th><th>".$gui['percentage']."</th><th>".$gui['accumulative']."</th>";
		$sum_prsent=0;
		if($iFetch>0){
			$sum=$Fetch[0]["wordsum"];
			$first=$sum*100/$sum_word;
			
			for($i=0;$i<$iFetch;$i++){
				$sum=$Fetch[$i]["wordsum"];
				$prsent=$sum*100/$sum_word;
				$prsent1=$prsent*100/$first;
				$sum_prsent+=$prsent;
				echo "<tr>";
				echo "<td>".($i+1)."</td><td>".$Fetch[$i][$groupby]."</td><td>".number_format($sum)."</td><td><span style='width:".$prsent1."px;background-color:red;'></span><span style:'width:".(100-$prsent1)."px;background-color:blue;'></span>".number_format($prsent,3)."</td><td>".number_format($sum_prsent,1)."</td>";
				echo "</tr>";
			}
		}
		echo "</table>";
	}
	else{
		echo("<h3>Word: <spen style='color:blue;'>$spell</spen></h3>");
		$newSpell=$PDO->quote($spell);
		$query = "SELECT count(*) FROM \"word\" WHERE (word $wordop $newSpell) ";/*查總词數*/
		$count_word=PDO_FetchOne($query);
		$query = "SELECT sum(count) FROM \"word\" WHERE (word $wordop $newSpell) ";/*查總數，并分類匯總*/
		$sum_word=PDO_FetchOne($query);

		echo "单词总个数：".number_format($count_word)."<br>";
		echo "单词总数：".number_format($sum_word)."<br>";
		
		$query = "select count(*) from (SELECT bookid FROM \"word\" WHERE (word $wordop $newSpell) group by bookid) ";/*查總數，并分類匯總*/
		$in_book_count=PDO_FetchOne($query);		
		echo "<p>In $in_book_count Books</p>";
		$query = "select bookid,co,su from (SELECT bookid,sum(count) as su,count(*) co FROM \"word\" WHERE (word $wordop $newSpell) group by bookid) order by co DESC  limit 0 ,20";/*查總數，并分類匯總*/
		$Fetch = PDO_FetchAll($query);
		$iFetch=count($Fetch);
		echo "<table>";
		echo "<th>序号</th><th>Book</th><th>单词个数</th><th>单词数量</th>";
		if($iFetch>0){	
			for($i=0;$i<$iFetch;$i++){
				echo "<tr>";
				echo "<td>".($i+1)."</td><td>".$toc_book[$Fetch[$i]["bookid"]]."</td><td>".number_format($Fetch[$i]["co"])."</td><td>".number_format($Fetch[$i]["su"])."</td>";
				echo "</tr>";
			}
		}
		echo "</table>";		

		$query = "select count(*) from (SELECT bookid FROM \"word\" WHERE (word $wordop $newSpell) group by word) ";/*查總數，并分類匯總*/
		$word_count=PDO_FetchOne($query);		
		echo "<p>$word_count Words</p>";
		$query = "select word,co from (SELECT word,sum(count) co FROM \"word\" WHERE (word $wordop $newSpell) group by word) order by co DESC  limit 0 ,100";/*查總數，并分類匯總*/
		$Fetch = PDO_FetchAll($query);
		$iFetch=count($Fetch);
		echo "<table>";
		echo "<th>序号</th><th>Word</th><th>数量</th>";
		if($iFetch>0){	
			for($i=0;$i<$iFetch;$i++){
				echo "<tr>";
				echo "<td>".($i+1)."</td><td>".$Fetch[$i]["word"]."</td><td>".number_format($Fetch[$i]["co"])."</td>";
				echo "</tr>";
			}
		}
		echo "</table>";		

	}


/*查询结束*/


?>

</body>
</html>