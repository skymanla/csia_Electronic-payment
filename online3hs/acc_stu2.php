<?php
/**
* skymanla
 * 端蝿俳柔 紳虞昔 重短 - 薄舌端蝿俳柔 重短辞
 * 端蝿俳柔 左壱辞 - 衣薦 穣徽
**/

if(!$_GET['mcode'] || !$_GET['num']) WebApp::redirect("/?main","設公吉 羨悦脊艦陥.");

$code = $_GET['mcode'];
$num = $_GET['num'];
$type = $_GET['type'];

if($_SESSION[ADMIN]){//俳嘘 淫軒切 臣 衣仙(硝郊搾繊せせせせせせせせせせせ)
	if($type=='p'){//俳採乞衣仙
		$sql = "update $tb_online3hs_bogo set str_bogo_acc1='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3hs.read2&mcode=$code&num=$num", "俳採乞 渋昔戚 刃戟鞠醸柔艦陥.");
	}else if($type=='t'){
		$sql = "update $tb_online3hs_bogo set str_bogo_acc2='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3hs.read2&mcode=$code&num=$num", "眼績 渋昔戚 刃戟鞠醸柔艦陥.");
	/*}else if($type=='c'){
		$sql = "update $tb_online3hs_bogo set str_bogo_acc3='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3hs.read2&mcode=$code&num=$num", "眼雁採辞 渋昔戚 刃戟鞠醸柔艦陥.");*/
	}else if($type=='a'){
		$sql = "update $tb_online3hs_bogo set str_bogo_acc4='Y', str_bogo_acc5='Y', str_phase='Y', str_phase2='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		//置曽 原巷軒
		$sql = "update $tb_online3hs set str_phase2='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3hs.read2&mcode=$code&num=$num", "嘘巷採舌 渋昔戚 刃戟鞠醸柔艦陥.");
	}
}

//穿衣坦軒
/*if($_SESSION[acc_tmp1] == 7){//眼雁嘘送据
	$sql = "select str_bogo_acc2 from $tb_online3hs_bogo where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$chk_acc = $BoardDB->sqlFetch($sql);
	if($chk_acc[str_bogo_acc2] == "N"){
		WebApp::redirect("/?act=online3hs.read2&mcode=$code&num=$num", "焼送 戚穿 渋昔戚 戚欠嬢走走 省紹柔艦陥.");
	}
	$sql = "update $tb_online3hs_bogo set str_bogo_acc3='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3hs.read2&mcode=$code&num=$num", "渋昔戚 刃戟鞠醸柔艦陥.");
}*/
if($_SESSION[acc_tmp3] == 8){//嘘巷採舌
	//識 渋昔 号走遂
	$sql = "select str_bogo_acc2 from $tb_online3hs_bogo where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$chk_acc = $BoardDB->sqlFetch($sql);
	if($chk_acc[str_bogo_acc2] == "N"){
		WebApp::redirect("/?act=online3hs.read2&mcode=$code&num=$num", "焼送 戚穿 渋昔戚 戚欠嬢走走 省紹柔艦陥.");
	}
	$sql = "update $tb_online3hs_bogo set str_bogo_acc4='Y', str_bogo_acc5='Y', str_phase='Y', str_phase2='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	//置曽 原巷軒
	$sql = "update $tb_online3hs set str_phase2='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3hs.read2&mcode=$code&num=$num", "渋昔戚 刃戟鞠醸柔艦陥.");
}else if($_SESSION[acc_tmp2] == 99){//眼績
	$sql = "select str_bogo_acc1 from $tb_online3hs_bogo where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$chk_acc = $BoardDB->sqlFetch($sql);
	if($chk_acc[str_bogo_acc1] == "N"){
		WebApp::redirect("/?act=online3hs.read2&mcode=$code&num=$num", "焼送 戚穿 渋昔戚 戚欠嬢走走 省紹柔艦陥.");
	}
	$sql = "update $tb_online3hs_bogo set str_bogo_acc2='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3hs.read2&mcode=$code&num=$num", "渋昔戚 刃戟鞠醸柔艦陥.");
}else if($_SESSION[acc_tmp5]==98){//俳採乞
	$sql = "update $tb_online3hs_bogo set str_bogo_acc1='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3hs.read2&mcode=$code&num=$num", "渋昔戚 刃戟鞠醸柔艦陥.");
}
?>