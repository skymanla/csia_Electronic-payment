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
		
		if($_SESSION[ADMIN] || $_SESSION[acc_tmp1] || $_SESSION[acc_tmp2] || $_SESSION[acc_tmp3] || $_SESSION[acc_tmp5]){
			//pass
		}else{
			$select_where = "and str_id='$str_id' and str_loginid='$str_loginid'";
		}
		$sql = "SELECT 
				*
			  FROM $tb_online3_bogo
			 WHERE num_oid='$oid' and num_mcode='$code' and num_serial='$num' $select_where";
		//echo $sql;
		$data = $BoardDB->sqlFetch($sql);
		
		if($_SESSION[ADMIN] || $_SESSION[acc_tmp1] || $_SESSION[acc_tmp2] || $_SESSION[acc_tmp3]){
			//pass
		}else{
			if($data[str_phase2]=="Y" || $data[str_bogo_acc2]=="Y"){
				WebApp::redirect("/?act=online3.list&mcode=$mcode","������ �Ǿ� ������ �Ұ����մϴ�.");
			}
		}
		
		if(empty($data)) WebApp::redirect("/?act=online3.write2&mcode=$code&num=$num","���� ������ �ۼ����� �ʾҽ��ϴ�.");
		
		$tpl->assign(array("year"=>date("Y"), "month"=>date("m"), "day"=>date("d"), "code"=>$_GET['mcode']));
		//�б����� �� �Ⱓ ��������
		$sql = "SELECT 
				num_oid, num_mcode, num_serial, str_name, str_tmp1, str_tmp2, str_tmp3, str_tmp4, str_s_date, str_e_date, str_date_tmp1, str_lec, str_lec_tmp, str_destini, str_par, str_par_tmp1, str_par_tmp2,
				str_in_tmp1, str_in_tmp2, str_in_tmp3, str_title, str_content, str_ip, str_to_time, str_acc_tmp1, str_acc_tmp2, str_acc_tmp3, str_acc_tmp4, str_acc_tmp5,
				TO_CHAR(dt_date, 'YYYY-MM-DD') dt_date, str_id, str_loginid, str_phase, str_phase2 
			  FROM $tb_online3
			 WHERE num_oid='$oid' and num_mcode='$code' and num_serial='$num' $select_where";
		$sin_data = $BoardDB->sqlFetch($sql);
		if($sin_data['str_tmp1']=="grade_m"){
			$str_tmp1 = "���б�";
		}else if($sin_data['str_tmp1']=="grade_h"){
			$str_tmp1 = "����б�";
		}
		$s_date = explode('-',$sin_data['str_s_date']);
		$e_date = explode('-',$sin_data['str_e_date']);
		$tpl->assign(array('str_tmp1'=>$str_tmp1,'str_tmp2'=>$sin_data['str_tmp2'],'str_tmp3'=>$sin_data['str_tmp3'],'str_tmp4'=>$sin_data['str_tmp4'],'s_date_y'=>$s_date[0],'s_date_m'=>$s_date[1],
							's_date_d'=>$s_date[2],'e_date_y'=>$e_date[0],'e_date_m'=>$e_date[1],'e_date_d'=>$e_date[2],
							'str_date_tmp1'=>$sin_data['str_date_tmp1'], 'str_title'=>$sin_data['str_title'],'str_par'=>$sin_data['str_par'],'str_par_tmp1'=>$sin_data['str_par_tmp1']));
		
		$tpl->setLayout('sub');
		if (getenv('REMOTE_ADDR') == '125.130.18.5') {
			$tpl->define("CONTENT","/html/online3/modify2_adm.htm");
		} else {
			$tpl->define("CONTENT","/html/online3/modify2.htm");
		}
		
		//÷������ ��������
		$tpl->define("FILE","CONTENT");
		$sql = "select * from $tb_online3_files where num_oid='$oid' and num_mcode='$code' and num_main='$num' $select_where";
		$files = $BoardDB->sqlFetchAll($sql);
		@array_walk($files,'cb_format_list');
		$tpl->parse('FILE', &$files);
		//echo $sql;
		
		@_format_data(&$data);
		$tpl->assign($data);
		
		$acc_link_val = "/?act=online3.acc_stu2&mcode=$code&num=$num";
		$acc_title_val = "����";
		if($_SESSION[acc_tmp1] == 7){//��米��
			$tpl->assign(array("acc_link"=>$acc_link_val , "acc_title"=>$acc_title_val));
		}else if($_SESSION[acc_tmp3] == 8){//����
			$tpl->assign(array("acc_link"=>$acc_link_val, "acc_title"=>$acc_title_val));
		}else if($_SESSION[acc_tmp2] == 99){//����
			$tpl->assign(array("acc_link"=>$acc_link_val, "acc_title"=>$acc_title_val));
		}else if($_SESSION[acc_tmp5] == 98){//�кθ�
			$tpl->assign(array("acc_link"=>$acc_link_val, "acc_title"=>$acc_title_val));
		}else{//������ ��� ����
			if($data[str_acc_tmp5] == "Y"){//�кθ� ������ ������ ������ �Ұ����ϰ� ����
				$tpl->assign(array("acc_link"=> "#","tmp5_ok"=>"onclick='return mody_click();'", "acc_title"=>"����"));
			}else{
				$tpl->assign(array("acc_link"=> "/?act=online3.modify2&mcode=$code&num=$num", "acc_title"=>"����"));
			}
			
		}
		
		$tpl->parse("CONTENT");

		break;
		
		
	case "POST":
		
		
	
		// ���� 500�� üũ
		$ChkLenboardContent = preg_replace("/\s+/", "", $boardContent);
		if(mb_strlen($ChkLenboardContent, 'euc-kr')<500)WebApp::moveBack('���� ������ �������� 500�� �̻����� �ۼ��ϼ���.');
		//if(mb_strlen($boardContent, 'euc-kr')<500)WebApp::moveBack('���� ������ �������� 500�� �̻����� �ۼ��ϼ���.');
		
		
		$ip = $_SERVER[REMOTE_ADDR];
		$str_year = date('Y');
		$str_loginid = $bid;
		//�ּҰ��� �����ͼ� DB���� ã�ƺ���
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
		//$update_set = "num_oid,num_mcode,num_serial,str_name,str_bogo_title,str_bogo_text,str_ip,str_to_time,dt_date,str_id,str_loginid,str_partcode,str_phase,str_phase2,str_year";
		$update_set = "str_bogo_text='$boardContent', dt_date=SYSDATE";
		//insert values
		$sql = "update $tb_online3_bogo set	$update_set	where num_oid='$oid' and num_mcode='$code' and num_serial='$num' and str_id='$bid'";
		//echo $sql; exit;
		//file ó��
		
		if($_FILES){
			$pathdir = _DIR_MAIN.'/hosts/'.$HOST.'/files/online3';
			if(!is_dir( $pathdir)) mkdir($pathdir,0777);
			
			$pathdir_y = _DIR_MAIN.'/hosts/'.$HOST.'/files/online3/'.$str_year;
			if(!is_dir( $pathdir_y)) mkdir($pathdir_y,0777);
			
			$pathdir_s = _DIR_MAIN.'/hosts/'.$HOST.'/files/online3/'.$str_year."/".$bid;
			if(!is_dir($pathdir_s)) mkdir($pathdir_s,0777);
			
			$pathdir_d = _DIR_MAIN.'/hosts/'.$HOST.'/files/online3/'.$str_year."/".$bid."/".$num;
			if(!is_dir($pathdir_d)) mkdir($pathdir_d,0777);
			
			$updirpath = "files/online3/$str_year/$bid/$num";
			$file_sql="select max(num_serial) from tab_onlie3_files where num_oid=".$oid." and num_mcode=".$code." and num_main=".$num;
			$file_serial = $BoardDB->sqlFetchOne($file_sql);
			$file_serial = ($file_serial) ? $file_serial+1 : 1;
			$cnt = count($_FILES['upfiles']['name']);
			
			$del_file = "delete from $tb_online3_files where num_oid='$oid' and num_mcode='$code' and num_main='$num' and str_id='$bid'";
			$BoardDB->query($del_file);
			$BoardDB->commit();
			
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
		}
		
		if ($BoardDB->query($sql)) {
			$BoardDB->commit();
			
			if ($_POST['page']) {
				$pagego = "&page=".$_GET['page'];
			}

			WebApp::redirect("/?act=online3.list&mcode=$code","�Ϸ�Ǿ����ϴ�.");
		} else {
			echo $sql;
			echo '<br />';
			exit;
			WebApp::moveback("������û ����..��õ� �ٶ��ϴ�.");
		}
		
		break;


}


