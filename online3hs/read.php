<?php
/**
* skymanla
 * ü���н� �¶��� ��û - ����ü���н� ��û��
**/

switch ($REQUEST_METHOD) {
	case "GET":

		if(!$_GET['mcode']) WebApp::redirect("/?main","�߸��� �����Դϴ�.");
		//���� ���� ã�� ����...
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
		if(empty($data)) WebApp::redirect("/?main","�߸��� �����Դϴ�.");
		@_format_data(&$data);
		$tpl->assign($data);
		
		$tpl->setCond('phase', false);
		if($_SESSION['MEM_TYPE'][0] == 's' || $_SESSION['MEM_TYPE'][0] == 'p'){
			if($data['str_phase'] == "Y" ){
				$tpl->setCond('phase', true);
			}
		}
		
		//������ �л��� �����ϰ�...
		$tpl->setCond("student", false);
		if($_SESSION['MEM_TYPE']['0']=='s'){
			$tpl->setCond("student", true);
		}
		
		//print_r($_SESSION);
		$acc_link_val = "/?act=online3hs.acc_stu&mcode=$code&num=$num";
		$acc_title_val = "����";
		if($_SESSION[ADMIN]){//�����ڿ� ���� �� �ݷ� ��ư
			$ad_button_array = array($data[str_acc_tmp5], $data[str_acc_tmp1], $data[str_acc_tmp2], $data[str_acc_tmp3]);
			for($i=0;$i < count($ad_button_array);$i++){
				if($ad_button_array[$i] == "N"){
					$link_tail = "acc_stu";
					$title_tail = "����";
				}else{
					$link_tail = "giveback";
					$title_tail = "�ݷ�";
				}
				$rs_btn[$i] = array($link_tail, $title_tail);
			}
			//print_r($rs_btn);
			$tpl->assign(array("acc_link_p"=>"/?act=online3hs.".$rs_btn[0][0]."&mcode=$code&num=$num&type=p", "acc_title_p"=>"�кθ�".$rs_btn[0][1],
									"acc_link_t"=>"/?act=online3hs.".$rs_btn[1][0]."&mcode=$code&num=$num&type=t", "acc_title_t"=>"����".$rs_btn[1][1],
									"acc_link_a"=>"/?act=online3hs.".$rs_btn[3][0]."&mcode=$code&num=$num&type=a", "acc_title_a"=>"��������".$rs_btn[3][1],
									));//"acc_link_c"=>"/?act=online3hs.".$rs_btn[2][0]."&mcode=$code&num=$num&type=c", "acc_title_c"=>"�μ�".$rs_btn[2][1],
		}else if($_SESSION[acc_tmp1] == 7){//��米��
			//$tpl->assign(array("acc_link"=>$acc_link_val , "acc_title"=>$acc_title_val));
		}else if($_SESSION[acc_tmp3] == 8){//����
			$tpl->assign(array("acc_link"=>$acc_link_val, "acc_title"=>$acc_title_val));
		}else if($_SESSION[acc_tmp2] == 99){//����
			$tpl->assign(array("acc_link"=>$acc_link_val, "acc_title"=>$acc_title_val));
		}else if($_SESSION[acc_tmp5] == 98){//�кθ�
			$tpl->assign(array("acc_link"=>$acc_link_val, "acc_title"=>$acc_title_val));
		}else{//������ ��� ����
			//if($data[str_acc_tmp5] == "Y"){//�кθ� ������ ������ ������ �Ұ����ϰ� ���� -> ���Ӻ��� ���� �Ұ��ϵ���
			if($data[str_acc_tmp1] == "Y"){//�кθ� ������ ������ ������ �Ұ����ϰ� ���� -> ���Ӻ��� ���� �Ұ��ϵ���
				$tpl->assign(array("acc_link"=> "#","tmp5_ok"=>"onclick='return mody_click();'", "acc_title"=>"����"));
			}else{
				$tpl->assign(array("acc_link"=> "/?act=online3hs.modify&mcode=$code&num=$num", "acc_title"=>"����"));
			}
			
		}
		//�кθ� ������ư ������
		$tpl->setCond('mody_par', false);
		if($_SESSION[acc_tmp1]==7 || $_SESSION[acc_tmp2]==99 || $_SESSION[acc_tmp3]==8 || $_SESSION[acc_tmp5]==98){
			$tpl->setCond('mody_par', true);
			$tpl->assign(array("mody_link"=>"/?act=online3hs.modify&mcode=$code&num=$num", "mody_title"=>"����"));
			/*if($data[str_acc_tmp5]=="Y"){//�кθ� ������ ������ ������ ���ϰ� ����
				$tpl->assign(array("mody_link"=> "#","tmp5_ok"=>"onclick='return mody_click();'", "mody_title"=>"����"));
			}else{
				$tpl->assign(array("mody_link"=>"/?act=online3hs.modify&mcode=$code&num=$num", "mody_title"=>"����"));
			}*/
			
		}
		//�ݷ���ư
		$tpl->setCond('give_back', false);
		if($_SESSION[acc_tmp2]==99 || $_SESSION[acc_tmp3]==8){//$_SESSION[acc_tmp1]==7 || 
			$tpl->setCond('give_back', true);
			$tpl->assign(array("back_url"=>"/?act=online3hs.giveback&mcode=$code&num=$num","back_title"=>"�ݷ�"));
		}
		//print_r($data);
		//exit;
		//��û��¥ ����
		$tpl->assign(array("year"=>date("Y"), "month"=>date("m"), "day"=>date("d"), "code"=>$_GET['mcode']));
		
		$tpl->setLayout('sub');
		$tpl->define("CONTENT","/html/online3hs/read.htm");
		$tpl->parse("CONTENT");

		break;


}


