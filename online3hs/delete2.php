<?php
if(!$_GET['mcode'] || !$_GET['num']) WebApp::redirect("/?main","�߸��� �����Դϴ�.");
if($_SESSION[ADMIN] || $_SESSION[acc_tmp1]==7 || $_SESSION[acc_tmp2]==99 || $_SESSION[acc_tmp3]==8 || $_SESSION[acc_tmp5]==98){
	$str_id = $_GET['id'];
	$str_loginid = $_GET['id2'];

	$targetID = $_GET['id2'];

}else{
	$str_id = $_SESSION['USERID'];
	$str_loginid = $_SESSION['LOGONID'];

	$targetID = $str_loginid;
}

//������ �� �� ��쿡�� ������ �� �� ���� ����
$sql = "select * from $tb_online3hs where num_oid='$oid' and num_mcode='$mcode' and num_serial='$num' and str_id='$str_id' and str_loginid='$str_loginid'";
$chk = $BoardDB->sqlFetch($sql);
$sql2 = "select * from $tb_online3hs_bogo where num_oid='$oid' and num_mcode='$mcode' and num_serial='$num' and str_id='$str_id' and str_loginid='$str_loginid'";
$chk2 = $BoardDB->sqlFetch($sql2);
if($_SESSION[acc_tmp1]==7 || $_SESSION[acc_tmp2]==99 || $_SESSION[acc_tmp3]==8 || $_SESSION[ADMIN]){
	//pass �������� ��� ���������� �Ǵ� ���� �׳� ����
}else{
	//if($chk[str_phase2]=="Y" || $chk[str_bogo_acc2]=="Y"){
	//	WebApp::redirect("/?act=online3hs.list&mcode=$mcode","������ �Ǿ� ������ �Ұ����մϴ�.");
	if($chk[str_phase2]=="Y" || $chk2[str_bogo_acc2]=="Y"){
		WebApp::redirect("/?act=online3hs.read2&&num=$num&mcode=$mcode","������ �Ǿ� ������ �Ұ����մϴ�.");
	}
}

if (getenv('REMOTE_ADDR') == '125.130.18.5') {
	//echo "asadf";exit;
}

$sql = "delete from $tb_online3hs_bogo where num_oid='$oid' and num_mcode='$mcode' and num_serial='$num' and str_id='$str_id' and str_loginid='$str_loginid'";


// �����α׿� ����
$delQuery = $sql;

if($BoardDB->query($sql)){
	$BoardDB->commit();
	
	//÷������ ����
	$sql = "delete from $tb_online3hs_files where num_oid='$oid' and num_mcode='$mcode' and num_main='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();


	// �α� ����..�޴� ������ �α� ���ܾ߰���...2010-04-30.juni
	// �α� ���� �뷮üũ �ؼ� ����ġ�� ũ�� ����� ������
	if(@filesize ("log/csiaOnline3hsDelete.log") > 1024*1024){
		@rename ("log/csiaOnline3hsDelete.log","csiaOnline3hsDelete.log".".".time());
	}
	$logfp=fopen("log/csiaOnline3hsDelete.log", "a+");
	chmod ("log/csiaOnline3hsDelete.log",0777);
	$timestamp=date("Y-m-d H:i:s");
	fwrite($logfp, "[".$timestamp."] [".$_SESSION['LOGONID']."] [".$targetID."]  [���� ����] ".$delQuery." [".$REMOTE_ADDR."]\n");
	// �α� ����

	WebApp::redirect("/?act=online3hs.list&mcode=$mcode","�����Ǿ����ϴ�.");

}else{
	echo "error!!!";
}
?>