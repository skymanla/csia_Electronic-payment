<?php
/**
* skymanla 청심국제중고 체험학습 신청
**/
if(!$_GET['mcode'] || !$_GET['num']) WebApp::redirect("/?main","잘못된 접근입니다.");

if($_SESSION[acc_tmp1]==7 || $_SESSION[acc_tmp2]==99 || $_SESSION[acc_tmp3]==8 || $_SESSION[acc_tmp5]==98 || $_SESSION[ADMIN]){
	//결제 담당자는 pass
	$sql = "select * from $tb_online3 where num_oid='$oid' and num_mcode='$mcode' and num_serial='$num'"; // and str_id='$str_id' and str_loginid='$str_loginid'";

	$targetID = $_GET['id2'];
}else{
	$str_id = $_SESSION['USERID'];
	$str_loginid = $_SESSION['LOGONID'];

	$targetID = $str_loginid;

	$sql = "select * from $tb_online3 where num_oid='$oid' and num_mcode='$mcode' and num_serial='$num' and str_id='$str_id' and str_loginid='$str_loginid'";
}


//승인이 다 난 경우에는 삭제를 할 수 없게 하자
$sql = "select * from $tb_online3 where num_oid='$oid' and num_mcode='$mcode' and num_serial='$num'"; // and str_id='$str_id' and str_loginid='$str_loginid'";
$chk = $BoardDB->sqlFetch($sql);

if($_SESSION[acc_tmp1]==7 || $_SESSION[acc_tmp2]==99 || $_SESSION[acc_tmp3]==8 || $_SESSION[ADMIN]){
	//pass 관리자의 경우 최종승인이 되던 말던 그냥 삭제
}else{
	if($chk[str_phase]=="Y" || $chk[str_phase2]=="Y" || $chk[str_acc_tmp1]=="Y" || $chk[str_acc_tmp2]=="Y" || $chk[str_acc_tmp3]=="Y" || $chk[str_acc_tmp4]=="Y"){
		//WebApp::redirect("/?act=online3.list&mcode=$mcode","승인이 되어 삭제가 불가능합니다.");
		WebApp::redirect("/?act=online3.read&&num=$num&mcode=$mcode","승인이 되어 삭제가 불가능합니다.");
	}
}

//print_r($_SESSION);
//삭제 변수 설정 - 삭제는 학생 및 학부모만 가능하게 -> 관리자까지 포함
if($_SESSION[acc_tmp1]==7 || $_SESSION[acc_tmp2]==99 || $_SESSION[acc_tmp3]==8 || $_SESSION[acc_tmp5]==98 || $_SESSION[ADMIN]){
	$sql = "delete from $tb_online3 where num_oid='$oid' and num_mcode='$mcode' and num_serial='$num'";

	// 삭제로그용 쿼리
	$delQuery = $sql;

	$BoardDB->query($sql);
	$BoardDB->commit();
	//신청서가 삭제되면 보고서도 같이 삭제가 되게 하자
	$sql2 = "delete from $tb_online3_bogo where num_oid='$oid' and num_mcode='$mcode' and num_serial='$num'";
	$BoardDB->query($sql2);
	$BoardDB->commit();
}else{
	$sql = "delete from $tb_online3 where num_oid='$oid' and num_mcode='$mcode' and num_serial='$num' and str_id='$str_id' and str_loginid='$str_loginid'";

	// 삭제로그용 쿼리
	$delQuery = $sql;

	$BoardDB->query($sql);
	$BoardDB->commit();
	//신청서가 삭제되면 보고서도 같이 삭제가 되게 하자
	$sql2 = "delete from $tb_online3_bogo where num_oid='$oid' and num_mcode='$mcode' and num_serial='$num'";
	$BoardDB->query($sql2);
	$BoardDB->commit();
}
//echo $sql;
//exit;


// 로그 파일..메뉴 삭제시 로그 남겨야게츰...2010-04-30.juni
// 로그 파일 용량체크 해서 지나치게 크면 백업후 새파일
if(@filesize ("log/csiaOnline3Delete.log") > 1024*1024){
	@rename ("log/csiaOnline3Delete.log","csiaOnline3Delete.log".".".time());
}
$logfp=fopen("log/csiaOnline3Delete.log", "a+");
chmod ("log/csiaOnline3Delete.log",0777);
$timestamp=date("Y-m-d H:i:s");
fwrite($logfp, "[".$timestamp."] [".$_SESSION['LOGONID']."] [".$targetID."] [신청서 삭제] ".$delQuery." [".$REMOTE_ADDR."]\n");
// 로그 종료


WebApp::redirect("/?act=online3.list&mcode=$mcode","삭제되었습니다.");
?>