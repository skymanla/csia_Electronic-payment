<?php
/**
* skymanla
 * 체험학습 온라인 신청 - 현장체험학습 신청서
**/

// 쿠키
$phpsessid = $_COOKIE['PHPSESSID'];

// DB 연결
$DB = &WebApp::singleton('DB');
$BoardDB = new DB('boarddb');

// FTP
include_once "module/inc.file_ftp.conn.php";
include_once "module/inc.pds_ftp.conn.php";
file_ftp_conn(false);
pds_ftp_conn(false);

switch ($REQUEST_METHOD) {
	case "GET":

		if(!$_GET['mcode']) WebApp::redirect("/?main","잘못된 접근입니다.");
		//본인 글을 찾기 위한...
		$timestamp = date('U');
		$avail_size=5242880*2;
		
		// 2014-09-30:cyj: antispam 방지2
		$_anti_tmp=rand(1,9); // 1-9중 난수선택
		$_anti_nwdate= $_anti_tmp.date("Yd");    //오늘날짜 count저장
		$tpl->assign("anti_tnum", $_anti_tmp);
		$tpl->assign("anti_nwdate", $_anti_nwdate);


		$bbsType="file";
		$avail_size_merge=5242880*2;
		
		
		$str_id = $_SESSION['USERID'];
		$str_loginid = $_SESSION['LOGONID'];
		$code = $_GET['mcode'];
		$num = $_GET['num'];
		
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
			  FROM TAB_ONLINE3
			 WHERE num_oid='$oid' and num_mcode='$code' and num_serial='$num' $select_where";
		$data = $BoardDB->sqlFetch($sql);
		if(empty($data)) WebApp::redirect("/?main","잘못된 접근입니다.");
		@_format_data(&$data);
		$tpl->assign($data);
		
		//print_r($_SESSION);
		
		//신청날짜 생성
		$tpl->assign(array("year"=>date("Y"), "month"=>date("m"), "day"=>date("d"), "code"=>$_GET['mcode']));
		
		$tpl->setLayout('sub');
		$tpl->define("CONTENT","/html/online3/write2.htm");
		$tpl->parse("CONTENT");

		break;

		break;


	case "POST":
		
		$ip = $_SERVER[REMOTE_ADDR];
		$str_year = date('Y');
		$max_serial = $BoardDB->sqlFetchOne("select max(num_serial) + 1 from TAB_ONLINE3 where num_oid=$oid");
		if ( !$max_serial ) { $max_serial = "1"; }

		$start_time = strval($_POST['str_start_time']).":".strval($_POST['str_start_min']);
		$end_time = strval($_POST['str_end_time']).":".strval($_POST['str_end_min']);
		//print_r($_SESSION);
		$str_id = $_SESSION['USERID'];
		$str_loginid = $_SESSION['LOGONID'];
		$str_partcode = $_SESSION['CAFE_PARTCODE']; 
		//DB column
		$insert_col = "num_oid, num_mcode, num_serial, str_name, str_tmp1, str_tmp2, str_tmp3, str_tmp4, str_s_date, str_e_date, str_date_tmp1, str_lec, str_lec_tmp, str_destini, str_par, str_par_tmp1, str_par_tmp2,
						str_in_tmp1, str_in_tmp2, str_in_tmp3, str_title, str_content, str_ip, str_to_time, dt_date, str_id, str_loginid, str_year, str_partcode";
		//insert values
		$insert_val = "'$oid', '$code', '$max_serial', '$str_name', '$sel_school', '$num_grade', '$t_class', '$str_no', '$time_g1-$time_g2-$time_g3', '$time_g4-$time_g5-$time_g6', '$time_g7', '$edu_type_g', '$choos_g5_txt',
						'$str_destini', '$str_par', '$str_par_tmp1', '$str_par_tmp2', '$str_in_tmp1', '$str_in_tmp2', '$str_in_tmp3', '$str_title', '$str_content2', '$ip', '$str_to_date', SYSDATE, '$str_id', '$str_loginid'
						,'$str_year', '$str_partcode'";

		$sql = "insert into TAB_ONLINE3
				($insert_col)
				values
				($insert_val) ";
		//echo $sql; exit;

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


?>