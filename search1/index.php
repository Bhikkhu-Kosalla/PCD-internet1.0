<?php
require_once '../public/config.php';
require_once "../public/load_lang.php";

if(isset($_GET["device"])){
	$currDevice=$_GET["device"];
}
else{
	if(isset($_COOKIE["device"])){
		$currDevice=$_COOKIE["device"];
	}
	else{
		$currDevice="computer";
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link type="text/css" rel="stylesheet" href="../studio/css/style.css"/>
	<link type="text/css" rel="stylesheet" href="../studio/css/color_day.css" id="colorchange" />
	<link type="text/css" rel="stylesheet" href="./css/style.css"/>
	<title><?php echo $_local->gui->pcd_studio;?></title>
	<script language="javascript" src="../studio/js/common.js"></script>
	<script language="javascript" src="js/search.js"></script>
	<script language="javascript" src="../pali_sent/pali_sent.js"></script>
	<script src="../public/js/jquery.js"></script>
	<script src="../studio/js/fixedsticky.js"></script>
	<script type="text/javascript">
	
		var g_device = "computer";
		var strSertch = location.search;
		if(strSertch.length>0){
			strSertch = strSertch.substr(1);
			var sertchList=strSertch.split('&');
			for ( i in sertchList){
				var item = sertchList[i].split('=');
				if(item[0]=="device"){
					g_device=item[1];
				}
			}
		}
		if(g_device=="mobile"){
			g_is_mobile=true;
		}
		else{
			g_is_mobile=false;
		}

		var g_langrage="en";
			function menuLangrage(obj){
				g_langrage=obj.value;
				setCookie('language',g_langrage,365);
				window.location.assign("index.php?language="+g_langrage);
			}

	</script>
<style>
#pre_search_result {
    position: absolute;
    background-color: var(--btn-hover-bg-color);
    border: 1px solid var(--btn-border-line-color);
    border-radius: 5px;
    max-width: 100%;
    width: 50em;
}
.pre_serach_block{
	border-bottom: 1px solid var(--shadow-color);
    padding: 5px 8px;
}
.pre_serach_block_title{
	display:flex;
	justify-content: space-between;
}
.pre_serach_content{
	padding: 4px 4px 4px 15px;
}
</style>
</head>
	<body class="indexbody" onLoad="">
		<!-- tool bar begin-->
		<div class='index_toolbar' style=" height: initial;">

			<div>		
				<div style="display:flex;">
					<select id="search_type" name="menu" style="display:inline-block" >
						<option value="all" ><?php echo $_local->gui->all;?></option>
						<option value="pali" >
							<?php echo $_local->gui->pali_canon;?>
							</option>
						<option value="bold" >
							<?php echo $_local->gui->vannana;?>
						</option>
						<option value="tran" >
							<?php echo $_local->gui->translate;?>
						</option>
					</select>
					<div>
						<div>
							<input id="dict_ref_search_input" type="input" placeholder=<?php echo $_local->gui->serach;?> onkeyup="search_input_keyup(event,this)" style="margin-left: 0.5em;width: 40em;max-width: 80%" onfocus="search_input_onfocus()">
						</div>
						<div id="pre_search_result">
							<div id="pre_search_chapter" class="pre_serach_block">
								<div id="pre_search_chapter_title"   class="pre_serach_block_title">
									<div id="pre_search_chapter_title_left">Chapter</div>
									<div id="pre_search_chapter_title_right"></div>
								</div>
								<div id="pre_search_chapter_content"   class="pre_serach_content">
								</div>
							</div>
							<div id="pre_search_sent"  class="pre_serach_block">
								<div id="pre_search_sent_title"   class="pre_serach_block_title">
									<div id="pre_search_sent_title_left">全文搜索</div>
									<div id="pre_search_sent_title_right"></div>								
								</div>
								<div id="pre_search_sent_content"   class="pre_serach_content">
								</div>
							</div>
							<div id="pre_search_word"  class="pre_serach_block">
								<div id="pre_search_word_title"   class="pre_serach_block_title">
									<div id="pre_search_word_title_left">单词</div>
									<div id="pre_search_word_title_right"></div>								
								</div>
								<div id="pre_search_word_content"   class="pre_serach_content">
								</div>
							</div>			
						</div>					
					</div>
				</div>
				<div style="display:block;">
					<ul id="dict_type" class="tab_a" >
					</ul>
				</div>
			</div>
			<div >
				<span><?php echo $_local->gui->language;?></span>
				<select id="id_language" name="menu" onchange="menuLangrage(this)">
					<option value="en" >English</option>
					<option value="si" >සිංහල</option>
					<option value="my" >缅甸语</option>
					<option value="zh-cn" >简体中文</option>
					<option value="zh-tw" >繁體中文</option>
				</select>
			</div>
		</div>	
		<!--tool bar end -->
		<script>
			document.getElementById("id_language").value="<?php echo($currLanguage); ?>";
		</script>
	<div >
		<!--  查词工具 拆分 -->
		<div><span id="input_parts"><span></div>
	</div>

		<div id="dict_ref_search_result" style="background-color:white;color:black;">
		</div>
	
		<div class="foot_div">
		<?php echo $_local->gui->poweredby;?>
		</div>
	</body>
</html>

