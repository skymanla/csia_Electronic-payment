<?
/**
* skymanla 청심국제중고 체험학습 신청
 * 고딩애들 모듈
 * 결재 미완료 추가, 일괄승인 추가
**/

if ( $_SESSION['ADMIN'] || $_SESSION['AUTH'] ) {

	if ( !$_SESSION['ADMIN'] ) {
		$_id_where = " and str_tmp1 = '".$_SESSION['USERID']."' ";
	} else {
		$_id_where = "";
	}

} else {
	WebApp::redirect("/?act=member.login", "로그인후 이용가능합니다.");
}

//mcode 없을 때
if(!$_GET['mcode']) WebApp::redirect("/?main", "잘못된 접근입니다.");

//담당 그룹 불러오기
//신청담당 -> 7
//신청교무 -> 8
$str_id = $_SESSION['USERID'];
$str_loginid = $_SESSION['LOGONID'];
$code = $_GET['mcode'];
//결제그룹 인원 체크
$sql = "select a.num_oid as num_oid, a.num_pcode as num_pcode, a.str_title as str_title, b.str_id as str_id from tab_group a left join tab_group_member b on a.num_oid=b.num_oid and a.num_pcode=b.num_pcode
		where b.num_oid='$oid' and b.str_id='$str_id'";
$g_data = $DB->sqlFetchAll($sql);
for($aa=0;$aa<count($g_data);$aa++){
	if($g_data[$aa]['num_pcode'] == 7){//담당자
		$_SESSION['acc_tmp1'] = 7;
	}else if($g_data[$aa]['num_pcode'] == 8){//부서장
		$_SESSION['acc_tmp3'] = 8;
	}
}
//담임 가져오기
if(is_array($_SESSION[CAFE_MEM_TYPE])){
	foreach($_SESSION[CAFE_MEM_TYPE] as $key => $val){
		if($school_year == substr($key, 0, 4)){
			$str_partcode = $key;//권한 변경에 의해 해당 학년에 학급이 없는 경우 담임 세션은 없앤다
		}else{
			$str_partcode = "";
		}
		
	}
}

if(debug()==true){
	//echo $str_partcode;
}
$sql = "select num_oid, str_id, chr_rank, str_partcode from tab_member_rank where num_oid='$oid' and str_id='$str_id' and str_partcode='$str_partcode' and str_confirmor_id='formation_set'";
$t_data = $DB->sqlFetch($sql);
//print_r($t_data);

//학부모 가져오기
$sql = "select * from tab_member_relation where num_oid_x='$oid' and str_id_x='$str_id'";
$p_data = $DB->sqlFetchAll($sql);

//결제권한 부여
if($t_data['str_partcode']){//담임
	$_SESSION['acc_tmp2'] = 99;
}else if($p_data[0]['str_id_y']){//학부모 - 연결된 자녀가 둘 이상일 경우
	$_SESSION['acc_tmp5'] = 98;
}

// 파라메터
$page = $_GET['page']? $_GET['page'] : 1 ;
// 페이지 셋팅
$listnum=10;

//search_phase
if($_GET['str_phase']=='1'){//신청서 결재 미완료
	$search_phase = "and str_phase='N'";
}else if($_GET['str_phase']=='2'){//보고서 결재 미완료
	$search_phase = "and str_phase2='N'";
}else if($_GET['str_phase']=='3'){//신청서 및 보고서 결재 미완료
	$search_phase = "and str_phase='N' and str_phase2='N'";
}else if($_GET['str_phase']=='4'){//신청서 및 보고서 결재 완료
	$search_phase = "and str_phase='Y' and str_phase2='Y'";
}else if($_GET['str_phase']=='5'){//학부모(신청서) 결재 미완료
	$search_phase = "and str_acc_tmp5='N'";
}else if($_GET['str_phase']=='6'){//학부모(보고서) 결재 미완료
	$search_phase = "and a.str_phase='Y' and b.str_bogo_acc1='N'";
}else if($_GET['str_phase']=='7'){//담임(신청서) 결재 미완료
	$search_phase = "and str_acc_tmp1='N'";
}else if($_GET['str_phase']=='8'){//담임(보고서) 결재 미완료
	$search_phase = "and a.str_phase='Y' and b.str_bogo_acc2='N'";
}else if($_GET['str_phase']=='9'){//담당부서(신청서) 결재 미완료
	$search_phase = "and str_acc_tmp2='N'";
}else if($_GET['str_phase']=='10'){//담당부서(보고서) 결재 미완료
	$search_phase = "and a.str_phase='Y' and b.str_bogo_acc3='N'";
}else if($_GET['str_phase']=='11'){//교무부장(신청서) 결재 미완료
	$search_phase = "and str_acc_tmp3='N'";
}else if($_GET['str_phase']=='12'){//교무부장(보고서) 결재 미완료
	$search_phase = "and a.str_phase='Y' and b.str_bogo_acc4='N'";
}

