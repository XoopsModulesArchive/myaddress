<?php

// ------------------------------------------------------------------------- //
//                      MyAddress - XOOPS addressbook                        //
//                        <http://forum.kuri3.net>                          //
// ------------------------------------------------------------------------- //

// some initial settings

include 'header.php';

// reject Guest and no perm
if (!$xoopsUser) {
    redirect_header(XOOPS_URL . '/user.php', 2, _MD_MYADDRESS_YOUMUSTREG);

    exit();
} elseif (!haveGroupPerm()) {
    redirect_header(XOOPS_URL . '/index.php', 2, _MD_MYADDRESS_YOUHAVENOPERM);

    exit();
}

$myts = MyTextSanitizer::getInstance(); // MyTextSanitizer object

$GLOBALS['xoopsOption']['template_main'] = 'myaddress_index.html';
require XOOPS_ROOT_PATH . '/header.php';

$mode = isset($_GET['mode']) ? (int)$_GET['mode'] : (isset($_POST['mode']) ? (int)$_POST['mode'] : $myaddress_mode);
$cid = isset($_GET['cid']) ? (int)$_GET['cid'] : (isset($_POST['cid']) ? (int)$_POST['cid'] : 0);

$xoopsTpl->assign(
    [
        'lang_total' => _MD_MYADDRESS_TOTAL,
'mod_url' => $mod_url,
'mod_copyright' => $mod_copyright,
'lang_nomatch' => _MD_MYADDRESS_NOFOUND,
'mode' => $mode,
'cid' => $cid,
'call_url' => 'viewcat.php',
    ]
);

// show the select box to choose a base mode of name or company
$xoopsTpl->assign('select_mode', select_mode($mode, $cid));

// show a link to add an addressbook
if (1 == $mode) {
    $xoopsTpl->assign('lang_add_data', _MD_MYADDRESS_ADDMYADDRESS);

    $xoopsTpl->assign('add_data_url', 'viewaddress.php?mode=' . $mode . '&amp;cid=' . $cid . '&amp;op=add');
} elseif (2 == $mode) {
    $xoopsTpl->assign('lang_add_data', _MD_MYADDRESS_ADDCOMPANY);

    $xoopsTpl->assign('add_data_url', 'viewcompany.php?mode=' . $mode . '&amp;cid=' . $cid . '&amp;op=add');
}

//--------------------------------------------------------------------//
//             show category tree                                     //
//--------------------------------------------------------------------//

$xoopsTpl->assign('subcategories', get_sub_categories($mode, 0));

//--------------------------------------------------------------------//
//               show numbers of addresses registered                 //
//--------------------------------------------------------------------//

$cids = $cattree->getAllChildId(0);
$cids = push_gperm_array($cids, 'myadrs_category');
$whr_cid = 'cid IN (';
foreach ($cids as $cat) {
    $whr_cid .= "$cat,";
}
$whr_cid = mb_substr($whr_cid, 0, -1) . ')';

if (1 == $mode) {
    $prs = $xoopsDB->query("SELECT COUNT(aid) FROM $table_addressbook WHERE $whr_cid");
} else {
    $prs = $xoopsDB->query("SELECT COUNT(c_id) FROM $table_company WHERE $whr_cid");
}
[$address_num_total] = $xoopsDB->fetchRow($prs);
$xoopsTpl->assign('address_global_sum', sprintf(_MD_MYADDRESS_THEREARE, $address_num_total));

//--------------------------------------------------------------------//
//                      show buttons for search                       //
//--------------------------------------------------------------------//

$xoopsTpl->assign(show_searchnav($mode, $cid));

// show footer

require XOOPS_ROOT_PATH . '/footer.php';