function _format_data(&$arr) {
	global $oid, $mcode, $sin_data;
	//�߰� ����
	if($arr[str_tmp1] == "grade_m"){
		$arr[grade_m] = "checked";
	}else if($arr[str_tmp1] == "grade_h"){
		$arr[grade_h] = "checked";
	}
	//��¥ �ڸ���
	$f_s_date = explode("-", $sin_data['str_s_date']);
	$arr['s_date_y'] = $f_s_date[0];
	$arr['s_date_m'] = $f_s_date[1];
	$arr['s_date_d'] = $f_s_date[2];
	$f_e_date = explode("-", $sin_data['str_e_date']);
	$arr['e_date_y'] = $f_e_date[0];
	$arr['e_date_m'] = $f_e_date[1];
	$arr['e_date_d'] = $f_e_date[2];
	
	//�н�����
	switch($sin_data[str_lec]){
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
	
	if ($arr['str_bogo_text']) {
		$arr['str_bogo_text']=$arr['str_bogo_text']->load();
	}
	//�����ϴ� ��� ��������ϴ� ��¥�� �纯��
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
	/*$filename = $arr['str_real_up']; 
	$reail_filename = urldecode($arr['str_realname_bogo']); 
	$file_dir = $arr['str_upname_bogo']; 
	
	header('Content-Type: application/x-octetstream');
	header('Content-Length: '.filesize($file_dir));
	header('Content-Disposition: attachment; filename='.$reail_filename);
	header('Content-Transfer-Encoding: binary');
	
	$fp = fopen($file_dir, "r");
	fpassthru($fp);
	fclose($fp);*/


}
?>
