<?php
/*
 * skymanla
 * �ϰ�����
 * ajax - json ���� �Ҷ� ������ php ���� �Ƚ����� json���� request �����°� ����
 * 20180220
 */
//�ϰ����ο� ����
$data = $_REQUEST;
	if($_SESSION[acc_tmp3] || $_SESSION[acc_tmp2] || $_SESSION[acc_tmp5]){//$_SESSION[acc_tmp1]
		//pass
	}else{
		//������� session�� ������ ������ ����
		$e_code = "900";
		echo $e_code;
		exit;
	}
	$miss_cnt = 0;
	$e_code;
	/*if($_SESSION[acc_tmp1]==7){//��米����
		$up_col = "str_bogo_acc2";
		$default_col = "str_bogo_acc3";
		$add_col="";
	}*/
	if($_SESSION[acc_tmp3]==8){//��������
		$up_col = "str_bogo_acc2";
		$default_col = "str_bogo_acc4";
		$add_col = ", str_bogo_acc5='Y', str_phase='Y', str_phase2='Y'";
	}else if($_SESSION[acc_tmp2]==99){//����
		$up_col = "str_bogo_acc1";
		$default_col = "str_bogo_acc2";
		$add_col="";
	}else if($_SESSION[acc_tmp5]==98){//�кθ�
		$default_col = "str_bogo_acc1";
		$add_col="";
	}

	for($a=0;$a<count($data['chk_idx']);$a++){
		//������ �ۼ� ���� Ȯ��
		$sql = "select num_serial where $tb_online3_bogo where num_oid='$oid' and num_mcode='".$data['mcode']."' and num_serial='".$data['chk_idx'][$a]."'";
		$chk_board = $BoardDB->sqlFetch($sql);
		if(!$chk_board['num_serial'] || $chk_board['num_serial'] == ""){//�������� ���� ���
			$e_code = "400";
			$miss_cnt++;
		}else{
			if($_SESSION[acc_tmp5]==98){//�������� �ִ� ���
				$sql = "update $tb_online3_bogo ".$default_col."='Y' ".$add_col." where num_oid='$oid' and num_mcode='".$data['mcode']."' and num_serial='".$data['chk_idx'][$a]."'";
				$BoardDB->query($sql);
				$BoardDB->commit();
			}else{
				$sql = "select ".$up_col." from $tb_online3_bogo where num_oid='$oid' and num_mcode='".$data['mcode']."' and num_serial='".$data['chk_idx'][$a]."'";
				$chk_acc = $BoardDB->sqlFetch($sql);
				if($chk_acc[$up_col] == "N"){//���� ���εǾ� ���� ���� ���
					$miss_cnt++;
					$e_code = "300";
				}else if($chk_acc[$up_col] == "Y"){
					$sql = "update $tb_online3_bogo ".$default_col."='Y' ".$add_col." where num_oid='$oid' and num_mcode='".$data['mcode']."' and num_serial='".$data['chk_idx'][$a]."'";
					$BoardDB->query($sql);
					$BoardDB->commit();
					if($_SESSION[acc_tmp3]==8){
						$sql = "update $tb_online3 set str_phase2='Y' where num_oid='$oid' and num_mcode='".$data['mcode']."' and num_serial='".$data['chk_idx'][$a]."'";
						$BoardDB->query($sql);
						$BoardDB->commit();
					}
				}	
			}	
		}
	}
	
	if(!$e_code){
		$result = true;
	}else{
		$result = $miss_cnt;
	}
	echo $result;
	exit;
?>