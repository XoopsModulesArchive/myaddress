<?php

// ------------------------------------------------------------------------- //
//                      MyAddress - XOOPS addressbook                        //
//                        <http://forum.kuri3.net>                          //
// ------------------------------------------------------------------------- //

// return true if user has a group perm
function haveGroupPerm()
{
    global $cattree;

    global $my_groups;

    global $myaddress_mid;

    $idarray = $cattree->getAllChildId(0);

    foreach ($idarray as $id) {
        if (haveGperm($id, 'myadrs_category')) {
            return true;
            break;
        }
    }

    return false;
}

// check if a user has a group permission to the category
function haveGperm($cid = 0, $perm_name = '')
{
    global $my_groups, $myaddress_mid;

    // Get the group permission handler.

    $gpermHandler = xoops_getHandler('groupperm');

    // Now check if the current user has an access privilege to the category by calling the checkRight() method of the handler class.

    if ($gpermHandler->checkRight($perm_name, (int)$cid, $my_groups, $myaddress_mid)) {
        // allowed, so display contents within the category

        return true;
    }

    //not allowed, display an error message or redirect to another page

    return false;
}

// re-array permitted categories from the given array
function push_gperm_array($cats, $perm_name)
{
    global $my_groups;

    global $myaddress_mid;

    // Get the group permission handler.

    $gpermHandler = xoops_getHandler('groupperm');

    // Now check if the current user has an access privilege to the category by calling the checkRight() method of the handler class.

    $catpermed = [];

    foreach ($cats as $cat) {
        if ($gpermHandler->checkRight($perm_name, (is_array($cat) ? (int)$cat['cid'] : (int)$cat), $my_groups, $myaddress_mid)) {
            $catpermed[] = $cat;
        }
    }

    return $catpermed;
}

// Returns the number of addresses included in a Category
function getNumberFromCat($mode, $cid)
{
    global $xoopsDB, $table_addressbook, $table_company;

    if (1 == $mode) {
        $sql = "SELECT COUNT(aid) FROM $table_addressbook WHERE cid=$cid";
    } else {
        $sql = "SELECT COUNT(c_id) FROM $table_company WHERE cid=$cid";
    }

    $rs = $xoopsDB->query($sql);

    [$numrows] = $xoopsDB->fetchRow($rs);

    return $numrows;
}

// Returns the number of whole myaddresses included in a Category
function getNumberFromCatTree($mode, $cid)
{
    global $cattree, $xoopsDB;

    global $table_addressbook, $table_company;

    $children = $cattree->getAllChildId($cid);

    $where = 'cid IN (';

    foreach ($children as $child) {
        $where .= "$child,";
    }

    $where .= "$cid)";

    if (1 == $mode) {
        $sql = "SELECT COUNT(aid) FROM $table_addressbook WHERE $where";
    } else {
        $sql = "SELECT COUNT(c_id) FROM $table_company WHERE $where";
    }

    $result = $xoopsDB->query($sql);

    [$numrows] = $xoopsDB->fetchRow($result);

    return $numrows;
}

// show keys for search
function get_keyarray()
{
    $searchkeys = [
        0 => _MD_MYADDRESS_SEARCHKEY0,
1 => _MD_MYADDRESS_SEARCHKEY1,
2 => _MD_MYADDRESS_SEARCHKEY2,
3 => _MD_MYADDRESS_SEARCHKEY3,
4 => _MD_MYADDRESS_SEARCHKEY4,
5 => _MD_MYADDRESS_SEARCHKEY5,
6 => _MD_MYADDRESS_SEARCHKEY6,
7 => _MD_MYADDRESS_SEARCHKEY7,
8 => _MD_MYADDRESS_SEARCHKEY8,
9 => _MD_MYADDRESS_SEARCHKEY9,
10 => _MD_MYADDRESS_SEARCHKEY10,
    ];

    return $searchkeys;
}

function can_edit_privatedata($uid)
{
    global $my_uid;

    if ($uid == $my_uid) {
        return true;
    }

    return false;
}

// get a company data
function get_company($c_id)
{
    global $xoopsDB;

    global $table_cat;

    global $table_company;

    $sql = "SELECT l.c_id, l.cdivision, l.cid, c.title, l.cname, l.czipcode, l.caddress1, l.caddress2, l.caddress3, l.cphone, l.cfax, l.cweb, l.ccomments FROM $table_company l LEFT JOIN $table_cat c ON l.cid=c.cid WHERE l.c_id=$c_id";

    $result = $xoopsDB->query($sql);

    return $xoopsDB->fetchRow($result);
}

