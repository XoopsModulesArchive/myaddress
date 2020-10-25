<?php

//-----------------------------------------------------------------//
//              to show the company profile and employee           //
//-----------------------------------------------------------------//

function draw_company($op, $zipconvert)
{
    global $myts, $xoopsDB, $cattree, $xoopsTpl;

    global $table_addressbook, $table_company, $table_cat, $table_zipcode;

    global $myaddress_numperpage, $myaddress_catorder;

    global $isadmin, $myaddress_mid, $my_uid, $my_groups, $mod_url, $mod_copyright;

    global $pop, $mode, $cid, $c_id, $aid, $from, $to;

    // get address by zipcode

    if ('' != $zipconvert) {
        if ('ziptoadrs' == $zipconvert) {
            $zipselkey = isset($_POST['myzipcode']) ? htmlspecialchars($_POST['myzipcode'], ENT_QUOTES | ENT_HTML5) : '';

            $ziparray = zipconvert($zipconvert, $zipselkey);
        } else {
            $zipselkey['address1'] = isset($_POST['myaddress1']) ? htmlspecialchars($_POST['myaddress1'], ENT_QUOTES | ENT_HTML5) : '';

            $zipselkey['address2'] = isset($_POST['myaddress2']) ? htmlspecialchars($_POST['myaddress2'], ENT_QUOTES | ENT_HTML5) : '';

            $ziparray = zipconvert($zipconvert, $zipselkey);
        }
    } else {
        $ziparray = [];
    }

    // hidden items

    $edititems = [
        'mod_url' => $mod_url,
'mod_copyright' => $mod_copyright,
'mode' => $mode,
'cid' => $cid,
'aid' => $aid,
'op' => $op,
'pop' => $pop,
'c_id' => $c_id,
    ];

    $list_requested = 0;

    $editemployee_requested = 0;

    $edititems['list_requested'] = $list_requested;

    $edititems['editemployee_requested'] = $editemployee_requested;

    $edititems['lang_categorypage'] = _MD_MYADDRESS_GOTOCATEGORYPAGE;

    $edititems['lang_companyprofile'] = _MD_MYADDRESS_COMPANYPROFILE;

    $edititems['lang_cat_title'] = _MD_MYADDRESS_CATEGORY;

    $edititems['lang_cname'] = _MD_MYADDRESS_CNAME;

    $edititems['lang_address'] = _MD_MYADDRESS_ADDRESS;

    $edititems['lang_phonefax'] = _MD_MYADDRESS_PHONEFAX;

    $edititems['lang_cphone'] = _MD_MYADDRESS_PHONE;

    $edititems['lang_cfax'] = _MD_MYADDRESS_FAX;

    $edititems['lang_web'] = _MD_MYADDRESS_WEB;

    $edititems['lang_ccomments'] = _MD_MYADDRESS_COMMENTS;

    $edititems['lang_addemployee'] = _MD_MYADDRESS_ADDEMPLOYEE;

    $edititems['lang_delete'] = _MD_MYADDRESS_DELETE;

    $edititems['lang_edit'] = _MD_MYADDRESS_EDIT;

    $edititems['lang_back'] = _MD_MYADDRESS_BACK;

    $sql = "SELECT l.cdivision, l.cid, c.title, l.cname, l.czipcode, l.caddress1, l.caddress2, l.caddress3, l.cphone, l.cfax, l.cweb, l.ccomments FROM $table_company l LEFT JOIN $table_cat c ON l.cid=c.cid WHERE l.c_id=$c_id";

    $result = $xoopsDB->query($sql);

    [$cdivision, $cid, $cat_title, $cname, $czipcode, $caddress1, $caddress2, $caddress3, $cphone, $cfax, $cweb, $ccomments] = $xoopsDB->fetchRow($result);

    $edititems['cat_title'] = htmlspecialchars($cat_title, ENT_QUOTES | ENT_HTML5);

    $edititems['cname'] = htmlspecialchars($cname, ENT_QUOTES | ENT_HTML5);

    $edititems['cdivision'] = htmlspecialchars($cdivision, ENT_QUOTES | ENT_HTML5);

    $edititems['czipcode'] = htmlspecialchars($czipcode, ENT_QUOTES | ENT_HTML5);

    $edititems['caddress1'] = htmlspecialchars($caddress1, ENT_QUOTES | ENT_HTML5);

    $edititems['caddress2'] = htmlspecialchars($caddress2, ENT_QUOTES | ENT_HTML5);

    $edititems['caddress3'] = htmlspecialchars($caddress3, ENT_QUOTES | ENT_HTML5);

    $edititems['cphone'] = htmlspecialchars($cphone, ENT_QUOTES | ENT_HTML5);

    $edititems['cfax'] = htmlspecialchars($cfax, ENT_QUOTES | ENT_HTML5);

    $edititems['cweb'] = "<A HREF='" . $cweb . "' TARGET='_blank'>" . $cweb . '</A>';

    $edititems['ccomments'] = htmlspecialchars($ccomments, ENT_QUOTES | ENT_HTML5);

    if ('list' == $op) {
        //-----------------------------------------------------------------//

        //                     to show employees list                      //

        //-----------------------------------------------------------------//

        $list_requested = 1;

        $edititems['list_requested'] = $list_requested;

        // get the total numbers of employees

        $sql = "SELECT count( aid ) FROM $table_addressbook WHERE c_id=$c_id AND ( uid = $my_uid || disclosed = 1 )";

        $cnt = $xoopsDB->query($sql);

        [$total] = $xoopsDB->fetchRow($cnt);

        $total = (int)$total;

        $edititems['total_found'] = $total;

        // set heading

        $edititems['lang_fullname'] = _MD_MYADDRESS_FULLNAME;

        $edititems['lang_ctitle'] = _MD_MYADDRESS_COTITLE;

        $edititems['lang_cphone'] = _MD_MYADDRESS_CPHONE;

        $edititems['lang_mycellphone'] = _MD_MYADDRESS_CELLPHONE;

        $edititems['lang_myemail'] = _MD_MYADDRESS_EMAIL;

        if ($total > 0) {
            // make a page navigation clause

            if ($total > $myaddress_numperpage) {
                require XOOPS_ROOT_PATH . '/class/pagenav.php';

                $nav = new XoopsPageNav($total, $myaddress_numperpage, $from, 'from', 'mode=' . $mode . '&amp;cid=' . $cid . '&amp;num=' . $myaddress_numperpage . '&amp;op=list&amp;c_id=' . $c_id . "&amp;pop=$pop");

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

            if ($total > $myaddress_numperpage) {
                $edititems['pagenav'] = $navinfo . ' &nbsp; ' . $nav_clause . ' &nbsp';
            } else {
                $edititems['pagenav'] = $navinfo;
            }

            $sql = "SELECT aid, cid, fullname, cdepart, ctitle, cphone, mycellphone1, myemail1, myemail2, uid, disclosed FROM $table_addressbook WHERE c_id=$c_id AND ( uid = $my_uid || disclosed = 1 ) ORDER BY fullname_jh";

            $result = $xoopsDB->query($sql, $myaddress_numperpage, $from);

            // show employees list every numperpage

            $employeedata = [];

            while (list($aid, $cid, $fullname, $cdepart, $ctitle, $cphone, $mycellphone1, $myemail1, $myemail2, $uid, $disclosed) = $xoopsDB->fetchRow($result)) {
                $employeedata['fullname'] = "<A HREF='" . $mod_url . '/viewcompany?mode=' . $mode . '&amp;cid=' . $cid . '&amp;op=renew&amp;pop=' . $pop . '&amp;c_id=' . $c_id . '&amp;aid=' . $aid . "'>" . htmlspecialchars($fullname, ENT_QUOTES | ENT_HTML5) . '</A>';

                $employeedata['cdepart'] = htmlspecialchars($cdepart, ENT_QUOTES | ENT_HTML5);

                $employeedata['ctitle'] = htmlspecialchars($ctitle, ENT_QUOTES | ENT_HTML5);

                $employeedata['cphone'] = htmlspecialchars($cphone, ENT_QUOTES | ENT_HTML5);

                $employeedata['mycellphone1'] = htmlspecialchars($mycellphone1, ENT_QUOTES | ENT_HTML5);

                if ('' != $myemail1) {
                    $employeedata['myemail1'] = "<A HREF='mailto:" . $myemail1 . "'><IMG SRC='" . XOOPS_URL . "/images/icons/email.gif' BORDER='0' ALT='" . sprintf(_SENDEMAILTO, htmlspecialchars($myemail1, ENT_QUOTES | ENT_HTML5)) . "'></A>";

                //					$employeedata['myemail1'] = "<A HREF='mailto:". $myemail1 ."'>".htmlspecialchars($myemail1)."</A>";
                } else {
                    $employeedata['myemail1'] = '';
                }

                if ('' != $myemail2) {
                    $employeedata['myemail2'] = "<A HREF='mailto:" . $myemail2 . "'><IMG SRC='" . XOOPS_URL . "/images/icons/email.gif' BORDER='0' ALT='" . sprintf(_SENDEMAILTO, htmlspecialchars($myemail2, ENT_QUOTES | ENT_HTML5)) . "'></A>";

                //					$employeedata['myemail2'] = "<A HREF='mailto:". $myemail2 ."'>".htmlspecialchars($myemail2)."</A>";
                } else {
                    $employeedata['myemail2'] = '';
                }

                //				$employeedata['adminlink'] = "<A HREF='". $mod_url ."/viewcompany?mode=2&amp;cid=".$cid."&amp;op=renew&amp;pop=".$pop."&amp;c_id=".$c_id."&amp;aid=".$aid."'>". _MD_MYADDRESS_EDIT. "</A> | <A HREF='". $mod_url ."/editcompany?mode=2&amp;cid=".$cid."&amp;op=dele&amp;pop=".$pop."&amp;c_id=".$c_id."&amp;aid=".$aid."'>". _MD_MYADDRESS_DELETE ."</A>";

                $edititems['employeesdata'][] = $employeedata;
            }
        }
    } else {
        if ('new' == $op) {
            //-----------------------------------------------------------------//

            //                  to add a new employee data                     //

            //-----------------------------------------------------------------//

            $edititems['lang_editemployee'] = _MD_MYADDRESS_NEWEMPLOYEE;

            $aid = 0;

            $disclosed = $_POST['disclosed'] ?? 1;

            $relations = $_POST['relations'] ?? 1;

            $first_name = isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name'], ENT_QUOTES | ENT_HTML5) : '';

            $last_name = isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name'], ENT_QUOTES | ENT_HTML5) : '';

            $first_name_jh = isset($_POST['first_name_jh']) ? htmlspecialchars($_POST['first_name_jh'], ENT_QUOTES | ENT_HTML5) : '';

            $last_name_jh = isset($_POST['last_name_jh']) ? htmlspecialchars($_POST['last_name_jh'], ENT_QUOTES | ENT_HTML5) : '';

            $first_name2 = isset($_POST['first_name2']) ? htmlspecialchars($_POST['first_name2'], ENT_QUOTES | ENT_HTML5) : '';

            $myzipcode = isset($_POST['myzipcode']) ? htmlspecialchars($_POST['myzipcode'], ENT_QUOTES | ENT_HTML5) : '';

            $myaddress1 = isset($_POST['myaddress1']) ? htmlspecialchars($_POST['myaddress1'], ENT_QUOTES | ENT_HTML5) : '';

            $myaddress2 = isset($_POST['myaddress2']) ? htmlspecialchars($_POST['myaddress2'], ENT_QUOTES | ENT_HTML5) : '';

            $myaddress3 = isset($_POST['myaddress3']) ? htmlspecialchars($_POST['myaddress3'], ENT_QUOTES | ENT_HTML5) : '';

            $myphone = isset($_POST['myphone']) ? htmlspecialchars($_POST['myphone'], ENT_QUOTES | ENT_HTML5) : '';

            $myfax = isset($_POST['myfax']) ? htmlspecialchars($_POST['myfax'], ENT_QUOTES | ENT_HTML5) : '';

            $mycellphone1 = isset($_POST['mycellphone1']) ? htmlspecialchars($_POST['mycellphone1'], ENT_QUOTES | ENT_HTML5) : '';

            $mycellphone2 = isset($_POST['mycellphone2']) ? htmlspecialchars($_POST['mycellphone2'], ENT_QUOTES | ENT_HTML5) : '';

            $myemail1 = isset($_POST['myemail1']) ? htmlspecialchars($_POST['myemail1'], ENT_QUOTES | ENT_HTML5) : '';

            $myemail2 = isset($_POST['myemail2']) ? htmlspecialchars($_POST['myemail2'], ENT_QUOTES | ENT_HTML5) : '';

            $myemail3 = isset($_POST['myemail3']) ? htmlspecialchars($_POST['myemail3'], ENT_QUOTES | ENT_HTML5) : '';

            $myemail4 = isset($_POST['myemail4']) ? htmlspecialchars($_POST['myemail4'], ENT_QUOTES | ENT_HTML5) : '';

            $myweb = isset($_post['myweb']) ? htmlspecialchars($_POST['myweb'], ENT_QUOTES | ENT_HTML5) : '';

            $mycomments = isset($_POST['mycomments']) ? htmlspecialchars($_POST['mycomments'], ENT_QUOTES | ENT_HTML5) : '';

            $cdepart = isset($_POST['cdepart']) ? htmlspecialchars($_POST['cdepart'], ENT_QUOTES | ENT_HTML5) : '';

            $ctitle = isset($_POST['ctitle']) ? htmlspecialchars($_POST['ctitle'], ENT_QUOTES | ENT_HTML5) : '';

            $cphone = isset($_POST['cphone']) ? htmlspecialchars($_POST['cphone'], ENT_QUOTES | ENT_HTML5) : $cphone;

            $cfax = isset($_POST['cfax']) ? htmlspecialchars($_POST['cfax'], ENT_QUOTES | ENT_HTML5) : $cfax;

            $cweb = isset($_post['cweb']) ? htmlspecialchars($_POST['cweb'], ENT_QUOTES | ENT_HTML5) : $cweb;

            $updated = '';
        } else {
            //-----------------------------------------------------------------//

            //                  to update an employee data                     //

            //-----------------------------------------------------------------//

            $edititems['lang_editemployee'] = _MD_MYADDRESS_EDITEMPLOYEE;

            if ('' == $zipconvert) {
                [
                    $aid,
                    $cid,
                    $disclosed,
                    $relations,
                    $first_name,
                    $last_name,
                    $first_name_jh,
                    $last_name_jh,
                    $first_name2,
                    $myzipcode,
                    $myaddress1,
                    $myaddress2,
                    $myaddress3,
                    $myphone,
                    $mycellphone1,
                    $mycellphone2,
                    $myfax,
                    $myemail1,
                    $myemail2,
                    $myemail3,
                    $myemail4,
                    $myweb,
                    $mycomments,
                    $c_id,
                    $cname,
                    $cdivision,
                    $cdepart,
                    $ctitle,
                    $cphone,
                    $cfax,
                    $ccphone,
                    $ccfax,
                    $updated,
                    $uid
                ] = get_myaddress($aid);

                // get cphone & cfax if mycfax or mycphone are blank

                $cphone = ('' == $cphone) ? $ccphone : $cphone;

                $cfax = ('' == $cfax) ? $ccfax : $cfax;
            } else {
                $aid = $_POST['aid'];

                $disclosed = $_POST['disclosed'];

                $uid = $_POST['uid'];

                $relations = $_POST['relations'];

                $first_name = htmlspecialchars($_POST['first_name'], ENT_QUOTES | ENT_HTML5);

                $last_name = htmlspecialchars($_POST['last_name'], ENT_QUOTES | ENT_HTML5);

                $first_name_jh = htmlspecialchars($_POST['first_name_jh'], ENT_QUOTES | ENT_HTML5);

                $last_name_jh = htmlspecialchars($_POST['last_name_jh'], ENT_QUOTES | ENT_HTML5);

                $first_name2 = htmlspecialchars($_POST['first_name2'], ENT_QUOTES | ENT_HTML5);

                $myzipcode = htmlspecialchars($_POST['myzipcode'], ENT_QUOTES | ENT_HTML5);

                $myaddress1 = htmlspecialchars($_POST['myaddress1'], ENT_QUOTES | ENT_HTML5);

                $myaddress2 = htmlspecialchars($_POST['myaddress2'], ENT_QUOTES | ENT_HTML5);

                $myaddress3 = htmlspecialchars($_POST['myaddress3'], ENT_QUOTES | ENT_HTML5);

                $myphone = htmlspecialchars($_POST['myphone'], ENT_QUOTES | ENT_HTML5);

                $myfax = htmlspecialchars($_POST['myfax'], ENT_QUOTES | ENT_HTML5);

                $mycellphone1 = htmlspecialchars($_POST['mycellphone1'], ENT_QUOTES | ENT_HTML5);

                $mycellphone2 = htmlspecialchars($_POST['mycellphone2'], ENT_QUOTES | ENT_HTML5);

                $myemail1 = htmlspecialchars($_POST['myemail1'], ENT_QUOTES | ENT_HTML5);

                $myemail2 = htmlspecialchars($_POST['myemail2'], ENT_QUOTES | ENT_HTML5);

                $myemail3 = htmlspecialchars($_POST['myemail3'], ENT_QUOTES | ENT_HTML5);

                $myemail4 = htmlspecialchars($_POST['myemail4'], ENT_QUOTES | ENT_HTML5);

                $myweb = htmlspecialchars($_POST['myweb'], ENT_QUOTES | ENT_HTML5);

                $mycomments = htmlspecialchars($_POST['mycomments'], ENT_QUOTES | ENT_HTML5);

                $cname = htmlspecialchars($_POST['cname'], ENT_QUOTES | ENT_HTML5);

                $cdivision = htmlspecialchars($_POST['cdivision'], ENT_QUOTES | ENT_HTML5);

                $cdepart = htmlspecialchars($_POST['cdepart'], ENT_QUOTES | ENT_HTML5);

                $ctitle = htmlspecialchars($_POST['ctitle'], ENT_QUOTES | ENT_HTML5);

                $cphone = htmlspecialchars($_POST['cphone'], ENT_QUOTES | ENT_HTML5);

                $cfax = htmlspecialchars($_POST['cfax'], ENT_QUOTES | ENT_HTML5);

                $updated = $_POST['updated'];
            }
        }

        $editemployee_requested = 1;

        $edititems['editemployee_requested'] = $editemployee_requested;

        $edititems['can_edit_privatedata'] = can_edit_privatedata($uid);

        $edititems['aid'] = $aid;

        $edititems['lang_myaddresscat'] = _MD_MYADDRESS_MYADDRESSCAT;

        $tree = $cattree->getChildTreeArray(0, $myaddress_catorder);

        $tree = push_gperm_array($tree, 'myadrs_category');

        $cidselbox = [];

        foreach ($tree as $leaf) {
            $leaf['prefix'] = mb_substr($leaf['prefix'], 0, -1);

            $leaf['prefix'] = str_replace('.', '--', $leaf['prefix']);

            $cidselbox['label'] = $leaf['prefix'] . $leaf['title'];

            $cidselbox['value'] = $leaf['cid'];

            $edititems['cidselboxdata'][] = $cidselbox;
        }

        $edititems['lang_disclosed'] = _MD_MYADDRESS_DISCLOSED;

        $edititems['disclosed'] = $disclosed;

        $edititems['lang_yes'] = _MD_MYADDRESS_YES;

        $edititems['lang_no'] = _MD_MYADDRESS_NO;

        $edititems['lang_relations'] = _MD_MYADDRESS_RELATIONS;

        $edititems['relations'] = htmlspecialchars($relations, ENT_QUOTES | ENT_HTML5);

        $edititems['clsselboxdata'] = getSelBoxRelations();

        $edititems['lang_fullname'] = _MD_MYADDRESS_FULLNAME;

        $edititems['lang_firstname'] = _MD_MYADDRESS_FIRSTNAME;

        $edititems['lang_first_name2'] = _MD_MYADDRESS_FIRSTNAME2;

        $edititems['lang_lastname'] = _MD_MYADDRESS_LASTNAME;

        $edititems['lang_fullnamejh'] = _MD_MYADDRESS_FULLNAME_JH;

        $edititems['lang_must_jh'] = _MD_MYADDRESS_MUST_JH;

        $edititems['last_name'] = $last_name;

        $edititems['first_name'] = $first_name;

        $edititems['last_name_jh'] = $last_name_jh;

        $edititems['first_name_jh'] = $first_name_jh;

        $edititems['first_name2'] = $first_name2;

        //------------------------------------------------------------------//

        //       	 search results of zipcode                            //

        //------------------------------------------------------------------//

        $edititems['zipsearchresult'] = '';

        if ('' != $zipconvert) {
            if (!isset($ziparray) || 0 == count($ziparray)) {
                $edititems['zipsearchresult'] = "&nbsp;<B><FONT COLOR='red'>[" . _MD_MYADDRESS_NOFOUND . ']</FONT></B>';
            } elseif (count($ziparray) < 2) {
                $myzipcode = mb_substr($ziparray[0]['zipcode'], 0, 3) . '-' . mb_substr($ziparray[0]['zipcode'], 3, 4);

                $myaddress1 = trim($ziparray[0]['pref']);

                $myaddress2 = trim($ziparray[0]['address2']);
            } else {
                $searchmore = sprintf(_MD_MYADDRESS_SEARCHMORE, count($ziparray));

                if ('adrstozip' == $zipconvert) {
                    // bug fixed at ver.1.1.3

                    if (!isset($_SESSION)) {
                        $_SESSION = [];
                    }

                    $_SESSION['adrs1'] = $myaddress1;

                    $_SESSION['adrs2'] = $myaddress2;

                    //					$edititems['zipsearchresult'] = "&nbsp;<B>[<A HREF=\"javascript:openWithSelfMain('searchmore.php?form=address&amp;adrs1=".$myaddress1."&amp;adrs2=".$myaddress2."','searchmore',400,400)\">".$searchmore."</A>]</B>";

                    $edititems['zipsearchresult'] = "&nbsp;<B>[<A HREF=\"javascript:openWithSelfMain('searchmore.php?form=address','searchmore',400,400)\">" . $searchmore . '</A>]</B>';
                } else {
                    $edititems['zipsearchresult'] = "&nbsp;<B>[<A HREF=\"javascript:openWithSelfMain('searchmore.php?form=address&amp;zip=" . $myzipcode . "','searchmore',400,400);\">" . $searchmore . '</A>]</B>';
                }
            }
        }

        $edititems['lang_zipcode'] = _MD_MYADDRESS_ZIPCODE;

        $edititems['myzipcode'] = $myzipcode;

        $edititems['lang_searchzip'] = _MD_MYADDRESS_SEARCHZIP;

        $edititems['lang_searchadrs'] = _MD_MYADDRESS_SEARCHADRS;

        $edititems['lang_address1'] = _MD_MYADDRESS_ADDRESS1;

        $edititems['myaddress1'] = $myaddress1;

        $edititems['lang_address2'] = _MD_MYADDRESS_ADDRESS2;

        $edititems['myaddress2'] = $myaddress2;

        $edititems['lang_address3'] = _MD_MYADDRESS_ADDRESS3;

        $edititems['myaddress3'] = $myaddress3;

        $edititems['lang_phonefax'] = _MD_MYADDRESS_PHONEFAX;

        $edititems['lang_phone'] = _MD_MYADDRESS_PHONE;

        $edititems['lang_fax'] = _MD_MYADDRESS_FAX;

        $edititems['myphone'] = $myphone;

        $edititems['myfax'] = $myfax;

        $edititems['lang_mycellphone'] = _MD_MYADDRESS_CELLPHONE;

        $edititems['mycellphone1'] = $mycellphone1;

        $edititems['mycellphone2'] = $mycellphone2;

        $edititems['lang_myemail'] = _MD_MYADDRESS_EMAIL;

        $edititems['myemail1'] = $myemail1;

        $edititems['myemail2'] = $myemail2;

        $edititems['myemail3'] = $myemail3;

        $edititems['myemail4'] = $myemail4;

        $edititems['lang_myweb'] = _MD_MYADDRESS_WEB;

        $edititems['myweb'] = $myweb;

        $edititems['lang_mycomments'] = _MD_MYADDRESS_COMMENTS;

        $edititems['mycomments'] = $mycomments;

        $edititems['lang_co_name'] = _MD_MYADDRESS_COMPANY;

        $edititems['lang_change_co'] = _MD_MYADDRESS_CHANGE_CO;

        $edititems['cname'] = $cname;

        $edititems['cdivision'] = $cdivision;

        $edititems['company'] = $cname . ' ' . $cdivision;

        $edititems['select_co'] = 'setcompany.php?cid=' . $cid . '&amp;op=list&amp;c_id=' . $c_id;

        $edititems['searchco'] = 'searchco';

        $edititems['lang_cdepart'] = _MD_MYADDRESS_DEPART;

        $edititems['cdepart'] = $cdepart;

        $edititems['lang_ctitle'] = _MD_MYADDRESS_COTITLE;

        $edititems['ctitle'] = $ctitle;

        $edititems['cphone'] = $cphone;

        $edititems['cfax'] = $cfax;

        $edititems['lang_updated'] = _MD_MYADDRESS_UPDATED;

        // escape all zero data

        if ('00000000000000' == $updated || '' == $updated) {
            $edititems['rend_updated'] = '';
        } else {
            $rend_updated = convert_timestamp($updated);

            $edititems['rend_updated'] = date('Y/m/d H:i:s', $rend_updated);
        }

        $edititems['updated'] = $updated;

        $edititems['lang_update'] = _MD_MYADDRESS_UPDATE;

        $edititems['lang_delete'] = _MD_MYADDRESS_DELETE;

        $edititems['lang_add'] = _MD_MYADDRESS_NEW;

        $edititems['lang_back'] = _MD_MYADDRESS_BACK;
    }

    return $edititems;
}
