<?php
/**
* skymanla û�ɱ����߰� ü���н� ��û
**/

require_once 'class.DB.php';
include_once 'class.PageUtil.php';
$DB = &WebApp::singleton('DB');
$BoardDB = new DB('boarddb');

function debug(){
	if($_SERVER[REMOTE_ADDR]=='218.148.64.59'){
		return true;
	}else{
		return false;
	}	
}

$tb_online3 = "tab_online3";
$tb_online3hs = "tab_online3hs";
$tb_online3_bogo = "tab_online3_bogo";
$tb_online3hs_bogo = "tab_online3hs_bogo";
$tb_online3_files = "tab_online3_files";
$tb_online3hs_files = "tab_online3hs_files";

if($_SESSION[ADMIN] || $_SESSION[MEM_TYPE][0] == 'n'){
	$tpl->setCond('admin_chk', true);
}else{
	$tpl->setCond('admin_chk', false);
}

$tpl->assign("CSS_FILE","/access/online3.css");

$board_title = "ü���н� �¶��� ��û(��)";


?>
