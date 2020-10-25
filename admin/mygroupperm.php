<?php

// ------------------------------------------------------------------------- //
//                      MyAddress - XOOPS addressbook                        //
//                        <http://forum.kuri3.net>                          //
// ------------------------------------------------------------------------- //

// Some initial settings
include 'admin_header.php';

require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';

require_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';

$module_id = $xoopsModule->getVar('mid');

// A list of items that we will be setting permissions to.

// The title of the group permission form
//$title_of_form = 'Permission form for MyAddress module';
$title_of_form = _AM_MYADDRESS_PERMTITLE;

// The name of permission which should be unique within the module
$perm_name = 'myadrs_category';

// A short description of this permission
//$perm_desc = 'Select categories that each group is allowed to view';
$perm_desc = _AM_MYADDRESS_PERMDESC;

// Create and display the form
$form = new XoopsGroupPermForm($title_of_form, $module_id, $perm_name, $perm_desc);

// get categories
$rscat = $xoopsDB->query("SELECT cid, title, pid FROM $table_cat ORDER BY cid") || die("Error: Can't Get Category table.");

$categories = [];

while (list($cid, $title, $pid) = $xoopsDB->fetchRow($rscat)) {
    $categories[$cid] = ['name' => $title, 'parent' => $pid];

    $form->addItem($cid, $title, $pid);
}

xoops_cp_header();
echo $form->render();
xoops_cp_footer();
