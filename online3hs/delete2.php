<?php
if(!$_GET['mcode'] || !$_GET['num']) WebApp::redirect("/?main","잘못된 접근입니다.");
if($_SESSION[ADMIN] || $_SESSION[acc_tmp1]==7 || $_SESSION[acc_tmp2]==99 || $_SESSION[acc_tmp3]==8 || $_SESSION[acc_tmp5]==98){
	$str_id = $_GET['id'];
	$str_loginid = $_GET['id2'];

	$targetID = $_GET['id2'];

}else{
	$str_id = $_SESSION['USERID'];
	$str_loginid = $_SESSION['LOGONID'];

	$targetID = $str_loginid;
}

//승인이 다 난 경우에는 삭제를 할 수 없게 하자
$sql = "select * from $tb_online3hs where num_oid='$oid' and num_mcode='$mcode' and num_serial='$num' and str_id='$str_id' and str_loginid='$str_loginid'";
$chk = $BoardDB->sqlFetch($sql);
$sql2 = "select * from $tb_online3hs_bogo where num_oid='$oid' and num_mcode='$mcode' and num_serial='$num' and str_id='$str_id' and str_loginid='$str_loginid'";
$chk2 = $BoardDB->sqlFetch($sql2);
if($_SESSION[acc_tmp1]==7 || $_SESSION[acc_tmp2]==99 || $_SESSION[acc_tmp3]==8 || $_SESSION[ADMIN]){
	//pass 관리자의 경우 최종승인이 되던 말던 그냥 삭제
}else{
	//if($chk[str_phase2]=="Y" || $chk[str_bogo_acc2]=="Y"){
	//	WebApp::redirect("/?act=online3hs.list&mcode=$mcode","승인이 되어 삭제가 불가능합니다.");
	if($chk[str_phase2]=="Y" || $chk2[str_bogo_acc2]=="Y"){
		WebApp::redirect("/?act=online3hs.read2&&num=$num&mcode=$mcode","승인이 되어 삭제가 불가능합니다.");
	}
}

if (getenv('REMOTE_ADDR') == '125.130.18.5') {
	//echo "asadf";exit;
}

$sql = "delete from $tb_online3hs_bogo where num_oid='$oid' and num_mcode='$mcode' and num_serial='$num' and str_id='$str_id' and str_loginid='$str_loginid'";


// 삭제로그용 쿼리
$delQuery = $sql;

if($BoardDB->query($sql)){
	$BoardDB->commit();
	
	//첨부파일 삭제
	$sql = "delete from $tb_online3hs_files where num_oid='$oid' and num_mcode='$mcode' and num_main='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();


	// 로그 파일..메뉴 삭제시 로그 남겨야게츰...2010-04-30.juni
	// 로그 파일 용량체크 해서 지나치게 크면 백업후 새파일
	if(@filesize ("log/csiaOnline3hsDelete.log") > 1024*1024){
		@rename ("log/csiaOnline3hsDelete.log","csiaOnline3hsDelete.log".".".time());
	}
	$logfp=fopen("log/csiaOnline3hsDelete.log", "a+");
	chmod ("log/csiaOnline3hsDelete.log",0777);
	$timestamp=date("Y-m-d H:i:s");
	fwrite($logfp, "[".$timestamp."] [".$_SESSION['LOGONID']."] [".$targetID."]  [보고서 삭제] ".$delQuery." [".$REMOTE_ADDR."]\n");
	// 로그 종료

	WebApp::redirect("/?act=online3hs.list&mcode=$mcode","삭제되었습니다.");

}else{
	echo "error!!!";
}
?>