<?php
/**
* skymanla
 * 체험학습 온라인 신청 - 현장체험학습 신청서 - 반려
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
		$sql = "update $tb_online3hs set str_acc_tmp5='N' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "학부모반려가 완료되었습니다.");
	}else if($type == "t"){
		$sql = "update $tb_online3hs set str_acc_tmp1='N' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "담임반려가 완료되었습니다.");
	/*}else if($type=="c"){
		$sql = "update $tb_online3hs set str_acc_tmp2='N' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "담당부서반려가 완료되었습니다.");*/
	}else if($type=="a"){
		$sql = "update $tb_online3hs set str_acc_tmp3='N', str_acc_tmp4='N', str_phase='N' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "교무부장반려가 완료되었습니다.");
	}
}

//전결처리
/*if($_SESSION[acc_tmp1] == 7){//담당교직원
	$sql = "select str_acc_tmp3 from $tb_online3hs where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$chk_acc = $BoardDB->sqlFetch($sql);
	if($chk_acc[str_acc_tmp3] == "Y"){
		WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "상위 승인의 반려가 되지 않았습니다..");
	}
	$sql = "update $tb_online3hs set str_acc_tmp2='N' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "반려가 완료되었습니다.");
}
 */ 
if($_SESSION[acc_tmp3] == 8){//교무부장
	//선 승인 방지용
	$sql = "update $tb_online3hs set str_acc_tmp3='N', str_acc_tmp4='N', str_phase='N' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "반려가 완료되었습니다.");
}else if($_SESSION[acc_tmp2] == 99){//담임
	$sql = "select str_acc_tmp3 from $tb_online3hs where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$chk_acc = $BoardDB->sqlFetch($sql);
	if($chk_acc[str_acc_tmp3] == "Y"){
		WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "상위 승인의 반려가 되지 않았습니다..");
	}
	$sql = "update $tb_online3hs set str_acc_tmp1='N' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "반려가 완료되었습니다.");
}else if($_SESSION[acc_tmp5]==98){//학부모
	$sql = "select str_acc_tmp1 from $tb_online3hs where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$chk_acc = $BoardDB->sqlFetch($sql);
	if($chk_acc[str_acc_tmp1] == "Y"){
		WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "상위 승인의 반려가 되지 않았습니다..");
	}
	$sql = "update $tb_online3hs set str_acc_tmp5='N' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "반려가 완료되었습니다.");
}
?>