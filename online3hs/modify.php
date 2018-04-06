<?php
/**
* skymanla
 * ü���н� �¶��� ��û - ����ü���н� ��û��
 * ���� ������ �� ��� ���� �߰� 20180221
 * ������ ��� ��ü������ ���� ��꿡 �߰����� ����(�Ѵٸ� ������ ��)
 * ��� ������ ��� api�� ���⿡ �⺻���� �������� �����ϸ� ���
 * 
**/

require_once "./lib/luna/lunar.php";

switch ($REQUEST_METHOD) {
	case "GET":

		if(!$_GET['mcode']) WebApp::redirect("/?main","�߸��� �����Դϴ�.");
		//���� ���� ã�� ����...
		$str_id = $_SESSION['USERID'];
		$str_loginid = $_SESSION['LOGONID'];
		$code = $_GET['mcode'];
		$num = $_GET['num'];
		
		if($_SESSION[ADMIN] || $_SESSION[acc_tmp5] || $_SESSION[acc_tmp1] || $_SESSION[acc_tmp2] || $_SESSION[acc_tmp3]){
			//pass
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
		if($_SESSION[ADMIN] || $_SESSION[acc_tmp1] || $_SESSION[acc_tmp2] || $_SESSION[acc_tmp3]){
			//pass
		}else{
			if($data[str_phase]=="Y" || $data[str_phase2]=="Y" || $data[str_acc_tmp1]=="Y"){
				WebApp::redirect("/?act=online3hs.list&mcode=$mcode","������ �Ǿ� ������ �Ұ����մϴ�.");
			}
		}
		if(empty($data)) WebApp::redirect("/?main","�߸��� �����Դϴ�.");
		@_format_data(&$data);
		$tpl->assign($data);
		
		//print_r($_SESSION);
		
		//��û��¥ ����
		$tpl->assign(array("year"=>date("Y"), "month"=>date("m"), "day"=>date("d"), "code"=>$_GET['mcode']));
		
		$tpl->setLayout('sub');
		$tpl->define("CONTENT","/html/online3/modify.htm");
		$tpl->parse("CONTENT");

		break;
		
		
	case "POST":
		


		// ���� 100�� üũ
		if(mb_strlen($str_content2, 'euc-kr')<100)WebApp::moveBack('ü���н���ȹ�� �������� 100�� �̻����� �ۼ��ϼ���.');
		
	
		$ip = $_SERVER[REMOTE_ADDR];
		$str_year = date('Y');
		
		//print_r($_SESSION);
		$str_id = $_SESSION['USERID'];
		$str_loginid = $_SESSION['LOGONID'];
		$str_partcode = $_SESSION['CAFE_PARTCODE']; 
		//DB column
		$time_s = $time_g1.'-'.$time_g2.'-'.$time_g3;
		$time_e = $time_g4.'-'.$time_g5.'-'.$time_g6;
		
		//���� üũ ����
			$A_time = sprintf("%d%02d%02d", $time_g1, $time_g2, $time_g3);
			$B_time = sprintf("%d%02d%02d", $time_g4, $time_g5, $time_g6);;
			//���۳�¥ ���� ��ȯ start
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
			//���۳�¥ ���� ��ȯ end
			
			//���ᳯ¥ ���� ��ȯ start
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
			//���ᳯ¥ ���� ��ȯ end
			
			//�� ��¥ ����(���)
			$date_term = intval((strtotime($B_s)-strtotime($A_s))/86400)+1;
			$minus_date = 0;
			for($i=0;$i<$date_term;$i++){
				$be_date = date("Y-m-d", strtotime($A_s.'+'.$i.' day'));
				//�����, �Ͽ��� ���� �ð� üũ
				$daily_chk = getSatSun($be_date);
				if($daily_chk == true) $minus_date++;
				//��� ������
				$Solar_end = getSolarEnd($be_date);
				if($Solar_end == true) $minus_date++;
			}
			//�� ��¥ ����(���� ������)
			$Lunar_date_term = intval((strtotime($B_l_s)-strtotime($A_l_s))/86400)+1;
			$Lunar_minus_date = 0;
			for($i=0;$i<$Lunar_date_term;$i++){
				$be_date = date("Y-m-d", strtotime($A_l_s.'+'.$i.' day'));
				$Lunar_end = getLunarEnd($be_date);
				if($Lunar_end==true) $Lunar_minus_date++;
			}
			$time_g7 = $date_term - ($minus_date+$Lunar_minus_date);
			
		$update_set = "str_tmp1='$sel_school', str_tmp2='$num_grade', str_tmp3='$t_class', str_tmp4='$str_no', str_s_date='$time_s', str_e_date='$time_e', str_date_tmp1='$time_g7', str_lec='$edu_type_g', str_lec_tmp='$choos_g5_txt',
						str_destini='$str_destini', str_par='$str_par', str_par_tmp1='$str_par_tmp1', str_par_tmp2='$str_par_tmp2', str_in_tmp1='$str_in_tmp1', str_in_tmp2='$str_in_tmp2', str_in_tmp3='$str_in_tmp3',
						str_title='$str_title', str_content='$str_content2', dt_date=SYSDATE";
		//print_r($_POST);
		$sql = "update $tb_online3hs set $update_set where num_oid='$oid' and num_mcode='$code' and num_serial='$serial'";
		
		//echo $sql; exit;

		if ($BoardDB->query($sql)) {
			$BoardDB->commit();

			if ($_POST['page']) {
				$pagego = "&page=".$_GET['page'];
			}

			WebApp::redirect("/?act=online3hs.list&mcode=$code","�Ϸ�Ǿ����ϴ�.");
		} else {
			echo $sql;
			echo '<br />';
			
			exit;
			WebApp::moveback("������û ����..��õ� �ٶ��ϴ�.");
		}
		
		break;


}


function _format_data(&$arr) {
	global $oid, $mcode,$tb_online3hs,$tb_online3hs_bogo,$tb_online3hs_files;
	//�߰� ����
	if($arr[str_tmp1] == "grade_m"){
		$arr[grade_m] = "checked";
	}else if($arr[str_tmp1] == "grade_h"){
		$arr[grade_h] = "checked";
	}
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
			$arr[choos_g1_chk] = "checked";
			break;
		case "choos_g2":
			$arr[choos_g2_chk] = "checked";
			break;
		case "choos_g3":
			$arr[choos_g3_chk] = "checked";
			break;
		case "choos_g4":
			$arr[choos_g4_chk] = "checked";
			break;
		case "choos_g5":
			$arr[choos_g5_chk] = "checked";
			$arr[choos_g5_tmp1] = "���� : ".$arr[str_lec_tmp];
			break;
	}
	//�����ϴ� ��� ��������ϴ� ��¥�� �纯��
	$fin_date = explode("-", $arr[dt_date]);
	$arr['fin_y']= $fin_date[0];
	$arr['fin_m'] = $fin_date[1];
	$arr['fin_d'] = $fin_date[2];
}

?>
