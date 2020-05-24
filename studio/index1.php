<?php
require 'checklogin.inc';
require 'config.php';

if(isset($_GET["language"])){
	$currLanguage=$_GET["language"];
}
else{
	if(isset($_COOKIE["language"])){
		$currLanguage=$_COOKIE["language"];
	}
	else{
		$currLanguage="en";
	}
}

//load language file
if(file_exists($dir_language.$currLanguage.".php")){
	require $dir_language.$currLanguage.".php";
}
else{
	include $dir_language."default.php";
}

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
	<link type="text/css" rel="stylesheet" href="css/style.css"/>
	<link type="text/css" rel="stylesheet" href="css/color_day.css" id="colorchange" />
	<title><?php echo $module_gui_str['editor']['1051'];?>PCD Studio</title>
	<script language="javascript" src="js/common.js"></script>
	<script language="javascript" src="js/filenew.js"></script>
	<script src="js/jquery-3.3.1.min.js"></script>
	<script src="js/fixedsticky.js"></script>
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

		var xmlhttp;
		function showUserFilaList()
		{

		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		var d=new Date();
		xmlhttp.onreadystatechange=serverResponse;
		xmlhttp.open("GET","getfilelist.php?t="+d.getTime(),true);
		xmlhttp.send();
		}

		function serverResponse()
		{
			
			if (xmlhttp.readyState==4)// 4 = "loaded"
			{
			  if (xmlhttp.status==200)
				{// 200 = "OK"
				var arrFileList = xmlhttp.responseText.split(",");
				var fileList="";
				var dirUserRoot="<?php echo $dir_user_base; ?>";
				var dirMyDoc=dirUserRoot + getCookie("userid") + "<?php echo $dir_mydocument; ?>/";
				for (x in arrFileList){
					fileNamePart=arrFileList[x].split(".");
					extName=fileNamePart[fileNamePart.length-1];
					switch(extName){
						case "xml":
							fileList += "<p><a href=\"editor.php?op=import&filename="+dirMyDoc+arrFileList[x]+"&device="+g_device+"&language=<?php echo $currLanguage; ?>\" target=\"_blank\"><?php echo $module_gui_str['editor']['1085'];?></a>"+arrFileList[x]+"</p>"
							break;
						case "pcs":
							fileList += "<p><a href=\"editor.php?op=open&filename="+dirMyDoc+arrFileList[x]+"&device="+g_device+"&language=<?php echo $currLanguage; ?>\" target=\"_blank\">"+arrFileList[x]+"</a><a herf='"+dirMyDoc+arrFileList[x]+"' target=\"_blank\"><svg class=\"icon\"><use xlink:href=\"svg/icon.svg#ic_file_download\"></use></svg></a></p>"
							break;
					}
					
				}
				
				document.getElementById('userfilelist').innerHTML=fileList;
				}
			  else
				{
				document.getElementById('userfilelist')="Problem retrieving data:" + xmlhttp.statusText;
				}
			}
		}
		
			var g_langrage="en";
			function menuLangrage(obj){
				g_langrage=obj.value;
				setCookie('language',g_langrage,365);
				window.location.assign("index.php?language="+g_langrage);
			}

	</script>

</head>
<body class="indexbody" onLoad="indexInit()">
		<!-- tool bar begin-->
		<div class='index_toolbar'>
			<div id="index_nav">
				<button class="selected" ><?php echo $module_gui_str['editor']['1018'];?></button>
				<button><a href="index_pc.php?language=<?php echo $currLanguage; ?>"><?php echo $module_gui_str['editor_wizard']['1002'];?></a></button>
				<button><a href="filenew.php?language=<?php echo $currLanguage; ?>"><?php echo $module_gui_str['editor']['1064'];?></a></button>
				<button><a href="index_tools.php?language=<?php echo $currLanguage; ?>"><?php echo $module_gui_str['editor']['1052'];?></a></button>
			</div>
			<div>
			
			</div>
			<div class="toolgroup1">
				<span><?php echo $module_gui_str['editor']['1050'];?></span>
				<select id="id_language" name="menu" onchange="menuLangrage(this)">
					<option value="en" >English</option>
					<option value="sinhala" >සිංහල</option>
					<option value="zh" >简体中文</option>
					<option value="tw" >繁體中文</option>
				</select>
			
			<?php 
				echo $module_gui_str['editor']['1049'];
				echo "<a href=\"setting.php?item=account\">";
				echo urldecode($_COOKIE["nickname"]);
				echo "</a>";
				echo $module_gui_str['editor']['1042'];
				echo "<a href='login.php?op=logout'>";
				echo $module_gui_str['editor']['1089'];
				echo "</a>";
			?>
			
			</div>
		</div>	
		<!--tool bar end -->
		<script>
			document.getElementById("id_language").value="<?php echo($currLanguage); ?>";
		</script>
	<div class="index_inner">
		<div id="id_app_name"><?php echo $module_gui_str['editor']['1051'];?>
			<span style="font-size: 70%;">1.6</span><br />
			<?php if($currLanguage=="en"){ ?>
				<span style="font-size: 70%;">Pali Cannon Database Studio</span>
			<?php 
			}
			else{
			?>
				<span style="font-size: 70%;">PCD Studio</span>
			<?php
			}
			?>
		</div>
				
		<div class="fun_block">
			<table width="100%">
				<tr>
					<td width="90%"><h2><?php echo $module_gui_str['editor']['1058'];?></h2></td>
					<td width="10%"><?php echo $module_gui_str['editor']['1059'];?>
						<select id="id_index_orderby"  onchange="index_orderby(this)">
							<option value="accessed" ><?php echo $module_gui_str['editor']['1060'];?></option>
							<option value="modified" ><?php echo $module_gui_str['editor']['1061'];?></option>
							<option value="created" ><?php echo $module_gui_str['editor']['1062'];?></option>
							<option value="caption" ><?php echo $module_gui_str['editor']['1063'];?></option>
						</select>					
					</td>
				</tr>
			</table>
			<!--<p><a href="editor.php?op=new&language=<?php echo $currLanguage; ?>&device=<?php echo $currDevice; ?>"><?php echo $module_gui_str['editor']['1064'];?></a></p>-->
			<div id="userfilelist">
			<?php echo $module_gui_str['editor']['1065'];?>
			</div>
		</div>
		
	</div>
<div class="foot_div">
<?php echo $module_gui_str['editor']['1066'];?>
</div>
</body>
</html>

