<?php

// ------------------------------------------------------------------------- //
//                      MyAddress - XOOPS addressbook                        //
//                        <http://forum.kuri3.net>                          //
// ------------------------------------------------------------------------- //

include 'admin_header.php';
require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
require_once XOOPS_ROOT_PATH . '/class/xoopscomments.php';
require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';

$myts = MyTextSanitizer::getInstance();
$cattree = new XoopsTree($table_cat, 'cid', 'pid');

$op = $_GET['op'] ?? '';
if ('' == $op && isset($_POST['op'])) {
    $op = $_POST['op'];
}

switch ($op) {
    case 'addCat':
        addCat();
        break;
    case 'delCatConfirm':
        delCatConfirm();
        break;
    case 'delCat':
        delCat();
        break;
    case 'editCat':
        editCat();
        break;
    case 'saveCat':
        saveCat();
        break;
    default:
    case 'main':
        mainCat();
        break;
}

function mainCat()
{
    global $xoopsDB, $xoopsConfig, $myts, $cattree;

    global $table_cat;

    xoops_cp_header();

    // Add a New Main Category

    OpenTable();

    echo "<form method='post' action='categories.php'>\n";

    echo '<h4>' . _AM_MYADDRESS_ADDMAIN . '</h4><br>' . _AM_MYADDRESS_TITLEC . "<input type='text' name='title' size='30' maxlength='50'><br>";

    echo '' . _AM_MYADDRESS_IMGURL . "<br><input type='text' name='imgurl' size='100' maxlength='150' value='http://'><br><br>";

    echo "<input type='hidden' name='cid' value='0'>\n";

    echo "<input type='hidden' name='op' value='addCat'>";

    echo "<input type='submit' value='" . _AM_MYADDRESS_ADD . "'><br></form>";

    CloseTable();

    echo '<br>';

    $result = $xoopsDB->query("SELECT count(*) FROM $table_cat");

    [$numrows] = $xoopsDB->fetchRow($result);

    if ($numrows > 0) {
        // Add a New Sub-Category

        OpenTable();

        echo "<form method='post' action='categories.php'>
		<h4>" . _AM_MYADDRESS_ADDSUB . '</h4><br>
		' . _AM_MYADDRESS_TITLEC . "<input type='text' name='title' size='30' maxlength='50'>&nbsp;" . _AM_MYADDRESS_IN . '&nbsp;';

        $cattree->makeMySelBox('title', 'title');

        echo "<input type='hidden' name='op' value='addCat'><br><br>";

        echo "<input type='submit' value='" . _AM_MYADDRESS_ADD . "'><br></form>";

        CloseTable();

        echo '<br>';

        // Modify Category

        OpenTable();

        echo "<form method='post' action='categories.php'>
		<h4>" . _AM_MYADDRESS_MODCAT . '</h4><br>';

        echo _AM_MYADDRESS_CATEGORYC;

        $cattree->makeMySelBox('title', 'title');

        echo "<br>
			<br>
			<input type='hidden' name='op' value='editCat'>
			<input type='submit' value='" . _AM_MYADDRESS_MODIFY . "'>
		</form>\n";

        CloseTable();

        echo '<br>';
    }

    xoops_cp_footer();
}

function editCat()
{
    global $xoopsDB, $_POST, $myts, $cattree;

    global $table_cat;

    $cid = $_POST['cid'];

    xoops_cp_header();

    OpenTable();

    echo '<h4>' . _AM_MYADDRESS_MODCAT . '</h4><br>';

    $result = $xoopsDB->query("SELECT pid, title, imgurl FROM $table_cat WHERE cid=$cid");

    [$pid, $title, $imgurl] = $xoopsDB->fetchRow($result);

    $title = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

    $imgurl = htmlspecialchars($imgurl, ENT_QUOTES | ENT_HTML5);

    echo "<form action='categories.php' method='post'>" . _AM_MYADDRESS_TITLEC . "<input type='text' name='title' value='$title' size='51' maxlength='50'><br><br>" . _AM_MYADDRESS_IMGURLMAIN . "<br><input type='text' name='imgurl' value='$imgurl' size='100' maxlength='150'><br><br>";

    echo _AM_MYADDRESS_PARENT . '&nbsp;';

    $cattree->makeMySelBox('title', 'title', $pid, 1, 'pid');

    echo "<br><input type='hidden' name='cid' value='$cid'>
	<input type='hidden' name='op' value='saveCat'><br>
	<input type='submit' value='" . _AM_MYADDRESS_SAVE . "'>
	<input type='button' value='" . _AM_MYADDRESS_DELETE . "' onClick='location=\"categories.php?pid=$pid&cid=$cid&op=delCatConfirm\"'>";

    echo "&nbsp;<input type='button' value='" . _AM_MYADDRESS_CANCEL . "' onclick='javascript:history.go(-1)'>";

    echo '</form>';

    CloseTable();

    xoops_cp_footer();
}

