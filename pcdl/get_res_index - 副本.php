<?php
require_once "../public/config.php";
require_once "../public/_pdo.php";
if(isset($_COOKIE["language"])){
	$lang=$_COOKIE["language"];
}
else{
	$lang="en";
}
require_once "language/db_{$lang}.php";

if(isset($_GET["book"])){
	$book=$_GET["book"];
}
else{
	echo "no book id";
	exit;
}
if(substr($book,0,1)=='p'){
	$book=substr($book,1);
}

if(isset($_GET["album"])){
	$album = $_GET["album"];
}
else{
	$album = -1;
}

if(isset($_GET["paragraph"])){
	$paragraph = $_GET["paragraph"];
}
else{
	$paragraph = -1;
}

	function format_file_size($size){
		if($size<102){
			$str_size=$size."B";
		}
		else if($size<(1024*102)){
			$str_size=sprintf("%.1f KB",$size/1024);
		}
		else{
			$str_size=sprintf("%.1f MB",$size/(1024*1024));
		}
		return($str_size);
	}

	$db_file = $dir_palicanon.'res.db3';
	PDO_Connect("sqlite:$db_file");

//资源名称
	$res_title="";
	if($album==-1){
		//查书
		if($paragraph==-1){
			//查整本书
			$query = "select * from 'book' where book_id='$book' ";
			$Fetch = PDO_FetchAll($query);
			$iFetch=count($Fetch);
			if($iFetch>0){
				echo "<div id='album_info_head'>";
				$res_title=$Fetch[0]["title"];
				echo "<h2>$res_title</h2>";
				echo "<div class='album_info'>";
				echo "<div class='cover'></div>";
				echo "<div class='infomation'>";
				//标题
				echo "<div class='title'>《".$res_title."》</div>";
				echo "<div class='type'>".$Fetch[0]["c1"]." ".$Fetch[0]["c2"]."</div>";
				echo "</div>";
				echo "</div>";
				
				//相关专辑
				$query = "select album.id,album.title, author.name from album LEFT JOIN author ON album.author = author.id  where album.book='$book' ";
				$Fetch_ather = PDO_FetchAll($query);
				$iFetchAther=count($Fetch_ather);
				if($iFetchAther>0){
					echo "<ul class='search_list'>";
					echo "<li class='title'>相关专辑</li>";
					foreach($Fetch_ather as $one_album){
						$read_link="reader.php?book=$book&album=".$one_album["id"]."&paragraph=$paragraph";
						$info_link="index_render_res_list($book,".$one_album["id"].",-1)";
						//echo "<a href='$read_link' target='_blank'>{$one_album["title"]}</a>";
						//echo "<div><div class='author_name'>".$one_album["name"]."</div><div onclick='$info_link'  class='info_button'></div></div>";

						echo "<li onclick='$info_link' >";
						echo "<span>{$one_album["title"]}</span>";
						echo "<div><span class='author_name'>".$one_album["name"]."</span><span class='ui-icon-carat-r ui-icon' style='display:inline-block;'></span></span>";
						echo "</li>";
					}
					echo "</ul>";
				}
				//相关专辑结束
				echo "</div>";
				
				//目录
				$db_file = $dir_palicanon."pali_text/p$book"."_pali.db3";
				PDO_Connect("sqlite:$db_file");
				$query = "select * from 'data' where level>'0' and level<8 ";
				$Fetch_Toc = PDO_FetchAll($query);
				$iFetchToc=count($Fetch_Toc);
				if($iFetchToc>0){
					$aLevel=array();
					foreach($Fetch_Toc as $one_title){
						$level=$one_title["level"];
						if(isset($aLevel["{$level}"])){
							$aLevel["{$level}"]++;
						}
						else{
							$aLevel["{$level}"]=1;
						}
					}
					ksort($aLevel);
					//找出不是一个的最大层级
					foreach($aLevel as $x=>$x_value){
						$maxLevel=$x;
						if($x_value>1){
							break;
						}
					}
					
					echo "<ul class='search_list'>";
					echo "<li class='title'  onclick='toc_hide_show()'>章节</li>";
					//echo "<li onclick='wizard_palicannon_heading_change()' >根目录</li>";
					foreach($Fetch_Toc as $one_title){
						$level=$one_title["level"];
						if($maxLevel==$level){
							$toc_paragraph=$one_title["paragraph"];
							$toc_title=$one_title["text"];
						$info_link="index_render_res_list($book,0,$toc_paragraph)";
						echo "<li onclick='$info_link' >";
						echo "<span>{$toc_title}</span>";
						echo "<div><span class='author_name'>2</span><span class='ui-icon-carat-r ui-icon' style='display:inline-block;'></span></span>";
						echo "</li>";
						}
					}
					echo "</ul>";
					/*
					echo "<li id='level_selector' class='level_selector'></li>";
					foreach($Fetch_Toc as $one_title){
						$toc_paragraph=$one_title["paragraph"];
						$info_show="show_par_res_in_toc($book,$toc_paragraph)";
						$level=$one_title["level"];
						$used_level[$level]=1;
						echo "<li class='toc_level_$level'><a onclick='$info_show'>".$one_title["text"]."</a></li>";
						echo "<div id='toc_para_res_$toc_paragraph' class='toc_para_res'></div>";
					}
					echo "</ul>";
					$level_selector_html="";
					for($iLevel=0;$iLevel<8;$iLevel++){
						if($used_level[$iLevel]==1){
							$level_selector_html.="<span onclick='toc_show_level($iLevel)'>$iLevel</span>";
						}
					}
					echo "<script>";
					echo "$(\"#level_selector\").html(\"$level_selector_html\");";
					echo "</script>";
					*/
					
					for($iToc=1;$iToc<9;$iToc++){
						echo "<div id='toc_p_h$iToc' class=\"toc_list_parent tool_bar_bg\"></div>";
						echo "<div id='toc_h$iToc' class=\"toc_list\"></div>";
					}

					$json="var toc_info = [";
					foreach($Fetch_Toc as $one_title){
						$toc_paragraph=$one_title["paragraph"];
						$level=$one_title["level"];
						$title=$one_title["text"];
						$json.="{ \"paragraph\":$toc_paragraph , \"level\":$level , \"title\":\"$title\" },";
					}
					$json=substr($json,0,-1);
					$json.="];";
					echo "<script>";
					echo "var toc_type='book';";
					echo "$json";
					echo "$(\".toc_list\").hide();";			
					echo "wizard_palicannon_heading_change();";
					echo "</script>";
				}
				//目录结束

			}	
		}
		else{
			//查书中的一个段
			$query = "select * from 'index' where book='$book' and paragraph=$paragraph group by album";
			$Fetch = PDO_FetchAll($query);
			$iFetch=count($Fetch);
			if($iFetch>0){
				$res_title=$Fetch[0]["title"];
				echo "<h2>$res_title</h2>";
				//相关段落
				echo "<ul class='search_list'>";
				echo "<li class='title'>可用资源</li>";
				foreach($Fetch as $one_para){
					$read_link="../pcdl/reader.php?book=$book&album=".$one_para["album"]."&paragraph=$paragraph";
					echo "<li class='noline'><a href='$read_link' target='_blank'>".$one_para["title"]."</a>-".$one_para["author"]."</li>";
				}
				echo "</ul>";

			}
			//查共享文档
			
			$db_file = $_file_db_fileindex;
			PDO_Connect("sqlite:$db_file");
			$query = "select * from fileindex where book='$book' and paragraph=$paragraph  and status>0 and share>0 order by create_time";
			$Fetch = PDO_FetchAll($query);
			$iFetch=count($Fetch);
			if($iFetch>0){
				echo "共享文档";
				echo "<ul class='search_list'>";
				foreach($Fetch as $one_file){
					//$read_link="reader.php?book=$book&album=".$one_para["album"]."&paragraph=$paragraph";
					$edit_link="../app/project.php?op=open&doc_id={$one_file["doc_id"]}";
					echo "<li class='noline'><a href='$edit_link' target='_blank'>".$one_file["title"]."</a>-".$one_file["userid"]."</li>";					
				}
				echo "</ul>";
			}
		}
	}
	else{
		//查专辑
		if($paragraph==-1){
			//查整张专辑
			$query = "select album.id,album.title,album.file,album.guid,album.type, author.name from album LEFT JOIN author ON album.author = author.id  where album.id='$album' ";

			//$query = "select * from 'album' where id='$album'";
			$Fetch = PDO_FetchAll($query);
			$iFetch=count($Fetch);
			if($iFetch>0){
				echo "<div id='album_info_head'>";
				//标题
				$res_title=$Fetch[0]["title"];
				$album_file_name=$Fetch[0]["file"];
				$album_guid=$Fetch[0]["guid"];
				$album_type=$Fetch[0]["type"];
				
				echo "<h2>$res_title</h2>";
				echo "<div class='album_info'>";
				echo "<div class='cover'></div>";
				echo "<div class='infomation'>";
				//标题
				echo "<div class='title'>".$res_title."</div>";
				//echo "<div class='type'>".$Fetch[0]["c1"]." ".$Fetch[0]["c2"]."</div>";
				echo "<div class='author'>".$Fetch[0]["name"]."</div>";
				echo "<div class='media'><span class='media_type'>".$media_type[$Fetch[0]["type"]]."</span></div>";
				echo "</div>";
				echo "</div>";
				
				
				$read_link="reader.php?book=$book&album=$album&paragraph=$paragraph";
				echo "<ul class='search_list'>";
				echo "<li class='title'>阅读</li>";
				echo "<li><a class='online_read' style='color: white;' href='$read_link' target='_blank'>在线阅读</a></li>";
				echo "</ul>";

				//下载
				echo "<ul class='search_list'>";
				$query = "select album_ebook.file_name, album_ebook.file_size, file_format.format from album_ebook LEFT JOIN file_format ON album_ebook.file_format=file_format.id where album='$album'";			
				$Fetch_ebook = PDO_FetchAll($query);
				if(count($Fetch_ebook)>0){
					echo "<li class='title'>下载</li>";
					foreach($Fetch_ebook as $one_ebook){
						$ebook_download_link="<a href='".$one_ebook["file_name"]."' target='_blank'>下载</a>";
						echo "<li><span class='file_format'>".$one_ebook["format"]."</span>".format_file_size($one_ebook["file_size"])."$ebook_download_link</li>";
					}
				}
				echo "</ul>";
				
				
				//相关专辑
				echo "<ul class='search_list'>";
				$query = "select album.id,album.title,album.file,album.guid,album.type, author.name from album LEFT JOIN author ON album.author = author.id  where album.book='$book' and album.id != $album ";
				//$query = "select * from 'album' where book='$book' and id != $album";
				$Fetch_ather = PDO_FetchAll($query);
				$iFetchAther=count($Fetch_ather);
				if($iFetchAther>0){
					echo "<li class='title'>相关专辑</li>";
					foreach($Fetch_ather as $one_album){
						$read_link="reader.php?book=$book&album=".$one_album["id"]."&paragraph=$paragraph";
						echo "<li ><a href='$read_link' target='_blank'>".$one_album["title"]."</a><span class='media_type'>".$media_type[$one_album["type"]]."</span>".$one_album["name"]."</li>";
					}
				}
				echo "</ul>";
				//相关专辑结束
				echo "</div>";
				
				//专辑目录
				$db_file = $dir_palicanon."pali_text/p$book"."_pali.db3";
				PDO_Connect("sqlite:$db_file");
				$query = "select * from 'data' where level>'0' and level<8 ";
				$Fetch_Toc = PDO_FetchAll($query);
				$iFetchToc=count($Fetch_Toc);
				$used_level = array(0,0,0,0,0,0,0,0,0);
				if($iFetchToc>0){
					if($album_type==3){//查询译文标题
						//打开翻译数据文件
						$db_file = '../'.$album_file_name;
						PDO_Connect("sqlite:$db_file");
						
						$query = "select * from 'album' where guid='$album_guid'";
						$Fetch_album = PDO_FetchAll($query);
						$this_album_id=$Fetch_album[0]["id"];
					}
					/*目录树
					echo "<ul class='search_list'>";
					echo "<li class='title'>目录</li>";
					echo "<li id='level_selector' class='level_selector'></li>";
					foreach($Fetch_Toc as $one_title){
						$toc_paragraph=$one_title["paragraph"];
						$toc_title=$one_title["text"];
						if($album_type==3){
							//查询翻译经文标题
							$query="SELECT text FROM \"data\" WHERE paragraph=$toc_paragraph and album=$this_album_id ";
							$toc_title = PDO_FetchOne($query);
						}
						$info_show="reader.php?book=$book&album=$album&paragraph=$toc_paragraph";
						$level=$one_title["level"];
						$used_level[$level]=1;
						echo "<li class='toc_level_$level'><a href='$info_show' target='_blank'>".$toc_title."</a></li>";
					}
					echo "</ul>";
					$level_selector_html="";
					for($iLevel=0;$iLevel<8;$iLevel++){
						if($used_level[$iLevel]==1){
							$level_selector_html.="<span onclick='toc_show_level($iLevel)'>$iLevel</span>";
						}
					}
					echo "<script>";
					echo "$(\"#level_selector\").html(\"$level_selector_html\");";
					echo "</script>";
					*/
					
					echo "<ul class='search_list'>";
					echo "<li class='title' onclick='toc_hide_show()'>章节</li>";
					//echo "<li onclick='wizard_palicannon_heading_change()' >根目录</li>";
					echo "</ul>";
					
					for($iToc=1;$iToc<9;$iToc++){
						echo "<div id='toc_p_h$iToc' class=\"toc_list_parent tool_bar_bg\"></div>";
						echo "<div id='toc_h$iToc' class=\"toc_list\"></div>";
					}

					$json="var toc_info = [";
					foreach($Fetch_Toc as $one_title){
						$toc_paragraph=$one_title["paragraph"];
						$level=$one_title["level"];
						$title=$one_title["text"];
						if($album_type==3){
							//查询翻译经文标题
							$query="SELECT text FROM \"data\" WHERE paragraph=$toc_paragraph and album=$this_album_id ";
							$title = PDO_FetchOne($query);
						}						
						$json.="{ \"paragraph\":$toc_paragraph , \"level\":$level , \"title\":\"$title\" },";
					}
					$json=substr($json,0,-1);
					$json.="];";
					echo "<script>";
					echo "var toc_type='album';";
					echo "var toc_book=$book;";
					echo "var toc_album=$album;";
					echo "$json";
					echo "$(\".toc_list\").hide();";			
					echo "wizard_palicannon_heading_change();";
					echo "</script>";

				}
				//专辑目录结束
			}			
		}
		else{
			//查专辑中的一个段
			$query = "select * from 'index' where album='$album' and paragraph=$paragraph";	
			$Fetch = PDO_FetchAll($query);
			$iFetch=count($Fetch);
			if($iFetch>0){
				//标题
				$res_title=$Fetch[0]["title"];
				echo "<h2>$res_title</h2>";
				
				echo "<div class='album_info'>";
				echo "<div class='cover'></div>";
				echo "<div class='infomation'>";
				//出自专辑 标题
				$query = "select album.id,album.title,album.type, author.name from album LEFT JOIN author ON album.author = author.id  where album.id='$album' ";
				$FetchAlbum = PDO_FetchAll($query);
				if(count($FetchAlbum)>0){
					//专辑标题
					$info_link="index_render_res_list($book,$album,-1)";
					echo "<div onclick='$info_link' class='title'>".$FetchAlbum[0]["title"]."</div>";
				}				
				echo "<div class='author'>".$FetchAlbum[0]["name"]."</div>";
				echo "<div class='author'><span class='media_type'>".$media_type[$FetchAlbum[0]["type"]]."</span></div>";
				echo "</div>";
				echo "</div>";
				

				
				//在线阅读
				$read_link="reader.php?book=$book&album=$album&paragraph=$paragraph";				
				echo "<ul class='search_list'>";
				echo "<li class='title'>阅读</li>";
				echo "<li><a class='online_read' style='color: white;' href='$read_link' target='_blank'>在线阅读</a></li>";
				echo "</ul>";
				
				//相关段落
				//$query = "select album.id,album.title,album.file,album.guid,album.type, author.name from album LEFT JOIN author ON album.author = author.id  where album.book='$book' and album.id != $album ";

				$query = "select idx.id , idx.title , idx.album , idx.type , author.name FROM 'index' as idx LEFT JOIN author ON idx.author = author.id WHERE idx.book='$book' and idx.paragraph=$paragraph and idx.album!=$album";
				$Fetch_ather = PDO_FetchAll($query);
				$iFetchAther=count($Fetch_ather);
				if($iFetchAther>0){
					echo "<ul class='search_list'>";
					echo "<li class='title'>相关资源</li>";
					foreach($Fetch_ather as $one_album){
						$read_link="reader.php?book=$book&album=".$one_album["album"]."&paragraph=$paragraph";
						echo "<li class='noline'><a href='$read_link' target='_blank'>".$one_album["title"]."</a><span class='media_type'>".$media_type[$one_album["type"]]."</span>".$one_album["name"]."</li>";
					}
					echo "</ul>";
				}
			}			
		}	
	}
	
	
	//echo $query."<br>";
	

	
	?>

