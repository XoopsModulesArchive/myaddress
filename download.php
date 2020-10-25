<?php

//  -------------------------------------------------------------------------//
//                     MyAddress - XOOPS addressbook                         //
//                       <http://forum.kuri3.net>                           //
// --------------------------------------------------------------------------//

include 'header.php';

// reject Guest and no perm
if (!$xoopsUser) {
    redirect_header(XOOPS_URL . '/user.php', 2, _MD_MYADDRESS_YOUMUSTREG);

    exit();
} elseif (!haveGroupPerm()) {
    redirect_header(XOOPS_URL . '/index.php', 2, _MD_MYADDRESS_YOUHAVENOPERM);

    exit();
}

$op = isset($_POST['search']) ? 'search' : (isset($_POST['download']) ? 'download' : 'form');

if ('form' == $op) {
    //--------------------------------------------------------------------//

    //                       show form                                    //

    //--------------------------------------------------------------------//

    $GLOBALS['xoopsOption']['template_main'] = 'myaddress_searchform.html';

    require XOOPS_ROOT_PATH . '/header.php';

    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

    // category

    $category_select = new XoopsFormSelect(_MD_MYADDRESS_CATEGORY, 'cid');

    $category_select->addOption(0, '-----');

    $tree = $cattree->getChildTreeArray(0, $myaddress_catorder);

    $tree = push_gperm_array($tree, 'myadrs_category');

    foreach ($tree as $leaf) {
        $leaf['prefix'] = mb_substr($leaf['prefix'], 0, -1);

        $leaf['prefix'] = str_replace('.', '--', $leaf['prefix']);

        $category_select->addOption($leaf['cid'], $leaf['prefix'] . $leaf['title']);
    }

    // uname

    $useronly_radio = new XoopsFormRadioYN(_MD_MYADDRESS_USERONLY, 'useronly', 1, _YES, _NO);

    $iamadmin = false;

    if ($xoopsUser && $xoopsUser->isAdmin()) {
        $iamadmin = true;
    }

    if ($iamadmin) {
        $uname_text = new XoopsFormText('', 'user_uname', 30, 60);

        $uname_match = new XoopsFormSelectMatchOption('', 'user_uname_match');

        $uname_tray = new XoopsFormElementTray(_AM_MYADDRESS_SUBMITTER, '&nbsp;');

        $uname_tray->addElement($uname_match);

        $uname_tray->addElement($uname_text);
    }

    // relations

    $relations_select = new XoopsFormSelect(_MD_MYADDRESS_RELATIONS, 'relations');

    $relations_select->addOption(0, '-----');

    $relarray = getSelBoxRelations();

    foreach ($relarray as $rel) {
        $relations_select->addOption($rel['value'], $rel['label']);
    }

    // cname_jh

    $cname_jh_text = new XoopsFormText('', 'cname_jh', 30, 60);

    $cname_jh_match = new XoopsFormSelectMatchOption('', 'cname_jh_match');

    $cname_jh_tray = new XoopsFormElementTray(_MD_MYADDRESS_COMPANY_JH, '&nbsp;');

    $cname_jh_tray->addElement($cname_jh_match);

    $cname_jh_tray->addElement($cname_jh_text);

    // fullname

    $fullname_text = new XoopsFormText('', 'fullname', 30, 60);

    $fullname_match = new XoopsFormSelectMatchOption('', 'fullname_match');

    $fullname_tray = new XoopsFormElementTray(_MD_MYADDRESS_FULLNAME, '&nbsp;');

    $fullname_tray->addElement($fullname_match);

    $fullname_tray->addElement($fullname_text);

    // fullname_jh

    $fullname_jh_text = new XoopsFormText('', 'fullname_jh', 30, 60);

    $fullname_jh_match = new XoopsFormSelectMatchOption('', 'fullname_jh_match');

    $fullname_jh_tray = new XoopsFormElementTray(_MD_MYADDRESS_FULLNAME_JH, '&nbsp;');

    $fullname_jh_tray->addElement($fullname_jh_match);

    $fullname_jh_tray->addElement($fullname_jh_text);

    // comments

    $comments_text = new XoopsFormText('', 'comments', 30, 100);

    $comments_match = new XoopsFormSelectMatchOption('', 'comments_match');

    $comments_tray = new XoopsFormElementTray(_MD_MYADDRESS_COMMENTS, '&nbsp;');

    $comments_tray->addElement($comments_match);

    $comments_tray->addElement($comments_text);

    // time updated

    $updated_text = new XoopsFormText('', 'updated', 30, 60);

    $updated_date = new XoopsFormSelect('', 'updated_date');

    $updated_date->addOptionArray(
        [
            '4' => _MD_MYADDRESS_YEAR,
'6' => _MD_MYADDRESS_YEARMONTH,
'8' => _MD_MYADDRESS_YEARMONTHDAY,
        ]
    );

    $updated_match = new XoopsFormSelect('', 'updated_match');

    $updated_match->addOptionArray([' > ' => _MD_MYADDRESS_LARGER, ' >= ' => _MD_MYADDRESS_NOTLESS, ' = ' => _MD_MYADDRESS_EQUAL, ' <= ' => _MD_MYADDRESS_NOTMORE, ' < ' => _MD_MYADDRESS_LESS]);

    //	$updated_match = new XoopsFormSelectMatchOption("", "updated_match");

    $updated_tray = new XoopsFormElementTray(_MD_MYADDRESS_UPDATED, '&nbsp;');

    $updated_tray->addElement($updated_date);

    $updated_tray->addElement($updated_match);

    $updated_tray->addElement($updated_text);

    // sort key

    $sort_select = new XoopsFormSelect(_MD_MYADDRESS_SORT, 'sort_key', 'fullname_jh');

    $sort_select->addOptionArray(['cid' => _MD_MYADDRESS_CATEGORY, 'relations' => _MD_MYADDRESS_RELATIONS, 'fullname_jh' => _MD_MYADDRESS_FULLNAME_JH, 'updated' => _MD_MYADDRESS_UPDATED]);

    // sort order

    $order_select = new XoopsFormSelect(_MD_MYADDRESS_ORDER, 'sort_order');

    $order_select->addOptionArray(['ASC' => _MD_MYADDRESS_ASC, 'DESC' => _MD_MYADDRESS_DESC]);

    $limit_select = new XoopsFormSelect(_MI_MYADDRESS_NUMPERPAGE, 'limit', 25);

    $limit_select->addOptionArray(['5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30, '50' => 50]);

    $submit_search = new XoopsFormButton('', 'search', _MD_MYADDRESS_BUTTONFORSEARCH, 'submit');

    $submit_download = new XoopsFormButton('', 'download', _MD_MYADDRESS_BUTTONFORDOWNLOAD, 'submit');

    $submit_tray = new XoopsFormElementTray('', '&nbsp;');

    $submit_tray->addElement($submit_search);

    $submit_tray->addElement($submit_download);

    $form = new XoopsThemeForm('', 'searchform', 'download.php');

    $form->addElement($useronly_radio);

    if ($iamadmin) {
        $form->addElement($uname_tray);
    }

    $form->addElement($category_select);

    $form->addElement($relations_select);

    $form->addElement($cname_jh_tray);

    $form->addElement($fullname_tray);

    $form->addElement($fullname_jh_tray);

    $form->addElement($comments_tray);

    $form->addElement($updated_tray);

    $form->addElement($sort_select);

    $form->addElement($order_select);

    $form->addElement($limit_select);

    $form->addElement($submit_tray);

    $form->assign($xoopsTpl);

    $xoopsTpl->assign('lang_advanced_search', _MI_TEXT_SMNAME1);

    //	$xoopsTpl->assign('lang_records', sprintf(_MD_MYADDRESS_TOTALRECORDS, '<span style="color:#ff0000;">'.$total.'</span>'));

    require_once XOOPS_ROOT_PATH . '/footer.php';
} elseif ('search' == $op) {
    //--------------------------------------------------------------------//

    //                  search and show results                           //

    //--------------------------------------------------------------------//

    $GLOBALS['xoopsOption']['template_main'] = 'myaddress_results.html';

    require XOOPS_ROOT_PATH . '/header.php';

    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

    $myts = MyTextSanitizer::getInstance();

    $_POST['search'] = 'search';

    $sql = "SELECT aid, cid, fullname, myphone, mycellphone1, myemail1, updated FROM $table_addressbook";

    $where = makeWhereClause();

    $order = makeOrderClause();

    // get total counts of affected addressbook

    $res_total = $xoopsDB->query("SELECT count(aid) FROM $table_addressbook " . $where);

    [$total] = $xoopsDB->fetchRow($res_total);

    $total = (int)$total;

    $limit = (!empty($_POST['limit'])) ? (int)$_POST['limit'] : 20;

    $start = (!empty($_POST['start'])) ? (int)$_POST['start'] : 0;

    $xoopsTpl->assign('lang_search', _MD_MYADDRESS_SEARCH);

    $xoopsTpl->assign('lang_results', _MD_MYADDRESS_RESULTS);

    if (0 == $total) {
        $xoopsTpl->assign('lang_nonefound', _MD_MYADDRESS_NOFOUND);
    } elseif ($start < $total) {
        $xoopsTpl->assign('total_found', $total);

        $xoopsTpl->assign('mode', 9);

        // assign header

        $xoopsTpl->assign('lang_fullname', _MD_MYADDRESS_FULLNAME);

        $xoopsTpl->assign('lang_myphone', _MD_MYADDRESS_PHONE);

        $xoopsTpl->assign('lang_mycellphone1', _MD_MYADDRESS_CELLPHONE);

        $xoopsTpl->assign('lang_myemail1', _MD_MYADDRESS_EMAIL1);

        $xoopsTpl->assign('lang_updated', _MD_MYADDRESS_UPDATED);

        $result = $xoopsDB->query($sql . $where . $order, $limit, $start);

        $myaddressdata = [];

        // assign addressbook data to template

        while (list($aid, $cid, $fullname, $myphone, $mycellphone1, $myemail1, $updated) = $xoopsDB->fetchRow($result)) {
            $myaddressdata['fullname'] = "<A HREF='viewaddress.php?mode=1&cid=$cid&op=edit&aid=" . $aid . "'>" . htmlspecialchars($fullname, ENT_QUOTES | ENT_HTML5) . '</A>';

            $myaddressdata['myphone'] = htmlspecialchars($myphone, ENT_QUOTES | ENT_HTML5);

            $myaddressdata['mycellphone1'] = htmlspecialchars($mycellphone1, ENT_QUOTES | ENT_HTML5);

            if ('' != $myemail1) {
                $myaddressdata['myemail1'] = "<A HREF='mailto:" . htmlspecialchars($myemail1, ENT_QUOTES | ENT_HTML5) . "'><IMG SRC='" . XOOPS_URL . "/images/icons/email.gif' BORDER='0' ALT='" . sprintf(_SENDEMAILTO, htmlspecialchars($myemail1, ENT_QUOTES | ENT_HTML5)) . "'></A>";

            //				$myaddressdata['myemail1'] = "<A HREF='mailto:". htmlspecialchars($myemail1) ."'>".htmlspecialchars($myemail1)."</A>";
            } else {
                $myaddressdata['myemail1'] = '';
            }

            //			if ( $myemail2 != "" ) {

            //						$myaddressdata['myemail2'] = "<A HREF='mailto:". $myemail2 ."'><IMG SRC='".XOOPS_URL."/images/icons/email.gif' BORDER='0' ALT='".sprintf( _SENDEMAILTO, $myemail2 )."'></A>";

            //				$myaddressdata['myemail2'] = "<A HREF='mailto:". $myemail2 ."'>".$myemail2."</A>";

            //			} else {

            //				$myaddressdata['myemail2'] = "";

            //			}

            //					$myaddressdata['adminlink'] = "<A HREF='". $mod_url ."/viewaddress.php?mode=$mode&cid=$cid&op=edit&aid=". $aid ."'>". _MD_MYADDRESS_EDIT. "</A> | <A HREF='". $mod_url ."/viewaddress.php?mode=$mode&cid=$cid&op=del&aid=". $aid ."'>". _MD_MYADDRESS_DELETE ."</A>";

            $myaddressdata['updated'] = htmlspecialchars($updated, ENT_QUOTES | ENT_HTML5);

            $xoopsTpl->append('myaddresses', $myaddressdata);
        }

        $totalpages = ceil($total / $limit);

        if ($totalpages > 1) {
            $hiddenform = "<FORM NAME='findnext' ACTION='download.php' METHOD='POST'>";

            foreach ($_POST as $k => $v) {
                $hiddenform .= "<INPUT TYPE='hidden' NAME='" . htmlspecialchars($k, ENT_QUOTES | ENT_HTML5) . "' value='" . htmlspecialchars($v, ENT_QUOTES | ENT_HTML5) . "'>\n";
            }

            if (!isset($_POST['limit'])) {
                $hiddenform .= "<INPUT TYPE='hidden' NAME='limit' VALUE='" . $limit . "'>\n";
            }

            if (!isset($_POST['start'])) {
                $hiddenform .= "<INPUT TYPE='hidden' NAME='start' VALUE='" . $start . "'>\n";
            }

            $prev = $start - $limit;

            if ($start - $limit >= 0) {
                $hiddenform .= "<A HREF='#0' ONCLICK='javascript:document.findnext.start.value=" . $prev . ";document.findnext.submit();'>" . _MD_MYADDRESS_PREVIOUS . "</A>&nbsp;\n";
            }

            $counter = 1;

            $currentpage = ($start + $limit) / $limit;

            while ($counter <= $totalpages) {
                if ($counter == $currentpage) {
                    $hiddenform .= '<b>' . $counter . '</b> ';
                } elseif (($counter > $currentpage - 4 && $counter < $currentpage + 4) || 1 == $counter || $counter == $totalpages) {
                    if ($counter == $totalpages && $currentpage < $totalpages - 4) {
                        $hiddenform .= '... ';
                    }

                    $hiddenform .= "<A HREF='#" . $counter . "' ONCLICK='javascript:document.findnext.start.value=" . ($counter - 1) * $limit . ";document.findnext.submit();'>" . $counter . '</A> ';

                    if (1 == $counter && $currentpage > 5) {
                        $hiddenform .= '... ';
                    }
                }

                $counter++;
            }

            $next = $start + $limit;

            if ($total > $next) {
                $hiddenform .= "&nbsp;<A HREF='#" . $total . "' ONCLICK='javascript:document.findnext.start.value=" . $next . ";document.findnext.submit();'>" . _MD_MYADDRESS_NEXT . "</a>\n";
            }

            $hiddenform .= '</FORM>';

            $xoopsTpl->assign('pagenav', $hiddenform);

            $xoopsTpl->assign('lang_numfound', sprintf(_MD_MYADDRESS_ADRSFOUND, $total));
        }
    }

    require_once XOOPS_ROOT_PATH . '/footer.php';
} else {
    //--------------------------------------------------------------------//

    //                         download csv file                          //

    //--------------------------------------------------------------------//

    $myts = MyTextSanitizer::getInstance();

    $today = date('YmdHi');

    header('Content-Type: application/octet-stream');

    header('Content-Disposition: attachment; filename=' . $today . 'myaddress.csv');

    $rs_adrs = $xoopsDB->query("SELECT * FROM $table_addressbook LIMIT 1");

    for ($i = 0; $i < mysqli_num_fields($rs_adrs); $i++) {
        print(mysql_field_name($rs_adrs, $i) . ',');
    }

    $rs_comp = $xoopsDB->query("SELECT * FROM $table_company LIMIT 1");

    $numf = mysqli_num_fields($rs_comp);

    for ($i = 0; $i < mysqli_num_fields($rs_comp); $i++) {
        print(mysql_field_name($rs_comp, $i) . ',');
    }

    print("\n");

    $sql = "SELECT * FROM $table_addressbook";

    $where = makeWhereClause();

    $order = makeOrderClause();

    $rs_adrs = $xoopsDB->query($sql . $where . $order);

    for ($j = 0; $j < $GLOBALS['xoopsDB']->getRowsNum($rs_adrs); $j++) {
        for ($k = 0; $k < mysqli_num_fields($rs_adrs); $k++) {
            $str = mysql_result($rs_adrs, $j, $k);

            $str = mb_convert_encoding($str, 'SJIS', 'EUC-JP');

            print($str . ',');
        }

        $c_id = (int)mysql_result($rs_adrs, $j, 'c_id');

        $rs_comp = $xoopsDB->query("SELECT * FROM $table_company WHERE c_id=$c_id");

        if (0 == $GLOBALS['xoopsDB']->getRowsNum($rs_comp)) {
            print($str . '0,');

            for ($k = 1; $k < $numf; $k++) {
                print($str . ',');
            }
        } else {
            for ($k = 0; $k < mysqli_num_fields($rs_comp); $k++) {
                $str = mysql_result($rs_comp, 0, $k);

                $str = mb_convert_encoding($str, 'SJIS', 'EUC-JP');

                print($str . ',');
            }
        }

        print("\n");
    }
}

//--------------------------------------------------------------------//
//                       criteria things function                      //
//--------------------------------------------------------------------//
function makeWhereClause()
{
    global $xoopsUser;

    global $xoopsModule;

    global $xoopsDB;

    global $cattree;

    global $myts;

    global $myaddress_catorder;

    global $table_company;

    global $myaddress_mid, $my_uid, $my_groups, $mod_url;

    $uid = $xoopsUser->uid();

    $where = ' WHERE';

    $iamadmin = false;

    if ($xoopsUser && $xoopsUser->isAdmin()) {
        $iamadmin = true;
    }

    if ($iamadmin) {
        $criteria = new CriteriaCompo();

        if (!empty($_POST['user_uname'])) {
            $match = (!empty($_POST['user_uname_match'])) ? (int)$_POST['user_uname_match'] : XOOPS_MATCH_START;

            switch ($match) {
                case XOOPS_MATCH_START:
                    $criteria->add(new Criteria('uname', $myts->addSlashes(trim($_POST['user_uname'])) . '%', 'LIKE'));
                    break;
                case XOOPS_MATCH_END:
                    $criteria->add(new Criteria('uname', '%' . $myts->addSlashes(trim($_POST['user_uname'])), 'LIKE'));
                    break;
                case XOOPS_MATCH_EQUAL:
                    $criteria->add(new Criteria('uname', $myts->addSlashes(trim($_POST['user_uname']))));
                    break;
                case XOOPS_MATCH_CONTAIN:
                    $criteria->add(new Criteria('uname', '%' . $myts->addSlashes(trim($_POST['user_uname'])) . '%', 'LIKE'));
                    break;
            }

            $memberHandler = xoops_getHandler('member');

            $foundusers = $memberHandler->getUsers($criteria, true);

            $where .= ' uid IN (';

            $isPlural = false;

            foreach (array_keys($foundusers) as $j) {
                $userid = $foundusers[$j]->getVar('uid');

                if (!$isPlural) {
                    $where .= (string)$userid;

                    $isPlural = true;
                } else {
                    $where .= ",$userid";
                }
            }

            $where .= ')';
        } else {
            if (!$_POST['useronly']) {
                $where .= " (uid=$uid || disclosed=1)";
            } else {
                $where .= " uid=$uid";
            }
        }
    } else {
        if ($_POST['useronly']) {
            $where .= " uid=$uid";
        } else {
            $where .= " (uid=$uid || disclosed= 1)";
        }
    }

    if (!empty($_POST['relations'])) {
        $relations = trim($_POST['relations']);

        $where .= " AND relations='$relations'";
    }

    if (!empty($_POST['fullname']) && '' != $_POST['fullname']) {
        $match = (!empty($_POST['fullname_match'])) ? (int)$_POST['fullname_match'] : XOOPS_MATCH_START;

        $fullname = trim($_POST['fullname']);

        $where .= ' AND';

        switch ($match) {
            case XOOPS_MATCH_START:
                $where .= " fullname like '" . $fullname . "%'";
                break;
            case XOOPS_MATCH_END:
                $where .= " fullname like '%" . $fullname . "'";
                break;
            case XOOPS_MATCH_EQUAL:
                $where .= " fullname='" . $fullname . "'";
                break;
            case XOOPS_MATCH_CONTAIN:
                $where .= " fullname like '%" . $fullname . "%'";
                break;
        }
    }

    if (!empty($_POST['fullname_jh']) && $_POST['fullname_jh']) {
        $match = (!empty($_POST['fullname_jh_match'])) ? (int)$_POST['fullname_jh_match'] : XOOPS_MATCH_START;

        $where .= ' AND';

        $fullname_jh = trim($_POST['fullname_jh']);

        switch ($match) {
            case XOOPS_MATCH_START:
                $where .= " fullname_jh like '" . $fullname_jh . "%'";
                break;
            case XOOPS_MATCH_END:
                $where .= " fullname_jh like '%" . $fullname_jh . "'";
                break;
            case XOOPS_MATCH_EQUAL:
                $where .= " fullname_jh='" . $fullname_jh . "'";
                break;
            case XOOPS_MATCH_CONTAIN:
                $where .= " fullname_jh like '%" . $fullname_jh . "%'";
                break;
        }
    }

    if (!empty($_POST['comments']) && $_POST['comments']) {
        $match = (!empty($_POST['comments_match'])) ? (int)$_POST['comments_match'] : XOOPS_MATCH_START;

        $where .= ' AND';

        $comments = trim($_POST['comments']);

        switch ($match) {
            case XOOPS_MATCH_START:
                $where .= " mycomments like '" . $comments . "%'";
                break;
            case XOOPS_MATCH_END:
                $where .= " mycomments like '%" . $comments . "'";
                break;
            case XOOPS_MATCH_EQUAL:
                $where .= " mycomments='" . $comments . "'";
                break;
            case XOOPS_MATCH_CONTAIN:
                $where .= " mycomments like '%" . $comments . "%'";
                break;
        }
    }

    if (!empty($_POST['updated']) && is_numeric($_POST['updated'])) {
        $match = (!empty($_POST['updated_match'])) ? trim($_POST['updated_match']) : _MD_MYADDRESS_LARGER;

        $updated = trim($_POST['updated']);

        $date = (!empty($_POST['updated_date'])) ? trim($_POST['updated_date']) : 4;

        $where .= " AND substring(updated, 1, $date)" . $match . mb_substr($updated, 0, $date);
    }

    if (!empty($_POST['cid']) && 0 != $_POST['cid']) {
        $cid = (int)$_POST['cid'];

        $children = $cattree->getAllChildId($cid);

        $children = push_gperm_array($children, 'myadrs_category');

        //		$children = $cattree->getPermittedChildId( $cid, $myaddress_catorder, $children=array(), $perm_name, $my_groups, $myaddress_mid );

        $where .= ' AND cid IN (';

        foreach ($children as $child) {
            $where .= " $child,";
        }

        $where .= " $cid )";
    }

    if (!empty($_POST['cname_jh']) && $_POST['cname_jh']) {
        $match = (!empty($_POST['cname_jh_match'])) ? (int)$_POST['cname_jh_match'] : XOOPS_MATCH_START;

        $cname_jh = trim($_POST['cname_jh']);

        switch ($match) {
            case XOOPS_MATCH_START:
                $whr = " cname_jh like '" . $cname_jh . "%'";
                break;
            case XOOPS_MATCH_END:
                $whr = " cname_jh like '%" . $cname_jh . "'";
                break;
            case XOOPS_MATCH_EQUAL:
                $whr = " cname_jh='" . $cname_jh . "'";
                break;
            case XOOPS_MATCH_CONTAIN:
                $whr = " cname_jh like '%" . $cname_jh . "%'";
                break;
        }

        $rs = $xoopsDB->query("SELECT c_id FROM $table_company WHERE $whr");

        if ($GLOBALS['xoopsDB']->getRowsNum($rs) > 0) {
            $where .= ' AND c_id IN (';

            $isPlural = false;

            while (list($c_id) = $xoopsDB->fetchRow($rs)) {
                if (!$isPlural) {
                    $where .= (string)$c_id;

                    $isPlural = true;
                } else {
                    $where .= ",$c_id";
                }
            }

            $where .= ')';
        }
    }

    return $where;
}

function makeOrderClause()
{
    $validsort = ['cid', 'relations', 'fullname_jh', 'updated'];

    $sort = ' ORDER BY ';

    $sort .= (!in_array($_POST['sort_key'], $validsort, true)) ? ' fullname_jh' : ' ' . $_POST['sort_key'] . ',fullname_jh';

    $order = ' ASC';

    if (isset($_POST['sort_order']) && 'DESC' == $_POST['sort_order']) {
        $order = ' DESC';
    }

    $order = $sort . $order;

    return $order;
}