function saveCat()
{
    global $xoopsDB, $myts;

    global $table_cat;

    if (empty($_POST['cid'])) {
        die("'cid' not specified.");
    }

    $cid = (int)$_POST['cid'];

    $pid = isset($_POST['pid']) ? (int)$_POST['pid'] : 0;

    if ($cid == $pid) {
        die('parent category cannot be the same id with the child category');
    }

    $title = $myts->addSlashes($_POST['title']);

    if (($_POST['imgurl']) || ('' != $_POST['imgurl'])) {
        $imgurl = $myts->addSlashes($_POST['imgurl']);
    }

    $xoopsDB->query("UPDATE $table_cat SET pid=$pid , title='$title' , imgurl='$imgurl' WHERE cid=$cid") || die('DB error: UPDATE cat table');

    redirect_header('categories.php', 1, _AM_MYADDRESS_DBUPDATED);
}

function delCat()
{
    global $xoopsDB, $cattree;

    global $table_addressbook, $table_cat;

    // get and check if the cid is valid

    $cid = $_GET['cid'];

    if (empty($_GET['cid']) || $cid < 1 || !is_numeric($cid)) {
        die('Invalid category id.');
    }

    //get all categories under the specified category

    $children = $cattree->getAllChildId($cid);

    $whr = 'cid IN (';

    foreach ($children as $child) {
        $whr .= "$child,";
    }

    $whr .= "$cid)";

    //	deleteMyAddress( $whr );

    $xoopsDB->query("DELETE FROM $table_cat WHERE $whr") || die('DB error: DELETE cat table');

    redirect_header('categories.php', 1, _AM_MYADDRESS_CATDELETED);

    exit();
}

function delCatConfirm()
{
    xoops_cp_header();

    OpenTable();

    // get and check cid is valid

    $cid = $_GET['cid'];

    if (empty($_GET['cid']) || $cid < 1 || !is_numeric($cid)) {
        die('Invalid category id.');
    }

    echo "
		<center>
		<h4><font color='#ff0000'>" . _AM_MYADDRESS_CATDEL_WARNING . "</font></h4>
		<br>
		<table>
		<tr>
			<td>
				<form action='categories.php?op=delCat&cid=$cid' method='post'>
					<input type='submit' value='" . _AM_MYADDRESS_YES . "'>
				</form>
			</td>
			<td>
				<form action='categories.php' method='post'>
					<input type='submit' value='" . _AM_MYADDRESS_NO . "'>
				</form>
			</td>
		</tr>
		</table>
	\n";

    CloseTable();

    xoops_cp_footer();
}

function addCat()
{
    global $xoopsDB, $myts;

    global $table_cat;

    $pid = (int)$_POST['cid'];

    $title = $myts->addSlashes($_POST['title']);

    if (isset($_POST['imgurl']) && ('' != $_POST['imgurl'])) {
        $imgurl = $myts->addSlashes($_POST['imgurl']);
    } else {
        $imgurl = '';
    }

    //	$title = $myts->addSlashes($title);

    $newid = $xoopsDB->genId($table_cat . '_cid_seq');

    $xoopsDB->query("INSERT INTO $table_cat (cid, pid, title, imgurl) VALUES ($newid, $pid, '$title', '$imgurl')") || die('DB error: INSERT category table');

    redirect_header('categories.php?op=editCategories', 1, _AM_MYADDRESS_NEWCATADDED);
}