function _format_data(&$arr) {
	global $oid, $mcode;
	//�߰� ����
	if($arr[str_tmp1] == "grade_m"){
		$arr[str_tmp1] = "��";
	}else if($arr[str_tmp1] == "grade_h"){
		$arr[str_tmp1] = "��";
	}
	//���������� ��� ���� �����ϰ�
	if($_SESSION[ADMIN] || $_SESSION[acc_tmp1]==7 || $_SESSION[acc_tmp2]==99 || $_SESSION[acc_tmp3]==8 || $_SESSION[acc_tmp5]==98){
		$arr['deleteUrl'] = "/?act=online3hs.delete&mcode=".$mcode."&num=".$arr['num_serial']."&id=".$arr['str_id']."&id2=".$arr['str_loginid'];
	}else{
		$arr['deleteUrl'] = "/?act=online3hs.delete&mcode=".$mcode."&num=".$arr['num_serial'];
	}
	$arr['listUrl'] = "/?act=online3hs.list&mcode=".$mcode."&page=".$_GET['page'];
	//��¥ �ڸ���
	$f_s_date = explode("-", $arr['str_s_date']);
	$arr['s_date_y'] = $f_s_date[0];
	$arr['s_date_m'] = $f_s_date[1];
	$arr['s_date_d'] = $f_s_date[2];
	$f_e_date = explode("-", $arr['str_e_date']);
	$arr['e_date_y'] = $f_e_date[0];
	$arr['e_date_m'] = $f_e_date[1];
	$arr['e_date_d'] = $f_e_date[2];
	
	//�н�����
	switch($arr[str_lec]){
		case "choos_g1":
			$arr[str_lec] = "��������";
			break;
		case "choos_g2":
			$arr[str_lec] = "ģ����ô �湮";
			break;
		case "choos_g3":
			$arr[str_lec] = "���� Ȱ��";
			break;
		case "choos_g4":
			$arr[str_lec] = "ü�� Ȱ��";
			break;
		case "choos_g5":
			$arr[str_lec] = "��Ÿ";
			$arr[str_lec_tmp1] = "���� : ".$arr[str_lec_tmp];
			break;
	}
	//��û��¥ ���
	$fin_date = explode("-", $arr[dt_date]);
	$arr['fin_y']= $fin_date[0];
	$arr['fin_m'] = $fin_date[1];
	$arr['fin_d'] = $fin_date[2];
}

?>
