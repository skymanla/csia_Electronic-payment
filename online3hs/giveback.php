<?php
/**
* skymanla
 * ü���н� �¶��� ��û - ����ü���н� ��û�� - �ݷ�
 * DB tmp Į�� ���� ����
 * str_acc_tmp1 = ����
 * str_acc_tmp2 = ���μ�
 * str_acc_tmp3 = ��������
 * str_acc_tmp4 = ���� & ����
 * str_acc_tmp5 = �кθ�  
**/

if(!$_GET['mcode'] || !$_GET['num']) WebApp::redirect("/?main","�߸��� �����Դϴ�.");

$code = $_GET['mcode'];
$num = $_GET['num'];
$type = $_GET['type'];

 if($_SESSION[ADMIN]){
	if($type == "p"){
		$sql = "update $tb_online3hs set str_acc_tmp5='N' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "�кθ�ݷ��� �Ϸ�Ǿ����ϴ�.");
	}else if($type == "t"){
		$sql = "update $tb_online3hs set str_acc_tmp1='N' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "���ӹݷ��� �Ϸ�Ǿ����ϴ�.");
	/*}else if($type=="c"){
		$sql = "update $tb_online3hs set str_acc_tmp2='N' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "���μ��ݷ��� �Ϸ�Ǿ����ϴ�.");*/
	}else if($type=="a"){
		$sql = "update $tb_online3hs set str_acc_tmp3='N', str_acc_tmp4='N', str_phase='N' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "��������ݷ��� �Ϸ�Ǿ����ϴ�.");
	}
}

//����ó��
/*if($_SESSION[acc_tmp1] == 7){//��米����
	$sql = "select str_acc_tmp3 from $tb_online3hs where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$chk_acc = $BoardDB->sqlFetch($sql);
	if($chk_acc[str_acc_tmp3] == "Y"){
		WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "���� ������ �ݷ��� ���� �ʾҽ��ϴ�..");
	}
	$sql = "update $tb_online3hs set str_acc_tmp2='N' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "�ݷ��� �Ϸ�Ǿ����ϴ�.");
}
 */ 
if($_SESSION[acc_tmp3] == 8){//��������
	//�� ���� ������
	$sql = "update $tb_online3hs set str_acc_tmp3='N', str_acc_tmp4='N', str_phase='N' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "�ݷ��� �Ϸ�Ǿ����ϴ�.");
}else if($_SESSION[acc_tmp2] == 99){//����
	$sql = "select str_acc_tmp3 from $tb_online3hs where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$chk_acc = $BoardDB->sqlFetch($sql);
	if($chk_acc[str_acc_tmp3] == "Y"){
		WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "���� ������ �ݷ��� ���� �ʾҽ��ϴ�..");
	}
	$sql = "update $tb_online3hs set str_acc_tmp1='N' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "�ݷ��� �Ϸ�Ǿ����ϴ�.");
}else if($_SESSION[acc_tmp5]==98){//�кθ�
	$sql = "select str_acc_tmp1 from $tb_online3hs where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$chk_acc = $BoardDB->sqlFetch($sql);
	if($chk_acc[str_acc_tmp1] == "Y"){
		WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "���� ������ �ݷ��� ���� �ʾҽ��ϴ�..");
	}
	$sql = "update $tb_online3hs set str_acc_tmp5='N' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "�ݷ��� �Ϸ�Ǿ����ϴ�.");
}
?>