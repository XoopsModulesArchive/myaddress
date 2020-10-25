<?php

// ------------------------------------------------------------------------- //
//                      MyAddress - XOOPS addressbook                        //
//                        <http://forum.kuri3.net>                          //
// ------------------------------------------------------------------------- //
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
//-------------------------------------------------------------------//
//           making form for add/update/delete myaddressbook         //
//-------------------------------------------------------------------//

function edit_address($op, $zipconvert)
{
    global $myts, $xoopsDB, $cattree, $xoopsTpl;

    global $table_addressbook, $table_company;

    global $table_cat, $table_zipcode, $table_relations;

    global $myaddress_numperpage, $myaddress_catorder;

    global $isadmin, $myaddress_mid, $my_uid, $my_groups, $mod_url;

    global $mode, $cid, $aid;

    //-----------------------------------------------------------------//

    //                     get address by zipcode                      //

    //-----------------------------------------------------------------//

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

    switch ($op) {
        //-----------------------------------------------------------------//
        //                     for add a new myaddress                     //
        //-----------------------------------------------------------------//
        case 'add':
            $heading = _MD_MYADDRESS_ADDMYADDRESS;

            $aid = 0;
            $disclosed = $_POST['disclosed'] ?? 1;
            $relations = isset($_POST['relations']) ? trim($_POST['relations']) : '';
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
            if ('http://' == $myweb) {
                $myweb = '';
            }
            $mycomments = isset($_POST['mycomments']) ? htmlspecialchars($_POST['mycomments'], ENT_QUOTES | ENT_HTML5) : '';
            $c_id = 0;
            $cname = isset($_POST['cname']) ? htmlspecialchars($_POST['cname'], ENT_QUOTES | ENT_HTML5) : '';
            $cdivision = isset($_POST['cdivision']) ? htmlspecialchars($_POST['cdivision'], ENT_QUOTES | ENT_HTML5) : '';
            $company = isset($_POST['company']) ? htmlspecialchars($_POST['company'], ENT_QUOTES | ENT_HTML5) : '';
            $cdepart = isset($_POST['cdepart']) ? htmlspecialchars($_POST['cdepart'], ENT_QUOTES | ENT_HTML5) : '';
            $ctitle = isset($_POST['ctitle']) ? htmlspecialchars($_POST['ctitle'], ENT_QUOTES | ENT_HTML5) : '';
            $cphone = isset($_POST['cphone']) ? htmlspecialchars($_POST['cphone'], ENT_QUOTES | ENT_HTML5) : '';
            $cfax = isset($_POST['cfax']) ? htmlspecialchars($_POST['cfax'], ENT_QUOTES | ENT_HTML5) : '';
            $ccphone = '';
            $ccfax = '';
            $updated = '';
            break;
        //-----------------------------------------------------------------//
        //                     for update myaddress                        //
        //-----------------------------------------------------------------//
        case 'edit':
            $heading = _MD_MYADDRESS_EDITMYADDRESS;
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

                $cphone = ('' == $cphone) ? $ccphone : $cphone;

                $cfax = ('' == $cfax) ? $ccfax : $cfax;

                // 04.06.16 added for text sanitalization

                $aid = (int)$aid;

                $cid = (int)$cid;

                $disclosed = (int)$disclosed;

                $relations = htmlspecialchars($relations, ENT_QUOTES | ENT_HTML5);

                $first_name = htmlspecialchars($first_name, ENT_QUOTES | ENT_HTML5);

                $last_name = htmlspecialchars($last_name, ENT_QUOTES | ENT_HTML5);

                $first_name_jh = htmlspecialchars($first_name_jh, ENT_QUOTES | ENT_HTML5);

                $last_name_jh = htmlspecialchars($last_name_jh, ENT_QUOTES | ENT_HTML5);

                $first_name2 = htmlspecialchars($first_name2, ENT_QUOTES | ENT_HTML5);

                $myzipcode = htmlspecialchars($myzipcode, ENT_QUOTES | ENT_HTML5);

                $myaddress1 = htmlspecialchars($myaddress1, ENT_QUOTES | ENT_HTML5);

                $myaddress2 = htmlspecialchars($myaddress2, ENT_QUOTES | ENT_HTML5);

                $myaddress3 = htmlspecialchars($myaddress3, ENT_QUOTES | ENT_HTML5);

                $myphone = htmlspecialchars($myphone, ENT_QUOTES | ENT_HTML5);

                $myfax = htmlspecialchars($myfax, ENT_QUOTES | ENT_HTML5);

                $mycellphone1 = htmlspecialchars($mycellphone1, ENT_QUOTES | ENT_HTML5);

                $mycellphone2 = htmlspecialchars($mycellphone2, ENT_QUOTES | ENT_HTML5);

                $myemail1 = htmlspecialchars($myemail1, ENT_QUOTES | ENT_HTML5);

                $myemail2 = htmlspecialchars($myemail2, ENT_QUOTES | ENT_HTML5);

                $myemail3 = htmlspecialchars($myemail3, ENT_QUOTES | ENT_HTML5);

                $myemail4 = htmlspecialchars($myemail4, ENT_QUOTES | ENT_HTML5);

                $myweb = htmlspecialchars($myweb, ENT_QUOTES | ENT_HTML5);

                $mycomments = htmlspecialchars($mycomments, ENT_QUOTES | ENT_HTML5);

                $c_id = (int)$c_id;

                $cname = htmlspecialchars($cname, ENT_QUOTES | ENT_HTML5);

                $cdivision = htmlspecialchars($cdivision, ENT_QUOTES | ENT_HTML5);

                $cdepart = htmlspecialchars($cdepart, ENT_QUOTES | ENT_HTML5);

                $ctitle = htmlspecialchars($ctitle, ENT_QUOTES | ENT_HTML5);

                $cphone = htmlspecialchars($cphone, ENT_QUOTES | ENT_HTML5);

                $cfax = htmlspecialchars($cfax, ENT_QUOTES | ENT_HTML5);
            } else {
                $disclosed = (int)$_POST['disclosed'];

                $relations = htmlspecialchars($_POST['relations'], ENT_QUOTES | ENT_HTML5);

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

                if ('http://' == $myweb) {
                    $myweb = '';
                }

                $mycomments = htmlspecialchars($_POST['mycomments'], ENT_QUOTES | ENT_HTML5);

                $c_id = (int)$_POST['c_id'];

                $cname = htmlspecialchars($_POST['cname'], ENT_QUOTES | ENT_HTML5);

                $cdivision = htmlspecialchars($_POST['cdivision'], ENT_QUOTES | ENT_HTML5);

                $cdepart = htmlspecialchars($_POST['cdepart'], ENT_QUOTES | ENT_HTML5);

                $ctitle = htmlspecialchars($_POST['ctitle'], ENT_QUOTES | ENT_HTML5);

                $cphone = htmlspecialchars($_POST['cphone'], ENT_QUOTES | ENT_HTML5);

                $cfax = htmlspecialchars($_POST['cfax'], ENT_QUOTES | ENT_HTML5);

                $updated = $_POST['updated'];
            }

            break;
        /*-----------------------------------------------------------------*/ default:
        break;
    }

    //-----------------------------------------------------------------//

    //              assign to the template                             //

    //-----------------------------------------------------------------//

    // some hidden items

    $addresses['mode'] = $mode;

    $addresses['cid'] = $cid;

    $addresses['op'] = $op;

    $addresses['aid'] = $aid;

    $addresses['c_id'] = $c_id;

    $addresses['lang_categorypage'] = "<A HREF='viewcat.php?mode=" . $mode . '&cid=' . $cid . "'>" . _MD_MYADDRESS_SEARCH . '</A>';

    $addresses['lang_editmyaddress'] = $heading;

    $addresses['lang_myaddresscat'] = _MD_MYADDRESS_MYADDRESSCAT;

    $tree = $cattree->getChildTreeArray(0, $myaddress_catorder);

    $tree = push_gperm_array($tree, 'myadrs_category');

    foreach ($tree as $leaf) {
        $leaf['prefix'] = mb_substr($leaf['prefix'], 0, -1);

        $leaf['prefix'] = str_replace('.', '--', $leaf['prefix']);

        $cidselbox['label'] = $leaf['prefix'] . $leaf['title'];

        $cidselbox['value'] = $leaf['cid'];

        $cidselboxdata[] = $cidselbox;
    }

    $addresses['cidselboxdata'] = $cidselboxdata;

    $addresses['lang_disclosed'] = _MD_MYADDRESS_DISCLOSED;

    $addresses['disclosed'] = $disclosed;

    $addresses['lang_yes'] = _MD_MYADDRESS_YES;

    $addresses['lang_no'] = _MD_MYADDRESS_NO;

    // get relation selbox by gid

    $addresses['lang_relations'] = _MD_MYADDRESS_RELATIONS;

    $addresses['relations'] = $relations;

    $addresses['clsselboxdata'] = getSelBoxRelations();

    $addresses['lang_fullname'] = _MD_MYADDRESS_FULLNAME;

    $addresses['lang_firstname'] = _MD_MYADDRESS_FIRSTNAME;

    $addresses['lang_first_name2'] = _MD_MYADDRESS_FIRSTNAME2;

    $addresses['lang_lastname'] = _MD_MYADDRESS_LASTNAME;

    $addresses['lang_fullname_jh'] = _MD_MYADDRESS_FULLNAME_JH;

    $addresses['lang_must_jh'] = _MD_MYADDRESS_MUST_JH;

    $addresses['last_name'] = $last_name;

    $addresses['first_name'] = $first_name;

    $addresses['last_name_jh'] = $last_name_jh;

    $addresses['first_name_jh'] = $first_name_jh;

    $addresses['first_name2'] = $first_name2;

    //------------------------------------------------------------------//

    //       	 search results of zipcode                            //

    //------------------------------------------------------------------//

    $addresses['zipsearchresult'] = '';

    if ('' != $zipconvert) {
        if (!isset($ziparray) || 0 == count($ziparray)) {
            $addresses['zipsearchresult'] = "&nbsp;<B><FONT COLOR='red'>[" . _MD_MYADDRESS_NOFOUND . ']</FONT></B>';
        } elseif (count($ziparray) < 2) {
            $myzipcode = mb_substr($ziparray[0]['zipcode'], 0, 3) . '-' . mb_substr($ziparray[0]['zipcode'], 3, 4);

            $myaddress1 = trim($ziparray[0]['pref']);

            $myaddress2 = trim($ziparray[0]['address2']);
        } else {
            if ('adrstozip' == $zipconvert) {
                if (!isset($_SESSION)) {
                    $_SESSION = [];
                }

                $_SESSION['adrs1'] = $myaddress1;

                $_SESSION['adrs2'] = $myaddress2;

                $addresses['zipsearchresult'] = "&nbsp;<B>[<A HREF='javascript:openWithSelfMain(\"searchmore.php?form=address\",\"searchmore\",400,400);'>" . sprintf(_MD_MYADDRESS_SEARCHMORE, count($ziparray)) . '</A>]</B>';
            } else {
                $addresses['zipsearchresult'] = "&nbsp;<B>[<A HREF=\"javascript:openWithSelfMain('searchmore.php?form=address&amp;zip=" . $myzipcode . "','searchmore',400,400);\">" . sprintf(_MD_MYADDRESS_SEARCHMORE, count($ziparray)) . '</A>]</B>';
            }
        }
    }

    $addresses['lang_zipcode'] = _MD_MYADDRESS_ZIPCODE;

    $addresses['myzipcode'] = $myzipcode;

    $addresses['lang_searchzip'] = _MD_MYADDRESS_SEARCHZIP;

    $addresses['lang_searchadrs'] = _MD_MYADDRESS_SEARCHADRS;

    $addresses['lang_address1'] = _MD_MYADDRESS_ADDRESS1;

    $addresses['myaddress1'] = $myaddress1;

    $addresses['lang_address2'] = _MD_MYADDRESS_ADDRESS2;

    $addresses['myaddress2'] = $myaddress2;

    $addresses['lang_address3'] = _MD_MYADDRESS_ADDRESS3;

    $addresses['myaddress3'] = $myaddress3;

    $addresses['lang_phonefax'] = _MD_MYADDRESS_PHONEFAX;

    $addresses['lang_phone'] = _MD_MYADDRESS_PHONE;

    $addresses['lang_fax'] = _MD_MYADDRESS_FAX;

    $addresses['myphone'] = $myphone;

    $addresses['myfax'] = $myfax;

    $addresses['lang_mycellphone'] = _MD_MYADDRESS_CELLPHONE;

    $addresses['mycellphone1'] = $mycellphone1;

    $addresses['mycellphone2'] = $mycellphone2;

    $addresses['lang_myemail'] = _MD_MYADDRESS_EMAIL;

    $addresses['myemail1'] = $myemail1;

    $addresses['myemail2'] = $myemail2;

    $addresses['myemail3'] = $myemail3;

    $addresses['myemail4'] = $myemail4;

    $addresses['lang_myweb'] = _MD_MYADDRESS_WEB;

    $addresses['myweb'] = $myweb;

    $addresses['lang_mycomments'] = _MD_MYADDRESS_COMMENTS;

    $addresses['mycomments'] = $mycomments;

    $addresses['lang_co_name'] = _MD_MYADDRESS_COMPANY;

    $addresses['lang_select_co'] = _MD_MYADDRESS_SELECT_CO;

    $addresses['select_co'] = 'setcompany.php?cid=' . $cid . '&amp;op=list&amp;c_id=' . $c_id;

    $addresses['searchco'] = 'searchco';

    $addresses['company'] = $cname . ' ' . $cdivision;

    $addresses['cname'] = $cname;

    $addresses['cdivision'] = $cdivision;

    $addresses['lang_cdepart'] = _MD_MYADDRESS_DEPART;

    $addresses['cdepart'] = $cdepart;

    $addresses['lang_ctitle'] = _MD_MYADDRESS_COTITLE;

    $addresses['ctitle'] = $ctitle;

    $addresses['cphone'] = $cphone;

    $addresses['cfax'] = $cfax;

    $addresses['lang_updated'] = _MD_MYADDRESS_UPDATED;

    if ('00000000000000' == $updated || '' == $updated) {
        $addresses['rend_updated'] = '';
    } else {
        $rend_updated = convert_timestamp($updated);

        $addresses['rend_updated'] = date('Y/m/d H:i:s', $rend_updated);
    }

    $addresses['updated'] = $updated;

    $addresses['lang_update'] = _MD_MYADDRESS_UPDATE;

    $addresses['lang_delete'] = _MD_MYADDRESS_DELETE;

    $addresses['lang_add'] = _MD_MYADDRESS_NEW;

    $addresses['lang_back'] = _MD_MYADDRESS_BACK;

    return $addresses;
}
