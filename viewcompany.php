<?php

// ------------------------------------------------------------------------- //
//                      MyAddress - XOOPS company profile                    //
//                        <http://forum.kuri3.net>                          //
// ------------------------------------------------------------------------- //

include 'header.php';
$myts = MyTextSanitizer::getInstance();

// reject Guest and no perm
if (!$xoopsUser) {
    redirect_header(XOOPS_URL . '/user.php', 2, _MD_MYADDRESS_YOUMUSTREG);

    exit();
} elseif (!haveGroupPerm()) {
    redirect_header(XOOPS_URL . '/index.php', 2, _MD_MYADDRESS_YOUHAVENOPERM);

    exit();
}

// check if any category exist
$result = $xoopsDB->query("SELECT count(cid) as count FROM $table_cat");
[$count] = $xoopsDB->fetchRow($result);
if ($count < 1) {
    redirect_header(XOOPS_URL . '/modules/myadress', 2, _MD_MYADDRESS_YOUMUSTADDCAT);

    exit();
}

// access to the company table if an variable 'action' exists
// POST from myself, GET from index.php,viewcat.php,setcompany.php
$action = isset($_POST['add']) ? 'add' : (isset($_POST['update']) ? 'update' : (isset($_POST['del']) ? 'del' : ''));
$action = (isset($_POST['dele']) || isset($_GET['dele'])) ? 'delemployee' : $action;
$action = isset($_POST['adde']) ? 'addemployee' : (isset($_POST['edite']) ? 'updateemployee' : $action);

// make form if argument 'op' exists
// GET from index.php,viewcat.php,setcompany.php, POST from myself only in case of zip search
$op = $_GET['op'] ?? ($_POST['op'] ?? '');
$op = isset($_POST['edit']) ? 'edit' : $op;        // for the company profile edit from myself
$op = isset($_POST['new']) ? 'new' : $op;    // for add employee from myself
$op = isset($_GET['renew']) ? 'renew' : $op;    // for update employee from myself,

$pop = $_GET['pop'] ?? ($_POST['pop'] ?? '');

$jump_url = ('y' == $pop) ? 'setcompany.php' : 'viewcompany.php';

// check if zipcode search requested
$zipconvert = isset($_POST['ziptoadrs']) ? 'ziptoadrs' : (isset($_POST['adrstozip']) ? 'adrstozip' : '');

$mode = $_POST['mode'] ?? ($_GET['mode'] ?? $myaddress_mode);
$cid = $_POST['cid'] ?? ($_GET['cid'] ?? 0);
$c_id = $_POST['c_id'] ?? ($_GET['c_id'] ?? 0);
$aid = $_POST['aid'] ?? ($_GET['aid'] ?? 0);
$from = $_GET['from'] ?? 0;
$to = $from + $myaddress_numperpage;

