<?php

// ------------------------------------------------------------------------- //
//                      MyAddress - XOOPS addressbook                        //
//                        <http://forum.kuri3.net>                          //
// ------------------------------------------------------------------------- //

include 'admin_header.php';

$op = $_GET['op'] ?? '';
if ('' == $op && isset($_POST['op'])) {
    $op = $_POST['op'];
}

xoops_cp_header();
OpenTable();

// number of all addresses
$ars = $xoopsDB->query("SELECT COUNT(aid) FROM $table_addressbook");
[$numrows] = $xoopsDB->fetchRow($ars);
$totalmyaddress = sprintf(_MD_MYADDRESS_THEREARE, $numrows);

echo "
	- <a href='categories.php?op=main'>" . _AM_MYADDRESS_CATEDIT . "</a>
	<br>
	<br>
	- <a href='mygroupperm.php'>" . _AM_MYADDRESS_GROUPPERM . "</a>
	<br>
	<br>
	- <a href='relations.php'>" . _AM_MYADDRESS_RELATIONS . "</a>
	<br>
	<br>
	- <a href='../../system/admin.php?fct=preferences&amp;op=showmod&amp;mod=" . $xoopsModule->getVar('mid') . "'>" . _AM_MYADDRESS_GENERALSET . "</a>
	<br>
	<br>
	<div align='center'>
		$totalmyaddress
	</div>
";

CloseTable();
xoops_cp_footer();
