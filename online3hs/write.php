<?php
/**
* skymanla
 * 체험학습 온라인 신청 - 현장체험학습 신청서
 * 음력 공휴일 및 양력 휴일 추가 20180221
 * 음력의 경우 대체휴일은 휴일 계산에 추가하지 않음(한다면 가능은 함)
 * 양력 휴일의 경우 api가 없기에 기본적인 국가지정 공휴일만 기록
 * 
**/

require_once "./lib/luna/lunar.php";

switch ($REQUEST_METHOD) {
	case "GET":

		if(!$_GET['mcode']) WebApp::redirect("/?main","잘못된 접근입니다.");
		//신청서 및 보고서 완결이 안 났는데 새로운 신청서 작성 방지용
		//print_r($_SESSION);
		$sql = "select * from $tb_online3hs where num_oid='$oid' and num_mcode='$mcode' and str_id='".$_SESSION['USERID']."' and str_loginid='".$_SESSION['LOGONID']."' order by dt_date desc";
		
		$chk1 = $BoardDB->sqlFetch($sql);
		
		if($chk1[str_phase]=='N'){//신청서 최종 결재가 안되었다면
			WebApp::moveBack("이전 신청서가 아직 결재완료가 되지 않았습니다.\n이전 페이지로 돌아갑니다.");
		}
		
		if($chk1[str_phase2] =='N'){//보고서 최종결재가 안되었다면
			WebApp::moveBack("보고서가 아직 결재완료가 되지 않았습니다.\n이전 페이지로 돌아갑니다.");
		}
		
		$n_year = WebApp::getConf('formation.school_year');//연도변수
		// 로그인한 학보모의 자녀정보를 불러오자. TAB_MEMBER_RELATION 학부모가 대신 작성할 수도 있으니까....
		$_p_sql = "select * from TAB_MEMBER_RELATION where num_oid_x=$oid and str_id_x='".$_SESSION['USERID']."'";
		$_p_data = $DB->sqlFetch($_p_sql);
		//학생의 부모를 가져오기
		$sql = "select 
					a.str_id_y as str_id_y, a.str_id_x as str_id, a.str_relation as str_relation, b.str_name as str_name
				from 
					TAB_MEMBER_RELATION a 
				LEFT OUTER JOIN 
					TAB_MEMBER_MERGE b
				ON 
					a.num_oid_y=b.num_oid and  a.str_id_x=b.str_id
				where a.num_oid_y='$oid' and a.str_id_y='".$_SESSION['USERID']."' order by a.dt_date desc";
		//echo $sql;
		$_s_data = $DB->sqlFetch($sql);
		if($_s_data['str_relation']=="mam"){
			$str_re = "모";
		}else{
			$str_re = "부";
		}
		$tpl->assign(array("par_name"=>$_s_data['str_name'], "par_rela"=>$str_re));
		//학생이면 통과하고
		if($_SESSION['MEM_TYPE'][0] == s){
			$_p_data['str_id_y'] = $_SESSION['USERID'];
		}else{
			if ( !$_p_data['str_id_y'] && !$_SESSION['ADMIN']) {
				WebApp::redirect("/?act=online3hs.list&mcode=$mcode".$pagego,"자녀설정을 먼저 해주시기 바랍니다.");
				exit;
			}
		}
		
		// 블러온 자녀의 학년반정보. 
		$_t_sql = "select 
						a.num_grade as t_grade, a.num_class as t_class, b.str_name as t_name, a.str_grade as str_grade, a.str_class as str_class, c.str_no as str_no
					from 
						TAB_CLASS_MEMBER a 
					LEFT OUTER JOIN 
						TAB_MEMBER_MERGE b 
					ON 
						a.num_oid=b.num_oid and a.str_id=b.str_id
					LEFT OUTER JOIN
						TAB_MEMBER_RANK c
					ON
						a.num_oid=c.num_oid and a.str_id=c.str_id 
					where 
						a.num_oid=$oid and a.str_id='".$_p_data['str_id_y']."' and a.num_year='".$n_year."'";
		//echo $_t_sql;
		$_t_data = $DB->sqlFetch($_t_sql);
		@_format_data(&$_t_data);
		$tpl->assign($_t_data);
		//print_r($_t_data);
		//신청날짜 생성
		$tpl->assign(array("year"=>date("Y"), "month"=>date("m"), "day"=>date("d"), "code"=>$_GET['mcode']));
		
		//남은 날짜 합산하기
		$sql = "select sum(str_date_tmp1) as sum_date from $tb_online3hs where str_id='".$_SESSION['USERID']."' and str_loginid='".$_SESSION['LOGONID']."' and num_mcode='".$_GET['mcode']."' and num_oid='$oid' and str_year='$n_year'";
		$sum_date = $BoardDB->sqlFetchOne($sql);
		$ex_date = 10-$sum_date;
		if($ex_date=='0'){
			WebApp::moveBack("10일의 기간을 모두 사용하였습니다.\n이전 페이지로 돌아갑니다.");
			exit;
		}
		$tpl->assign("sum_date", $ex_date);
		
		$tpl->setLayout('sub');

		$tpl->define("CONTENT","/html/online3hs/write.htm");
		$tpl->parse("CONTENT");

		break;


	case "POST":

		// 본문 100자 체크
		if(mb_strlen($str_content2, 'euc-kr')<100)WebApp::moveBack('체험학습계획은 공백포함 100자 이상으로 작성하세요.');
		
		$ip = $_SERVER[REMOTE_ADDR];
		$n_year = WebApp::getConf('formation.school_year');//연도변수
		
		//$str_year = date('Y');
		$max_serial = $BoardDB->sqlFetchOne("select max(num_serial) + 1 from $tb_online3hs where num_oid=$oid and num_mcode=$code");
		if ( !$max_serial ) { $max_serial = "1"; }
		
		//휴일 체크 시작
			$A_time = sprintf("%d%02d%02d", $time_g1, $time_g2, $time_g3);
			$B_time = sprintf("%d%02d%02d", $time_g4, $time_g5, $time_g6);;
			//시작날짜 음력 변환 start
			$A_in_date = $A_time;
			if(strlen($A_in_date) >= 8){
				$A_year    = substr($A_in_date, 0, 4);
			    $A_month    = substr($A_in_date, 4, 2);
			    $A_day    = substr($A_in_date, 6, 2);
			}
			$A_lunar = getLunarDate($A_year, $A_month, $A_day);
			if($A_lunar[0] == 0){
			    $A_s = sprintf("%d-%02d-%02d", $A_year, $A_month, $A_day);
				$A_l_s = sprintf("%d-%02d-%02d", $A_lunar[1], $A_lunar[2], $A_lunar[3]);
			}
			//시작날짜 음력 변환 end
			
			//종료날짜 음력 변환 start
			$B_in_date = $B_time;
			if(strlen($B_in_date) >= 8){
				$B_year    = substr($B_in_date, 0, 4);
			    $B_month    = substr($B_in_date, 4, 2);
			    $B_day    = substr($B_in_date, 6, 2);
			}
			$B_lunar = getLunarDate($B_year, $B_month, $B_day);
			if($B_lunar[0] == 0){
			    $B_s = sprintf("%d-%02d-%02d", $B_year, $B_month, $B_day);
			    $B_l_s = sprintf("%d-%02d-%02d", $B_lunar[1], $B_lunar[2], $B_lunar[3]);
			}
			//종료날짜 음력 변환 end
			
			//두 날짜 차이(양력)
			$date_term = intval((strtotime($B_s)-strtotime($A_s))/86400)+1;
			$minus_date = 0;
			for($i=0;$i<$date_term;$i++){
				$be_date = date("Y-m-d", strtotime($A_s.'+'.$i.' day'));
				//토요일, 일요일 제외 시간 체크
				$daily_chk = getSatSun($be_date);
				if($daily_chk == true) $minus_date++;
				//양력 공휴일
				$Solar_end = getSolarEnd($be_date);
				if($Solar_end == true) $minus_date++;
			}
			//두 날짜 차이(음력 공휴일)
			$Lunar_date_term = intval((strtotime($B_l_s)-strtotime($A_l_s))/86400)+1;
			$Lunar_minus_date = 0;
			for($i=0;$i<$Lunar_date_term;$i++){
				$be_date = date("Y-m-d", strtotime($A_l_s.'+'.$i.' day'));
				$Lunar_end = getLunarEnd($be_date);
				if($Lunar_end==true) $Lunar_minus_date++;
			}
			$time_g7 = $date_term - ($minus_date+$Lunar_minus_date);
		//print_r($_SESSION);

		$str_id = $_SESSION['USERID'];
		$str_loginid = $_SESSION['LOGONID'];
		$str_partcode = $_SESSION['CAFE_PARTCODE']; 


		// 필수값에 대한 검증 2017-09-05
		// 아이디
		if ( !$str_id ) { WebApp::moveback("로그인 정보가 정확하지 않습니다. \n재로그인후 작성 바랍니다."); }
		if ( !$str_loginid ) { WebApp::moveback("로그인 정보가 정확하지 않습니다. \n재로그인후 작성 바랍니다."); }
		if ( !$str_partcode ) { WebApp::moveback("회원님의 학급정보가 정확하지 않습니다. \n학급정보 확인후 작성 바랍니다."); }

		//DB column
		$insert_col = "num_oid, num_mcode, num_serial, str_name, str_tmp1, str_tmp2, str_tmp3, str_tmp4, str_s_date, str_e_date, str_date_tmp1, str_lec, str_lec_tmp, str_destini, str_par, str_par_tmp1, str_par_tmp2,
						str_in_tmp1, str_in_tmp2, str_in_tmp3, str_title, str_content, str_ip, str_to_time, dt_date, str_id, str_loginid, str_year, str_partcode";
		//insert values
		$insert_val = "'$oid', '$code', '$max_serial', '$str_name', '$sel_school', '$num_grade', '$t_class', '$str_no', '$time_g1-$time_g2-$time_g3', '$time_g4-$time_g5-$time_g6', '$time_g7', '$edu_type_g', '$choos_g5_txt',
						'$str_destini', '$str_par', '$str_par_tmp1', '$str_par_tmp2', '$str_in_tmp1', '$str_in_tmp2', '$str_in_tmp3', '$str_title', '$str_content2', '$ip', '$str_to_date', SYSDATE, '$str_id', '$str_loginid'
						,'$n_year', '$str_partcode'";

		$sql = "insert into $tb_online3hs
				($insert_col)
				values
				($insert_val) ";
		if(debug()==true){
			//echo $sql; exit;
		}

		if ($BoardDB->query($sql)) {
			$BoardDB->commit();

			if ($_POST['page']) {
				$pagego = "&page=".$_GET['page'];
			}


			// 로그 파일..메뉴 삭제시 로그 남겨야게츰...2010-04-30.juni
			// 로그 파일 용량체크 해서 지나치게 크면 백업후 새파일
			if(@filesize ("log/csiaonline3hsWrite.log") > 1024*1024){
				@rename ("log/csiaonline3hsWrite.log","csiaonline3hsWrite.log".".".time());
			}
			$logfp=fopen("log/csiaonline3hsWrite.log", "a+");
			chmod ("log/csiaonline3hsWrite.log",0777);
			$timestamp=date("Y-m-d H:i:s");
			$insertQuery = "'$oid', '$code', '$max_serial', '$str_name', '$sel_school', '$num_grade', '$t_class', '$str_no', '$time_g1-$time_g2-$time_g3', '$time_g4-$time_g5-$time_g6', '$time_g7', '$edu_type_g', '$choos_g5_txt', '$str_destini', '$str_par', '$str_par_tmp1', '$str_par_tmp2', '$str_in_tmp1', '$str_in_tmp2', '$str_in_tmp3', '$str_title', '본문생략', '$ip', '$str_to_date', SYSDATE, '$str_id', '$str_loginid','$n_year', '$str_partcode'";
			fwrite($logfp, "[".$timestamp."] [".$_SESSION['LOGONID']."] [신청서 작성] ".$insertQuery." [".$REMOTE_ADDR."]\n");
			// 로그 종료



			WebApp::redirect("/?act=online3hs.list&mcode=$code","완료되었습니다.");
		} else {
			//echo $sql;
			//echo '<br />';
			
			//exit;
			WebApp::moveback("접수신청 실패..재시도 바랍니다.");
		}

		break;
}


function _format_data(&$arr) {
	global $oid, $mcode;
	
	$arr['grade'] = mb_substr($arr['str_grade'], 0, 1, 'euc-kr');
	$arr['num_grade'] = substr($arr['str_grade'], 2, 2);
	$str_class = explode("반", $arr['str_class']);
	$arr['class'] = $str_class[0];
	if($arr['grade'] == "고"){
		$arr['grade_h'] = "checked";
	}else if($arr['grade'] == "중"){
		$arr['grade_m'] = "checked";
	}

}

?>