switch ($action) {
    //-----------------------------------------------------------------//
    //                     add a new company                           //
    //-----------------------------------------------------------------//
    case 'add':

        $errors = [];

        // Check if cname exist
        if ('' == $_POST['cname']) {
            $errors[] = _MD_MYADDRESS_EH2105;
        }

        // Check if cname_jh exist
        if ('' == $_POST['cname_jh']) {
            $errors[] = _MD_MYADDRESS_EH2106;
        }

        if (count($errors) > 0) {
            if ('y' != $pop) {
                require XOOPS_ROOT_PATH . '/header.php';
            } else {
                xoops_header();
            }

            echo '<h4><b>' . $xoopsConfig['sitename'] . _MD_MYADDRESS_ERROR . '</b></h4><br>';

            echo '<div>';

            foreach ($errors as $er) {
                echo '<span style=\"color: #ff0000; font-weight: bold;\">' . $er . '</span><br>';
            }

            echo '</div><br>';

            echo "<div>[ <a href='javascript:history.go(-1)'>" . _BACK . '</a> ]</div>';

            if ('y' != $pop) {
                require_once XOOPS_ROOT_PATH . '/footer.php';

                exit();
            }

            xoops_footer();

            exit();
        }

        $c_id = add_company();
        redirect_header($jump_url . '?mode=' . $mode . '&amp;cid=' . $cid . '&amp;op=list&amp;c_id=' . $c_id, 2, _MD_MYADDRESS_RECEIVED_CO);
        exit();
        break;
    //-----------------------------------------------------------------//
    //                     update company                              //
    //-----------------------------------------------------------------//
    case 'update':

        $errors = [];

        // Check if names exist
        if ('' == $_POST['cname']) {
            $errors[] = _MD_MYADDRESS_EH2105;
        }

        // Check if names_jh exist
        if ('' == $_POST['cname_jh']) {
            $errors[] = _MD_MYADDRESS_EH2106;
        }

        if (count($errors) > 0) {
            if ('y' != $pop) {
                require XOOPS_ROOT_PATH . '/header.php';
            } else {
                xoops_header();
            }

            echo '<h4><b>' . $xoopsConfig['sitename'] . _MD_MYADDRESS_ERROR . '</b></h4><br>';

            echo '<div>';

            foreach ($errors as $er) {
                echo '<span style=\"color: #ff0000; font-weight: bold;\">' . $er . '</span><br>';
            }

            echo '</div><br>';

            echo "<div>[ <a href='javascript:history.go(-1)'>" . _BACK . '</a> ]</div>';

            if ('y' != $pop) {
                require_once XOOPS_ROOT_PATH . '/footer.php';

                exit();
            }

            xoops_footer();

            exit();
        }

        update_company($c_id);
        redirect_header($jump_url . '?mode=' . $mode . '&amp;cid=' . $cid . '&amp;op=list&amp;c_id=' . $c_id, 2, _MD_MYADDRESS_COMPANYUPDATING);
        exit();
        break;
    //-----------------------------------------------------------------//
    //                     delete company                              //
    //-----------------------------------------------------------------//
    case 'del':

        $delete_ok = isset($_POST['delete_ok']) ? (int)$_POST['delete_ok'] : 0;
        if (1 != $delete_ok) {
            if ('y' != $pop) {
                require XOOPS_ROOT_PATH . '/header.php';
            } else {
                xoops_header();
            }

            xoops_confirm(['del' => 'del', 'delete_ok' => 1, 'c_id' => $c_id, 'mode' => $mode, 'cid' => $cid, 'pop' => $pop], 'viewcompany.php', _MD_MYADDRESS_SURETODELCOMPANY . '<br>' . _MD_MYADDRESS_REMOVECOMPANYINFO, _DELETE);

            if ('y' != $pop) {
                require XOOPS_ROOT_PATH . '/footer.php';
            } else {
                xoops_footer();
            }
        } else {
            // at first, delete all employees belonging to the company

            $num_deleted = delete_all_employees_by_id($c_id);

            // and then, delete the company

            delete_company($c_id);

            $jump_url = ('y' == $pop) ? 'setcompany.php' : 'viewcat.php';

            redirect_header($jump_url . '?mode=' . $mode . '&amp;cid=' . $cid, 2, sprintf(_MD_MYADDRESS_COMPANYDELETING, $num_deleted));

            exit();
        }
        break;
    //-----------------------------------------------------------------//
    //                     add a new employee data                     //
    //-----------------------------------------------------------------//
    case 'addemployee':

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
        redirect_header($jump_url . '?mode=' . $mode . '&amp;cid=' . $cid . '&amp;op=list&amp;c_id=' . $c_id, 2, _MD_MYADDRESS_MYADDRESSRECEIVED);
        exit();
        break;
    //-----------------------------------------------------------------//
    //                     update an employee data                     //
    //-----------------------------------------------------------------//
    case 'updateemployee':

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
        redirect_header($jump_url . '?mode=' . $mode . '&amp;cid=' . $cid . '&amp;op=list&amp;c_id=' . $c_id, 2, _MD_MYADDRESS_MYADDRESSUPDATING);
        exit();
        break;
    //-----------------------------------------------------------------//
    //                     delete an employee data                     //
    //-----------------------------------------------------------------//
    case 'delemployee':

        $delete_ok = isset($_POST['delete_ok']) ? (int)$_POST['delete_ok'] : 0;
        if (1 != $delete_ok) {
            require XOOPS_ROOT_PATH . '/header.php';

            xoops_confirm(['dele' => 'dele', 'delete_ok' => 1, 'aid' => $aid, 'mode' => 1, 'cid' => $cid, 'c_id' => $c_id], 'viewcompany.php', _MD_MYADDRESS_SURETODELADDRESS . '<br>' . _MD_MYADDRESS_REMOVEADDRESSINFO, _DELETE);

            require XOOPS_ROOT_PATH . '/footer.php';
        } else {
            delete_myaddress($aid);

            redirect_header($jump_url . '?mode=' . $mode . '&amp;cid=' . $cid . '&amp;op=list&amp;c_id=' . $c_id, 2, _MD_MYADDRESS_MYADDRESSDELETING);

            exit();
        }
        break;
    //-----------------------------------------------------------------//
    //                     Show and Edit company profile               //
    //-----------------------------------------------------------------//
    default:

        //-----------------------------------------------------------------//
        //                     get address by zipcode                      //
        //-----------------------------------------------------------------//

        if ('y' != $pop) {
            if ('add' == $op || 'edit' == $op) {
                $GLOBALS['xoopsOption']['template_main'] = 'myaddress_edit_companyprofile.html';

                require XOOPS_ROOT_PATH . '/header.php';

                include 'include/editcompany.php';

                $xoopsTpl->assign(edit_company($op, $zipconvert));
            } else {
                $GLOBALS['xoopsOption']['template_main'] = 'myaddress_drawcompany_main.html';

                require XOOPS_ROOT_PATH . '/header.php';

                include 'include/drawcompany.php';

                $xoopsTpl->assign(draw_company($op, $zipconvert));
            }

            require XOOPS_ROOT_PATH . '/footer.php';
        } else {
            xoops_header();

            require_once XOOPS_ROOT_PATH . '/class/template.php';

            $xoopsTpl = new XoopsTpl();

            include 'include/editcompany.php';

            $xoopsTpl->assign(edit_company($op, $zipconvert));

            $xoopsTpl->display('db:myaddress_edit_companyprofile.html');

            xoops_footer();
        }
        break;
}
