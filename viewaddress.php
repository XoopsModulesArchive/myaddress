<?php

// --------------------------------------------------------------------------- //
//                      MyAddress - XOOPS addressbook                          //
//                        <http://forum.kuri3.net>                            //
// --------------------------------------------------------------------------- //

include 'header.php';

$myts = MyTextSanitizer::getInstance();

// reject Guest and no perm group
if (!$xoopsUser) {
    redirect_header(XOOPS_URL . '/user.php', 2, _MD_MYADDRESS_YOUMUSTREG);

    exit();
} elseif (!haveGroupPerm()) {
    redirect_header(XOOPS_URL . '/index.php', 2, _MD_MYADDRESS_YOUHAVENOPERM);

    exit();
}

// check if any category exists
$result = $xoopsDB->query("SELECT count(cid) as count FROM $table_cat");
[$count] = $xoopsDB->fetchRow($result);
if ($count < 1) {
    redirect_header(XOOPS_URL . '/modules/myaddress', 2, _MD_MYADDRESS_YOUMUSTADDCAT);

    exit();
}

$action = isset($_POST['add']) ? 'add' : (isset($_POST['update']) ? 'update' : (isset($_POST['del']) ? 'del' : ''));
$op = $_GET['op'] ?? ($_POST['op'] ?? '');

$pop = $_GET['pop'] ?? ($_POST['pop'] ?? '');
$zipconvert = isset($_POST['ziptoadrs']) ? 'ziptoadrs' : (isset($_POST['adrstozip']) ? 'adrstozip' : '');
$mode = $_POST['mode'] ?? ($_GET['mode'] ?? $myaddress_mode);
$cid = $_POST['cid'] ?? ($_GET['cid'] ?? 0);
$aid = $_POST['aid'] ?? ($_GET['aid'] ?? 0);

