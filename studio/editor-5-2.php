<?php
require 'checklogin.inc';
require_once '../public/config.php';
require_once '../public/load_lang.php';

//load language file
if(file_exists($dir_language.$currLanguage.".php")){
	require $dir_language.$currLanguage.".php";
}
else{
	include $dir_language."default.php";
}


if(isset($_GET["device"])){$currDevice=$_GET["device"];}
else{$currDevice="computer";}

//require "module/editor/language/$currLanguage.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link type="text/css" rel="stylesheet" href="css/style.css"/>
	<link type="text/css" rel="stylesheet" href="css/color_day.css" id="colorchange" />
	<link type="text/css" rel="stylesheet" href="css/style_mobile.css" media="screen and (max-width:767px)">
	<link type="text/css" rel="stylesheet" href="../public/css/notify.css"/>
	<?php
		if(file_exists($dir_user_base.$userid.$dir_myApp."/style.css")){
			echo "<link type=\"text/css\" rel=\"stylesheet\" href=\"{$dir_user_base}{$userid}{$dir_myApp}/style.css\"/>";
		}
	?>
	<title id="file_title"><?php echo $_local->gui->pcd_studio; ?></title>
	<script language="javascript" src="config.js"></script>
	<script language="javascript" src="js/data.js"></script>
	<script language="javascript" src="js/common.js"></script>
	<script language="javascript" src="js/render.js"></script>	
	<script language="javascript" src="js/xml.js"></script>
	<script language="javascript" src="js/editor.js"></script>
	<script language="javascript" src="js/wizard.js"></script>
	<script language="javascript" src="js/wordmap.js"></script>
	<script language="javascript" src="js/dict.js"></script>
	<script language="javascript" src="js/relation.js"></script>
	<script language="javascript" src="js/relation_list.json"></script>
	
	<script language="javascript" src="../public/js/notify.js"></script>
	<script language="javascript" src="../public/script/my.js"></script>
	
	<script language="javascript" src="module/editor/language/default.js"></script>	
	<script src="js/jquery-3.3.1.min.js"></script>
	<script src="../public/js/term.js"></script>
	<script src="./js/message.js"></script>
	<script src="./sent/sent.js"></script>
	<script language="javascript" src="module/editor/language/<?php echo $currLanguage; ?>.js"></script>
	
	<script language="javascript" src="module/editor_palicannon/palicannon.js"></script>
	<script language="javascript" src="module/editor_palicannon/language/<?php echo $currLanguage; ?>.js"></script>
	<script language="javascript" src="module/editor_toc/module_function.js"></script>
	<script language="javascript" src="module/editor_toc/language/<?php echo $currLanguage; ?>.js"></script>
	<script language="javascript" src="module/editor_bookmark/module_function.js"></script>
	<script language="javascript" src="module/editor_bookmark/language/<?php echo $currLanguage; ?>.js"></script>
	<script language="javascript" src="module/editor_layout/module_function.js"></script>
	<script language="javascript" src="module/editor_layout/language/<?php echo $currLanguage; ?>.js"></script>
	<script language="javascript" src="module/editor_project/module_function.js"></script>
	<script language="javascript" src="module/editor_project/language/<?php echo $currLanguage; ?>.js"></script>
	<script language="javascript" src="module/editor_dictionary/module_function.js"></script>
	<script language="javascript" src="module/editor_dictionary/language/<?php echo $currLanguage; ?>.js"></script>
	
	<script language="javascript" src="module/editor_plugin/module_function.js"></script>
	<script language="javascript" src="module/editor_plugin/language/<?php echo $currLanguage; ?>.js"></script>

	<script language="javascript" src="note/note.js"></script>
	<!--<script language="javascript" src="<?php //echo $dir_user_base.$userid.$dir_myApp; ?>/dictlist.json"></script>-->

	<script language="javascript">
	<?php 
	//加载js语言包
	//require_once '../public/load_lang_js.php';
	?>
	<?php
	//加载js语言包
	if(file_exists($_dir_lang.$currLanguage.".json")){
		echo "var gLocal = ".file_get_contents($_dir_lang.$currLanguage.".json").";";
	}
	else{
		echo "var gLocal = ".file_get_contents($_dir_lang."default.json").";";
	}
	?>
		var gDownloadListString="<?php echo str_replace('"','\"',file_get_contents("dl.json"));?>";
		
		var g_device="computer";
		var strSertch = location.search;
		var gConfigDirMydocument="<?php echo $dir_user_base.$userid.$dir_mydocument; ?>/";
		
		if(strSertch.length>0){
			strSertch = strSertch.substr(1);
			var sertchList=strSertch.split('&');
			for (x in sertchList){
				var item = sertchList[x].split('=');
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
		
		var gCaseTable=<?php echo file_get_contents("..\public\js\case.json"); ?>
	</script>
</head>
<body class="mainbody" id="mbody" onLoad="editor_windowsInit()">
	<style id="display_set">
	</style>
	<!-- tool bar begin-->
	<div id="btn_close_printprev">
		<a href="#" onclick="printpreview(false)">&nbsp;&nbsp; </a>
	</div>
	
	<div id='toolbar'>
		<button id="B_Navi" class="icon_btn" onclick="setNaviVisibility()" type="button">
				<svg class="icon">
					<use xlink:href="svg/icon.svg#ic_menu"></use>
				</svg>
		</button>
		<span>
			<span id="net_up"></span><span id="net_down"></span>
			<span id="msg_tool_bar"></span>
		</span>
		<span id="editor_doc_title"></span>
		<span id="editor_doc_notify"></span>
		<div id="menu_button_home"></div><!--
		<button id="menu_button_home" class="icon_btn" onclick="goHome()" type="button" title="<?php echo $module_gui_str['editor']['1001'];?>">
				<svg class="icon">
					<use xlink:href="svg/icon.svg#ic_home"></use>
				</svg>
		</button> -->
		
		<span class="dropdown toolbtn" onmouseover="switchMenu(this,'topTools')">
			<div>
			<button class="icon_btn" onClick="switchMenu(this,'topTools')" id="tools_view">
				<svg class="icon">
					<use xlink:href="svg/icon.svg#ic_toc"></use>
				</svg>
			</button>
			</div>
		</span>
		<div class="toolgroup1" id="topTools">
			<div id="load_progress_div">

			<svg id="circleProcess" xmlns="http://www.w3.org/2000/svg">
				<circle id="circle" cx="50%" cy="50%" r="32%" ></circle>
			</svg>
			</div>		
			<div class="dropdown" onmouseover="switchMenu(this,'code_list')" onmouseout="hideMenu()">
				<div style="">
					<button class="dropbtn icon_btn">
						<?php echo $module_gui_str['editor']['1099']; ?>
						<svg class="small_icon">
							<use xlink:href="svg/icon.svg#ic_down"></use>
						</svg>
					</button>
				</div>
				<span>
				</span>
				<div class="dropdown-content black_background" id="code_list" style="width:auto">
					<div class="code_list_dropdown" style="margin-left: 0.5em;margin-right: 0.5em;margin-top: 0.4em;margin-bottom: 0.2em;height:2em;width: auto">
							<span style="margin-left:auto; display:flex; align-items: center; height:2em; width:auto;">							<?php echo $module_gui_str['editor_layout']['1005']; ?>：</span>
							<select id="code_list0" onchange="layoutWordHeadCode(0,this)" style="height:2em;width: auto">
								<option value="org">Pāḷi Roman</option>
								<option value="si_c">සින්හල</option>
								<option value="myanmar">myanmar</option>
								<option value="telugu">తెలుగు</option>
							</select>
					</div>
					<div class="code_list_dropdown" style="margin-left: 0.5em;margin-right: 0.5em;margin-top: 0.2em;margin-bottom: 0.4em;height:auto;width: auto">
							<span style="margin-left:auto; display:flex; align-items: center; height:auto; width: auto">							<?php echo $module_gui_str['editor_layout']['1006'];//次要編碼 ?>：</span>
							<select id="code_list1" onchange="layoutWordHeadCode(1,this)" style="height:2em;width: auto" >
								<option value="none">None</option>
								<option value="org">Pāḷi Roman</option>
								<option value="myanmar">myanmar</option>
								<option value="si_c">සින්හල</option>
								<option value="zh">简体中文</option>
								<option value="tw">正體中文</option>
								<option value="telugu">తెలుగు</option>
							</select>
					</div>
				</div>
			</div>
			<span class=border_right></span>
				<div id="usual_tools_div" class="dropdown" onmouseover="switchMenu(this,'usual_tools')" onmouseout="hideMenu()">
						<button class="dropbtn icon_btn" onclick="switchMenu(this,'usual_tools')" id="view_setting" title="View" >
							<svg class="icon">
								<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_build"></use>
							</svg>
							<svg class="small_icon">
								<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_down"></use>
							</svg>
						</button>
						<div class="dropdown-content black_background" id="usual_tools" style="display: none;">
							<a ><span>
								<button id="magic_dict" style="color: var(--btn-color);" onclick="menu_dict_match1()">
									<?php echo $module_gui_str['editor']['1100']; //解析神器?>
								<svg class="icon">
									<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_youtube_searched_for"></use>
								</svg>
								</button>
							</span></a>
							<a ><span>
								<button type="button" style="color: var(--btn-color);" onclick="splitAll()">
									<?php echo $module_gui_str['editor']['1106']; //拆詞神器?>
									<svg class="icon">
										<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_content_cut"></use>
									</svg>
								</button>
							</span></a>
							<a ><span>
								<button type="button" style="color: var(--btn-color);" onclick="magic_sentence_cut()">
									<?php echo $module_gui_str['editor']['1105']; //切句神器?>
									<svg class="icon">
										<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_content_cut"></use>
									</svg>
								</button>
							</span></a>
						</div>
				</div>
				<div class="dropdown" onmouseover="switchMenu(this,'menuMouseActSet')" onmouseout="hideMenu()">
						<button class="dropbtn icon_btn" onclick="switchMenu(this,'menuMouseActSet')" id="view_setting" title="View">
							<svg class="icon">
								<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_mouse"></use>
							</svg>
							<svg class="small_icon">
								<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_down"></use>
							</svg>
						</button>
						<div class="dropdown-content black_background" id="menuMouseActSet" style="display: none;">
							<a >
								<span  onclick="set_word_click_action('Edit_Dialog','edit')" style="display: flex; color: var(--btn-color);">
									<input id="Edit_Dialog" style="display:none;" type="checkbox" style="width: 14px; height: 14px" checked>
									<svg id="icon_Edit_Dialog_on" style="display:block;" class="icon">
										<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_assignment_on"></use>
									</svg>
									<svg id="icon_Edit_Dialog_off" style="display:none;" class="icon">
										<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_assignment_off"></use>
									</svg>
									<?php echo $module_gui_str['editor']['1103'];//點詞
									echo $module_gui_str['editor_project']['1012']; //編輯?>
								</span>
							</a>
							<a >
								<span onclick="set_word_click_action('Look_Up','lookup')" style="display: flex; color: var(--btn-color);">
									<input id="Look_Up" style="display:none;" type="checkbox" checked>
									<svg id="icon_Look_Up_on" style="display:block;" class="icon">
										<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_search"></use>
									</svg>
									<svg id="icon_Look_Up_off" style="display:none;" class="icon">
										<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_block"></use>
									</svg>
									<?php echo $module_gui_str['editor']['1103']; echo $module_gui_str['editor_dictionary']['1006'];//查詢 ?>
								</span>
							</a>
							<a >
								<span onclick="set_word_click_action('Trans_as','translate')" style="display: flex; color: var(--btn-color);">
									<input id="Trans_as" type="checkbox" style="display:none;" >
									<svg id="icon_Trans_as_on" style="display:none;" class="icon">
										<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_translate"></use>
									</svg>
									<svg id="icon_Trans_as_off" style="display:block;" class="icon">
										<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_block"></use>
									</svg>
									<?php echo $module_gui_str['editor']['1103']; echo $module_gui_str['editor']['1104'];//輸入 ?>
								</span>
							</a>
							<a >
								<span onclick="set_word_click_action('input_smart_switch','normal')" style="display: flex; color: var(--btn-color);">
									<input id="input_smart_switch" style="display:none;" type="checkbox" checked>
									<svg id="icon_input_smart_switch_on"class="icon">
										<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_keyboard"></use>
									</svg>
									<svg id="icon_input_smart_switch_off" style="display:none;" class="icon">
										<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_block"></use>
									</svg>
									<?php echo $module_gui_str['editor_project']['1042'];//智能鍵入 ?>
								</span>
							</a>
						</div>
				</div>
			<span class=border_right></span>
				<div class="dropdown" onmouseover="switchMenu(this,'menuUseMode')" onmouseout="hideMenu()">
					<div style="">
						<button class="dropbtn icon_btn" onClick="switchMenu(this,'menuUseMode')" id="use_mode">	
							<svg class="icon">
								<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_reader_mode"></use>
							</svg>
						</button>
					</div>
					<div class="dropdown-content black_background" id="menuUseMode">
						<a href="#" onclick="setUseMode('Edit')">
							<svg class="icon">
								<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_mode_edit"></use>
							</svg>
							编辑模式
						</a>
						<a href="#" onclick="setUseMode('note')">
							<svg class="icon">
								<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_reader_mode"></use>
							</svg>
							笔记模式
						</a>
						<a href="#" onclick="setUseMode('Read')">
							<svg class="icon">
								<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_reader_mode"></use>
							</svg>
							阅读模式
						</a>
					</div>
				</div>

				<button id="info_panal" class="icon_btn" type="button" onclick="setInfoPanalVisibility()" title="<?php echo $module_gui_str['editor']['1004'];?>">
					<svg class="icon">
						<use xlink:href="svg/icon.svg#ic_info_outline"></use>
					</svg>
				</button>
				
					<button id="B_FontReduce" class="icon_btn" type="button" class="btn" onclick="setPageFontSize(0.9)">
						<svg class="icon">
							<use xlink:href="svg/icon.svg#ic_zoom_out"></use>
						</svg>
					</button> 
					<button id="B_FontGain" class="icon_btn" type="button" class="btn" onclick="setPageFontSize(1.1)">
						<svg class="icon">
							<use xlink:href="svg/icon.svg#ic_zoom_in"></use>
						</svg>
					</button>
				
					<div class="dropdown" onmouseover="switchMenu(this,'menuColorMode')" onmouseout="hideMenu()">
						<button class="icon_btn" onClick="switchMenu(this,'menuColorMode')" id="color_mode" title="<?php echo $module_gui_str['editor']['1005'];?>">
						<svg class="icon">
							<use xlink:href="svg/icon.svg#ic_color_lens"></use>
						</svg>
						<svg class="small_icon">
							<use xlink:href="svg/icon.svg#ic_down"></use>
						</svg>
						</button>
						<div class="dropdown-content" id="menuColorMode">
							<a href="#" onclick="setPageColor(0)"><?php echo $module_gui_str['editor']['1006'];//白色?></a>
							<a href="#" onclick="setPageColor(1)"><?php echo $module_gui_str['editor']['1007'];//黃昏?></a>
							<a href="#" onclick="setPageColor(2)"><?php echo $module_gui_str['editor']['1008'];//夜間?></a>
						</div>
					</div>

				<div class="dropdown" onmouseover="switchMenu(this,'menuViewSet')" onmouseout="hideMenu()">
						<button class="dropbtn icon_btn" onClick="switchMenu(this,'menuViewSet')" id="view_setting" title="<?php echo $module_gui_str['editor']['1009'];?>">
							<svg class="icon">
								<use xlink:href="svg/icon.svg#ic_visibility"></use>
							</svg>
							<svg class="small_icon">
								<use xlink:href="svg/icon.svg#ic_down"></use>
							</svg>
						</button>
						<div class="dropdown-content" id="menuViewSet">
							<a id="WBW_All"><span><input onclick="set_WBW_ALL_Visibility(this)" type="checkbox" style="width: 14px; height: 14px" checked /><?php echo $module_gui_str['editor_bookmark']['1002'];//全選?></span></a>
							<a id="B_Meaning"><span><input id="WBW_B_Meaning" onclick="setMeaningVisibility(this)" type="checkbox" style="width: 14px; height: 14px" checked /><?php echo $module_gui_str['editor']['1010'];//整體含義?></span></a>
							<a id="B_Org"><span><input id="WBW_B_Org" onclick="setOrgVisibility(this)" type="checkbox" style="width: 14px; height: 14px" checked /><?php echo $module_gui_str['editor']['1011'];//拆分組成?></span></a>
							<a id="B_OrgMeaning"><span><input id="WBW_B_OrgMeaning" onclick="setOrgMeaningVisibility(this)" type="checkbox" style="width: 14px; height: 14px" checked /><?php echo $module_gui_str['editor']['1012'];//組分含義?></span></a>
							<a id="B_Gramma" style="border-bottom: 2px solid var(--btn-hover-bg-color)"><span><input id="WBW_B_Gramma" onclick="setGrammaVisibility(this)" type="checkbox" style="width: 14px; height: 14px" checked /><?php echo $module_gui_str['editor']['1013'];//語法信息?></span></a>
							<a id="B_Relation" style="border-bottom: 2px solid var(--btn-hover-bg-color)"><span><input id="WBW_B_Relation" onclick="rela_visibility(this)" type="checkbox" style="width: 14px; height: 14px" checked /><?php echo "Relation";//語法信息?></span></a>
							<a id="B_ParTranEn"><span><input onclick="setParTranEnVisibility(this)" type="checkbox" style="width: 14px; height: 14px" checked /><?php echo $module_gui_str['editor']['1014'];//English?></span></a>
							<a id="B_ParTranCn"><span><input onclick="setParTranCnVisibility(this)" type="checkbox" style="width: 14px; height: 14px" checked /><?php echo $module_gui_str['editor']['1015'];//中文?></span></a>
							
							<ul id="layout_arrange" class="tab_b" >
								<li id="layout_arrange_h" class="act" onclick="tab_click('','layout_arrange_h',setArrange,0)">横向</li>
								<li id="layout_arrange_v" onclick="tab_click('','layout_arrange_v',setArrange,1)">纵向</li>
							</ul>
						
							<ul  class="tab_b" >
								<li id="layout_pbp" class="act" onclick="tab_click('','layout_pbp',setSbs,0)">逐段</li>
								<li id="layout_sbs" onclick="tab_click('','layout_sbs',setSbs,1)">逐句</li>
								
							</ul>
						</div>
				</div>
				<span class=border_right></span>
			
				<button id="B_ApplyAuto" class="icon_btn" onclick="applyAllSysMatch()" type="button" title="apply all">
					<svg class="icon">
						<use xlink:href="svg/icon.svg#ic_done_all"></use>
					</svg>
				</button> 
		</div>
		<button id="B_Save" class="icon_btn" onclick="editor_save()" type="button" title="<?php echo $module_gui_str['editor']['1017'];?>">
			<svg class="icon">
				<use xlink:href="svg/icon.svg#ic_save"></use>
			</svg>
		</button>
		<span class=border_right></span>
		<div class="tab_a1">
			<button id="tab_rb_dict" class="icon_btn" onclick="tab_click_b('dict_ref_search','tab_rb_dict',right_panal_slide_toggle,'tab_rb_dict')"  title="<?php echo $module_gui_str['editor']['1023'];?>">
				<svg class="button_icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_find_in_page"></use></svg>
			</button>
			<button id="tab_rb_term" class="icon_btn" onclick="tab_click_b('term_dict','tab_rb_term',right_panal_slide_toggle,'tab_rb_term')"  title="<?php echo $module_gui_str['editor']['1117'];?>">
				<svg class="button_icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_scholar"></use></svg>
			</button>
			<button id="tab_rb_msg" class="icon_btn" onclick="tab_click_b('msg_panal_right','tab_rb_msg',right_panal_slide_toggle,'tab_rb_msg')"  title="<?php echo $module_gui_str['editor']['1116'];?>">
				<svg class="button_icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_question_answer"></use></svg>
			</button>
			<button id="tab_rb_download" class="icon_btn" onclick="tab_click_b('pc_res_loader','tab_rb_download',right_panal_slide_toggle,'tab_rb_download')"  title="<?php echo $module_gui_str['editor']['1118'];?>">
				<svg class="icon">
					<use xlink:href="svg/icon.svg#ic_move_to_inbox"></use>
				</svg>
			</button>
		</div>
		
	</div><span id="load_progress_num" ></span>
	<!--tool bar end -->
	
	<!--loading -->
	<svg viewBox="0 0 1000 4" id="loading_bar" xmlns="http://www.w3.org/2000/svg">
			<line x1="0" y1="2" x2="1000" y2="2" id="loading" stroke-width="5px" stroke-linecap="round"/>
	</svg>
	<!--tool bar end -->
	<style id="mycss">
	</style>
		
	<div class="main">
		<!-- leftmenu begin--> 
		<div id="leftmenuinner" class="viewswitch_off">

			<div id="menubartoolbar" style="display: none">
				<select id="id_editor_menu_select" name="menu" onchange="menuSelected(this)">
					<option value="menu_toc" selected><?php echo $module_gui_str['editor']['1019'];?></option>				
					<option value="menu_pali_cannon"><?php echo $module_gui_str['editor']['1020'];?></option>
					<option value="menu_bookmark"><?php echo $module_gui_str['editor']['1021'];?></option>
					<option value="menu_project"><?php echo $module_gui_str['editor']['1022'];?></option>
					<option value="menu_dict"><?php echo $module_gui_str['editor']['1023'];?></option>
					<option value="menu_layout"><?php echo $module_gui_str['editor']['1024'];?></option>
					<option value="menu_plugin"><?php echo $module_gui_str['editor']['1025'];?></option>
				</select>
			</div>
			<div id="menubartoolbar_New">
				<ul class="common-tab">
					<li id="menu_toc_li" class="common-tab_li_act" onclick="menuSelected_2(menu_toc,'menu_toc_li','menu' )"><?php echo $module_gui_str['editor']['1019'];?></li>
					<li id="menu_bookmark_li" class="common-tab_li" onclick="menuSelected_2(menu_bookmark,'menu_bookmark_li','menu' )"><?php echo $module_gui_str['editor']['1021'];?></li>
					<li id="menu_project_li" class="common-tab_li" onclick="menuSelected_2(menu_project,'menu_project_li','menu' )"><?php echo $module_gui_str['editor']['1022'];?></li>
					<li id="menu_dict_li" class="common-tab_li" onclick="menuSelected_2(menu_dict,'menu_dict_li','menu' )"><?php echo $module_gui_str['editor']['1023'];?></li>
					<li id="menu_layout_li" class="common-tab_li" onclick="menuSelected_2(menu_layout,'menu_layout_li','menu' )"><?php echo $module_gui_str['editor']['1024'];?></li>
					<li id="menu_plugin_li" class="common-tab_li" onclick="menuSelected_2(menu_plugin,'menu_plugin_li','menu' )"><?php echo $module_gui_str['editor']['1025'];?></li>
				</ul>
			</div>			
			
			<div class='toc' id='leftmenuinnerinner'>	
			<!-- toc begin -->
				<style>
				<?php include 'module/editor_toc/style.css';?>
				</style>
				<?php 
				//require 'module/editor_toc/language/default.php';
				//require "module/editor_toc/language/$currLanguage.php";
				require 'module/editor_toc/gui.html';
				?>
			<!-- toc end -->
			
			<!-- book mark begin -->
				<style>
				<?php include 'module/editor_bookmark/style.css';?>
				</style>
				<?php 
				//require 'module/editor_bookmark/language/default.php';
				//require "module/editor_bookmark/language/$currLanguage.php";
				require 'module/editor_bookmark/gui.html';
				?>
			<!-- book mark end -->
			
			<!-- Layout -->
				<style>
				<?php include 'module/editor_layout/style.css';?>
				</style>
				<?php 
				//require 'module/editor_layout/language/default.php';
				//require "module/editor_layout/language/$currLanguage.php";
				require 'module/editor_layout/gui.html';
				?>
			<!-- layout end -->
			
			<!-- project begin -->
				<style>
				<?php include 'module/editor_project/style.css';?>
				</style>
				<?php 
				//require 'module/editor_project/language/default.php';
				//require "module/editor_project/language/$currLanguage.php";
				require 'module/editor_project/gui.html';
				?>

			<!-- project end -->
			
			<!-- dictionary begin -->
				<style>
				<?php include 'module/editor_dictionary/style.css';?>
				</style>
				<?php 
				//require 'module/editor_dictionary/language/default.php';
				//require "module/editor_dictionary/language/$currLanguage.php";
				require 'module/editor_dictionary/gui.html';
				?>

			<!-- dictionary end -->
			
				<style>
				<?php include 'module/editor_palicannon/style.css';?>
				</style>
				<?php 
				//require 'module/editor_palicannon/language/default.php';
				//require "module/editor_palicannon/language/$currLanguage.php";
				//require 'module/editor_palicannon/gui.html';
				?>
	
				<style>
				<?php include 'module/editor_plugin/style.css';?>
				</style>
				<?php 
				//require 'module/editor_plugin/language/default.php';
				//require "module/editor_plugin/language/$currLanguage.php";
				require 'module/editor_plugin/gui.html';
				?>
						
			</div>
		
		</div>
		<!-- leftmenu end -->	
		<div id='BV' class='blackscreen' onClick='setNaviVisibility()'></div>
		
		<!-- begin-->
	<div class='mainview' id='body_mainview'>

		<div id="wizard_div"></div>
		<div id="wizard_div_mybook"></div>
		<div id="wizard_div_palicannon"></div>
		
		<div id="sutta_text">
			<div class="sutta_top_blank"></div>
		</div>
			
<!--  infomation panal -->	
		<div id="id_info_panal">
			<select id="id_info_window_select" name="menu" onchange="windowsSelected(this)">
				<option value="view_vocabulary"><?php echo $module_gui_str['editor']['1067'];?></option>
				<option value="view_dict_all"><?php echo $module_gui_str['editor']['1068'];?></option>
				<option value="view_dict_curr"><?php echo $module_gui_str['editor']['1069'];?></option>
				<option value="view_debug"><?php echo $module_gui_str['editor']['1070'];?></option>
			</select>
			<button type="button" onclick="setInfoPanalSize('hidden')"><?php echo $module_gui_str['editor']['1071'];?></button>
			<button type="button" onclick="setInfoPanalSize('min')"><?php echo $module_gui_str['editor']['1072'];?></button>
			<button type="button" onclick="setInfoPanalSize('half')"><?php echo $module_gui_str['editor']['1073'];?></button>
			<button type="button" onclick="setInfoPanalSize('0.6')"><?php echo $module_gui_str['editor']['1074'];?></button>
			<button type="button" onclick="setInfoPanalSize('max')"><?php echo $module_gui_str['editor']['1075'];?></button>
		
			<div id='id_info_panal_inner'>
			<div id="word_table">
				<p><br/><?php echo $module_gui_str['editor']['1076'];?><input id="button_wordlist_refresh" onclick="refreshWordList()" type="button" value="<?php echo $module_gui_str['editor']['1081'];?>" /> </p>
				<div id="word_table_inner"></div>
			</div>

			<div id="id_dict_match_result">
				<p><br/><?php echo $module_gui_str['editor']['1077'];?></p>
				<div id="id_dict_match_result_inner"></div>
			</div>
			
			<div id="id_dict_curr_word">
				<div id="id_dict_curr_word_inner"></div>
			</div>		
			
			<div class="debugMsg" id="id_debug"><!--调试信息-->
				<div id="id_debug_output"></div>
			</div>
			
		</div>
	
		</div>		

			
<!--  infomation panal end -->				
	</div>
		<!-- end-->
	<!--class="main"-->
	<div class="debug_info"><span id="debug"></span></div>
	
	<div id="modifyDiv">
		<div id="modifywin">
			<div>
				<ul id="id_select_modyfy_type" class="common-tab">
					<li id="detail_li" class="common-tab_li_act" onclick="select_modyfy_type('modify_detaile','detail_li')"><?php echo $module_gui_str['editor']['1041'];?></li>
					<li id="mark_li" class="common-tab_li" onclick="select_modyfy_type('modify_bookmark','mark_li')"><?php echo $module_gui_str['editor']['1021'];?></li>
					<li id="note_li" class="common-tab_li" onclick="select_modyfy_type('modify_note','note_li')"><?php echo $module_gui_str['editor']['1043'];?></li>
					<li id="spell_li" class="common-tab_li" onclick="select_modyfy_type('modify_spell','spell_li')"><?php echo $module_gui_str['editor']['1044'];?></li>
					
				</ul>
			</div>
			<div id="modify_detaile">
				<!-- 意思 -->
				<div class="edit_detail_p">
					<span class="edit_detail_span"><?php echo $module_gui_str['editor']['1010'];?>：</span>
					<input type="text" id="input_meaning" value="" name="in_meaning">
					<div class="case_dropdown">
						<svg class="edit_icon">
							<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_more"></use>
						</svg>
						<div id="word_mdf_mean_dropdown" class="case_dropdown-content">
						</div>
					</div>
				</div>
				<!-- 拆分 -->
				<div class="edit_detail_p">
					<span class="edit_detail_span"><?php echo $module_gui_str['editor']['1011'];?>：</span>
					<input type="text" id="input_org" value="" name="in_org" onkeydown="match_key(this)" onkeyup="unicode_key(this) " onchange="input_org_change()">
					<div class="case_dropdown">
						<svg class="edit_icon">
							<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_more"></use>
						</svg>
						<div id="word_mdf_parts_dropdown" class="case_dropdown-content">
						</div>
					</div>				
				</div>
				<!-- 拆分意思 -->
				<div class="edit_detail_p" >
					<span class="edit_detail_span"><?php echo $module_gui_str['editor']['1012'];?>：</span>
					<div id="input_org_select" style="width:80%; display:inline-flex;"></div>
					<input type="text" id="input_om" value="" name="in_om" onblur="input_org_switch('input_om','input_org_select')">
				</div>
				<!-- 格位 -->
				<div class="edit_detail_p">
					<span class="edit_detail_span"><?php echo $module_gui_str['editor']['1013'];?>：</span>				
					<p><input type="text" id="input_case" value="" name="in_case" onblur="input_org_switch('input_case','input_select_case')" ></p>
					<div id="input_select_case" style="display:inline-flex;">
						<div style="display:inline-flex;">
							<span></span>
							<span></span>
							<span></span>
							<span></span>
						</div>
						<button style="margin-left:auto; display:none;" onclick="input_org_switch('input_select_case','input_case')"><?php echo $module_gui_str['editor']['1044'];?></button>
						<div class="case_dropdown">
							<svg class="edit_icon">
								<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_more"></use>
							</svg>
							<!--下拉菜单-->
							<div id="word_mdf_case_dropdown" class="case_dropdown-content">
							</div>

						</div>								
					</div>
				</div>
				<!-- 关系 -->
				<div class="edit_detail_p">
					<span class="edit_detail_span">
						<svg class="icon" style="font-size: larger; -webkit-transform: rotate(135deg);">
							<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_link"></use>
						</svg>
					<button style="padding: 1px 6px;" onclick="rela_add_word()">
						<svg class="icon">
							<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_add"></use>
						</svg>
					</button>
					</span>
					<div id="relation_div">
						</div>
						<imput type="hidden" id="id_relation_text" value="" />
						
				</div>				
				<!-- 语基 -->
				<div class="edit_detail_p">
					<span class="edit_detail_span"><?php echo $module_gui_str['editor']['1084'];?></span>
					<input type="text" id="id_text_parent"  onkeydown="match_key(this)" onkeyup="unicode_key(this)" />
					<div class="case_dropdown">
						<svg class="edit_icon">
							<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_more"></use>
						</svg>
						<div id="word_mdf_parent_dropdown" class="case_dropdown-content">
						</div>
					</div>				
				</div>				

			</div>
			
			<div id="modify_bookmark">
				<ul id="id_book_mark_color_select" class="bookmark-tab">
					<li id="id_bmc0" class="bookmarkcolorblock bookmarkcolor0" onclick="setBookMarkColor(this,'bmc0')">
						<svg class="icon">
								<use xlink:href="svg/icon.svg#ic_clear">×</use>
						</svg>
					</li>
					<li id="id_bmc1" class="bookmarkcolorblock bookmarkcolor1" onclick="setBookMarkColor(this,'bmc1')">
						<svg class="icon">
								<use xlink:href="svg/icon.svg#ic_bookmark_on">1</use>
						</svg>
					</li>
					<li id="id_bmc2" class="bookmarkcolorblock bookmarkcolor2" onclick="setBookMarkColor(this,'bmc2')">
						<svg class="icon">
								<use xlink:href="svg/icon.svg#ic_bookmark_on">2</use>
						</svg>
					</li>
					<li id="id_bmc3" class="bookmarkcolorblock bookmarkcolor3" onclick="setBookMarkColor(this,'bmc3')">
						<svg class="icon">
								<use xlink:href="svg/icon.svg#ic_bookmark_on">3</use>
						</svg>
					</li>
					<li id="id_bmc4" class="bookmarkcolorblock bookmarkcolor4" onclick="setBookMarkColor(this,'bmc4')">
						<svg class="icon">
								<use xlink:href="svg/icon.svg#ic_bookmark_on">4</use>
						</svg>
					</li>
					<li id="id_bmc5" class="bookmarkcolorblock bookmarkcolor5" onclick="setBookMarkColor(this,'bmc5')">
						<svg class="icon">
								<use xlink:href="svg/icon.svg#ic_bookmark_on">5</use>
						</svg>
					</li>
					<li id="id_bmca" class="bookmarkcolorblock bookmarkcolora" onclick="setBookMarkColor(this,'bmca')">
						<svg class="icon">
								<use xlink:href="svg/icon.svg#ic_bookmark_on">A</use>
						</svg>
					</li>
				</ul>
				<textarea id="id_text_bookmark" rows="6" col="28" style="margin-left: 3px; margin-right: 3px; width: 95%;"></textarea>
			</div>
			
			<div id="modify_note">
				<textarea id="id_text_note" rows="7" col="28" style="margin-left: 3px; margin-right: 3px; width: 95%;"></textarea>
			</div>
			
			<div id="modify_spell">
				<span id="id_text_id"></span><br/>
				<?php echo $module_gui_str['editor']['1082'];?><br />
				<input type="input" id="id_text_pali" onkeydown="match_key(this)" onkeyup="unicode_key(this)" /><br/>
				<?php echo $module_gui_str['editor']['1083'];?><br />
				<input type="input" id="id_text_real"  onkeydown="match_key(this)" onkeyup="unicode_key(this)" />
				<br/>
				<br/>
			</div>
			
			<div id="modify_apply">
			</div>
		</div>
	</div>

	<div id="id_text_edit_form">
		<div id="id_text_edit_caption"  class="dialog-title">
			<div><button id="id_text_edit_cancel" type="button" onclick="edit_tran_cancal()"><?php echo $module_gui_str['editor']['1028'];?></button></div>
			<div><span id="id_text_edit_caption_text">Translate</span></div>
			<div id="id_text_edit_bottom">
				<button id="id_text_edit_delete" type="button" onclick="edit_tran_delete()"><?php echo $module_gui_str['editor']['1029'];?></button>
			</div>
			<div><button id="id_text_edit_save" type="button" onclick="edit_tran_save()"><?php echo $module_gui_str['editor']['1027'];?></button></div>
		</div>
		<div id="id_text_edit_info">
			<select id="id_heading_edit_level" >
					<option value="0"><?php echo $module_gui_str['editor']['1031'];?></option>
					<option value="1"><?php echo $module_gui_str['editor']['1032'];?></option>
					<option value="2"><?php echo $module_gui_str['editor']['1033'];?></option>
					<option value="3"><?php echo $module_gui_str['editor']['1034'];?></option>
					<option value="4"><?php echo $module_gui_str['editor']['1035'];?></option>
					<option value="5"><?php echo $module_gui_str['editor']['1036'];?></option>
					<option value="6"><?php echo $module_gui_str['editor']['1037'];?></option>
					<option value="7"><?php echo $module_gui_str['editor']['1038'];?></option>
					<option value="8"><?php echo $module_gui_str['editor']['1039'];?></option>
			</select>
			<select id="id_text_edit_language">
					<option value="pali">Pali</option>
					<option value="en">English</option>
					<option value="zh">简体中文</option>
					<option value="tw">正體中文</option>
			</select>
			<span>
				<?php echo $module_gui_str['editor_project']['1011'];?>
				<input type="input" id="id_text_edit_author" onkeydown="match_key(this)" onkeyup="unicode_key(this)"/>
				<?php echo $module_gui_str['editor_project']['1042'];?>
				<input id="id_text_edit_area_smart_switch" type="checkbox" checked="">
			</span>
		</div>
		<textarea id="id_text_edit_area" rows="10" width="100%" onkeydown="match_key(this)" onkeyup="unicode_key(this)">
		</textarea>

	</div>
	
	<!--  Tool bar on right side -->
	<div id="right_tool_bar" onmouseover="editor_show_right_tool_bar(true)">
		<div id="right_tool_bar_title">

			<ul id="id_select_modyfy_type" class="common-tab" >
				<button class="res_button" style="padding: 0" onclick="editor_show_right_tool_bar(false)">
					<svg class="button_icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_input"></use></svg>
				</button>	
				
				<button onclick="dict_search('_home_')">Home</button>
				<button onclick="dict_turbo_split()">Turbo Split</button>
				<a href="setting.php?item=dictionary" target="_blank">
					<svg class="icon">
						<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_settings"></use>
					</svg>
				</a>
			</ul>
			<!--  右侧工具条 -->
			<div>
				<div id="dict_ref_search_head">
					<div id="dict_ref_search_input_div">
						<div id="dict_ref_search_input_head">
							<table>
								<tr>
									<td>
										<div class="case_dropdown">
											<svg class="button_icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_more"></use></svg>
											<div id="dict_ref_dict_link" class="case_dropdown-content">
												<a onclick="">[dict]</a>
											</div>
										</div>
									</td>
									<td style="width: 95%;">
										<input id="dict_ref_search_input" type="input"  onkeyup="dict_input_keyup(event,this)">
									</td>
								</tr>
							</table>
						</div>
						<!--  查词工具 拆分 -->
						<div>
							<div id="input_parts"></div>
							<div id="dict_word_auto_split"></div>
						</div>
					</div>
				</div>
				
				<div id="term_dict_head" style="display: none">
				</div>
				
				<div id="msg_panal_right_head" style="display: none">
					<div id="msg_panal_list_toolbar">
					<span id="msg_list_title"><?php echo $module_gui_str['editor']['1116'];//Message?></span>
					<button onclick="msg_reload()">Messge Reload</button>
					</div>
					<div id="msg_panal_content_toolbar">
					<button onclick="msg_show_list_panal()"><?php echo $module_gui_str['tools']['1014'];//Back?></button><span id="msg_content_title"></span>
					</div>
				</div>
				
				<div id="pc_res_loader_head" style="display: none">
				</div>
			</div>
					
		</div>
		
		<div id="right_tool_bar_inner">
			<!--  参考字典查询结果 -->
			<div id="dict_ref_search">
				<div id="dict_ref_search_result">
				</div>
			</div>
			
			<!--  术语字典 -->
			<div id="term_dict">
			</div>

			<!--  消息 -->
			<div id="msg_panal_right">
				<div id="msg_panal_list">
				</div>
				<div id="msg_panal_content">
				</div>
			</div>
			
			<!--  三藏购物车 -->
			<div id="pc_res_loader">
				<div id="pc_res_load_button">
					<button  id="id_open_editor_load_stream"  onclick="pc_loadStream(0)"><?php //echo $module_gui_str['editor']['1030'];?>
						<svg class="button_icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_cloud_download"></use></svg>
					</button>
					<button  id="id_cancel_stream" onclick="pc_cancelStream()"><?php //echo $module_gui_str['editor']['1028'];?>
						<svg class="button_icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_note_add"></use></svg>
					</button>
					<button  id="pc_empty_download_list" onclick="pc_empty_download_list()"><?php //echo $module_gui_str['editor']['1045'];?>
						<svg class="button_icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_delete"></use></svg>
					</button>
					<button onclick="get_pc_res_download_list_from_cookie()"><?php //echo $module_gui_str['editor']['1081'];?>
						<svg class="button_icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_autorenew"></use></svg>
					</button>
				</div>
				
				<div id="pc_res_list_div">
				</div>

				<div id="id_book_res_load_progress"></div>
				<canvas id="book_res_load_progress_canvas" width="300" height="30"></canvas>
			</div>
		
		</div>
	</div>
	<!--  Tool bar on right side end -->
	
	<div class="pop_win_full"><div id="pop_win_inner"></div></div>
	
	<!--  Term pop window-->
	<div class="pop_win_full" id="term_win">
		<div class="pop_win_inner">
			<div class="win_title">
				<div>
					<button onclick="win_close('term_win')">Close</button>
				</div>
				<div><span>Term</span></div>
				<div>
					<button onclick="term_save()">Save</button>
				</div>
			</div>
			<div class="win_body">
				<div class="win_body_inner" id="term_body">
				</div>
			</div>
		</div>
	</div>
<div id="word_mean" style="max-width:22em;"></div>
<div id="word_parts" style="max-width:22em;"></div>
<div id="word_partmean" style="max-width:22em;"></div>
<div id="word_gramma" style="max-width:22em;"></div>

<script type="text/javascript"> 
//侦测页面滚动

var scrollEventLock=false;
var suttaDom = document.getElementById('sutta_text');
 window.addEventListener('scroll',winScroll);
 function winScroll(e){ 
	 if(scrollEventLock){
		debugOutput("scroll Event Lock");
		return;
	 }
	 if(getElementHeight(suttaDom)<getWindowHeight()){
		return;
	 }
	 var top = getElementViewTop(suttaDom);
	 //top < 0 ? fixedDom.classList.add("fixed") : fixedDom.classList.remove("fixed");
	 if(top>-500){
		scrollEventLock=true;
		//prev_page();
		scrollEventLock=false;
		debugOutput("goto prev page");
	 }
	 if(getElementBottomOutsideHeight(suttaDom)<1500){
		scrollEventLock=true;
		//next_page();
		scrollEventLock=false;
		debugOutput("goto next page");
	 }
	//debugOutput( document.body.scrollTop);
	debugOutput("top:"+top+"; outside:"+getElementBottomOutsideHeight(suttaDom));
	//debugOutput("scrollHeight="+suttaDom.scrollHeight+ ";  clientHeight="+suttaDom.clientHeight +";offsetHeight="+suttaDom.offsetHeight);
 }
 
 function getElementViewTop(element){
 　　　　var actualTop = element.offsetTop;
	var elementScrollTop=GetPageScroll().y;//document.body.scrollTop;
 　　　　return actualTop-elementScrollTop;
 　　} 
function getElementBottomOutsideHeight(element){
	var winHeight=getWindowHeight();//document.body.clientHeight;
	var elementHeight=getElementHeight(element);//suttaDom.scrollHeight;
	var elementTop=getElementViewTop(element);
	return(elementHeight+elementTop-winHeight);
}

function getElementHeight(element){
	var scrW, scrH; 
	if(element.innerHeight && element.scrollMaxY) 
	{	// Mozilla	
		scrW = element.innerWidth + element.scrollMaxX;	
		scrH = element.innerHeight + element.scrollMaxY; 
	} 
	else if(element.scrollHeight > element.offsetHeight)
	{	// all but IE Mac	
		scrW = element.scrollWidth;	
		scrH = element.scrollHeight; 
	} else if(element) 
	{ // IE Mac	
		scrW = element.offsetWidth;	
		scrH = element.offsetHeight;
	} 
	return(scrH);
}
function getWindowHeight(){
	var winW, winH; 
	if(window.innerHeight) 
	{ // all except IE	
		winW = window.innerWidth; 
		winH = window.innerHeight; 
	} else if (document.documentElement && document.documentElement.clientHeight)
	{	// IE 6 Strict Mode	
		winW = document.documentElement.clientWidth;	 
		winH = document.documentElement.clientHeight; 
	} else if (document.body) { // other	
		winW = document.body.clientWidth;	
		winH = document.body.clientHeight; 
	}  
	return(winH);
}
//滚动条位置
function GetPageScroll() 
{ 
	var pos=new Object();
	var x, y; 
	if(window.pageYOffset) 
	{	// all except IE	
		y = window.pageYOffset;	
		x = window.pageXOffset; 
	} else if(document.documentElement && document.documentElement.scrollTop) 
	{	// IE 6 Strict	
		y = document.documentElement.scrollTop;	
		x = document.documentElement.scrollLeft; 
	} else if(document.body) {	// all other IE	
		y = document.body.scrollTop;	
		x = document.body.scrollLeft;   
	} 
	pos.x=x;
	pos.y=y;
	return(pos);
}


var Dragging=function(validateHandler){
 //参数为验证点击区域是否为可移动区域，如果是返回欲移动元素，负责返回null 
var draggingObj=null;
//dragging Dialog 
var diffX=0;
var diffY=0;
function mouseHandler(e){
	 switch(e.type){
	 case 'mousedown':
		draggingObj=validateHandler(e);
		//验证是否为可点击移动区域 
		if(draggingObj!=null){
			diffX=e.clientX-draggingObj.offsetLeft;
			diffY=e.clientY-draggingObj.offsetTop;
		 }
	 break;
	 case 'mousemove':
		 if(draggingObj){
			 draggingObj.style.left=(e.clientX-diffX)+'px';
			 draggingObj.style.top=(e.clientY-diffY)+'px';
		 }
		 break;
	 case 'mouseup': 
	 draggingObj =null;
		 diffX=0;
		 diffY=0;
		 break;
	 }
}
;
 return {
	 enable:function(){
		 document.addEventListener('mousedown',mouseHandler);
		 document.addEventListener('mousemove',mouseHandler);
		 document.addEventListener('mouseup',mouseHandler);
		}
	, disable:function(){
		 document.removeEventListener('mousedown',mouseHandler);
		 document.removeEventListener('mousemove',mouseHandler);
		 document.removeEventListener('mouseup',mouseHandler);
		}
	 }
 }
 
 function getDraggingDialog(e){
try{
	var target=e.target;
	if(target && target.className){
		while(target ){
			if(target.className){
				if(target.className.indexOf('dialog-title')==-1){
					target=target.offsetParent;
				}
				else{
					break;
				}
			}
			else{
				target=target.offsetParent;
			}
		}
		
		if(target!=null){
			return target.offsetParent;
		}
		else{
			return null;
		}
	}
}
catch(e){

}
 
}
// Dragging(getDraggingDialog).enable();

</script> 

	<div id="word_tool_bar_div">
		<div id="word_tool_bar" class="word_head_bar"style="font-size: 85%">
			<button style="padding: 1px 6px;" onclick="rela_link_click()">
				<svg class="icon" style="fill: black; webkit-transform: rotate(135deg);">
					<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_attachment"></use>
				</svg>
			</button>
			<button style="padding: 1px 6px;" onclick="rela_link_click(false)">
				<svg class="icon" style="fill: black;">
					<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="svg/icon.svg#ic_clear"></use>
				</svg>
			</button>
		</div>
	</div>
</body>
</html>

