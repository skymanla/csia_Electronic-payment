<?php
/**
* skymanla û�ɱ����߰� ü���н� ��û
**/
if(!$_GET['mcode'] || !$_GET['num']) WebApp::redirect("/?main","�߸��� �����Դϴ�.");

if($_SESSION[acc_tmp1]==7 || $_SESSION[acc_tmp2]==99 || $_SESSION[acc_tmp3]==8 || $_SESSION[acc_tmp5]==98 || $_SESSION[ADMIN]){
	//���� ����ڴ� pass
	$sql = "select * from $tb_online3hs where num_oid='$oid' and num_mcode='$mcode' and num_serial='$num'"; // and str_id='$str_id' and str_loginid='$str_loginid'";

	$targetID = $_GET['id2'];
}else{
	$str_id = $_SESSION['USERID'];
	$str_loginid = $_SESSION['LOGONID'];

	$targetID = $str_loginid;

	$sql = "select * from $tb_online3hs where num_oid='$oid' and num_mcode='$mcode' and num_serial='$num' and str_id='$str_id' and str_loginid='$str_loginid'";
}


//������ �� �� ��쿡�� ������ �� �� ���� ����
$sql = "select * from $tb_online3hs where num_oid='$oid' and num_mcode='$mcode' and num_serial='$num'"; // and str_id='$str_id' and str_loginid='$str_loginid'";
$chk = $BoardDB->sqlFetch($sql);

if($_SESSION[acc_tmp1]==7 || $_SESSION[acc_tmp2]==99 || $_SESSION[acc_tmp3]==8 || $_SESSION[ADMIN]){
	//pass �������� ��� ���������� �Ǵ� ���� �׳� ����
}else{
	if($chk[str_phase]=="Y" || $chk[str_phase2]=="Y" || $chk[str_acc_tmp1]=="Y" || $chk[str_acc_tmp2]=="Y" || $chk[str_acc_tmp3]=="Y" || $chk[str_acc_tmp4]=="Y"){
		//WebApp::redirect("/?act=online3hs.list&mcode=$mcode","������ �Ǿ� ������ �Ұ����մϴ�.");
		WebApp::redirect("/?act=online3hs.read&&num=$num&mcode=$mcode","������ �Ǿ� ������ �Ұ����մϴ�.");
	}
}

//print_r($_SESSION);
//���� ���� ���� - ������ �л� �� �кθ� �����ϰ� -> �����ڱ��� ����
if($_SESSION[acc_tmp1]==7 || $_SESSION[acc_tmp2]==99 || $_SESSION[acc_tmp3]==8 || $_SESSION[acc_tmp5]==98 || $_SESSION[ADMIN]){
	$sql = "delete from $tb_online3hs where num_oid='$oid' and num_mcode='$mcode' and num_serial='$num'";

	// �����α׿� ����
	$delQuery = $sql;

	$BoardDB->query($sql);
	$BoardDB->commit();
	//��û���� �����Ǹ� ������ ���� ������ �ǰ� ����
	$sql2 = "delete from $tb_online3hs_bogo where num_oid='$oid' and num_mcode='$mcode' and num_serial='$num'";
	$BoardDB->query($sql2);
	$BoardDB->commit();
}else{
	$sql = "delete from $tb_online3hs where num_oid='$oid' and num_mcode='$mcode' and num_serial='$num' and str_id='$str_id' and str_loginid='$str_loginid'";

	// �����α׿� ����
	$delQuery = $sql;

	$BoardDB->query($sql);
	$BoardDB->commit();
	//��û���� �����Ǹ� ������ ���� ������ �ǰ� ����
	$sql2 = "delete from $tb_online3hs_bogo where num_oid='$oid' and num_mcode='$mcode' and num_serial='$num'";
	$BoardDB->query($sql2);
	$BoardDB->commit();
}
//echo $sql;
//exit;


// �α� ����..�޴� ������ �α� ���ܾ߰���...2010-04-30.juni
// �α� ���� �뷮üũ �ؼ� ����ġ�� ũ�� ����� ������
if(@filesize ("log/csiaOnline3hsDelete.log") > 1024*1024){
	@rename ("log/csiaOnline3hsDelete.log","csiaOnline3hsDelete.log".".".time());
}
$logfp=fopen("log/csiaOnline3hsDelete.log", "a+");
chmod ("log/csiaOnline3hsDelete.log",0777);
$timestamp=date("Y-m-d H:i:s");
fwrite($logfp, "[".$timestamp."] [".$_SESSION['LOGONID']."] [".$targetID."] [��û�� ����] ".$delQuery." [".$REMOTE_ADDR."]\n");
// �α� ����


WebApp::redirect("/?act=online3hs.list&mcode=$mcode","�����Ǿ����ϴ�.");
?>