switch ($action) {
    //-----------------------------------------------------------------//
    //                     add a new address                           //
    //-----------------------------------------------------------------//
    case 'add':

        $errors = [];

        // Check if names exist
        if ('' == $_POST['last_name']) {
            $errors[] = _MD_MYADDRESS_EH2102;
        }
        if ('' == $_POST['first_name']) {
            $errors[] = _MD_MYADDRESS_EH2101;
        }
        if ('' == $_POST['last_name_jh']) {
            $errors[] = _MD_MYADDRESS_EH2104;
        }
        if ('' == $_POST['first_name_jh']) {
            $errors[] = _MD_MYADDRESS_EH2103;
        }
        $myemail1 = $myts->stripSlashesGPC(trim($_POST['myemail1']));
        if ('' != $myemail1 && !checkEmail($myemail1)) {
            $errors[] = sprintf(_MD_MYADDRESS_EH2107, _MD_MYADDRESS_EMAIL1);
        }
        $myemail2 = $myts->stripSlashesGPC(trim($_POST['myemail2']));
        if ('' != $myemail2 && !checkEmail($myemail2)) {
            $errors[] = sprintf(_MD_MYADDRESS_EH2107, _MD_MYADDRESS_EMAIL2);
        }
        $myemail3 = $myts->stripSlashesGPC(trim($_POST['myemail3']));
        if ('' != $myemail3 && !checkEmail($myemail3)) {
            $errors[] = sprintf(_MD_MYADDRESS_EH2107, _MD_MYADDRESS_EMAIL3);
        }
        $myemail4 = $myts->stripSlashesGPC(trim($_POST['myemail4']));
        if ('' != $myemail4 && !checkEmail($myemail4)) {
            $errors[] = sprintf(_MD_MYADDRESS_EH2107, _MD_MYADDRESS_EMAIL4);
        }

        if (count($errors) > 0) {
            require XOOPS_ROOT_PATH . '/header.php';

            echo '<h4><b>' . $xoopsConfig['sitename'] . _MD_MYADDRESS_ERROR . '</b></h4><br>';

            echo '<div>';

            foreach ($errors as $er) {
                echo '<span style=\"color: #ff0000; font-weight: bold;\">' . $er . '</span><br>';
            }

            echo '</div><br>';

            echo "<div>[ <a href='javascript:history.go(-1)'>" . _BACK . '</a> ]</div>';

            require_once XOOPS_ROOT_PATH . '/footer.php';

            exit();
        }

        add_myaddress();
        redirect_header("viewcat.php?mode=1&cid=$cid", 2, _MD_MYADDRESS_MYADDRESSRECEIVED);
        exit();
        break;
    //-----------------------------------------------------------------//
    //                     update myaddress                            //
    //-----------------------------------------------------------------//
    case 'update':

        $errors = [];

        // Check if names exist
        if ('' == $_POST['last_name']) {
            $errors[] = _MD_MYADDRESS_EH2102;
        }
        if ('' == $_POST['first_name']) {
            $errors[] = _MD_MYADDRESS_EH2101;
        }
        if ('' == $_POST['last_name_jh']) {
            $errors[] = _MD_MYADDRESS_EH2104;
        }
        if ('' == $_POST['first_name_jh']) {
            $errors[] = _MD_MYADDRESS_EH2103;
        }

        $myemail1 = $myts->stripSlashesGPC(trim($_POST['myemail1']));
        if ('' != $myemail1 && !checkEmail($myemail1)) {
            $errors[] = sprintf(_MD_MYADDRESS_EH2107, _MD_MYADDRESS_EMAIL1);
        }
        $myemail2 = $myts->stripSlashesGPC(trim($_POST['myemail2']));
        if ('' != $myemail2 && !checkEmail($myemail2)) {
            $errors[] = sprintf(_MD_MYADDRESS_EH2107, _MD_MYADDRESS_EMAIL2);
        }
        $myemail3 = $myts->stripSlashesGPC(trim($_POST['myemail3']));
        if ('' != $myemail3 && !checkEmail($myemail3)) {
            $errors[] = sprintf(_MD_MYADDRESS_EH2107, _MD_MYADDRESS_EMAIL3);
        }
        $myemail4 = $myts->stripSlashesGPC(trim($_POST['myemail4']));
        if ('' != $myemail4 && !checkEmail($myemail4)) {
            $errors[] = sprintf(_MD_MYADDRESS_EH2107, _MD_MYADDRESS_EMAIL4);
        }

        if (count($errors) > 0) {
            require XOOPS_ROOT_PATH . '/header.php';

            echo '<h4><b>' . $xoopsConfig['sitename'] . _MD_MYADDRESS_ERROR . '</b></h4><br>';

            echo '<div>';

            foreach ($errors as $er) {
                echo '<span style=\"color: #ff0000; font-weight: bold;\">' . $er . '</span><br>';
            }

            echo '</div><br>';

            echo "<div>[ <a href='javascript:history.go(-1)'>" . _BACK . '</a> ]</div>';

            require_once XOOPS_ROOT_PATH . '/footer.php';

            exit();
        }

        update_myaddress($aid);
        redirect_header('viewcat.php?mode=1&cid=' . $cid, 2, _MD_MYADDRESS_MYADDRESSUPDATING);
        exit();
        break;
    //-----------------------------------------------------------------//
    //                     delete myaddress                            //
    //-----------------------------------------------------------------//
    case 'del':
        $delete_ok = isset($_POST['delete_ok']) ? (int)$_POST['delete_ok'] : 0;
        if (1 != $delete_ok) {
            require XOOPS_ROOT_PATH . '/header.php';

            xoops_confirm(['del' => 'del', 'delete_ok' => 1, 'aid' => $aid, 'mode' => 1, 'cid' => $cid], 'viewaddress.php', _MD_MYADDRESS_SURETODELADDRESS . '<br>' . _MD_MYADDRESS_REMOVEADDRESSINFO, _DELETE);

            require XOOPS_ROOT_PATH . '/footer.php';
        } else {
            delete_myaddress($aid);

            redirect_header('viewcat.php?mode=1&cid=' . $cid, 2, _MD_MYADDRESS_MYADDRESSDELETING);

            exit();
        }
        break;
    //-----------------------------------------------------------------//
    //                     make form of add/edit/delete                //
    //-----------------------------------------------------------------//
    default:

        $GLOBALS['xoopsOption']['template_main'] = 'myaddress_addressbook_main.html';

        require XOOPS_ROOT_PATH . '/header.php';

        $xoopsTpl->assign('mod_url', $mod_url);
        $xoopsTpl->assign('mod_copyright', $mod_copyright);
        $xoopsTpl->assign('mode', $mode);
        $xoopsTpl->assign('cid', $cid);
        $xoopsTpl->assign('op', $op);

        include 'include/editaddress.php';
        $xoopsTpl->assign(edit_address($op, $zipconvert));
        require XOOPS_ROOT_PATH . '/footer.php';
        break;
}
