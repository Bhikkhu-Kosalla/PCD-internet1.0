<?php
require_once '../path.php';
require_once "../public/load_lang.php";
require_once "../public/_pdo.php";
require_once "../public/function.php";


	if(isset($_GET["op"])){
		$op=$_GET["op"];
	}
	else{
		$op="login";
	}

	
	switch($op){
		case "login":
		{
			break;
		}
		case "logout":
		{
			if(isset($_COOKIE["nickname"])){
				$message_comm = "用户".$_COOKIE["nickname"]."已经退出";
			}
			setcookie("uid", "", time()-60,"/");
			setcookie("username", "", time()-60,"/");
			setcookie("userid", "", time()-60,"/");
			setcookie("nickname", "", time()-60,"/");
			setcookie("email", "", time()-60,"/");
			break;
		}
		case "new":
		{
			break;
		}
	}
	
	$post_nickname = "";
	$post_username = "";
	$post_password = "";
	$post_email = "";
	if(isset($_POST["op"]) && $_POST["op"]=="new"){
		$op="new";
		$post_username=$_POST["username"];
		$post_password=$_POST["password"];
		$post_nickname=$_POST["nickname"];
		$post_email=$_POST["email"];
		if(empty($post_username)){
			$error_username = "用户名不能为空";
		}
		if(empty($post_password)){
			$error_password = "密码不能为空";
		}
		if(empty($post_nickname)){
			$error_nickname =  "称呼不能为空";
		}
		if(!empty($post_username) && !empty($post_password) && !empty($post_nickname)){
			$md5_password=md5($post_password);
			$new_userid=UUID::v4();
			PDO_Connect("sqlite:"._FILE_DB_USERINFO_);
			$query = "select * from user where \"username\"=".$PDO->quote($post_username);
			$Fetch = PDO_FetchAll($query);
			$iFetch=count($Fetch);
			if($iFetch>0){//username is exite
				$error_username = "user name is exite";
			}
			else{
				$query="INSERT INTO user ('id','userid','username','password','nickname','email') VALUES (NULL,".$PDO->quote($new_userid).",".$PDO->quote($post_username).",".$PDO->quote($md5_password).",".$PDO->quote($post_nickname).",".$PDO->quote($post_email).")";
				$stmt = @PDO_Execute($query);
				if (!$stmt || ($stmt && $stmt->errorCode() != 0)) {
					$error = PDO_ErrorInfo();
					$error_comm = $error[2]."抱歉！请再试一次";
				}
				else{
					//created user recorder 
					$newUserPath=_DIR_USER_BASE_.'/'.$new_userid;
					$userDirMyDocument=$newUserPath._DIR_MYDOCUMENT_;
					if(!file_exists($newUserPath)){
						if(mkdir($newUserPath)){
							mkdir($userDirMyDocument);
						}
						else{
							$error_comm = "建立用户目录失败，请联络网站管理员。";
						}
					}
					$message_comm = "新账户建立成功";
					$op="login";
					unset($_POST["username"]);
				}
			}
		}
		else{
			
		}		
	}
	else{
		if(isset($_POST["username"])){
			$_username_ok = true;
			if($_POST["username"]==""){
				$_username_ok=false;
				$_post_error="用户名不能为空";
			}
			else if(isset($_POST["password"])){
				$md5_password=md5($_POST["password"]);
				PDO_Connect("sqlite:"._FILE_DB_USERINFO_);
				$query = "select * from user where \"username\"=".$PDO->quote($_POST["username"])." and \"password\"=".$PDO->quote($md5_password);
				$Fetch = PDO_FetchAll($query);
				$iFetch=count($Fetch);
				if($iFetch>0){//username is exite
					$uid=$Fetch[0]["id"];
					$username=$Fetch[0]["username"];
					$userid=$Fetch[0]["userid"];
					$nickname=$Fetch[0]["nickname"];
					$email=$Fetch[0]["email"];
					setcookie("uid", $uid, time()+60*60*24*365,"/");
					setcookie("username", $username, time()+60*60*24*365,"/");
					setcookie("userid", $userid, time()+60*60*24*365,"/");
					setcookie("nickname", $nickname, time()+60*60*24*365,"/");
					setcookie("email", $email, time()+60*60*24*365,"/");
	
					$newUserPath=_DIR_USER_BASE_.'/'.$userid.'/';
					if(!file_exists($newUserPath)){
						echo "error:cannot find user dir:$newUserPath<br/>";
					}
?><!DOCTYPE html>
<html>
	<head>
		<title>wikipali starting</title>
		<meta http-equiv="refresh" content="0,../studio/index.php"/>
	</head>
	
	<body>
		<br>
		<br>
		<p align="center"><a href="../studio/index.php">Auto Redirecting to Homepage! IF NOT WORKING, CLICK HERE</a></p>
    </body>
</html>
<?php
					
					exit;
				}
				else{
					$_post_error="用户名或密码错误";
				}
			}
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link type="text/css" rel="stylesheet" href="../studio/css/font.css"/>
		<link type="text/css" rel="stylesheet" href="../studio/css/style.css"/>
		<link type="text/css" rel="stylesheet" href="../studio/css/color_day.css" id="colorchange" />
		<title>wikipali login</title>
		<script src="../public/js/comm.js"></script>
		<script src="../studio/js/jquery-3.3.1.min.js"></script>
		<script src="../studio/js/fixedsticky.js"></script>
		<style>
			#login_body{
				display: flex;
				padding: 10em;
				margin: auto;
			}
			#login_left {
				padding-right: 12em;
				padding-top: 5em;
			}
			.title{
				font-size: 150%;
				margin-top: 1em;
				margin-bottom: 0.5em;
			}
			#login_form{
				padding: 2em 0 1em 0;
			}
			#tool_bar {
				padding: 1em;
				display: flex;
				justify-content: space-between;
			}
		#login_shortcut {
			display: flex;
			flex-direction: column;
			padding: 2em 0;
		}
		#login_shortcut button{
			height:3em;
		}
		#button_area{
			text-align: right;
			    padding: 1em 0;
		}
		.form_help{
			font-weight: 300;
			color: var(--bookx);
		}
		.login_form input,select{
			margin-top:2em;
			padding:0.5em 0.5em;
		}
		.form_error{
			color:var(--error-text);
		}
		#login_form_div{
			width:30em;
		}
		
		#ucenter_body {
			display: flex;
			flex-direction: column;
			margin: 0;
			padding: 0;
			background-color: var(--tool-bg-color3);
			color: var(--btn-color);
		}
		.icon_big {
			height: 2em;
			width: 2em;
			fill: var(--btn-color);
			transition: all 0.2s ease;
		}
		.form_field_name{
			position: absolute;
			margin-left: 7px;
			margin-top: 2em;
			color: var(--btn-border-line-color);
			-webkit-transition-duration: 0.4s;
			-moz-transition-duration: 0.4s;
			transition-duration: 0.4s;
			transform: translateY(0.5em);
		}
		.viewswitch_on {
			position: absolute;
			margin-left: 7px;
			margin-top: 1.5em;
			color: var(--bookx);
			-webkit-transition-duration: 0.4s;
			-moz-transition-duration: 0.4s;
			transition-duration: 0.4s;
			transform: translateY(-15px);
		}
		
		</style>
		<script>

		function login_init(){
			$("input").focus(function(){
				let name = $(this).attr("name");
				var objNave = document.getElementById("tip_"+name);
				objNave.className = "viewswitch_on";
			});	
			$(".form_field_name").click(function(){
				let id = $(this).attr("id");
				var objNave = document.getElementById(id);
				objNave.className = "viewswitch_on";
				let arrId=id.split("_");
				document.getElementById('input_'+arrId[1]).focus();
			});	
			
		}
		</script>
	<link type="text/css" rel="stylesheet" href="mobile.css" media="screen and (max-width:767px)">
	</head>
	<body id="ucenter_body" onload="login_init()">
	<div id="tool_bar">
		<div>
		</div>
		<div>
		<?php
		require_once '../lang/lang.php';
		?>
		</div>
	</div>	
	<div id="login_body" >

	<div id="login_left">
		<div  >
			<svg  style="height: 8em;width: 25em;">
				<use xlink:href="../public/images/svg/wikipali_login_page.svg#logo_login"></use>
			</svg>
		</div>	
		<div style="    padding: 1em 0 0 3.5em;font-weight: 300;">
		巴利文献编辑平台
		<ul style="padding-left: 1.2em;">
			<li>线上字典数据库</li>
			<li>用户数据分享</li>
			<li>共同协作编辑</li>
		</ul>
		</div>
	</div>	
	<div id="login_right">
		<div id = "login_form_div" class="fun_block" >
		<?php
		if(isset($error_comm)){
			echo '<div class="form_error">';
			echo $error_comm;
			echo '</div>';
		}
		if(isset($message_comm)){
			echo '<div class="form_help">';
			echo $message_comm;
			echo '</div>';
		}
		if($op=="new"){
		?>
			<div class="title">
			创建您的wikipāli账户
			</div>
			<div class="login_new">
				<span class="form_help">已有账户？</span><a href="index.php?language=<?php echo $currLanguage;?>">&nbsp;&nbsp;&nbsp;&nbsp;登入账户</a>
			</div>
			<div class="login_form" style="    padding: 3em 0 3em 0;">
			<form action="index.php" method="post">
				<div>
				    <span id='tip_nickname' class='form_field_name'>称呼（昵称）</span>
					<input type="input" name="nickname" value="<?php echo $nickname;?>" />
					<div class="form_help">
					其他人看到的您的名字
					</div>
					<div id="error_nickname" class="form_error">
					<?php
					if(isset($error_nickname)){echo $error_nickname;}
					?>
					</div>
					<select name="language" style="width: 100%;">
						<option>英文</option>
						<option>简体中文</option>
						<option>繁体中文</option>
					</select>
					<span id='tip_email' class='form_field_name'>电子邮件地址</span>
					<input type="input" name="email"  value="<?php echo $post_email;?>" />
					<span id='tip_username' class='form_field_name'>账号</span>
					<input type="input" name="username"  value="<?php echo $post_username;?>" />
					<div id="error_username" class="form_error">
					<?php
					if(isset($error_username)){echo $error_username;}
					?>
					</div>
					<div class="form_help">
					请使用英文字母和数字。标点符号仅限半角字符。并避免使用下列符号：? ! ; , :
					</div>
					<span id='tip_password' class='form_field_name'><?php echo $_local->gui->password;?></span>
					<input type="password" name="password"  value="<?php echo $post_password;?>" placeholder="<?php echo $_local->gui->password;?>"/>
					<input type="password" name="repassword"  value="<?php echo $post_password;?>" placeholder="确认密码"/>
					<div class="form_help">
					请混合使用五个字符以上的字母和数字
					</div>
					<div id="error_password" class="form_error">
					<?php
					if(isset($error_password)){echo $error_password;}
					?>
					</div>
					<input type="hidden" name="op" value="new" />
				</div>
				<div id="button_area">
					<input type="submit" value="继续" style="background-color: var(--link-hover-color);border-color: var(--link-hover-color);" />
				</div>
				</form>
			</div>			
			
		<?php
		}
		else{
		?>
			<div class="title">
			<?php
			if(isset($_POST["username"]) && $_username_ok==true){
				echo $_POST["username"];
			}
			else{
				echo "登入账户";
			}
			?>
			</div>
			<div class="login_new">
				<span class="form_help">新用户？</span><a href="index.php?language=<?php echo $currLanguage;?>&op=new">&nbsp;&nbsp;&nbsp;&nbsp;建立账户</a>
			</div>
			
			<div class="login_form" style="    padding: 3em 0 3em 0;">
			<form action="index.php" method="post">
				<div>
				<?php
				if(isset($_POST["username"]) && $_username_ok==true){
					echo "<span id='tip_password' class='form_field_name'>Password</span>";
					echo '<input type="password" name="password" placeholder="password"/>';
					echo "<input type=\"hidden\" name=\"username\" value=\"{$_POST["username"]}\"  />";
					if(isset($_post_error)){
						echo '<div id="error_nikename" class="form_error">';
						echo $_post_error;
						echo '</div>';
					}
				}
				else{
					echo "<span id='tip_username' class='form_field_name'>ID/Email</span>";
					echo '<input type="input" name="username" id="input_username" />';
					if(isset($_post_error)){
						echo '<div id="error_nikename" class="form_error">';
						echo $_post_error;
						echo '</div>';
					}
				}
				?>
				</div>
				<div id="button_area">
					<input type="submit" value="继续" style="background-color: var(--link-hover-color);border-color: var(--link-hover-color);" />
				</div>
				</form>
			</div>
			<div id="login_shortcut">
				<button class="form_help">使用Google登入</button>
				<button class="form_help">使用Facebook登入</button>
				<button class="form_help">使用微信登入</button>
			</div>			
			<?php
		}
			?>

		</div>	
	</div>	
	</div>

	</body>
</html>