if($_GET['str_phase']=='6' || $_GET['str_phase']=='8' || $_GET['str_phase']=='10' || $_GET['str_phase']=='12'){
	$report_sess = true;
}else{
	$report_sess = false;
}

if($_GET['key']=="id"){//아이디 찾기
	if($report_sess == true){
		$search_idx = "and a.str_loginid='".$_GET['search']."'";
	}else{
		$search_idx = "and str_loginid='".$_GET['search']."'";
	}
	
}else if($_GET['key']=="name"){//이름 찾기
	if($report_sess==true){
		$search_idx = "and a.str_name like '%".$_GET['search']."%'";
	}else{
		$search_idx = "and str_name like '%".$_GET['search']."%'";
	}
}
//본인이 작성한 글만 가져오자
if($_SESSION['acc_tmp1'] || $_SESSION['acc_tmp3'] || $_SESSION['acc_tmp4'] || $_SESSION[ADMIN]){
	if($report_sess == true){
		$sql = "SELECT COUNT(*) FROM 
				$tb_online3 a left join $tb_online3_bogo b 
				on a.num_mcode=b.num_mcode and a.num_serial=b.num_serial
				WHERE a.num_oid='$oid' and a.num_mcode='$mcode' $search_phase $search_idx";
	}else{
		$sql = "SELECT COUNT(*) FROM $tb_online3 WHERE num_oid='$oid' and num_mcode='$mcode' $search_phase $search_idx";
	}
	
}else if($_SESSION['acc_tmp2']){
	if($report_sess==true){
		$sql = "SELECT COUNT(*) FROM 
				$tb_online3 a left join $tb_online3_bogo b
				on on a.num_mcode=b.num_mcode and a.num_serial=b.num_serial
				WHERE a.num_oid='$oid' and a.num_mcode='$mcode' and a.str_partcode='".$t_data['str_partcode']."' $search_phase $search_idx";
	}else{
		$sql = "SELECT COUNT(*) FROM $rb_request_m WHERE num_oid='$oid' and num_mcode='$mcode' and str_partcode='".$t_data['str_partcode']."' $search_phase $search_idx";
	}
}else if($_SESSION['acc_tmp5']){//학부모가 자녀를 둘 이상 있을 경우
	$p_cnt = count($p_data);
	if($p_cnt>1)$eet=" or ";
	for($pi=0;$pi<$p_cnt;$pi++){
		if($pi==$p_cnt-1)$eet="";
		if($report_sess==true){
			$p_where .= "a.str_id='".$p_data[$pi]['str_id_y']."'".$eet;
		}else{
			$p_where .= "str_id='".$p_data[$pi]['str_id_y']."'".$eet;
		}
		
	}
	/**
	 * TAB_ONLINE3 a left join TAB_ONLINE3_BOGO b
				on on a.num_mcode=b.num_mcode and a.num_serial=b.num_serial
	 **/
	if($report_sess==true){
		$sql = "SELECT COUNT(*) FROM 
				$tb_online3 a left join $tb_online3_bogo b
				on on a.num_mcode=b.num_mcode and a.num_serial=b.num_serial 
				WHERE a.num_oid='$oid' and a.num_mcode='$mcode' and $p_where $search_phase $search_idx";
	}else{
		$sql = "SELECT COUNT(*) FROM $rb_request_m WHERE num_oid='$oid' and num_mcode='$mcode' and $p_where $search_phase $search_idx";
	}

}else{
	if($report_sess==true){
		$sql = "SELECT COUNT(*) FROM 
				$tb_online3 a left join $tb_online3_bogo b
				on on a.num_mcode=b.num_mcode and a.num_serial=b.num_serial 
				WHERE a.num_oid='$oid' and a.num_mcode='$mcode' and a.str_id='$str_id' and a.str_loginid='$str_loginid' $search_phase $search_idx";
	}else{
		$sql = "SELECT COUNT(*) FROM $tb_online3 WHERE num_oid='$oid' and num_mcode='$mcode' and str_id='$str_id' and str_loginid='$str_loginid' $search_phase $search_idx";
	}
	
}

$total = $BoardDB->sqlFetchOne($sql);
$pageUtil = new PageUtil($page, $total);
$_PAGE_LIST = $pageUtil->printHtml();

// 온라인 신청 목록
$seek = ($page-1)*$pageUtil->getListCut();
$offset = $seek + $listnum;

