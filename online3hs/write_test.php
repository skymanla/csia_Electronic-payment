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
		if($_SESSION[ADMIN] || $_SESSION[acc_tmp5]){
			//pass
		}else{
			$select_where = "and str_id='$str_id' and str_loginid='$str_loginid'";
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
		$tpl->define("CONTENT","/html/online3/write_test.htm");
		$tpl->parse("CONTENT");

		break;


	case "POST":
		
	
		print_r($_FILES);
		echo '<br /><br />';
		print_r($HTTP_POST_FILES);
		exit;


		$ip = $_SERVER[REMOTE_ADDR];
		$str_year = date('Y');
		$str_loginid = $bid;
		//최소값만 가져와서 DB에서 찾아보기
		$sql = "SELECT 
				num_oid, num_mcode, num_serial, str_name, str_tmp1, str_tmp2, str_tmp3, str_tmp4, str_s_date, str_e_date, str_date_tmp1, str_lec, str_lec_tmp, str_destini, str_par, str_par_tmp1, str_par_tmp2,
				str_in_tmp1, str_in_tmp2, str_in_tmp3, str_title, str_content, str_ip, str_to_time, str_acc_tmp1, str_acc_tmp2, str_acc_tmp3, str_acc_tmp4, str_acc_tmp5,
				TO_CHAR(dt_date, 'YYYY-MM-DD') dt_date, str_id, str_loginid, str_phase, str_phase2 
			  FROM $tb_online3
			 WHERE num_oid='$oid' and num_mcode='$code' and num_serial='$num' and str_id='$bid'"; 
		$data = $BoardDB->sqlFetch($sql);
				 
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
		
		if($upfiles) { 
			$num_file = count($upfiles);
		} else { 
			$num_file = 0;
		}
		
		file_ftp_conn();
		
		$online_dir = $FILE_FTP_ROOT."/hosts/$oid/online3";
		$online_dir_d1 = $FILE_FTP_ROOT."/hosts/$oid/online3/$str_year";
		if(!$FILE_FTP->chdir($online_dir_d1)){
			if($FILE_FTP->mkdir($online_dir)){
				if(!$FILE_FTP->chdir($online_dir_d1)){
					$FILE_FTP->mkdir($online_dir_d1);
				}
			}
		}
		
		$source_dir = $FILE_FTP_ROOT.'/tmp_upload/'.$phpsessid.'/'.$timestamp;
		if(!$FILE_FTP->chdir($source_dir)){
			if($FILE_FTP->mkdir($source_dir)){
				$FILE_FTP->mkdir($source_dir.'/'.$timestamp);
			}
		}
		$FILE_FTP->chdir($source_dir);
		if($upfile_list = $FILE_FTP->getList($source_dir)) {
			$num_file = count($upfile_list);
			foreach($upfile_list as $item) {
				$str_ftype = strtolower(substr($item['filename'],strrpos($item['filename'],".")+1));
				if(strlen($str_ftype)>4) $str_ftype = "unknown";
				$upload_files[] = array('filename'=>$item['filename'],'ext'=>strtolower($item['type']));
			}
		}
		print_r($upload_files);
		exit;
		$file_serial = $BoardDB->sqlFetchOne("SELECT num_serial FROM $tb_online3_files WHERE num_oid=$oid AND num_mcode=$code AND num_main=$num AND rownum=1") + 1;
		$FILE_FTP->chdir($source_dir);
		
		if($upload_files) {
			for($i=0,$cnt=count($upload_files);$i<$cnt;$i++) {
				$filerow = $upload_files[$i];
				$str_upfile = $filerow['filename'];
				$source_path = $source_dir."/".$str_upfile;
				$str_refile = "$mcode.$serial.$file_serial.$timestamp";
				$target_path = $online_dir_d1."/$str_refile";
				$num_down = 0;
				if(!$FILE_FTP->rename($str_upfile,$target_path)) $num_down = -1;
				$upload_files[$i]['str_refile'] = $str_refile;
				if(!$fsize = ftp_size($FILE_FTP->conn,$target_path)) $fsize = 0;
				$str_upfile_euc_kr=($skin=='default' || $skin=='whimoonbbs' || $skin=='defaultacc' || $skin=='default2' || $skin=='daily' || $skin=='dfvote')? iconv("UTF-8",'EUC-KR',$str_upfile):$str_upfile;
						
				$sql = "INSERT INTO $FILE_TABLE (
							NUM_OID,NUM_MCODE,NUM_MAIN,NUM_SERIAL,STR_UPFILE,STR_REFILE,NUM_DOWN,NUM_SIZE,STR_FTYPE
						) VALUES (
							$oid,$mcode,$serial,$file_serial,'".$str_upfile_euc_kr."','$str_refile',$num_down,$fsize,'".$upload_files[$i]['ext']."'
						)";
				$BoardDB->query($sql);
				$BoardDB->commit();
						
				$file_serial++;
				$num_file_normal++;
			}
		}
		@_rmdir($FILE_FTP,$phpsessid,$timestamp);
		$FILE_FTP->close();
		exit;
			

		if ($BoardDB->query($sql)) {
			$BoardDB->commit();
			
			if ($_POST['page']) {
				$pagego = "&page=".$_GET['page'];
			}

			WebApp::redirect("/?act=online3.list&mcode=$code","완료되었습니다.");
		} else {
			echo $sql;
			echo '<br />';
			exit;
			WebApp::moveback("접수신청 실패..재시도 바랍니다.");
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