<?php
/*
 * skymanla
 * �¶��ΰ��� ���� ÷������ �ٿ�ε� ���
 * */
include 'Directory.php';
include _MODULE;

$sql = "select * from $tb_online3hs_files where num_oid='$oid' and num_main='$num' and num_serial='$idx' and str_id='$bid'";
$data = $BoardDB->sqlFetch($sql);
if(!$data) WebApp::redirect("/?main","�߸��� �����Դϴ�.");
$filename = $data['str_real_up'];
$reail_filename = urldecode($data['str_realname_bogo']);

$HOST = ereg_replace('^www\.','',strtolower(getenv('HTTP_HOST')));

$file_dir =  _DIR_MAIN."/hosts/".$HOST."/".$data['str_upname_bogo'];

header('Content-Type: application/x-octetstream');
header('Content-Length: '.filesize($file_dir));
header('Content-Disposition: attachment; filename='.$reail_filename);
header('Content-Transfer-Encoding: binary');

$fp = fopen($file_dir, "r");
fpassthru($fp);
fclose($fp);

?>