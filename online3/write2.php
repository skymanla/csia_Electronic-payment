<?php
/**
* skymanla
 * 체험학습 온라인 신청 - 현장체험학습 신청서
**/

include "Directory.php";
include _DB_INFO;
include _MODULE;

# 변수 설정
$_VARS = decodeVARS();

$phpsessid = $_COOKIE['PHPSESSID'];
$timestamp = date('U');

// DB 연결
$DB = &WebApp::singleton('DB');
$BoardDB = new DB('boarddb');

// FTP
include_once "module/inc.file_ftp.conn.php";
file_ftp_conn(false);

switch ($REQUEST_METHOD) {
	case "GET":

		if(!$_GET['mcode']) WebApp::redirect("/?main","잘못된 접근입니다.");
		//본인 글을 찾기 위한...
		$str_id = $_SESSION['USERID'];
		$str_loginid = $_SESSION['LOGONID'];
		$code = $_GET['mcode'];
		$num = $_GET['num'];
		$timestamp = date('U');
		$bbsType="file";
		if($_SESSION[ADMIN] || $_SESSION[acc_tmp1] || $_SESSION[acc_tmp2] || $_SESSION[acc_tmp3] || $_SESSION[acc_tmp5]){
			//결재권자는 pass
		}else{
			//학생
			$select_where = "and str_id='$str_id' and str_loginid='$str_loginid'";
		}
		//신청서 결재가 완료되지 않은 경우
		$sql = "select * from $tb_online3 where num_oid='$oid' and num_mcode='$code' and num_serial='$num' $select_where";
		$chk = $BoardDB->sqlFetch($sql);
		
		if($chk['str_phase']!="Y"){
			WebApp::redirect("/?act=online3.read&mcode=$code&num=$num","아직 신청서 결재가 완료되지 않았습니다.");
		}
		//보고서 정보 가져오기
		$sql = "select * from $tb_online3_bogo where num_oid='$oid' and num_mcode='$code' and num_serial='$num' $select_where";
		$bogo = $BoardDB->sqlFetch($sql);
		if($bogo){
			WebApp::redirect("/?act=online3.modify2&num=$num&mcode=$code","이미 작성한 보고서가 있습니다.수정 페이지로 이동합니다.");
		}
		//신청서 정보 가져오기
		$sql = "SELECT 
				num_oid, num_mcode, num_serial, str_name, str_tmp1, str_tmp2, str_tmp3, str_tmp4, str_s_date, str_e_date, str_date_tmp1, str_lec, str_lec_tmp, str_destini, str_par, str_par_tmp1, str_par_tmp2,
				str_in_tmp1, str_in_tmp2, str_in_tmp3, str_title, str_content, str_ip, str_to_time, str_acc_tmp1, str_acc_tmp2, str_acc_tmp3, str_acc_tmp4, str_acc_tmp5,
				TO_CHAR(dt_date, 'YYYY-MM-DD') dt_date, str_id, str_loginid, str_phase, str_phase2 
			  FROM $tb_online3
			 WHERE num_oid='$oid' and num_mcode='$code' and num_serial='$num' $select_where";
		$data = $BoardDB->sqlFetch($sql);
		if(empty($data)) WebApp::redirect("/?main","잘못된 접근입니다.");
		@_format_data(&$data);
		$tpl->assign($data);
		
		//print_r($_SESSION);
		
		//신청날짜 생성
		$tpl->assign(array("year"=>date("Y"), "month"=>date("m"), "day"=>date("d"), "code"=>$_GET['mcode']));
		
		$tpl->setLayout('sub');
		///if($_SERVER[REMOTE_ADDR]=='125.130.18.5'){//test
		//	$tpl->define("CONTENT","/html/online3/write2_test.htm");
		//}else{
			$tpl->define("CONTENT","/html/online3/write2.htm");
		//}
		
		$tpl->parse("CONTENT");

		break;


	case "POST":

		// 본문 500자 체크
		$ChkLenboardContent = preg_replace("/\s+/", "", $boardContent);
		if(mb_strlen($ChkLenboardContent, 'euc-kr')<500)WebApp::moveBack('보고서 내용은 공백제외 500자 이상으로 작성하세요.');

		$ip = $_SERVER[REMOTE_ADDR];
		$str_year = date('Y');
		$str_loginid = $bid;
		//최소값만 가져와서 DB에서 찾아보기
		$sql = "SELECT 
				num_oid, num_mcode, num_serial, str_name, str_tmp1, str_tmp2, str_tmp3, str_tmp4, str_s_date, str_e_date, str_date_tmp1, str_lec, str_lec_tmp, str_destini, str_par, str_par_tmp1, str_par_tmp2,
				str_in_tmp1, str_in_tmp2, str_in_tmp3, str_title, str_content, str_ip, str_to_time, str_acc_tmp1, str_acc_tmp2, str_acc_tmp3, str_acc_tmp4, str_acc_tmp5,
				TO_CHAR(dt_date, 'YYYY-MM-DD') dt_date, str_id, str_loginid, str_phase, str_phase2, str_partcode, str_year
			  FROM $tb_online3
			 WHERE num_oid='$oid' and num_mcode='$code' and num_serial='$num' and str_id='$bid'"; 
		$data = $BoardDB->sqlFetch($sql);
		$str_name = $data['str_name'];
		$str_id = $data['str_loginid'];
		$str_partcode = $data['str_partcode'];
		//DB column
		$insert_col = "num_oid,num_mcode,num_serial,str_name,str_bogo_title,str_bogo_text,str_ip,str_to_time,dt_date,str_id,str_loginid,str_partcode,str_phase,str_phase2,str_year";
		//insert values
		$insert_val = "'$oid','$code','$num','".$data['str_name']."','$title','$boardContent','$ip','".$data['str_date_tmp1']."',SYSDATE,'$bid','".$data['str_loginid']."','".$data['str_partcode']."',
						'".$data['str_phase']."','".$data['str_phase2']."','$str_year'";

		$sql = "insert into $tb_online3_bogo
				($insert_col)
				values
				($insert_val) ";
		//echo $sql; exit;
		//file 처리
		
		$pathdir = _DIR_MAIN.'/hosts/'.$HOST.'/files/online3';
		if(!is_dir( $pathdir)) mkdir($pathdir,0777);
		
		$pathdir_y = _DIR_MAIN.'/hosts/'.$HOST.'/files/online3/'.$str_year;
		if(!is_dir( $pathdir_y)) mkdir($pathdir_y,0777);
		
		$pathdir_s = _DIR_MAIN.'/hosts/'.$HOST.'/files/online3/'.$str_year."/".$bid;
		if(!is_dir($pathdir_s)) mkdir($pathdir_s,0777);
		
		$pathdir_d = _DIR_MAIN.'/hosts/'.$HOST.'/files/online3/'.$str_year."/".$bid."/".$num;
		if(!is_dir($pathdir_d)) mkdir($pathdir_d,0777);
		
		$updirpath = "files/online3/$str_year/$bid/$num";
		$file_sql="select max(num_serial) from $tb_online3_files where num_oid=".$oid." and num_mcode=".$code." and num_main=".$num;
		$file_serial = $BoardDB->sqlFetchOne($file_sql);
		$file_serial = ($file_serial) ? $file_serial+1 : 1;
		$cnt = count($_FILES['upfiles']['name']);
		
		for($i=0;$i<$cnt;$i++){
			$file_name=$_FILES['upfiles']['name'][$i];
			if($file_name){
				$file_type=strtolower(array_pop(explode(".",$file_name)));
				$file_tmp_name = $_FILES['upfiles']['tmp_name'][$i];
				$file_size = $_FILES['upfiles']['size'][$i];
				$str_ftype = strtolower(substr($_FILES['upfiles']['name'][$i],strrpos($_FILES['upfiles']['name'][$i],".")+1));
				$up_file_name_s = $bid.".".$mcode.".".$num.".".$i.".".$str_ftype;
				$up_file_name = "files/online3/$str_year/$bid/$num/".$up_file_name_s;
				//$up_file_name = iconv("UTF-8",'EUC-KR',$up_file_name);
				//echo $up_file_name;exit;
				$re_file_name = $_FILES['upfiles']['name'][$i];
				if(is_uploaded_file($file_tmp_name) && ($file_size>0)){
					$destination = $pathdir_d."/".$up_file_name_s;
					if(move_uploaded_file($file_tmp_name, $destination)){
						$file_sql="insert into $tb_online3_files values ('$oid','$code','$num','$file_serial','$str_name','$bid','$str_id','$str_partcode','$re_file_name','$up_file_name','$ip',SYSDATE,'$str_year','$up_file_name_s')";
						if($BoardDB->query($file_sql)) {
							$BoardDB->commit();
							$file_serial++;
						}
					}
				}
			}
		}
		if ($BoardDB->query($sql)) {
			$BoardDB->commit();
			
			if ($_POST['page']) {
				$pagego = "&page=".$_GET['page'];
			}

			// 로그 파일..메뉴 삭제시 로그 남겨야게츰...2010-04-30.juni
			// 로그 파일 용량체크 해서 지나치게 크면 백업후 새파일
			if(@filesize ("log/csiaOnline3Write.log") > 1024*1024){
				@rename ("log/csiaOnline3Write.log","csiaOnline3Write.log".".".time());
			}
			$logfp=fopen("log/csiaOnline3Write.log", "a+");
			chmod ("log/csiaOnline3Write.log",0777);
			$timestamp=date("Y-m-d H:i:s");
			$insertQuery = "'$oid','$code','$num','".$data['str_name']."','$title','본문생략','$ip','".$data['str_date_tmp1']."',SYSDATE,'$bid','".$data['str_loginid']."','".$data['str_partcode']."', '".$data['str_phase']."','".$data['str_phase2']."','$str_year'";
			fwrite($logfp, "[".$timestamp."] [".$_SESSION['LOGONID']."] [보고서 작성] ".$insertQuery." [".$REMOTE_ADDR."]\n");
			// 로그 종료

			WebApp::redirect("/?act=online3.list&mcode=$code","완료되었습니다.");
		} else {
			//echo $sql;
			//echo '<br />';
			//exit;
			WebApp::moveback("보고서 저장 실패\n재시도 바랍니다.");
		}

		break;
}