//결재권자에 한해서 체크박스 표출 여부
$tpl->setCond("acc_button", false);
if(debug()==true){
	//print_r($_SESSION);
}
if($_SESSION[acc_tmp2] || $_SESSION[acc_tmp3] || $_SESSION[acc_tmp5]){//$_SESSION[acc_tmp1]
	//pass
	$tpl->setCond("acc_button", true);
	$Oauth = true;
}

if($_SESSION[ADMIN] || $_SESSION['acc_tmp1'] || $_SESSION['acc_tmp3']){//결제권자 넣어주기
	//pass
}else if($t_data['str_partcode']){
	if($report_sess==true){
		$select_where = "and a.str_partcode='".$t_data['str_partcode']."'";
	}else{
		$select_where = "and str_partcode='".$t_data['str_partcode']."'";
	}
	
}else if($_SESSION['acc_tmp5']){//학부모일 때 특수처리해야함...
	$p_cnt = count($p_data);
	if($p_cnt>1)$eet=" or ";
	for($pi=0;$pi<$p_cnt;$pi++){
		if($pi==$p_cnt-1)$eet="";
		if($report_sess==true){
			$select_where .= "a.str_id='".$p_data[$pi]['str_id_y']."'".$eet;
		}else{
			$select_where .= "str_id='".$p_data[$pi]['str_id_y']."'".$eet;
		}
		
	}
	$select_where = "and ".$select_where;
}else{
	if($report_sess==true){
		$select_where = "and a.str_id='$str_id' and a.str_loginid='$str_loginid'";
	}else{
		$select_where = "and str_id='$str_id' and str_loginid='$str_loginid'";
	}
	
}
if($report_sess==true){
		$sql = "SELECT * FROM (
			SELECT /*+ INDEX_DESC (TAB_ONLINE3 IDX_TAB_ONLINE3) */
				a.num_oid, a.num_mcode, a.num_serial, a.str_name, a.str_tmp1, a.str_tmp2, a.str_tmp3, a.str_tmp4, a.str_s_date, a.str_e_date, a.str_date_tmp1, a.str_lec, a.str_lec_tmp, a.str_destini, a.str_par, a.str_par_tmp1, a.str_par_tmp2,
				a.str_in_tmp1, a.str_in_tmp2, a.str_in_tmp3, a.str_title, a.str_to_time, a.str_acc_tmp1, a.str_acc_tmp2, a.str_acc_tmp3, a.str_acc_tmp4, a.str_acc_tmp5,
				TO_CHAR(a.dt_date, 'YYYY-MM-DD') dt_date, a.str_id, a.str_loginid, a.str_partcode, a.str_phase, a.str_phase2, b.str_bogo_acc1, b.str_bogo_acc2, b.str_bogo_acc3, b.str_bogo_acc4, rownum AS rnum
			  FROM $tb_online3 a left join $tb_online3_bogo b on a.num_mcode=b.num_mcode and a.num_serial=b.num_serial
			 WHERE a.num_oid='$oid' and a.num_mcode='$mcode' $select_where $search_phase $search_idx 
		) WHERE rnum > $seek  AND rnum <= $offset";	
	}else{
		$sql = "SELECT * FROM (
			SELECT /*+ INDEX_DESC (TAB_ONLINE3 IDX_TAB_ONLINE3) */
				num_oid, num_mcode, num_serial, str_name, str_tmp1, str_tmp2, str_tmp3, str_tmp4, str_s_date, str_e_date, str_date_tmp1, str_lec, str_lec_tmp, str_destini, str_par, str_par_tmp1, str_par_tmp2,
				str_in_tmp1, str_in_tmp2, str_in_tmp3, str_title, str_content, str_ip, str_to_time, str_acc_tmp1, str_acc_tmp2, str_acc_tmp3, str_acc_tmp4, str_acc_tmp5,
				TO_CHAR(dt_date, 'YYYY-MM-DD') dt_date, str_id, str_loginid, str_partcode, str_phase, str_phase2, rownum AS rnum
			  FROM $tb_online3
			 WHERE num_oid='$oid' and num_mcode='$mcode' $select_where $search_phase $search_idx
		) WHERE rnum > $seek  AND rnum <= $offset";
	}
//HH24:MI
//echo $sql;
$data = $BoardDB->sqlFetchAll($sql);

if($Oauth == true){
	$tpl->assign("acc_link","");
	$tpl->assign("acc_title","승인");
}

@array_walk($data,'cb_format_list');

$tpl->setCond('phase2', false);

$tpl->setCond('use_search', ($_SESSION['ADMIN']) ? true : false);
$tpl->setCond('use_excel_down', ($_SESSION['ADMIN']) ? true : false);
//$tpl->setCond('use_write_able', ( @in_array("p", $_SESSION['MEM_TYPE']) && !$_SESSION['ADMIN'] ) ? true : false);
$tpl->setCond('use_write_able', ($_SESSION['ADMIN'] || $_SESSION['MEM_TYPE'][0] == 's') ? true : false);