// get employees from company
function get_employee_by_id($c_id)
{
    global $xoopsDB;

    global $table_addressbook;

    global $table_company;

    global $my_uid;

    $sql = "SELECT l.aid, l.cid, l.fullname, l.ctitle, l.cphone, l.mycellphone1, l.myemail1, l.myemail2, l.uid, l.dosclosed c.cname, c.division FROM $table_addressbook l LEFT JOIN $table_company c ON l.c_id=c.c_id WHERE l.c_id=$c_id AND ( uid = $my_uid || disclosed = 1 )";

    $result = $xoopsDB->query($sql);

    return $result;
}

// get an addressbook
function get_myaddress($aid)
{
    global $xoopsDB;

    global $table_addressbook;

    global $table_company;

    $sql = "SELECT l.aid, l.cid, l.disclosed, l.relations, l.first_name, l.last_name, l.first_name_jh, l.last_name_jh, l.first_name2, l.myzipcode, l.myaddress1, l.myaddress2, l.myaddress3, l.myphone, l.mycellphone1, l.mycellphone2, l.myfax, l.myemail1, l.myemail2, l.myemail3, l.myemail4, l.myweb, l.mycomments, c.c_id, c.cname, c.cdivision, l.cdepart, l.ctitle, l.cphone, l.cfax, c.cphone, c.cfax, l.updated, l.uid FROM $table_addressbook l LEFT JOIN $table_company c ON l.c_id=c.c_id WHERE l.aid=$aid";

    $result = $xoopsDB->query($sql);

    return $xoopsDB->fetchRow($result);
}

// register new myaddress
function add_myaddress()
{
    global $xoopsUser, $xoopsDB;

    global $table_addressbook;

    global $myts;

    $cid = (int)$_POST['cid'];

    $disclosed = (int)$_POST['disclosed'];

    $relations = $myts->addSlashes($_POST['relations']);

    $first_name = $myts->addSlashes($_POST['first_name']);

    $last_name = $myts->addSlashes($_POST['last_name']);

    $first_name_jh = $myts->addSlashes($_POST['first_name_jh']);

    $last_name_jh = $myts->addSlashes($_POST['last_name_jh']);

    $first_name2 = $myts->addSlashes($_POST['first_name2']);

    $myzipcode = $myts->addSlashes($_POST['myzipcode']);

    $myaddress1 = $myts->addSlashes($_POST['myaddress1']);

    $myaddress2 = $myts->addSlashes($_POST['myaddress2']);

    $myaddress3 = $myts->addSlashes($_POST['myaddress3']);

    $myphone = $myts->addSlashes($_POST['myphone']);

    $myfax = $myts->addSlashes($_POST['myfax']);

    $mycellphone1 = $myts->addSlashes($_POST['mycellphone1']);

    $mycellphone2 = $myts->addSlashes($_POST['mycellphone2']);

    $myemail1 = $myts->stripSlashesGPC(trim($_POST['myemail1']));

    $myemail2 = $myts->stripSlashesGPC(trim($_POST['myemail2']));

    $myemail3 = $myts->stripSlashesGPC(trim($_POST['myemail3']));

    $myemail4 = $myts->stripSlashesGPC(trim($_POST['myemail4']));

    $myweb = $myts->addSlashes($_POST['myweb']);

    $myweb = formatURL($myweb);

    $mycomments = $myts->addSlashes($_POST['mycomments']);

    $c_id = (int)$_POST['c_id'];

    $cdepart = $myts->addSlashes($_POST['cdepart']);

    $ctitle = $myts->addSlashes($_POST['ctitle']);

    $cphone = $myts->addSlashes($_POST['cphone']);

    $cfax = $myts->addSlashes($_POST['cfax']);

    $uid = $xoopsUser->uid();

    $newid = $xoopsDB->genId($table_addressbook . '_aid_seq');

    $fullname = $last_name . '　' . $first_name;

    $fullname_jh = $last_name_jh . '　' . $first_name_jh;

    //	$fullname_jk = mb_convert_kana( $fullname_jh, "h" );

    //	$sql = sprintf("INSERT INTO %s ( aid, cid, relations, disclosed, uid, first_name, last_name, fullname, first_name_jh, last_name_jh, fullname_jh, first_name2, myzipcode, myaddress1, myaddress2, myaddress3, myphone, myfax, mycellphone1, mycellphone2, myemail1, myemail2, myemail3, myemail4, myweb, mycomments, c_id, cdepart, ctitle, cphone, cfax ) VALUES ( %u, %u, %s, %u, %u, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %u, '%s', '%s', '%s', '%s' )", $table_addressbook, $newid, $cid, $relations, $disclosed, $uid, $first_name, $last_name, $fullname, $first_name_jh, $last_name_jh, $fullname_jh, $first_name2, $myzipcode, $myaddress1, $myaddress2, $myaddress3, $myphone, $myfax, $mycellphone1, $mycellphone2, $myemail1, $myemail2, $myemail3, $myemail4, $myweb, $mycomments, $c_id, $cdepart, $ctitle, $cphone, $cfax );

    $sql = "INSERT INTO $table_addressbook ( aid, cid, relations, disclosed, uid, first_name, last_name, fullname, first_name_jh, last_name_jh, fullname_jh, first_name2, myzipcode, myaddress1, myaddress2, myaddress3, myphone, myfax, mycellphone1, mycellphone2, myemail1, myemail2, myemail3, myemail4, myweb, mycomments, c_id, cdepart, ctitle, cphone, cfax ) VALUES ( $newid, $cid, '$relations', $disclosed, $uid, '$first_name', '$last_name', '$fullname', '$first_name_jh', '$last_name_jh', '$fullname_jh', '$first_name2', '$myzipcode', '$myaddress1', '$myaddress2', '$myaddress3', '$myphone', '$myfax', '$mycellphone1', '$mycellphone2', '$myemail1', '$myemail2', '$myemail3', '$myemail4', '$myweb', '$mycomments', $c_id, '$cdepart', '$ctitle', '$cphone', '$cfax')";

    $xoopsDB->query($sql) || die('DB error: INSERT addressbook table');

    if (0 == $newid) {
        $newid = $xoopsDB->getInsertId();
    }
}

