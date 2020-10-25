<?php

// ------------------------------------------------------------------------- //
//                      MyAddress - XOOPS addressbook                        //
//                        <http://forum.kuri3.net>                          //
// ------------------------------------------------------------------------- //

include 'header.php';
// include Smarty template engine and initialize it
require_once XOOPS_ROOT_PATH . '/class/template.php';
$xoopsTpl = new XoopsTpl();
$myts = MyTextSanitizer::getInstance();

// reject Guest and no perm
if (!$xoopsUser) {
    redirect_header(XOOPS_URL . '/user.php', 2, _MD_MYADDRESS_YOUMUSTREG);

    exit();
} elseif (!haveGroupPerm()) {
    redirect_header(XOOPS_URL . '/index.php', 2, _MD_MYADDRESS_YOUHAVENOPERM);

    exit();
}

$mode = 2;

$cid = $_GET['cid'] ?? 0;
$c_id = $_GET['c_id'] ?? 0;
$from = $_GET['from'] ?? 0;

$skey = isset($_GET['skey']) ? (is_numeric($_GET['skey']) ? (int)$_GET['skey'] : trim($_GET['skey'])) : 'all';

// check if variable 'op' exist
// GET from myself and viewaddress.php, POST from myself in case of emplyee data maintenance
$op = $_GET['op'] ?? ($_POST['op'] ?? '');

// Include the page header
xoops_header(false);
echo "
<SCRIPT LANGUAGE=\"JavaScript\">
<!--
function selectCompany(form) {
	var n = (form.elements.length-1)/6;
	if( n == 1 ) {
// changed at ver.1.1.3
//		opener.document.address.company.value = form.company.value;
		opener.document.getElementById('company_name').firstChild.nodeValue=form.company.value;
		opener.document.address.c_id.value = form.c_id.value;
		opener.document.address.cname.value = form.cname.value;
		opener.document.address.cdivision.value = form.cdivision.value;
		opener.document.address.cphone.value = form.cphone.value;
		opener.document.address.cfax.value = form.cfax.value;

	} else {
		for(i=0 ; i<n ; i++)	{
			if(form.c_id[i].checked) {
// changed at ver.1.1.3
//				opener.document.address.company.value = form.company[i].value;
			opener.document.getElementById('company_name').firstChild.nodeValue=form.company[i].value;
				opener.document.address.c_id.value = form.c_id[i].value;
				opener.document.address.cname.value = form.cname[i].value;
				opener.document.address.cdivision.value = form.cdivision[i].value;
				opener.document.address.cphone.value = form.cphone[i].value;
				opener.document.address.cfax.value = form.cfax[i].value;
			}
		}
	}
	opener.focus();
	window.close();
}
//-->
</SCRIPT>\n";
echo "
</HEAD>
<BODY>\n";

$xoopsTpl->assign(
    [
        'lang_total' => _MD_MYADDRESS_TOTAL,
'mod_url' => $mod_url,
'mod_copyright' => $mod_copyright,
'mode' => $mode,
'cid' => $cid,
'op' => $op,
'lang_subcategory' => _MD_MYADDRESS_SUBCATEGORY,
'call_url' => 'setcompany.php',
'subcategories' => get_sub_categories($mode, $cid),
'lang_add_data' => _MD_MYADDRESS_ADD_COMPANY,
'add_data_url' => 'viewcompany.php?mode=' . $mode . '&amp;cid=' . $cid . '&amp;op=add&amp;pop=y',
'lang_close' => _CLOSE,
    ]
);

$cids = $cattree->getAllChildId($cid);
$cids = push_gperm_array($cids, 'myadrs_category');
$cids[] = $cid;
$address_total_sum = get_address_total_sum_from_cats($mode, $cids);

$xoopsTpl->assign('mainpathstring', "<A HREF='setcompany.php?mode=2'>" . _MD_MYADDRESS_MAIN . '</A>');
$xoopsTpl->assign('address_sub_title', $cattree->getNicePathFromId($cid, 'title', "setcompany.php?mode=$mode"));

$prs = $xoopsDB->query("SELECT COUNT(c_id) FROM $table_company WHERE cid=$cid");
[$address_small_sum] = $xoopsDB->fetchRow($prs);
$xoopsTpl->assign('address_small_sum', $address_small_sum);
$xoopsTpl->assign('address_total_sum', (empty($address_total_sum) ? $address_small_sum : $address_total_sum));

//--------------------------------------------------------------------//
//                      show buttons for search                       //
//--------------------------------------------------------------------//

$xoopsTpl->assign(show_searchnav($mode, $cid, 'setcompany.php'));

//--------------------------------------------------------------------//
//                     show the results of search                     //
//--------------------------------------------------------------------//

if ('search' == $op) {
    require_once 'include/searchresults.php';

    $xoopsTpl->assign(search_results($mode, $cid, $skey));
} elseif ('list' == $op && 0 != $c_id) {
    include 'include/searchresults.php';

    $xoopsTpl->assign(search_results($mode, $cid, 0, $c_id, 'n'));
}
$xoopsTpl->display('db:myaddress_get_company_in_box.html');
$xoopsTpl->display('db:myaddress_footer.html');
xoops_footer();
exit();
