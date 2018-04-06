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
		if(debug()==true){
			print_r($_SESSION);
		}
		$tpl->setCond("acc_button", false);
		if($_SESSION[ADMIN] || $_SESSION[acc_tmp1] || $_SESSION[acc_tmp2] || $_SESSION[acc_tmp3] || $_SESSION[acc_tmp5]){
			//pass
			$tpl->setCond("acc_button", true);
		}else{
			$select_where = "and str_id='$str_id' and str_loginid='$str_loginid'";
		}

		$sql = "SELECT 
				num_oid, num_mcode, num_serial, str_name, str_tmp1, str_tmp2, str_tmp3, str_tmp4, str_s_date, str_e_date, str_date_tmp1, str_lec, str_lec_tmp, str_destini, str_par, str_par_tmp1, str_par_tmp2,
				str_in_tmp1, str_in_tmp2, str_in_tmp3, str_title, str_content, str_ip, str_to_time, str_acc_tmp1, str_acc_tmp2, str_acc_tmp3, str_acc_tmp4, str_acc_tmp5,
				TO_CHAR(dt_date, 'YYYY-MM-DD') dt_date, str_id, str_loginid, str_phase, str_phase2 
			  FROM $tb_online3hs
			 WHERE num_oid='$oid' and num_mcode='$code' and num_serial='$num' $select_where";
		$data = $BoardDB->sqlFetch($sql);
		if(empty($data)) WebApp::redirect("/?main","잘못된 접근입니다.");
		@_format_data(&$data);
		$tpl->assign($data);
		
		$tpl->setCond('phase', false);
		if($_SESSION['MEM_TYPE'][0] == 's' || $_SESSION['MEM_TYPE'][0] == 'p'){
			if($data['str_phase'] == "Y" ){
				$tpl->setCond('phase', true);
			}
		}
		
		//삭제는 학생만 가능하게...
		$tpl->setCond("student", false);
		if($_SESSION['MEM_TYPE']['0']=='s'){
			$tpl->setCond("student", true);
		}
		
		//print_r($_SESSION);
		$acc_link_val = "/?act=online3hs.acc_stu&mcode=$code&num=$num";
		$acc_title_val = "승인";
		if($_SESSION[ADMIN]){//관리자용 승인 및 반려 버튼
			$ad_button_array = array($data[str_acc_tmp5], $data[str_acc_tmp1], $data[str_acc_tmp2], $data[str_acc_tmp3]);
			for($i=0;$i < count($ad_button_array);$i++){
				if($ad_button_array[$i] == "N"){
					$link_tail = "acc_stu";
					$title_tail = "승인";
				}else{
					$link_tail = "giveback";
					$title_tail = "반려";
				}
				$rs_btn[$i] = array($link_tail, $title_tail);
			}
			//print_r($rs_btn);
			$tpl->assign(array("acc_link_p"=>"/?act=online3hs.".$rs_btn[0][0]."&mcode=$code&num=$num&type=p", "acc_title_p"=>"학부모".$rs_btn[0][1],
									"acc_link_t"=>"/?act=online3hs.".$rs_btn[1][0]."&mcode=$code&num=$num&type=t", "acc_title_t"=>"담임".$rs_btn[1][1],
									"acc_link_a"=>"/?act=online3hs.".$rs_btn[3][0]."&mcode=$code&num=$num&type=a", "acc_title_a"=>"교무부장".$rs_btn[3][1],
									));//"acc_link_c"=>"/?act=online3hs.".$rs_btn[2][0]."&mcode=$code&num=$num&type=c", "acc_title_c"=>"부서".$rs_btn[2][1],
		}else if($_SESSION[acc_tmp1] == 7){//담당교사
			//$tpl->assign(array("acc_link"=>$acc_link_val , "acc_title"=>$acc_title_val));
		}else if($_SESSION[acc_tmp3] == 8){//부장
			$tpl->assign(array("acc_link"=>$acc_link_val, "acc_title"=>$acc_title_val));
		}else if($_SESSION[acc_tmp2] == 99){//담임
			$tpl->assign(array("acc_link"=>$acc_link_val, "acc_title"=>$acc_title_val));
		}else if($_SESSION[acc_tmp5] == 98){//학부모
			$tpl->assign(array("acc_link"=>$acc_link_val, "acc_title"=>$acc_title_val));
		}else{//본인의 경우 수정
			//if($data[str_acc_tmp5] == "Y"){//학부모가 승인을 했으면 수정이 불가능하게 하자 -> 담임부터 수정 불가하도록
			if($data[str_acc_tmp1] == "Y"){//학부모가 승인을 했으면 수정이 불가능하게 하자 -> 담임부터 수정 불가하도록
				$tpl->assign(array("acc_link"=> "#","tmp5_ok"=>"onclick='return mody_click();'", "acc_title"=>"수정"));
			}else{
				$tpl->assign(array("acc_link"=> "/?act=online3hs.modify&mcode=$code&num=$num", "acc_title"=>"수정"));
			}
			
		}
		//학부모 수정버튼 생성용
		$tpl->setCond('mody_par', false);
		if($_SESSION[acc_tmp1]==7 || $_SESSION[acc_tmp2]==99 || $_SESSION[acc_tmp3]==8 || $_SESSION[acc_tmp5]==98){
			$tpl->setCond('mody_par', true);
			$tpl->assign(array("mody_link"=>"/?act=online3hs.modify&mcode=$code&num=$num", "mody_title"=>"수정"));
			/*if($data[str_acc_tmp5]=="Y"){//학부모가 승인을 했으면 수정을 못하게 하자
				$tpl->assign(array("mody_link"=> "#","tmp5_ok"=>"onclick='return mody_click();'", "mody_title"=>"수정"));
			}else{
				$tpl->assign(array("mody_link"=>"/?act=online3hs.modify&mcode=$code&num=$num", "mody_title"=>"수정"));
			}*/
			
		}
		//반려버튼
		$tpl->setCond('give_back', false);
		if($_SESSION[acc_tmp2]==99 || $_SESSION[acc_tmp3]==8){//$_SESSION[acc_tmp1]==7 || 
			$tpl->setCond('give_back', true);
			$tpl->assign(array("back_url"=>"/?act=online3hs.giveback&mcode=$code&num=$num","back_title"=>"반려"));
		}
		//print_r($data);
		//exit;
		//신청날짜 생성
		$tpl->assign(array("year"=>date("Y"), "month"=>date("m"), "day"=>date("d"), "code"=>$_GET['mcode']));
		
		$tpl->setLayout('sub');
		$tpl->define("CONTENT","/html/online3hs/read.htm");
		$tpl->parse("CONTENT");

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
	//결제권자일 경우 삭제 가능하게
	if($_SESSION[ADMIN] || $_SESSION[acc_tmp1]==7 || $_SESSION[acc_tmp2]==99 || $_SESSION[acc_tmp3]==8 || $_SESSION[acc_tmp5]==98){
		$arr['deleteUrl'] = "/?act=online3hs.delete&mcode=".$mcode."&num=".$arr['num_serial']."&id=".$arr['str_id']."&id2=".$arr['str_loginid'];
	}else{
		$arr['deleteUrl'] = "/?act=online3hs.delete&mcode=".$mcode."&num=".$arr['num_serial'];
	}
	$arr['listUrl'] = "/?act=online3hs.list&mcode=".$mcode."&page=".$_GET['page'];
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
	//신청날짜 등록
	$fin_date = explode("-", $arr[dt_date]);
	$arr['fin_y']= $fin_date[0];
	$arr['fin_m'] = $fin_date[1];
	$arr['fin_d'] = $fin_date[2];
}

?>
