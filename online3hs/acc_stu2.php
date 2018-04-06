<?php
/**
* skymanla
 * 체험학습 온라인 신청 - 현장체험학습 신청서
 * 체험학습 보고서 - 결제 업뎃
**/

if(!$_GET['mcode'] || !$_GET['num']) WebApp::redirect("/?main","잘못된 접근입니다.");

$code = $_GET['mcode'];
$num = $_GET['num'];
$type = $_GET['type'];

if($_SESSION[ADMIN]){//학교 관리자 올 결재(알바비점ㅋㅋㅋㅋㅋㅋㅋㅋㅋㅋㅋ)
	if($type=='p'){//학부모결재
		$sql = "update $tb_online3hs_bogo set str_bogo_acc1='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3hs.read2&mcode=$code&num=$num", "학부모 승인이 완료되었습니다.");
	}else if($type=='t'){
		$sql = "update $tb_online3hs_bogo set str_bogo_acc2='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3hs.read2&mcode=$code&num=$num", "담임 승인이 완료되었습니다.");
	/*}else if($type=='c'){
		$sql = "update $tb_online3hs_bogo set str_bogo_acc3='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3hs.read2&mcode=$code&num=$num", "담당부서 승인이 완료되었습니다.");*/
	}else if($type=='a'){
		$sql = "update $tb_online3hs_bogo set str_bogo_acc4='Y', str_bogo_acc5='Y', str_phase='Y', str_phase2='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		//최종 마무리
		$sql = "update $tb_online3hs set str_phase2='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3hs.read2&mcode=$code&num=$num", "교무부장 승인이 완료되었습니다.");
	}
}

//전결처리
/*if($_SESSION[acc_tmp1] == 7){//담당교직원
	$sql = "select str_bogo_acc2 from $tb_online3hs_bogo where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$chk_acc = $BoardDB->sqlFetch($sql);
	if($chk_acc[str_bogo_acc2] == "N"){
		WebApp::redirect("/?act=online3hs.read2&mcode=$code&num=$num", "아직 이전 승인이 이루어지지 않았습니다.");
	}
	$sql = "update $tb_online3hs_bogo set str_bogo_acc3='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3hs.read2&mcode=$code&num=$num", "승인이 완료되었습니다.");
}*/
if($_SESSION[acc_tmp3] == 8){//교무부장
	//선 승인 방지용
	$sql = "select str_bogo_acc2 from $tb_online3hs_bogo where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$chk_acc = $BoardDB->sqlFetch($sql);
	if($chk_acc[str_bogo_acc2] == "N"){
		WebApp::redirect("/?act=online3hs.read2&mcode=$code&num=$num", "아직 이전 승인이 이루어지지 않았습니다.");
	}
	$sql = "update $tb_online3hs_bogo set str_bogo_acc4='Y', str_bogo_acc5='Y', str_phase='Y', str_phase2='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	//최종 마무리
	$sql = "update $tb_online3hs set str_phase2='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3hs.read2&mcode=$code&num=$num", "승인이 완료되었습니다.");
}else if($_SESSION[acc_tmp2] == 99){//담임
	$sql = "select str_bogo_acc1 from $tb_online3hs_bogo where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$chk_acc = $BoardDB->sqlFetch($sql);
	if($chk_acc[str_bogo_acc1] == "N"){
		WebApp::redirect("/?act=online3hs.read2&mcode=$code&num=$num", "아직 이전 승인이 이루어지지 않았습니다.");
	}
	$sql = "update $tb_online3hs_bogo set str_bogo_acc2='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3hs.read2&mcode=$code&num=$num", "승인이 완료되었습니다.");
}else if($_SESSION[acc_tmp5]==98){//학부모
	$sql = "update $tb_online3hs_bogo set str_bogo_acc1='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3hs.read2&mcode=$code&num=$num", "승인이 완료되었습니다.");
}
?>