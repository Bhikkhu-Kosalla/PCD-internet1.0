<?php
require 'checklogin.inc';
require '../public/config.php';

$FileName = $dir_user_base.$userid.$dir_mydocument.$_GET["filename"];
echo file_get_contents($FileName);

?>