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
		//��û�� �� ���� �ϰ��� �� ���µ� ���ο� ��û�� �ۼ� ������
		//print_r($_SESSION);
		$sql = "select * from $tb_online3hs where num_oid='$oid' and num_mcode='$mcode' and str_id='".$_SESSION['USERID']."' and str_loginid='".$_SESSION['LOGONID']."' order by dt_date desc";
		
		$chk1 = $BoardDB->sqlFetch($sql);
		
		if($chk1[str_phase]=='N'){//��û�� ���� ���簡 �ȵǾ��ٸ�
			WebApp::moveBack("���� ��û���� ���� ����Ϸᰡ ���� �ʾҽ��ϴ�.\n���� �������� ���ư��ϴ�.");
		}
		
		if($chk1[str_phase2] =='N'){//���� �������簡 �ȵǾ��ٸ�
			WebApp::moveBack("������ ���� ����Ϸᰡ ���� �ʾҽ��ϴ�.\n���� �������� ���ư��ϴ�.");
		}
		
		$n_year = WebApp::getConf('formation.school_year');//��������
		// �α����� �к����� �ڳ������� �ҷ�����. TAB_MEMBER_RELATION �кθ� ��� �ۼ��� ���� �����ϱ�....
		$_p_sql = "select * from TAB_MEMBER_RELATION where num_oid_x=$oid and str_id_x='".$_SESSION['USERID']."'";
		$_p_data = $DB->sqlFetch($_p_sql);
		//�л��� �θ� ��������
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
			$str_re = "��";
		}else{
			$str_re = "��";
		}
		$tpl->assign(array("par_name"=>$_s_data['str_name'], "par_rela"=>$str_re));
		//�л��̸� ����ϰ�
		if($_SESSION['MEM_TYPE'][0] == s){
			$_p_data['str_id_y'] = $_SESSION['USERID'];
		}else{
			if ( !$_p_data['str_id_y'] && !$_SESSION['ADMIN']) {
				WebApp::redirect("/?act=online3hs.list&mcode=$mcode".$pagego,"�ڳ༳���� ���� ���ֽñ� �ٶ��ϴ�.");
				exit;
			}
		}
		
		// ���� �ڳ��� �г������. 
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
		//��û��¥ ����
		$tpl->assign(array("year"=>date("Y"), "month"=>date("m"), "day"=>date("d"), "code"=>$_GET['mcode']));
		
		//���� ��¥ �ջ��ϱ�
		$sql = "select sum(str_date_tmp1) as sum_date from $tb_online3hs where str_id='".$_SESSION['USERID']."' and str_loginid='".$_SESSION['LOGONID']."' and num_mcode='".$_GET['mcode']."' and num_oid='$oid' and str_year='$n_year'";
		$sum_date = $BoardDB->sqlFetchOne($sql);
		$ex_date = 10-$sum_date;
		if($ex_date=='0'){
			WebApp::moveBack("10���� �Ⱓ�� ��� ����Ͽ����ϴ�.\n���� �������� ���ư��ϴ�.");
			exit;
		}
		$tpl->assign("sum_date", $ex_date);
		
		$tpl->setLayout('sub');

		$tpl->define("CONTENT","/html/online3hs/write.htm");
		$tpl->parse("CONTENT");

		break;


	case "POST":

		// ���� 100�� üũ
		if(mb_strlen($str_content2, 'euc-kr')<100)WebApp::moveBack('ü���н���ȹ�� �������� 100�� �̻����� �ۼ��ϼ���.');
		
		$ip = $_SERVER[REMOTE_ADDR];
		$n_year = WebApp::getConf('formation.school_year');//��������
		
		//$str_year = date('Y');
		$max_serial = $BoardDB->sqlFetchOne("select max(num_serial) + 1 from $tb_online3hs where num_oid=$oid and num_mcode=$code");
		if ( !$max_serial ) { $max_serial = "1"; }
		
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
		//print_r($_SESSION);

		$str_id = $_SESSION['USERID'];
		$str_loginid = $_SESSION['LOGONID'];
		$str_partcode = $_SESSION['CAFE_PARTCODE']; 


		// �ʼ����� ���� ���� 2017-09-05
		// ���̵�
		if ( !$str_id ) { WebApp::moveback("�α��� ������ ��Ȯ���� �ʽ��ϴ�. \n��α����� �ۼ� �ٶ��ϴ�."); }
		if ( !$str_loginid ) { WebApp::moveback("�α��� ������ ��Ȯ���� �ʽ��ϴ�. \n��α����� �ۼ� �ٶ��ϴ�."); }
		if ( !$str_partcode ) { WebApp::moveback("ȸ������ �б������� ��Ȯ���� �ʽ��ϴ�. \n�б����� Ȯ���� �ۼ� �ٶ��ϴ�."); }

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


			// �α� ����..�޴� ������ �α� ���ܾ߰���...2010-04-30.juni
			// �α� ���� �뷮üũ �ؼ� ����ġ�� ũ�� ����� ������
			if(@filesize ("log/csiaonline3hsWrite.log") > 1024*1024){
				@rename ("log/csiaonline3hsWrite.log","csiaonline3hsWrite.log".".".time());
			}
			$logfp=fopen("log/csiaonline3hsWrite.log", "a+");
			chmod ("log/csiaonline3hsWrite.log",0777);
			$timestamp=date("Y-m-d H:i:s");
			$insertQuery = "'$oid', '$code', '$max_serial', '$str_name', '$sel_school', '$num_grade', '$t_class', '$str_no', '$time_g1-$time_g2-$time_g3', '$time_g4-$time_g5-$time_g6', '$time_g7', '$edu_type_g', '$choos_g5_txt', '$str_destini', '$str_par', '$str_par_tmp1', '$str_par_tmp2', '$str_in_tmp1', '$str_in_tmp2', '$str_in_tmp3', '$str_title', '��������', '$ip', '$str_to_date', SYSDATE, '$str_id', '$str_loginid','$n_year', '$str_partcode'";
			fwrite($logfp, "[".$timestamp."] [".$_SESSION['LOGONID']."] [��û�� �ۼ�] ".$insertQuery." [".$REMOTE_ADDR."]\n");
			// �α� ����



			WebApp::redirect("/?act=online3hs.list&mcode=$code","�Ϸ�Ǿ����ϴ�.");
		} else {
			//echo $sql;
			//echo '<br />';
			
			//exit;
			WebApp::moveback("������û ����..��õ� �ٶ��ϴ�.");
		}

		break;
}


function _format_data(&$arr) {
	global $oid, $mcode;
	
	$arr['grade'] = mb_substr($arr['str_grade'], 0, 1, 'euc-kr');
	$arr['num_grade'] = substr($arr['str_grade'], 2, 2);
	$str_class = explode("��", $arr['str_class']);
	$arr['class'] = $str_class[0];
	if($arr['grade'] == "��"){
		$arr['grade_h'] = "checked";
	}else if($arr['grade'] == "��"){
		$arr['grade_m'] = "checked";
	}

}

?>