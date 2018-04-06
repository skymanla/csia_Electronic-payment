<?php
/**
* skymanla
 * 체험학습 온라인 신청 - 현장체험학습 신청서
**/

switch ($REQUEST_METHOD) {
	case "GET":

		if(!$_GET['mcode']) WebApp::redirect("/?main","잘못된 접근입니다.");
		//본인 글을 찾기 위한...
		$str_id = $_SESSION['USERID'];
		$str_loginid = $_SESSION['LOGONID'];
		$code = $_GET['mcode'];
		$num = $_GET['num'];
		
		$tpl->setCond("acc_button", false);
		if($_SESSION[ADMIN] || $_SESSION[acc_tmp1] || $_SESSION[acc_tmp2] || $_SESSION[acc_tmp3] || $_SESSION[acc_tmp5]){
			//pass
			$tpl->setCond("acc_button", true);
		}else{
			$select_where = "and str_id='$str_id' and str_loginid='$str_loginid'";
		}
		$sql = "select * from $tb_online3 where num_oid='$oid' and num_mcode='$code' and num_serial='$num' $select_where";
		$chk = $BoardDB->sqlFetch($sql);
		if($chk['str_phase']!="Y"){
			WebApp::redirect("/?act=online3.read&mcode=$code&num=$num","아직 신청서 결재가 완료되지 않았습니다.");
		} 
		
		$sql = "SELECT 
				num_oid, num_mcode, num_serial, str_name, str_bogo_title, str_bogo_text, str_ip, str_to_time, to_char(dt_date, 'YYYY-MM-DD') dt_date, str_bogo_acc1, str_bogo_acc2, str_bogo_acc3,
				str_bogo_acc4, str_bogo_acc5, str_id, str_loginid, str_partcode, str_phase, str_phase2, str_year
			  FROM $tb_online3_bogo
			 WHERE num_oid='$oid' and num_mcode='$code' and num_serial='$num' $select_where";
		//echo $sql;
		$data = $BoardDB->sqlFetch($sql);
		if(empty($data)) WebApp::redirect("/?act=online3.write2&mcode=$code&num=$num","아직 보고서를 작성하지 않았습니다.");
		
		$tpl->assign(array("year"=>date("Y"), "month"=>date("m"), "day"=>date("d"), "code"=>$_GET['mcode']));
		//학급정보 및 기간 가져오기
		$sql = "SELECT 
				num_oid, num_mcode, num_serial, str_name, str_tmp1, str_tmp2, str_tmp3, str_tmp4, str_s_date, str_e_date, str_date_tmp1, str_lec, str_lec_tmp, str_destini, str_par, str_par_tmp1, str_par_tmp2,
				str_in_tmp1, str_in_tmp2, str_in_tmp3, str_title, str_content, str_ip, str_to_time, str_acc_tmp1, str_acc_tmp2, str_acc_tmp3, str_acc_tmp4, str_acc_tmp5,
				TO_CHAR(dt_date, 'YYYY-MM-DD') dt_date, str_id, str_loginid, str_phase, str_phase2 
			  FROM $tb_online3
			 WHERE num_oid='$oid' and num_mcode='$code' and num_serial='$num' $select_where";
		$sin_data = $BoardDB->sqlFetch($sql);
		if($sin_data['str_tmp1']=="grade_m"){
			$str_tmp1 = "중학교";
		}else if($sin_data['str_tmp1']=="grade_h"){
			$str_tmp1 = "고등학교";
		}
		$s_date = explode('-',$sin_data['str_s_date']);
		$e_date = explode('-',$sin_data['str_e_date']);
		$tpl->assign(array('str_tmp1'=>$str_tmp1,'str_tmp2'=>$sin_data['str_tmp2'],'str_tmp3'=>$sin_data['str_tmp3'],'str_tmp4'=>$sin_data['str_tmp4'],'s_date_y'=>$s_date[0],'s_date_m'=>$s_date[1],
							's_date_d'=>$s_date[2],'e_date_y'=>$e_date[0],'e_date_m'=>$e_date[1],'e_date_d'=>$e_date[2],
							'str_date_tmp1'=>$sin_data['str_date_tmp1'], 'str_title'=>$sin_data['str_title'],'str_par'=>$sin_data['str_par'],'str_par_tmp1'=>$sin_data['str_par_tmp1']));
		
		$tpl->setLayout('sub');
		$tpl->define("CONTENT","/html/online3/read2.htm");
		
		//첨부파일 가져오기
		$tpl->define("FILE","CONTENT");
		$sql = "select * from $tb_online3_files where num_oid='$oid' and num_mcode='$code' and num_main='$num' $select_where";
		$files = $BoardDB->sqlFetchAll($sql);
		@array_walk($files,'cb_format_list');
		$tpl->parse('FILE', &$files);
		//echo $sql;
		
		@_format_data(&$data);
		$tpl->assign($data);
		
		//삭제는 학생만 가능하게...
		$tpl->setCond("student", false);
		if($_SESSION['MEM_TYPE']['0']=='s'){
			$tpl->setCond("student", true);
		}
		
		$acc_link_val = "/?act=online3.acc_stu2&mcode=$code&num=$num";
		$acc_title_val = "승인";
		if($_SESSION[ADMIN]){
			$ad_button_array = array($data[str_bogo_acc1], $data[str_bogo_acc2], $data[str_bogo_acc3], $data[str_bogo_acc4]);
			for($i=0;$i < count($ad_button_array);$i++){
				if($ad_button_array[$i] == "N"){
					$link_tail = "acc_stu2";
					$title_tail = "승인";
				}else{
					$link_tail = "giveback2";
					$title_tail = "반려";
				}
				$rs_btn[$i] = array($link_tail, $title_tail);
			}
			$tpl->assign(array("acc_link_p"=>"/?act=online3.".$rs_btn[0][0]."&mcode=$code&num=$num&type=p", "acc_title_p"=>"학부모".$rs_btn[0][1],
									"acc_link_t"=>"/?act=online3.".$rs_btn[1][0]."&mcode=$code&num=$num&type=t", "acc_title_t"=>"담임".$rs_btn[1][1],
									"acc_link_a"=>"/?act=online3.".$rs_btn[3][0]."&mcode=$code&num=$num&type=a", "acc_title_a"=>"교무부장".$rs_btn[3][1]
									));//"acc_link_c"=>"/?act=online3.".$rs_btn[2][0]."&mcode=$code&num=$num&type=c", "acc_title_c"=>"부서".$rs_btn[2][1],
		}else if($_SESSION[acc_tmp1] == 7){//담당교사
			//$tpl->assign(array("acc_link"=>$acc_link_val , "acc_title"=>$acc_title_val));
		}else if($_SESSION[acc_tmp3] == 8){//부장
			$tpl->assign(array("acc_link"=>$acc_link_val, "acc_title"=>$acc_title_val));
		}else if($_SESSION[acc_tmp2] == 99){//담임
			$tpl->assign(array("acc_link"=>$acc_link_val, "acc_title"=>$acc_title_val));
		}else if($_SESSION[acc_tmp5] == 98){//학부모
			$tpl->assign(array("acc_link"=>$acc_link_val, "acc_title"=>$acc_title_val));
		}else{//본인의 경우 수정
			//if($data[str_bogo_acc1] == "Y"){//학부모가 승인을 했으면 수정이 불가능하게 하자
			if($data[str_bogo_acc2] == "Y"){//학부모가 승인을 했으면 수정이 불가능하게 하자 - > 담임부터로 수정
				$tpl->assign(array("acc_link"=> "#","tmp5_ok"=>"onclick='return mody_click();'", "acc_title"=>"수정"));
			}else{
				$tpl->assign(array("acc_link"=> "/?act=online3.modify2&mcode=$code&num=$num", "acc_title"=>"수정"));
			}
			
		}

		if (getenv('REMOTE_ADDR') == '125.130.18.5') {
			$tpl->assign(array("acc_link"=> "/?act=online3.modify2&mcode=$code&num=$num", "acc_title"=>"수정"));
		}
		
		//학부모 수정버튼 생성용
		$tpl->setCond('mody_par2', false);
		if($_SESSION[acc_tmp1]==7 || $_SESSION[acc_tmp2]==99 || $_SESSION[acc_tmp3]==8 || $_SESSION[acc_tmp5]==98){
			$tpl->setCond('mody_par2', true);
			$tpl->assign(array("mody_link"=>"/?act=online3.modify2&mcode=$code&num=$num", "mody_title"=>"수정"));
			/*if($data[str_acc_tmp5]=="Y"){//학부모가 승인을 했으면 수정을 못하게 하자
				$tpl->assign(array("mody_link"=> "#","tmp5_ok"=>"onclick='return mody_click();'", "mody_title"=>"수정"));
			}else{
				$tpl->assign(array("mody_link"=>"/?act=online3.modify&mcode=$code&num=$num", "mody_title"=>"수정"));
			}*/
			
		}
		//반려버튼
		$tpl->setCond('give_back2', false);
		if($_SESSION[acc_tmp2]==99 || $_SESSION[acc_tmp3]==8){//$_SESSION[acc_tmp1]==7 || 
			$tpl->setCond('give_back2', true);
			$tpl->assign(array("back_url"=>"/?act=online3.giveback2&mcode=$code&num=$num","back_title"=>"반려"));
		}
		
		$tpl->parse("CONTENT");

		break;
		
		
	case "POST":
		$ip = $_SERVER[REMOTE_ADDR];
		$str_year = date('Y');
		
		$start_time = strval($_POST['str_start_time']).":".strval($_POST['str_start_min']);
		$end_time = strval($_POST['str_end_time']).":".strval($_POST['str_end_min']);
		//print_r($_SESSION);
		$str_id = $_SESSION['USERID'];
		$str_loginid = $_SESSION['LOGONID'];
		$str_partcode = $_SESSION['CAFE_PARTCODE']; 
		//DB column
		$time_s = $time_g1.'-'.$time_g2.'-'.$time_g3;
		$time_e = $time_g4.'-'.$time_g5.'-'.$time_g6;
		$update_set = "str_tmp1='$sel_school', str_tmp2='$num_grade', str_tmp3='$t_class', str_tmp4='$str_no', str_s_date='$time_s', str_e_date='$time_e', str_date_tmp1='$time_g7', str_lec='$edu_type_g', str_lec_tmp='$choos_g5_txt',
						str_destini='$str_destini', str_par='$str_par', str_par_tmp1='$str_par_tmp1', str_par_tmp2='$str_par_tmp2', str_in_tmp1='$str_in_tmp1', str_in_tmp2='$str_in_tmp2', str_in_tmp3='$str_in_tmp3',
						str_title='$str_title', str_content='$str_content2', dt_date=SYSDATE";
		//print_r($_POST);
		$sql = "update $tb_online3 set $update_set where num_oid='$oid' and num_mcode='$code' and num_serial='$serial'";
		
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
	global $oid, $mcode, $sin_data;
	//중고 구분
	if($arr[str_tmp1] == "grade_m"){
		$arr[grade_m] = "checked";
	}else if($arr[str_tmp1] == "grade_h"){
		$arr[grade_h] = "checked";
	}
	
	//결제권자일 경우 삭제 가능하게
	if($_SESSION[ADMIN] || $_SESSION[acc_tmp1]==7 || $_SESSION[acc_tmp2]==99 || $_SESSION[acc_tmp3]==8 || $_SESSION[acc_tmp5]==98){
		$arr['deleteUrl'] = "/?act=online3.delete2&mcode=".$mcode."&num=".$arr['num_serial']."&id=".$arr['str_id']."&id2=".$arr['str_loginid'];
	}else{
		$arr['deleteUrl'] = "/?act=online3.delete2&mcode=".$mcode."&num=".$arr['num_serial'];
	}
	$arr['listUrl']="/?act=online3.list&mcode=".$mcode."&page=".$_GET['page'];
	//날짜 자르기
	$f_s_date = explode("-", $sin_data['str_s_date']);
	$arr['s_date_y'] = $f_s_date[0];
	$arr['s_date_m'] = $f_s_date[1];
	$arr['s_date_d'] = $f_s_date[2];
	$f_e_date = explode("-", $sin_data['str_e_date']);
	$arr['e_date_y'] = $f_e_date[0];
	$arr['e_date_m'] = $f_e_date[1];
	$arr['e_date_d'] = $f_e_date[2];
	
	//학습형태
	switch($sin_data[str_lec]){
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
	if ($arr['str_bogo_text']) {
		$arr['str_bogo_text']=$arr['str_bogo_text']->load();
	}
	//수정하는 경우 수정등록하는 날짜로 재변경
	$fin_date = explode("-", $arr[dt_date]);
	$arr['fin_y']= $fin_date[0];
	$arr['fin_m'] = $fin_date[1];
	$arr['fin_d'] = $fin_date[2];

}

function cb_format_list(&$arr){
	global $code, $oid, $num, $str_id;
	$arr['file_name'] = $arr['str_realname_bogo'];
	$arr['file_url'] = $arr['str_upname_bogo'];
	$arr['download'] = "/?act=online3.download&mcode=$code&num=$num&bid=".$arr['str_id']."&idx=".$arr['num_serial'];	
}
?>
