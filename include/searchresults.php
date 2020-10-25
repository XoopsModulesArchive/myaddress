<?php

// ------------------------------------------------------------------------- //
//                      MyAddress - XOOPS addressbook                        //
//                        <http://forum.kuri3.net>                          //
// ------------------------------------------------------------------------- //

//-------------------------------------------------------------------//
//               Display search results in list style                //
//-------------------------------------------------------------------//

function search_results($mode, $cid, $skey, $c_id = 0, $needSearch = 'y')
{
    global $myts, $xoopsDB, $cattree, $xoopsTpl;

    global $table_addressbook, $table_company;

    global $myaddress_numperpage, $myaddress_catorder;

    global $isadmin, $myaddress_mid, $my_uid, $my_groups, $mod_url;

    global $op, $from;

    $children = $cattree->getAllChildId($cid);

    $children = push_gperm_array($children, 'myadrs_category');

    $keyarray = get_keyarray();

    $skey_last = count($keyarray) - 2;

    $skey_all = count($keyarray) - 1;

    $requestStart = true;

    $requestEnd = true;

    if (0 == $skey) {
        $requestStart = false;
    } elseif ($skey == $skey_last) {
        $requestEnd = false;
    } elseif ($skey == $skey_all) {
        $requestStart = false;

        $requestEnd = false;
    }

    switch ($mode) {
        // name-based mode
        case 1:
            $count = "SELECT count(l.aid) FROM $table_addressbook l";
            $select = "SELECT l.aid, l.cid, l.c_id, l.fullname, l.myphone, l.mycellphone1, l.myemail1, l.myemail2, l.cphone, l.uid, c.cid, c.cname, c.cdivision, c.cphone FROM $table_addressbook l LEFT JOIN $table_company c ON l.c_id=c.c_id ";
            $where = ' WHERE';
            if ($requestStart) {
                $where .= " substring(l.last_name_jh, 1, 2) >= '$keyarray[$skey]' AND";
            }
            if ($requestEnd) {
                $skey2 = $skey + 1;

                $where .= " substring(l.last_name_jh, 1, 2) < '$keyarray[$skey2]' AND";
            }
            $where .= " ( l.uid = $my_uid || l.disclosed = 1 )";
            $where .= ' AND l.cid IN (';
            foreach ($children as $child) {
                $where .= " $child,";
            }
            $where .= " $cid )";
            $order = ' ORDER BY l.fullname_jh';
            break;
        // company-based mode
        case 2:
            $count = "SELECT count(c_id) FROM $table_company";
            $select = "SELECT c_id, cid, cdivision, cname, cphone, cfax, cweb  FROM $table_company";
            $where = ' WHERE';
            if ('list' == $op && $needSearch = 'n') {
                $where .= " c_id=$c_id";
            } else {
                if ($requestStart) {
                    $where .= " substring(cname_jh, 1, 2) >= '$keyarray[$skey]' AND";
                }

                if ($requestEnd) {
                    $skey2 = $skey + 1;

                    $where .= " substring(cname_jh, 1, 2) < '$keyarray[$skey2]' AND";
                }

                //				$where .=" uid = $my_uid";

                $where .= ' cid IN (';

                foreach ($children as $child) {
                    $where .= " $child,";
                }

                $where .= " $cid )";
            }
            $order = ' ORDER BY cname_jh';
            break;
        default:
            break;
    }

    // get the total numbers of affected addresses

    $sql = $count . $where;

    $res_total = $xoopsDB->query($sql);

    [$total] = $xoopsDB->fetchRow($res_total);

    $total = (int)$total;

    $addresses = [
        'lang_search' => _MD_MYADDRESS_SEARCH,
'lang_results' => _MD_MYADDRESS_RESULTS,
'total_found' => $total,
    ];

    if (0 == $total) {
        $addresses['lang_nonefound'] = _MD_MYADDRESS_NOFOUND;
    } else {
        // make a page navigation clause

        if ($total > $myaddress_numperpage) {
            require XOOPS_ROOT_PATH . '/class/pagenav.php';

            $nav = new XoopsPageNav($total, $myaddress_numperpage, $from, 'from', "mode=$mode&cid=$cid&op=search&skey=$skey");

            $nav_clause = $nav->renderNav(10);
        } else {
            $nav_clause = '';
        }

        // display the information of the present page

        $to = $from + $myaddress_numperpage;

        if ($to > $total) {
            $to = $total;
        }

        $navinfo = sprintf(_MD_MYADDRESS_NAVINFO, $from + 1, $to, $total);

        $addresses['pagenav'] = $navinfo . '&nbsp;' . $nav_clause . '&nbsp';

        switch ($mode) {
            // name-based mode
            case 1:
                // get addressbook
                $sql = $select . $where . $order;
                $result = $xoopsDB->query($sql, $myaddress_numperpage, $from);
                // assign header
                $addresses['lang_fullname'] = _MD_MYADDRESS_FULLNAME;
                $addresses['lang_myphone'] = _MD_MYADDRESS_PHONE;
                $addresses['lang_mycellphone1'] = _MD_MYADDRESS_CELLPHONE;
                $addresses['lang_myemail1'] = _MD_MYADDRESS_EMAIL1;
                $addresses['lang_myemail2'] = _MD_MYADDRESS_EMAIL2;
                $addresses['lang_cname'] = _MD_MYADDRESS_CNAME;
                $addresses['lang_cphone'] = _MD_MYADDRESS_CPHONE;

                // assign addressbook data to template
                while (list($aid, $acid, $c_id, $fullname, $myphone, $mycellphone1, $myemail1, $myemail2, $mycphone, $uid, $ccid, $cname, $cdivision, $ccphone) = $xoopsDB->fetchRow($result)) {
                    $myaddressdata = [];

                    if ('' != $fullname) {
                        $myaddressdata['fullname'] = "<A href='" . $mod_url . '/viewaddress.php?mode=' . $mode . '&amp;cid=' . $acid . '&amp;op=edit&amp;aid=' . $aid . "'>" . htmlspecialchars($fullname, ENT_QUOTES | ENT_HTML5) . '<A>';
                    } else {
                        $myaddressdata['fullname'] = 'Error: no fullname';
                    }

                    if ('' != $cname) {
                        $myaddressdata['cname'] = "<A href='" . $mod_url . '/viewcompany.php?mode=' . $mode . '&amp;cid=' . $ccid . '&amp;op=list&amp;c_id=' . $c_id . "'>" . htmlspecialchars($cname, ENT_QUOTES | ENT_HTML5) . ' ' . $cdivision . '</A>';
                    } else {
                        $myaddressdata['cname'] = '';
                    }

                    if ('' == $mycphone) {
                        $mycphone = $ccphone;
                    }

                    $myaddressdata['cphone'] = htmlspecialchars($mycphone, ENT_QUOTES | ENT_HTML5);

                    $myaddressdata['c_id'] = htmlspecialchars($c_id, ENT_QUOTES | ENT_HTML5);

                    $myaddressdata['acid'] = htmlspecialchars($acid, ENT_QUOTES | ENT_HTML5);

                    $myaddressdata['ccid'] = htmlspecialchars($ccid, ENT_QUOTES | ENT_HTML5);

                    $myaddressdata['myphone'] = htmlspecialchars($myphone, ENT_QUOTES | ENT_HTML5);

                    $myaddressdata['mycellphone1'] = htmlspecialchars($mycellphone1, ENT_QUOTES | ENT_HTML5);

                    if ('' != $myemail1) {
                        $myaddressdata['myemail1'] = "<A HREF='mailto:" . htmlspecialchars($myemail1, ENT_QUOTES | ENT_HTML5) . "'><IMG SRC='" . XOOPS_URL . "/images/icons/email.gif' BORDER='0' ALT='" . sprintf(_SENDEMAILTO, htmlspecialchars($myemail1, ENT_QUOTES | ENT_HTML5)) . "'></A>";
                    } else {
                        $myaddressdata['myemail1'] = '';
                    }

                    if ('' != $myemail2) {
                        $myaddressdata['myemail2'] = "<A HREF='mailto:" . htmlspecialchars($myemail2, ENT_QUOTES | ENT_HTML5) . "'><IMG SRC='" . XOOPS_URL . "/images/icons/email.gif' BORDER='0' ALT='" . sprintf(_SENDEMAILTO, htmlspecialchars($myemail2, ENT_QUOTES | ENT_HTML5)) . "'></A>";
                    } else {
                        $myaddressdata['myemail2'] = '';
                    }

                    if ($isadmin || $my_uid == $uid) {
                        $myaddressdata['adminlink'] = "<A HREF='" . $mod_url . "/viewaddress.php?cid=$cid&op=edit&aid=" . $aid . "'>" . _MD_MYADDRESS_EDIT . "</A> | <A HREF='" . $mod_url . "/viewaddress.php?cid=$cid&op=del&aid=" . $aid . "'>" . _MD_MYADDRESS_DELETE . '</A>';
                    } else {
                        $myaddressdata['adminlink'] = '';
                    }

                    $addresses['myaddress'][] = $myaddressdata;
                }
                break;
            // company-based mode
            case 2:
                // get a company profile
                $sql = $select . $where . $order;

                $result = $xoopsDB->query($sql, $myaddress_numperpage, $from);
                // assign header
                $addresses['lang_cname'] = _MD_MYADDRESS_COMPANY;
                $addresses['lang_cdivision'] = _MD_MYADDRESS_DIVISION;
                $addresses['lang_cphone'] = _MD_MYADDRESS_PHONE;
                $addresses['lang_cfax'] = _MD_MYADDRESS_FAX;
                $addresses['lang_cweb'] = _MD_MYADDRESS_WEB;
                $addresses['lang_send'] = _SEND;

                // assign a company profile data to the template
                while (list($c_id, $ccid, $cdivision, $cname, $cphone, $cfax, $cweb) = $xoopsDB->fetchRow($result)) {
                    //					if( $caller == "viewcat.php" ) {

                    //						$companyprofile['cname'] = "<A href='".$mod_url."/viewcompany.php?mode=".$mode."&amp;cid=".$ccid."&amp;op=list&amp;c_id=".$c_id."'>".htmlspecialchars($cname)."</A>";

                    //						$companyprofile['cdivision'] = "<A href='".$mod_url."/viewcompany.php?mode=".$mode."&amp;cid=".$ccid."&amp;op=list&amp;c_id=".$c_id."'>".htmlspecialchars($cdivision)."</A>";

                    //					} else {

                    $companyprofile['cname'] = htmlspecialchars($cname, ENT_QUOTES | ENT_HTML5);

                    $companyprofile['cdivision'] = htmlspecialchars($cdivision, ENT_QUOTES | ENT_HTML5);

                    //					}

                    $companyprofile['c_id'] = htmlspecialchars($c_id, ENT_QUOTES | ENT_HTML5);

                    $companyprofile['company'] = htmlspecialchars($cname, ENT_QUOTES | ENT_HTML5) . ' ' . htmlspecialchars($cdivision, ENT_QUOTES | ENT_HTML5);

                    $companyprofile['cphone'] = htmlspecialchars($cphone, ENT_QUOTES | ENT_HTML5);

                    $companyprofile['cfax'] = htmlspecialchars($cfax, ENT_QUOTES | ENT_HTML5);

                    $companyprofile['ccid'] = htmlspecialchars($ccid, ENT_QUOTES | ENT_HTML5);

                    if ('' != $cweb && 'http://' != trim($cweb)) {
                        $companyprofile['alt_msg'] = sprintf(_VISITWEBSITE, htmlspecialchars($cweb, ENT_QUOTES | ENT_HTML5));

                        $companyprofile['cweb'] = htmlspecialchars($cweb, ENT_QUOTES | ENT_HTML5);

                    //						$companyprofile['cweb'] = "<A HREF='".htmlspecialchars($cweb)."' TARGET='_blank'><IMG SRC='".XOOPS_URL."/images/icons/www.gif' BORDER='0' ALT='".sprintf( _VISITWEBSITE, htmlspecialchars($cweb) )."'></A>";
                    } else {
                        $companyprofile['cweb'] = '';
                    }

                    //					if( $uid == $my_uid || $isadmin ) {

                    $companyprofile['lang_edit'] = _MD_MYADDRESS_EDIT;

                    $companyprofile['lang_delete'] = _MD_MYADDRESS_DELETE;

                    //					$companyprofile['adminlink'] = "<A HREF='". $mod_url ."/viewcompany.php?cid=$cid&op=edit&c_id=". $c_id ."'>". _MD_MYADDRESS_EDIT. "</A> | <A HREF='". $mod_url ."/viewcompany.php?cid=$cid&action=del&c_id=". $c_id ."'>". _MD_MYADDRESS_DELETE ."</A>";

                    //					}

                    $addresses['mycompany'][] = $companyprofile;
                }
                break;
            default:
                echo 'mode is broken!';
                break;
        }
    }

    return $addresses;
}
