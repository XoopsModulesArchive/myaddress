<?php

// ------------------------------------------------------------------------- //
//                      MyAddress - XOOPS addressbook                        //
//                        <http://forum.kuri3.net>                          //
// ------------------------------------------------------------------------- //

include 'header.php';
$myts = MyTextSanitizer::getInstance();

// reject Guest and no perm
if (!$xoopsUser) {
    redirect_header(XOOPS_URL . '/user.php', 2, _MD_MYADDRESS_YOUMUSTREG);

    exit();
} elseif (!haveGroupPerm()) {
    redirect_header(XOOPS_URL . '/index.php', 2, _MD_MYADDRESS_YOUHAVENOPERM);

    exit();
}

$mode = isset($_GET['mode']) ? (int)$_GET['mode'] : (isset($_POST['mode']) ? (int)$_POST['mode'] : $myaddress_mode);
$cid = isset($_GET['cid']) ? (int)$_GET['cid'] : (isset($_POST['cid']) ? (int)$_POST['cid'] : 0);
$c_id = isset($_GET['c_id']) ? (int)$_GET['c_id'] : 0;

$skey = isset($_GET['skey']) ? (is_numeric($_GET['skey']) ? (int)$_GET['skey'] : trim($_GET['skey'])) : 'all';

$from = $_GET['from'] ?? 0;

// check if argument 'op' exists
// _GET from viewcat.php,setcompany.php, _POST from myself in case of employee data maintenace
$op = $_GET['op'] ?? ($_POST['op'] ?? '');

// define template file
$GLOBALS['xoopsOption']['template_main'] = 'myaddress_viewcat.html';

// Include the page header
require XOOPS_ROOT_PATH . '/header.php';

$xoopsTpl->assign('lang_total', _MD_MYADDRESS_TOTAL);
$xoopsTpl->assign('mod_url', $mod_url);
$xoopsTpl->assign('mod_copyright', $mod_copyright);
$xoopsTpl->assign('mode', $mode);
$xoopsTpl->assign('cid', $cid);
$xoopsTpl->assign('op', $op);

// show the select box to choose a name-based or company-based mode
$xoopsTpl->assign('select_mode', select_mode($mode, $cid));

// show a link to add an addressbook
if (1 == $mode) {
    $xoopsTpl->assign('lang_add_data', _MD_MYADDRESS_ADDMYADDRESS);

    $xoopsTpl->assign('add_data_url', 'viewaddress.php?mode=' . $mode . '&amp;cid=' . $cid . '&amp;op=add');
} elseif (2 == $mode) {
    $xoopsTpl->assign('lang_add_data', _MD_MYADDRESS_ADDCOMPANY);

    $xoopsTpl->assign('add_data_url', 'viewcompany.php?mode=' . $mode . '&amp;cid=' . $cid . '&amp;op=add');
}

$xoopsTpl->assign('lang_subcategory', _MD_MYADDRESS_SUBCATEGORY);
$xoopsTpl->assign('subcategories', get_sub_categories($mode, $cid));
$xoopsTpl->assign('call_url', 'viewcat.php');

$cids = $cattree->getAllChildId($cid);
$cids = push_gperm_array($cids, 'myadrs_category');
$cids[] = $cid;
$address_total_sum = get_address_total_sum_from_cats($mode, $cids);

$xoopsTpl->assign('mainpathstring', "<A HREF='index.php?mode=$mode'>" . _MD_MYADDRESS_MAIN . '</A>');
$xoopsTpl->assign('address_sub_title', $cattree->getNicePathFromId($cid, 'title', "viewcat.php?mode=$mode"));

if (1 == $mode) {
    $prs = $xoopsDB->query("SELECT COUNT(aid) FROM $table_addressbook WHERE cid=$cid");
} elseif (2 == $mode) {
    $prs = $xoopsDB->query("SELECT COUNT(c_id) FROM $table_company WHERE cid=$cid");
}
[$address_small_sum] = $xoopsDB->fetchRow($prs);
$xoopsTpl->assign('address_small_sum', $address_small_sum);
$xoopsTpl->assign('address_total_sum', (empty($address_total_sum) ? $address_small_sum : $address_total_sum));

//--------------------------------------------------------------------//
//                      show buttons for search                       //
//--------------------------------------------------------------------//

$xoopsTpl->assign(show_searchnav($mode, $cid));

//---------------------------------------------------------------//
//                 show the results of search                    //
//---------------------------------------------------------------//

if ('search' == $op) {
    require_once 'include/searchresults.php';

    $xoopsTpl->assign(search_results($mode, $cid, $skey));
}

//--------------------------------------------------------------------//
//            show the company profile & the employees list           //
//--------------------------------------------------------------------//

if ('list' == $op) {
    require_once 'include/searchresults.php';

    $xoopsTpl->assign('employees', employees_search_results($skey));
}

require XOOPS_ROOT_PATH . '/footer.php';