// Update a myaddress
function update_myaddress($aid)
{
    global $xoopsDB;

    global $table_addressbook;

    global $myts;

    $cid = (int)$_POST['cid'];

    $disclosed = (int)$_POST['disclosed'];

    $relations = $myts->addSlashes($_POST['relations']);

    $first_name = $myts->addSlashes($_POST['first_name']);

    $last_name = $myts->addSlashes($_POST['last_name']);

    $first_name_jh = $myts->addSlashes($_POST['first_name_jh']);

    $last_name_jh = $myts->addSlashes($_POST['last_name_jh']);

    $first_name2 = $myts->addSlashes($_POST['first_name2']);

    $myzipcode = $myts->addSlashes($_POST['myzipcode']);

    $myaddress1 = $myts->addSlashes($_POST['myaddress1']);

    $myaddress2 = $myts->addSlashes($_POST['myaddress2']);

    $myaddress3 = $myts->addSlashes($_POST['myaddress3']);

    $myphone = $myts->addSlashes($_POST['myphone']);

    $myfax = $myts->addSlashes($_POST['myfax']);

    $mycellphone1 = $myts->addSlashes($_POST['mycellphone1']);

    $mycellphone2 = $myts->addSlashes($_POST['mycellphone2']);

    $myemail1 = $myts->stripSlashesGPC(trim($_POST['myemail1']));

    $myemail2 = $myts->stripSlashesGPC(trim($_POST['myemail2']));

    $myemail3 = $myts->stripSlashesGPC(trim($_POST['myemail3']));

    $myemail4 = $myts->stripSlashesGPC(trim($_POST['myemail4']));

    $myweb = $myts->addSlashes($_POST['myweb']);

    $myweb = formatURL($myweb);

    $mycomments = $myts->addSlashes($_POST['mycomments']);

    $c_id = (int)$_POST['c_id'];

    $cdepart = $myts->addSlashes($_POST['cdepart']);

    $ctitle = $myts->addSlashes($_POST['ctitle']);

    $cphone = $myts->addSlashes($_POST['cphone']);

    $cfax = $myts->addSlashes($_POST['cfax']);

    $fullname = $last_name . '　' . $first_name;

    $fullname_jh = $last_name_jh . '　' . $first_name_jh;

    //	$fullname_jk = mb_convert_kana( $fullname_jh, "h" );

    $sql = "UPDATE $table_addressbook SET cid=$cid, disclosed=$disclosed, relations='$relations', first_name='$first_name', last_name='$last_name', fullname='$fullname', first_name_jh='$first_name_jh', last_name_jh='$last_name_jh', fullname_jh='$fullname_jh', first_name2='$first_name2', myzipcode='$myzipcode', myaddress1='$myaddress1', myaddress2='$myaddress2', myaddress3='$myaddress3', myphone='$myphone', myfax='$myfax', mycellphone1='$mycellphone1', mycellphone2='$mycellphone2', myemail1='$myemail1', myemail2='$myemail2', myemail3='$myemail3', myemail4='$myemail4', myweb='$myweb', mycomments='$mycomments', c_id=$c_id, cdepart='$cdepart', ctitle='$ctitle', cphone='$cphone', cfax='$cfax' WHERE aid=$aid";

    // update addressbook

    $xoopsDB->query($sql) || die('DB error: UPDATE addressbook table');
}

