<?php

// return appropriate name from uid
function get_name_from_uid($uid)
{
    if ($uid > 0) {
        $poster = new XoopsUser($uid);

        $name = $poster->uname();
    } else {
        $name = XOOPS_USER_GUESTNAME;
    }

    return $name;
}

// change mode
function select_mode($mode, $cid)
{
    $PHP_SELF = $_SERVER['PHP_SELF'];

    $mode_list = [
        1 => _MD_MYADDRESS_SEARCHBYNAME,
2 => _MD_MYADDRESS_SEARCHBYCOMPANY,
    ];

    $mode_sel = "<FORM ACTION='" . $PHP_SELF . "' NAME='selmode' METHOD='POST'>" . _MD_MYADDRESS_SELECTMODE . "&nbsp;<SELECT SIZE=\"1\" NAME=\"mode\" ONCHANGE=\"location='" . $PHP_SELF . '?cid=' . $cid . "&amp;mode='+this.options[this.selectedIndex].value\">";

    foreach ($mode_list as $k => $v) {
        $sel = '';

        if ($k == $mode) {
            $sel = ' selected="selected"';
        }

        $mode_sel .= '<OPTION VALUE="' . $k . '"' . $sel . '>' . $v . '</OPTION>';
    }

    $mode_sel .= '</SELECT>';

    $mode_sel .= '<INPUT TYPE=HIDDEN NAME="cid" VALUE="' . $cid . '">';

    $mode_sel .= '</FORM>';

    return $mode_sel;
}

function show_searchnav($mode, $cid, $url = 'viewcat.php')
{
    $url .= '?mode=' . $mode . '&amp;cid=' . $cid . '&amp;op=search';

    $keyarray = get_keyarray();

    foreach ($keyarray as $key => $val) {
        $skeys[] = ['skey' => $key, 'char' => $val];
    }

    $searchnav = [
        'lang_buttonforsearch' => _MD_MYADDRESS_BUTTONFORSEARCH,
'action_url' => $url,
'action_key' => $skeys,
    ];

    return $searchnav;
}

// get list of sub categories in header space
function get_sub_categories($mode, $parent_id)
{
    global $xoopsDB, $myts, $table_cat, $myaddress_catorder;

    global $my_groups, $myaddress_mid, $cattree;

    $ret = [];

    $crs = $xoopsDB->query("SELECT cid, title, imgurl FROM $table_cat WHERE pid=$parent_id ORDER BY $myaddress_catorder") || die('Error: Get Category.');

    while (list($cid, $title, $imgurl) = $xoopsDB->fetchRow($crs)) {
        // Show first child of this category

        $subcat = [];

        $arr = $cattree->getFirstChild($cid, 'title');

        $arr = push_gperm_array($arr, 'myadrs_category');

        foreach ($arr as $child) {
            $subcat[] = [
                'mode' => $mode,
                'cid' => $child['cid'],
                'title' => htmlspecialchars($child['title'], ENT_QUOTES | ENT_HTML5),
                'address_small_sum' => get_address_small_sum_from_cat($mode, $child['cid']),
            ];
        }

        // Category's banner default

        if ('http://' == $imgurl) {
            $imgurl = '';
        }

        // Total sum of addresses

        $cids = $cattree->getAllChildId($cid);

        $cids = push_gperm_array($cids, 'myadrs_category');

        if ((haveGperm($cid, 'myadrs_category')) || count($cids) > 0) {
            $cids[] = $cid;

            $address_total_sum = get_address_total_sum_from_cats($mode, $cids);

            $ret[] = [
                'mode' => $mode,
                'cid' => $cid,
                'imgurl' => htmlspecialchars($imgurl, ENT_QUOTES | ENT_HTML5),
                'address_small_sum' => get_address_small_sum_from_cat($mode, $cid),
                'address_total_sum' => $address_total_sum,
                'title' => htmlspecialchars($title, ENT_QUOTES | ENT_HTML5),
                'subcategories' => $subcat,
            ];
        }
    }

    return $ret;
}

// Returns the number of addresses included in a Category
function get_address_small_sum_from_cat($mode, $cid)
{
    global $xoopsDB, $table_addressbook, $table_company;

    if (1 == $mode) {
        $sql = "SELECT COUNT(aid) FROM $table_addressbook WHERE cid=$cid";
    } elseif (2 == $mode) {
        $sql = "SELECT COUNT(c_id) FROM $table_company WHERE cid=$cid";
    } else {
        $numrows = 0;

        exit;
    }

    $rs = $xoopsDB->query($sql);

    [$numrows] = $xoopsDB->fetchRow($rs);

    return $numrows;
}

// Returns the number of whole addresses included in a Category
function get_address_total_sum_from_cats($mode, $cids)
{
    global $xoopsDB, $table_addressbook, $table_company;

    $whr_cid = 'cid IN (';

    foreach ($cids as $cat) {
        $whr_cid .= "$cat,";
    }

    $whr_cid = mb_substr($whr_cid, 0, -1) . ')';

    if (1 == $mode) {
        $sql = "SELECT COUNT(aid) FROM $table_addressbook WHERE $whr_cid";
    } elseif (2 == $mode) {
        $sql = "SELECT COUNT(c_id) FROM $table_company WHERE $whr_cid";
    } else {
        $numrows = 0;

        exit;
    }

    $rs = $xoopsDB->query($sql);

    [$numrows] = $xoopsDB->fetchRow($rs);

    return $numrows;
}