function _format_data(&$arr) {
	global $oid, $mcode;
	//중고 구분
	if($arr[str_tmp1] == "grade_m"){
		$arr[str_tmp1] = "중";
	}else if($arr[str_tmp1] == "grade_h"){
		$arr[str_tmp1] = "고";
	}
	//날짜 자르기
	$f_s_date = explode("-", $arr['str_s_date']);
	$arr['s_date_y'] = $f_s_date[0];
	$arr['s_date_m'] = $f_s_date[1];
	$arr['s_date_d'] = $f_s_date[2];
	$f_e_date = explode("-", $arr['str_e_date']);
	$arr['e_date_y'] = $f_e_date[0];
	$arr['e_date_m'] = $f_e_date[1];
	$arr['e_date_d'] = $f_e_date[2];
	
	//학습형태
	switch($arr[str_lec]){
		case "choos_g1":
			$arr[str_lec] = "가족여행";
			break;
		case "choos_g2":
			$arr[str_lec] = "친·인척 방문";
			break;
		case "choos_g3":
			$arr[str_lec] = "견학 활동";
			break;
		case "choos_g4":
			$arr[str_lec] = "체험 활동";
			break;
		case "choos_g5":
			$arr[str_lec] = "기타";
			$arr[str_lec_tmp1] = "사유 : ".$arr[str_lec_tmp];
			break;
	}
	//수정하는 경우 수정등록하는 날짜로 재변경
	$fin_date = explode("-", $arr[dt_date]);
	$arr['fin_y']= $fin_date[0];
	$arr['fin_m'] = $fin_date[1];
	$arr['fin_d'] = $fin_date[2];
}

function _rmdir($FILE_FTP,$phpsessid,$timestamp) {
	global $FILE_FTP_ROOT;
	$sess_dir = $FILE_FTP_ROOT."/tmp_upload/".$phpsessid;
	$FILE_FTP->chdir($sess_dir);
	$FILE_FTP->rmdir($timestamp);
	$FILE_FTP->chdir($FILE_FTP_ROOT."/tmp_upload");
	$FILE_FTP->rmdir($phpsessid);
}


?>