// 템플릿 설정
$tpl->setLayout('sub');
if(debug()==true){
	$tpl->define("CONTENT","/html/online3/list_test.htm");
}else{
	$tpl->define("CONTENT","/html/online3/list.htm");
}

$tpl->define("LIST","CONTENT");

$tpl->assign('code', $_GET['mcode']);
$tpl->assign('page_num', $_GET['page']);
$tpl->assign('PAGE_LIST', $_PAGE_LIST);
$tpl->parse('LIST', &$data);

if($_GET['str_phase']){
	$tpl->assign('str_phase'.$_GET['str_phase'], 'checked');
}
$tpl->parse("CONTENT");



// {{{ FUNCTION
function cut_str($str,$len,$tail="...") {
	if(strlen($str) > $len) {
		for($i=0; $i<$len; $i++) if(ord($str[$i])>127) $i++;
		$str=substr($str,0,$i).$tail;
	}
	return $str;
}

function cb_format_list(&$arr,$key,$param)
{
	GLOBAL $pageUtil,$BoardDB,$tb_online3,$tb_online3_bogo;

	$arr['list_num'] = $pageUtil->total - $arr['rnum'] + 1;
	
	if($arr['str_tmp1'] == "grade_m"){
		$arr['str_tmp1'] = "중";
	}else{
		$arr['str_tmp1'] = "고";
	}
	$arr['str_s_date'] = str_replace("-", ".", $arr['str_s_date']);
	$arr['str_e_date'] = str_replace("-", ".", $arr['str_e_date']);
	
	if($arr['str_acc_tmp5']=='N') $arr['str_tmp_chk']="<br />(신청서 결재 대기)";
	if($arr['str_acc_tmp5']=='Y') $arr['str_tmp_chk']="<br /><span style='color:blue'>(학부모 결재)</span>";
	if($arr['str_acc_tmp1']=='Y') $arr['str_tmp_chk']="<br /><span style='color:blue'>(담임 결재)</span>";
	if($arr['str_acc_tmp2']=='Y') $arr['str_tmp_chk']="<br /><span style='color:blue'>(담당부서 결재)</span>";
	if($arr['str_phase']=="Y") $arr['str_tmp_chk']="<br />(신청서 결재 완료)";
	/*if($arr['str_phase']=="Y"){
		$arr['str_phase'] = "<br />(신청서 결재 완료)";
	}else{
		$arr['str_phase'] = "<br />(신청서 결재 중)"."<br />".$arr['str_tmp_chk'];
	}*/

	// 보고서 작성일자 
	$sql = "select TO_CHAR(dt_date, 'YYYY-MM-DD') dt_date from $tb_online3_bogo where num_oid='".$arr['num_oid']."' and num_mcode='".$arr['num_mcode']."' and num_serial='".$arr['num_serial']."'";
	if ($bogo_date = $BoardDB->sqlFetchOne($sql)) {
		$arr['bogo_date'] = $bogo_date;
	} else {
		$arr['bogo_date'] = "-";
	}

	if($arr['str_phase2']=="Y"){
		$arr['str_phase2'] = "<br />(보고서 결재 완료)";
	}else{
		$sql_chk = "select count(*) as cnt from $tb_online3_bogo where num_oid='".$arr['num_oid']."' and num_mcode='".$arr['num_mcode']."' and num_serial='".$arr['num_serial']."'";
		$bogo_chk = $BoardDB->sqlFetchOne($sql_chk);
		if($bogo_chk == 1){
			$sql_bogo = "select str_bogo_acc3, str_bogo_acc2, str_bogo_acc1 from $tb_online3_bogo where num_oid='".$arr['num_oid']."' and num_mcode='".$arr['num_mcode']."' and num_serial='".$arr['num_serial']."'";
			$bogo_status = $BoardDB->sqlFetch($sql_bogo);
			if($bogo_status['str_bogo_acc1']=='N') $arr['str_phase2'] = "<br />(보고서 결재 대기)";
			if($bogo_status['str_bogo_acc1']=='Y') $arr['str_phase2'] = "<br /><span style='color:blue'>(학부모 결재)</span>";
			if($bogo_status['str_bogo_acc2']=='Y') $arr['str_phase2'] = "<br /><span style='color:blue'>(담임 결재)</span>";
			if($bogo_status['str_bogo_acc3']=='Y') $arr['str_phase2'] = "<br /><span style='color:blue'>(담당부서 결재)</span>";
			//$arr['str_phase2'] = "<br />(보고서 결재 중)";
		}else{
			$arr['str_phase2'] = "<br />(보고서 미작성)";
		}
	}
}
// }}}
?>
