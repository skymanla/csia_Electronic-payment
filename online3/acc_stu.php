<?php
/**
* skymanla
 * 端蝿俳柔 紳虞昔 重短 - 薄舌端蝿俳柔 重短辞
 * DB tmp 町軍 鎧遂 舛軒
 * str_acc_tmp1 = 眼績
 * str_acc_tmp2 = 眼雁採辞
 * str_acc_tmp3 = 嘘巷採舌
 * str_acc_tmp4 = 嘘姶 & 嘘舌
 * str_acc_tmp5 = 俳採乞  
**/

//鯵紺渋昔 羨悦
if(!$_GET['mcode'] || !$_GET['num']) WebApp::redirect("/?main","設公吉 羨悦脊艦陥.");

$code = $_GET['mcode'];
$num = $_GET['num'];
$type = $_GET['type'];

if($_SESSION[ADMIN]){//俳嘘 淫軒切 臣 衣仙(硝郊搾繊せせせせせせせせせせせ)
	if($type=='p'){//俳採乞衣仙
		$sql = "update $tb_online3 set str_acc_tmp5='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "俳採乞 渋昔戚 刃戟鞠醸柔艦陥.");
	}else if($type=='t'){
		$sql = "update $tb_online3 set str_acc_tmp1='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "眼績 渋昔戚 刃戟鞠醸柔艦陥.");
	}
	/*else if($type=='c'){
		$sql = "update $tb_online3 set str_acc_tmp2='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "眼雁採辞 渋昔戚 刃戟鞠醸柔艦陥.");
	}*/
	else if($type=='a'){
		$sql = "update $tb_online3 set str_acc_tmp3='Y', str_acc_tmp4='Y', str_phase='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
		$BoardDB->query($sql);
		$BoardDB->commit();
		WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "嘘巷採舌 渋昔戚 刃戟鞠醸柔艦陥.");
	}
}

//穿衣坦軒
if($_SESSION[acc_tmp1] == 7){//眼雁嘘送据
	/*$sql = "select str_acc_tmp1 from $tb_online3 where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$chk_acc = $BoardDB->sqlFetch($sql);
	if($chk_acc[str_acc_tmp1] == "N"){
		WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "焼送 戚穿 渋昔戚 戚欠嬢走走 省紹柔艦陥.");
	}
	$sql = "update $tb_online3 set str_acc_tmp2='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "渋昔戚 刃戟鞠醸柔艦陥.");*/
	WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "設公吉 渋昔脊艦陥.");
}else if($_SESSION[acc_tmp3] == 8){//嘘巷採舌
	//識 渋昔 号走遂
	//$sql = "select str_acc_tmp2 from $tb_online3 where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$sql = "select str_acc_tmp1 from $tb_online3 where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$chk_acc = $BoardDB->sqlFetch($sql);
	if($chk_acc[str_acc_tmp1] == "N"){
		WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "焼送 戚穿 渋昔戚 戚欠嬢走走 省紹柔艦陥.");
	}
	$sql = "update $tb_online3 set str_acc_tmp3='Y', str_acc_tmp4='Y', str_phase='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "渋昔戚 刃戟鞠醸柔艦陥.");
}else if($_SESSION[acc_tmp2] == 99){//眼績
	$sql = "select str_acc_tmp5 from $tb_online3 where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$chk_acc = $BoardDB->sqlFetch($sql);
	if($chk_acc[str_acc_tmp5] == "N"){
		WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "焼送 戚穿 渋昔戚 戚欠嬢走走 省紹柔艦陥.");
	}
	$sql = "update $tb_online3 set str_acc_tmp1='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "渋昔戚 刃戟鞠醸柔艦陥.");
}else if($_SESSION[acc_tmp5]==98){//俳採乞
	$sql = "update $tb_online3 set str_acc_tmp5='Y' where num_oid='$oid' and num_mcode='$code' and num_serial='$num'";
	$BoardDB->query($sql);
	$BoardDB->commit();
	WebApp::redirect("/?act=online3hs.read&mcode=$code&num=$num", "渋昔戚 刃戟鞠醸柔艦陥.");
}
?>