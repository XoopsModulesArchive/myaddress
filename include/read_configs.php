<?php

// ------------------------------------------------------------------------- //
//                      MyAddress - XOOPS addressbook                        //
//                        <http://forum.kuri3.net>                          //
// ------------------------------------------------------------------------- //
if (!defined('XOOPS_ROOT_PATH')) {
    exit;
}

global $xoopsConfig, $xoopsDB, $xoopsUser;

// module information
$mod_url = XOOPS_URL . '/modules/myaddress';
$mod_path = XOOPS_ROOT_PATH . '/modules/myaddress';
$xoopsModule = XoopsModule::getByDirname('myaddress');
$myaddress_mid = $xoopsModule->getVar('mid');
$mod_version = $xoopsModule->getVar('version');
$version = $mod_version / 100;
$mod_copyright = "<a href='http://forum.kuri3.net/'><b>MyAddress " . $version . '</b></a>';

// global language file
$language = $xoopsConfig['language'];
if (file_exists("$mod_path/language/$language/main.php")) {
    require_once "$mod_path/language/$language/main.php";
} else {
    require_once "$mod_path/language/english/main.php";

    $language = 'english';
}

// read from xoops_config
// get my mid
//	$rs = $xoopsDB->query( "SELECT mid FROM ".$xoopsDB->prefix('modules')." WHERE dirname='myaddress'" ) ;
//	list( $myaddress_mid ) = $xoopsDB->fetchRow( $rs );

// read configs from xoops_config directly
$rs = $xoopsDB->query('SELECT conf_name,conf_value FROM ' . $xoopsDB->prefix('config') . " WHERE conf_modid=$myaddress_mid");
while (list($key, $val) = $xoopsDB->fetchRow($rs)) {
    $myaddress_configs[$key] = $val;
}

// check if
foreach ($myaddress_configs as $key => $val) {
    if (0 == strncmp($key, 'myaddress_', 10)) {
        $$key = $val;
    }
}

// User Informations
if (empty($xoopsUser)) {
    $my_uid = 0;

    $my_groups = XOOPS_GROUP_ANONYMOUS;

    $isadmin = false;
} else {
    $my_uid = $xoopsUser->uid();

    $my_groups = $xoopsUser->getGroups();

    $isadmin = $xoopsUser->isAdmin($myaddress_mid);
}

// DB table name (if you use this module multiplly, change here.)
$table_addressbook = $xoopsDB->prefix('myaddress_addressbook');
$table_cat = $xoopsDB->prefix('myaddress_cat');
$table_company = $xoopsDB->prefix('myaddress_company');
$table_relations = $xoopsDB->prefix('myaddress_relations');
// ver.1.1.2
// the zipcode table is now replaced by the postalcode table created by Marijunna.
//	$table_zipcode = $xoopsDB->prefix( "myaddress_zipcode" );
$table_zipcode = $xoopsDB->prefix('postalcode');

// make a category tree object
require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
$cattree = new XoopsTree($table_cat, 'cid', 'pid');