// Delete myaddress
function delete_myaddress($aid)
{
    global $xoopsDB;

    global $table_addressbook;

    $result = $xoopsDB->query("DELETE FROM $table_addressbook WHERE aid='$aid'") || die('DB error: DELETE addressbook table');
}

// register new company profile
function add_company()
{
    global $xoopsUser, $xoopsDB;

    global $table_company;

    global $myts;

    $cdivision = $myts->addSlashes($_POST['cdivision']);

    $cid = (int)$_POST['cid'];

    $cname = $myts->addSlashes($_POST['cname']);

    $cname_jh = $myts->addSlashes($_POST['cname_jh']);

    $czipcode = $myts->addSlashes($_POST['czipcode']);

    $caddress1 = $myts->addSlashes($_POST['caddress1']);

    $caddress2 = $myts->addSlashes($_POST['caddress2']);

    $caddress3 = $myts->addSlashes($_POST['caddress3']);

    $cphone = $myts->addSlashes($_POST['cphone']);

    $cfax = $myts->addSlashes($_POST['cfax']);

    $cweb = $myts->addSlashes($_POST['cweb']);

    $cweb = formatURL($cweb);

    $ccomments = $myts->addSlashes($_POST['ccomments']);

    $newid = $xoopsDB->genId($table_company . '_c_id_seq');

    $uid = $xoopsUser->uid();

    $sql = sprintf(
        "INSERT INTO %s ( c_id, cdivision, cid, cname, cname_jh, czipcode, caddress1, caddress2, caddress3, cphone, cfax, cweb, ccomments, uid ) VALUES ( %u, '%s', %u, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %u )",
        $table_company,
        $newid,
        $cdivision,
        $cid,
        $cname,
        $cname_jh,
        $czipcode,
        $caddress1,
        $caddress2,
        $caddress3,
        $cphone,
        $cfax,
        $cweb,
        $ccomments,
        $uid
    );

    $xoopsDB->query($sql) || die('DB error: INSERT company table');

    if (0 == $newid) {
        $newid = $xoopsDB->getInsertId();
    }

    return $newid;
}

// Update a company profile
function update_company($c_id)
{
    global $xoopsDB;

    global $table_company;

    global $myts;

    $cdivision = $myts->addSlashes($_POST['cdivision']);

    $cid = (int)$_POST['cid'];

    $cname = $myts->addSlashes($_POST['cname']);

    $cname_jh = $myts->addSlashes($_POST['cname_jh']);

    $czipcode = $myts->addSlashes($_POST['czipcode']);

    $caddress1 = $myts->addSlashes($_POST['caddress1']);

    $caddress2 = $myts->addSlashes($_POST['caddress2']);

    $caddress3 = $myts->addSlashes($_POST['caddress3']);

    $cphone = $myts->addSlashes($_POST['cphone']);

    $cfax = $myts->addSlashes($_POST['cfax']);

    $cweb = $myts->addSlashes($_POST['cweb']);

    $cweb = formatURL($cweb);

    $ccomments = $myts->addSlashes($_POST['ccomments']);

    $sql = "UPDATE $table_company SET c_id=$c_id, cdivision='$cdivision', cid=$cid, cname='$cname', cname_jh='$cname_jh', czipcode='$czipcode', caddress1='$caddress1', caddress2='$caddress2', caddress3='$caddress3', cphone='$cphone', cfax='$cfax', cweb='$cweb', ccomments='$ccomments' WHERE c_id='$c_id'";

    // update company profile

    $xoopsDB->query($sql) || die('DB error: UPDATE company table');
}

// Delete all employees belonging to the company
function delete_all_employees_by_id($c_id)
{
    global $xoopsDB;

    global $table_addressbook;

    $result = $xoopsDB->query("DELETE FROM $table_addressbook WHERE c_id='$c_id'") || die('DB error: DELETE addressbook table');

    return $GLOBALS['xoopsDB']->getAffectedRows();
}

// Delete company profile
function delete_company($c_id)
{
    global $xoopsDB;

    global $table_company;

    $result = $xoopsDB->query("DELETE FROM $table_company WHERE c_id='$c_id'") || die('DB error: DELETE company table');
}

