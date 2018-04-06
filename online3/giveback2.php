<?php
/**
* skymanla
 * 체험학습 온라인 신청 - 현장체험학습 보고서 - 반려
 * DB tmp 칼럼 내용 정리
 * str_acc_tmp1 = 담임
 * str_acc_tmp2 = 담당부서
 * str_acc_tmp3 = 교무부장
 * str_acc_tmp4 = 교감 & 교장
 * str_acc_tmp5 = 학부모  
**/

if(!$_GET['mcode'] || !$_GET['num']) WebApp::redirect("/?main","잘못된 접근입니다.");

$code = $_GET['mcode'];
$num = $_GET['num'];
$type = $_GET['type'];

if($_SESSION[ADMIN]){
	if($type == "p"){
		$sql = "update $tb_online3_bogo set str_bogo_acc1='N' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3.read&mcode=$code&num=$num", "학부모반려가 완료되었습니다.");
	}else if($type == "t"){
		$sql = "update $tb_online3_bogo set str_bogo_acc2='N' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3.read&mcode=$code&num=$num", "담임반려가 완료되었습니다.");
	}/*else if($type=="c"){
		$sql = "update $tb_online3_bogo set str_bogo_acc3='N' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3.read&mcode=$code&num=$num", "담당부서반려가 완료되었습니다.");
	}*/
}

//전결처리
/*if($_SESSION[acc_tmp1] == 7){//담당교직원
	$sql = "select str_bogo_acc4 from $tb_online3_bogo where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$chk_acc = $BoardDB->sqlFetch($sql);
	if($chk_acc[str_bogo_acc4] == "Y"){
		WebApp::redirect("/?act=online3.read2&mcode=$code&num=$num", "상위 승인의 반려가 되지 않았습니다..");
	}
	$sql = "update $tb_online3_bogo set str_bogo_acc3='N' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3.read2&mcode=$code&num=$num", "반려가 완료되었습니다.");
}
 * 
 */
if($_SESSION[acc_tmp2] == 99){//담임
	$sql = "select str_bogo_acc4 from $tb_online3_bogo where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$chk_acc = $BoardDB->sqlFetch($sql);
	if($chk_acc[str_bogo_acc4] == "Y"){
		WebApp::redirect("/?act=online3.read2&mcode=$code&num=$num", "상위 승인의 반려가 되지 않았습니다..");
	}
	$sql = "update $tb_online3_bogo set str_bogo_acc2='N' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3.read2&mcode=$code&num=$num", "반려가 완료되었습니다.");
}else if($_SESSION[acc_tmp5]==98){//학부모
	$sql = "select str_bogo_acc2 from $tb_online3_bogo where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$chk_acc = $BoardDB->sqlFetch($sql);
	if($chk_acc[str_bogo_acc1] == "Y"){
		WebApp::redirect("/?act=online3.read2&mcode=$code&num=$num", "상위 승인의 반려가 되지 않았습니다..");
	}
	$sql = "update $tb_online3_bogo set str_bogo_acc1='N' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3.read2&mcode=$code&num=$num", "반려가 완료되었습니다.");
}
?>