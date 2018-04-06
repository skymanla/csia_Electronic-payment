<?php
/*
 * skymanla
 * 일괄승인
 * ajax - json 으로 할라 했으나 php 버전 안습으로 json으로 request 보내는건 포기
 * 20180220
 */
//일괄승인용 접근
$data = $_REQUEST;
	if($_SESSION[acc_tmp3] || $_SESSION[acc_tmp2] || $_SESSION[acc_tmp5]){//$_SESSION[acc_tmp1]
		//pass
	}else{
		//결재권자 session이 없으면 페이지 종료
		$e_code = "900";
		echo $e_code;
		exit;
	}
	$miss_cnt = 0;
	$e_code;
	/*if($_SESSION[acc_tmp1]==7){//담당교직원
		$up_col = "str_bogo_acc2";
		$default_col = "str_bogo_acc3";
		$add_col="";
	}*/
	if($_SESSION[acc_tmp3]==8){//교무부장
		$up_col = "str_bogo_acc2";
		$default_col = "str_bogo_acc4";
		$add_col = ", str_bogo_acc5='Y', str_phase='Y', str_phase2='Y'";
	}else if($_SESSION[acc_tmp2]==99){//담임
		$up_col = "str_bogo_acc1";
		$default_col = "str_bogo_acc2";
		$add_col="";
	}else if($_SESSION[acc_tmp5]==98){//학부모
		$default_col = "str_bogo_acc1";
		$add_col="";
	}

	for($a=0;$a<count($data['chk_idx']);$a++){
		//보고서 작성 여부 확인
		$sql = "select num_serial where $tb_online3hs_bogo where num_oid='$oid' and num_mcode='".$data['mcode']."' and num_serial='".$data['chk_idx'][$a]."'";
		$chk_board = $BoardDB->sqlFetch($sql);
		if(!$chk_board['num_serial'] || $chk_board['num_serial'] == ""){//보고서가 없는 경우
			$e_code = "400";
			$miss_cnt++;
		}else{
			if($_SESSION[acc_tmp5]==98){//보고서가 있는 경우
				$sql = "update $tb_online3hs_bogo ".$default_col."='Y' ".$add_col." where num_oid='$oid' and num_mcode='".$data['mcode']."' and num_serial='".$data['chk_idx'][$a]."'";
				$BoardDB->query($sql);
				$BoardDB->commit();
			}else{
				$sql = "select ".$up_col." from $tb_online3hs_bogo where num_oid='$oid' and num_mcode='".$data['mcode']."' and num_serial='".$data['chk_idx'][$a]."'";
				$chk_acc = $BoardDB->sqlFetch($sql);
				if($chk_acc[$up_col] == "N"){//이전 승인되어 있지 않은 경우
					$miss_cnt++;
					$e_code = "300";
				}else if($chk_acc[$up_col] == "Y"){
					$sql = "update $tb_online3hs_bogo ".$default_col."='Y' ".$add_col." where num_oid='$oid' and num_mcode='".$data['mcode']."' and num_serial='".$data['chk_idx'][$a]."'";
					$BoardDB->query($sql);
					$BoardDB->commit();
					if($_SESSION[acc_tmp3]==8){
						$sql = "update $tb_online3hs set str_phase2='Y' where num_oid='$oid' and num_mcode='".$data['mcode']."' and num_serial='".$data['chk_idx'][$a]."'";
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