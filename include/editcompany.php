<?php

function edit_company($op, $zipconvert)
{
    global $myts, $xoopsDB, $cattree, $xoopsTpl;

    global $table_addressbook, $table_company, $table_cat, $table_zipcode;

    global $table_relations, $myaddress_numperpage, $myaddress_catorder;

    global $isadmin, $myaddress_mid, $my_uid, $my_groups, $mod_url, $mod_copyright;

    global $pop, $mode, $cid, $c_id;

    if ('' != $zipconvert) {
        if ('ziptoadrs' == $zipconvert) {
            $zipselkey = isset($_POST['czipcode']) ? htmlspecialchars($_POST['czipcode'], ENT_QUOTES | ENT_HTML5) : '';

            $ziparray = zipconvert($zipconvert, $zipselkey);
        } else {
            $address1 = isset($_POST['caddress1']) ? htmlspecialchars($_POST['caddress1'], ENT_QUOTES | ENT_HTML5) : '';

            $address2 = isset($_POST['caddress2']) ? htmlspecialchars($_POST['caddress2'], ENT_QUOTES | ENT_HTML5) : '';

            $zipselkey['address1'] = $address1;

            $zipselkey['address2'] = $address2;

            $ziparray = zipconvert($zipconvert, $zipselkey);
        }
    } else {
        $ziparray = [];
    }

    $edititems = [
        'mod_url' => $mod_url,
'mod_copyright' => $mod_copyright,
'mode' => $mode,
'cid' => $cid,
'op' => $op,
'pop' => $pop,
'c_id' => $c_id,
    ];

    if ('add' == $op) {
        //-----------------------------------------------------------------//

        //                     to add a new company profile                //

        //-----------------------------------------------------------------//

        $heading = _MD_MYADDRESS_ADDCOMPANY;

        $c_id = 0;

        $cname = isset($_POST['cname']) ? htmlspecialchars($_POST['cname'], ENT_QUOTES | ENT_HTML5) : '';

        $cname_jh = isset($_POST['cname_jh']) ? htmlspecialchars($_POST['cname_jh'], ENT_QUOTES | ENT_HTML5) : '';

        $cdivision = isset($_POST['cdivision']) ? htmlspecialchars($_POST['cdivision'], ENT_QUOTES | ENT_HTML5) : '';

        $czipcode = isset($_POST['czipcode']) ? htmlspecialchars($_POST['czipcode'], ENT_QUOTES | ENT_HTML5) : '';

        $caddress1 = isset($_POST['caddress1']) ? htmlspecialchars($_POST['caddress1'], ENT_QUOTES | ENT_HTML5) : '';

        $caddress2 = isset($_POST['caddress2']) ? htmlspecialchars($_POST['caddress2'], ENT_QUOTES | ENT_HTML5) : '';

        $caddress3 = isset($_POST['caddress3']) ? htmlspecialchars($_POST['caddress3'], ENT_QUOTES | ENT_HTML5) : '';

        $cphone = isset($_POST['cphone']) ? htmlspecialchars($_POST['cphone'], ENT_QUOTES | ENT_HTML5) : '';

        $cfax = isset($_POST['cfax']) ? htmlspecialchars($_POST['cfax'], ENT_QUOTES | ENT_HTML5) : '';

        $cweb = isset($_post['cweb']) ? htmlspecialchars($_POST['cweb'], ENT_QUOTES | ENT_HTML5) : 'http://';

        $ccomments = isset($_POST['ccomments']) ? htmlspecialchars($_POST['ccomments'], ENT_QUOTES | ENT_HTML5) : '';

        $updated = '';
    } elseif ('edit' == $op) {
        //-----------------------------------------------------------------//

        //                     to update company profile                  //

        //-----------------------------------------------------------------//

        $heading = _MD_MYADDRESS_EDITCOMPANY;

        if ('' == $zipconvert) {
            $sql = "SELECT c_id, cdivision, cid, cname, cname_jh, czipcode, caddress1, caddress2, caddress3, cphone, cfax, cweb, ccomments, updated FROM $table_company WHERE c_id=$c_id";

            $result = $xoopsDB->query($sql);

            [$c_id, $cdivision, $cid, $cname, $cname_jh, $czipcode, $caddress1, $caddress2, $caddress3, $cphone, $cfax, $cweb, $ccomments, $updated] = $xoopsDB->fetchRow($result);
        } else {
            $c_id = $_POST['c_id'];

            $cname = htmlspecialchars($_POST['cname'], ENT_QUOTES | ENT_HTML5);

            $cname_jh = htmlspecialchars($_POST['cname_jh'], ENT_QUOTES | ENT_HTML5);

            $cdivision = htmlspecialchars($_POST['cdivision'], ENT_QUOTES | ENT_HTML5);

            $czipcode = htmlspecialchars($_POST['czipcode'], ENT_QUOTES | ENT_HTML5);

            $caddress1 = htmlspecialchars($_POST['caddress1'], ENT_QUOTES | ENT_HTML5);

            $caddress2 = htmlspecialchars($_POST['caddress2'], ENT_QUOTES | ENT_HTML5);

            $caddress3 = htmlspecialchars($_POST['caddress3'], ENT_QUOTES | ENT_HTML5);

            $cphone = htmlspecialchars($_POST['cphone'], ENT_QUOTES | ENT_HTML5);

            $cfax = htmlspecialchars($_POST['cfax'], ENT_QUOTES | ENT_HTML5);

            $cweb = htmlspecialchars($_POST['cweb'], ENT_QUOTES | ENT_HTML5);

            $ccomments = htmlspecialchars($_POST['ccomments'], ENT_QUOTES | ENT_HTML5);

            $updated = $_POST['updated'];
        }
    }

    // some hidden items

    $edititems['mode'] = $mode;

    $edititems['pop'] = $pop;

    $edititems['cid'] = $cid;

    $edititems['op'] = $op;

    $edititems['c_id'] = $c_id;

    $edititems['lang_editcompany'] = $heading;

    $edititems['lang_cat_title'] = _MD_MYADDRESS_CATEGORY;

    $tree = $cattree->getChildTreeArray(0, $myaddress_catorder);

    $tree = push_gperm_array($tree, 'myadrs_category');

    $cidselbox = [];

    foreach ($tree as $leaf) {
        $leaf['prefix'] = mb_substr($leaf['prefix'], 0, -1);

        $leaf['prefix'] = str_replace('.', '--', $leaf['prefix']);

        $cidselbox['label'] = $leaf['prefix'] . $leaf['title'];

        $cidselbox['value'] = $leaf['cid'];

        $cidselboxdata[] = $cidselbox;
    }

    $edititems['cidselboxdata'] = $cidselboxdata;

    $edititems['lang_cname'] = _MD_MYADDRESS_CNAME;

    $edititems['cname'] = htmlspecialchars($cname, ENT_QUOTES | ENT_HTML5);

    $edititems['lang_cname_jh'] = _MD_MYADDRESS_FULLNAME_JH;

    $edititems['cname_jh'] = htmlspecialchars($cname_jh, ENT_QUOTES | ENT_HTML5);

    $edititems['lang_cdivision'] = _MD_MYADDRESS_DIVISION;

    $edititems['cdivision'] = htmlspecialchars($cdivision, ENT_QUOTES | ENT_HTML5);

    //------------------------------------------------------------------//

    //       	     search results of zipcode                            //

    //------------------------------------------------------------------//

    $edititems['zipsearchresult'] = '';

    if ('' != $zipconvert) {
        if (!isset($ziparray) || 0 == count($ziparray)) {
            $edititems['zipsearchresult'] = "&nbsp;<B><FONT COLOR='red'>[" . _MD_MYADDRESS_NOFOUND . ']</FONT></B>';
        } elseif (count($ziparray) < 2) {
            $czipcode = mb_substr($ziparray[0]['zipcode'], 0, 3) . '-' . mb_substr($ziparray[0]['zipcode'], 3, 4);

            $caddress1 = trim($ziparray[0]['pref']);

            $caddress2 = trim($ziparray[0]['address2']);
        } else {
            if ('adrstozip' == $zipconvert) {
                // bug fixed at ver.1.1.3

                if (!isset($_SESSION)) {
                    $_SESSION = [];
                }

                $_SESSION['adrs1'] = $caddress1;

                $_SESSION['adrs2'] = $caddress2;

                //					$edititems['zipsearchresult'] = "&nbsp;<B>[<A HREF=\"javascript:openWithSelfMain('searchmore.php?form=company&amp;adrs1=".$caddress1."&amp;adrs2=".$caddress2."','searchmore',400,400)\">".sprintf( _MD_MYADDRESS_SEARCHMORE, count($ziparray) )."</A>]</B>";

                $edititems['zipsearchresult'] = "&nbsp;<B>[<A HREF=\"javascript:openWithSelfMain('searchmore.php?form=company','searchmore',400,400)\">" . sprintf(_MD_MYADDRESS_SEARCHMORE, count($ziparray)) . '</A>]</B>';
            } else {
                $edititems['zipsearchresult'] = "&nbsp;<B>[<A HREF=\"javascript:openWithSelfMain('searchmore.php?form=company&amp;zip=" . $czipcode . "','searchmore',400,400);\">" . sprintf(_MD_MYADDRESS_SEARCHMORE, count($ziparray)) . '</A>]</B>';
            }
        }
    }

    $edititems['lang_zipcode'] = _MD_MYADDRESS_ZIPCODE;

    $edititems['czipcode'] = htmlspecialchars($czipcode, ENT_QUOTES | ENT_HTML5);

    $edititems['lang_searchzip'] = _MD_MYADDRESS_SEARCHZIP;

    $edititems['lang_searchadrs'] = _MD_MYADDRESS_SEARCHADRS;

    $edititems['lang_address1'] = _MD_MYADDRESS_ADDRESS1;

    $edititems['caddress1'] = htmlspecialchars($caddress1, ENT_QUOTES | ENT_HTML5);

    $edititems['lang_address2'] = _MD_MYADDRESS_ADDRESS2;

    $edititems['caddress2'] = htmlspecialchars($caddress2, ENT_QUOTES | ENT_HTML5);

    $edititems['lang_address3'] = _MD_MYADDRESS_ADDRESS3;

    $edititems['caddress3'] = htmlspecialchars($caddress3, ENT_QUOTES | ENT_HTML5);

    $edititems['lang_phonefax'] = _MD_MYADDRESS_PHONEFAX;

    $edititems['lang_cphone'] = _MD_MYADDRESS_PHONE;

    $edititems['lang_cfax'] = _MD_MYADDRESS_FAX;

    $edititems['cphone'] = htmlspecialchars($cphone, ENT_QUOTES | ENT_HTML5);

    $edititems['cfax'] = htmlspecialchars($cfax, ENT_QUOTES | ENT_HTML5);

    $edititems['lang_web'] = _MD_MYADDRESS_WEB;

    $edititems['cweb'] = htmlspecialchars($cweb, ENT_QUOTES | ENT_HTML5);

    $edititems['lang_comments'] = _MD_MYADDRESS_COMMENTS;

    $edititems['ccomments'] = $myts->displayTarea($ccomments);

    $edititems['lang_updated'] = _MD_MYADDRESS_UPDATED;

    if ('add' != $op) {
        $rend_updated = convert_timestamp($updated);

        $edititems['rend_updated'] = date('Y/m/d H:i:s', $rend_updated);
    }

    $edititems['updated'] = $updated;

    $edititems['lang_add'] = _MD_MYADDRESS_NEW;

    $edititems['lang_back'] = _MD_MYADDRESS_BACK;

    $edititems['lang_update'] = _MD_MYADDRESS_UPDATE;

    $edititems['lang_delete'] = _MD_MYADDRESS_DELETE;

    return $edititems;
}
