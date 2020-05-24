<script>
		
	function goto_url(obj,url){
		var id=obj.getAttributeNode("id").value;
		if(id!=gCurrPage){
			window.location.assign(url);
		}
	}
	
</script>
		<div class="index_left_panal">
			<ul class="navi_button">
				<li id="pali_canon" onclick="goto_url(this,'index_pc.php?language=<?php echo $currLanguage; ?>')">
					<span  class="navi_icon">
						<svg class="icon">
							<use xlink:href="./svg/icon.svg#ic_add_circle"></use>
						</svg>	
					</span>	
					<span class="navi_text">
						<?php echo $_local->gui->pali_canon;?>
					</span>
				</li>
				<li id="recent_scan" onclick="goto_url(this,'index.php?language=<?php echo $currLanguage; ?>')">
					<span  class="navi_icon">
						<svg class="icon">
							<use xlink:href="./svg/icon.svg#ic_archive"></use>
						</svg>	
					</span>	
					<span class="navi_text">				
					<?php echo $_local->gui->recent_scan;?>
					</span>
				</li>
				<li id="group_index"  onclick="goto_url(this,'group.php?language=<?php echo $currLanguage; ?>')">
					<span  class="navi_icon">
						<svg class="icon">
							<use xlink:href="./svg/icon.svg#ic_two_person"></use>
						</svg>	
					</span>	
					<span class="navi_text">
					Group
					</span>
				</li>
				<li id="recycle_bin"  onclick="goto_url(this,'recycle.php')">
					<span  class="navi_icon">
						<svg class="icon">
							<use xlink:href="./svg/icon.svg#ic_delete"></use>
						</svg>	
					</span>	
					<span class="navi_text">
					<?php echo $_local->gui->recycle_bin;?>
					</span>
				</li>
			</ul>
		</div>
		
		<!-- tool bar begin-->
		<div class='index_toolbar'>
			<div id="index_nav">
			<svg class="icon" style="    fill: #f1ca23;height: 2em;">
				<use xlink:href="../public/images/svg/ic_logo1.svg#ic_logo_small"></use>
			</svg>
			Studio
			</div>
			<div >
					<div>
						<div>
							<input id="search_input" type="input" placeholder=<?php echo $_local->gui->serach;?> onkeyup="search_input_keyup(event,this)" style="margin-left: 0.5em;width: 40em;max-width: 80%" onfocus="search_input_onfocus()">
						</div>
						<div id="pre_search_result">
							<div id="pre_search_chapter" class="pre_serach_block">
								<div id="pre_search_chapter_title"   class="pre_serach_block_title">
									<div id="pre_search_chapter_title_left">我的文档</div>
									<div id="pre_search_chapter_title_right"></div>
								</div>
								<div id="pre_search_chapter_content"   class="pre_serach_content">
								</div>
							</div>
							<div id="pre_search_sent"  class="pre_serach_block">
								<div id="pre_search_sent_title"   class="pre_serach_block_title">
									<div id="pre_search_sent_title_left">群组文档</div>
									<div id="pre_search_sent_title_right"></div>								
								</div>
								<div id="pre_search_sent_content"   class="pre_serach_content">
								</div>
							</div>
							<div id="pre_search_word"  class="pre_serach_block">
								<div id="pre_search_word_title"   class="pre_serach_block_title">
									<div id="pre_search_word_title_left">群组</div>
									<div id="pre_search_word_title_right"></div>								
								</div>
								<div id="pre_search_word_content"   class="pre_serach_content">
								</div>
							</div>			
						</div>					
					</div>
				
			</div>
			<div class="toolgroup1">
				<span><?php echo $_local->gui->language;?></span>
				<select id="id_language" name="menu" onchange="menuLangrage(this)">
					<option value="en" >English</option>
					<option value="si" >සිංහල</option>
					<option value="my" >myanmar</option>
					<option value="zh-cn" >简体中文</option>
					<option value="zh-tw" >繁體中文</option>
				</select>
			
			<?php 
			
				echo "<a href=\"setting.php?item=account\">";
				echo urldecode($_COOKIE["nickname"]);
				echo "</a>";
				echo "<a href='../ucenter/index.php?op=logout'>";
				echo $_local->gui->logout;
				echo "</a>";
			?>
				<button class="icon_btn" id="file_select" >
					<a href="setting.php" target='_blank'>
					<svg class="icon">
						<use xlink:href="svg/icon.svg#ic_settings"></use>
					</svg>
					</a>
				</button>			
			</div>
		</div>	
		<!--tool bar end -->
		