// Get company name by c_id
function get_cname_by_id($c_id)
{
    global $xoopsDB;

    global $table_company;

    $rs = $xoopsDM->query("SELECT cname FROM $table_company WHERE c_id = '$c_id'");

    if ($rs) {
        [$cname] = $xoopsDb->fetchRow($rs);
    } else {
        $cname = '';
    }

    return $cname;
}

// zipcode conversion
function zipconvert($zipconvert, $zipselkey)
{
    global $xoopsDB;

    global $table_zipcode;

    if ('ziptoadrs' == $zipconvert) {
        $ziparray = zip_to_address($zipselkey);
    } else {
        $ziparray = address_to_zip($zipselkey['address1'], $zipselkey['address2']);
    }

    return $ziparray;
}

// Get address1,2 by table-zipcode
function zip_to_address($zipcode = '')
{
    global $xoopsDB;

    global $table_zipcode;

    $address = [];

    if ('' != $zipcode) {
        // abrebiate zipcode

        $zipkey = trim(mb_substr($zipcode, 0, 3) . trim(mb_substr($zipcode, 4, 4)));

        // search zipcode table by zipcode

        // changed item name 'zipcode' to 'postal' at the ver.1.1.2.

        $result = $xoopsDB->query("SELECT postal, pref, concat(city, town) AS address2 FROM $table_zipcode WHERE postal LIKE '$zipkey%' ORDER BY postal");

        if ($result) {
            while (list($rszip, $pref, $adrs2) = $xoopsDB->fetchRow($result)) {
                $address[] = ['zipcode' => $rszip, 'pref' => $pref, 'address2' => $adrs2];
            }
        }
    }

    return $address;
}

// Get zipcode by table-zipcode
function address_to_zip($address1, $address2)
{
    global $xoopsDB;

    global $table_zipcode;

    $zipcode = [];

    if ('' != $address1 || '' != $address2) {
        // search zipcode table by address

        $isSql = false;

        // changed item name 'zipcode' to 'postal' at the ver.1.1.2.

        $sql = "SELECT postal, pref, concat(city, town) AS address2 FROM $table_zipcode WHERE ";

        if ('' != $address1) {
            $sql .= " pref LIKE '" . trim($address1) . "%'";

            $isSql = true;
        }

        if ('' != $address2) {
            if ($isSql) {
                $sql .= ' AND';
            }

            // bug fixed at ver.1.1.3 前方一致に変更

            //			$sql .= " concat(city, town) LIKE '%".trim( $address2 )."%'";

            $sql .= " concat(city, town) LIKE '" . trim($address2) . "%'";
        }

        // changed item name 'zipcode' to 'postal' at the ver.1.1.2.

        $sql .= ' ORDER BY postal';

        $result = $xoopsDB->query($sql);

        if ($result) {
            while (list($rszip, $pref, $address2) = $xoopsDB->fetchRow($result)) {
                $zipcode[] = ['zipcode' => $rszip, 'pref' => $pref, 'address2' => $address2];
            }
        }
    }

    return $zipcode;
}

// get select box of relations
function getSelBoxRelations()
{
    global $my_uid;

    global $xoopsDB;

    global $table_relations;

    $memberHandler = xoops_getHandler('member');

    $group_ids = $memberHandler->getGroupsByUser($my_uid);

    $sql = "SELECT title FROM $table_relations WHERE gid IN (";

    $comma = false;

    foreach ($group_ids as $group) {
        //		if ( $group > 3 ) {

        if (!$comma) {
            $sql .= (string)$group;

            $comma = true;
        } else {
            $sql .= ",$group";
        }

        //		}
    }

    $sql .= ') ORDER BY weight';

    $result = $xoopsDB->query($sql);

    $relarray = [];

    while (list($title) = $xoopsDB->fetchRow($result)) {
        $relarray[] = ['value' => trim($title), 'label' => trim($title)];
    }

    return $relarray;
}

// Unfortunately, strtotime() can't convert mysql timestamps of the form YYYYMMDDhhmmss (the default 14 character timestamp).  Here's a function to do it for you:
function convert_timestamp($timestamp, $adjust = '')
{
    $timestring = mb_substr($timestamp, 0, 8) . ' ' . mb_substr($timestamp, 8, 2) . ':' . mb_substr($timestamp, 10, 2) . ':' . mb_substr($timestamp, 12, 2);

    return strtotime($timestring . " $adjust");
}

// Remember that the $adjust string needs to be properly spaced- "+ 30 days", not "+30days"
// get bouser
function get_brouser()
{
    [$brous, $others] = preg_split('[/]', getenv('HTTP_USER_AGENT'), 2);

    return $brous;
}
