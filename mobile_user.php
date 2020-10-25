<?php

$xoopsOption['pagetype'] = 'user';
require __DIR__ . '/mainfile.php';

/**
 * redirect if user agent is not a mobile phone
 **/
$ua = $_SERVER['HTTP_USER_AGENT'];
if (preg_match('DoCoMo', $ua)) {
    $uagent = 'DoCoMo';
} elseif (preg_match('J-PHONE', $ua)) {
    $uagent = 'DoCoMo';
} elseif (preg_match("UP\.Browser", $ua)) {
    $uagent = 'ez';
} else {
    $uagent = '';

    // header("Location: ".XOOPS_URL."/user.php");
    // exit;
}

if (function_exists('mb_http_output')) {
    mb_http_output('shift_jis');

    ob_start('mb_outputHandler');
} else {
    ob_start();
}

$op = (isset($_POST['op']) ? trim($_POST['op']) : (isset($_GET['op']) ? trim($_GET_['op']) : 'main'));
$emsg = isset($_GET['emsg']) ? trim($_GET['emsg']) : '';

if ('main' == $op) {
    if (!$xoopsUser) {
        // include Smarty template engine and initialize it

        //		require_once XOOPS_URL.'/class/template.php';

        //		$xoopsTpl = new XoopsTpl();

        //header("Content-Type: text/html; charset=Shift_JIS");

        echo '<html><head>
    	<meta http-equiv="content-type" content="text/html; charset="Shift_JIS">
    	<meta http-equiv="content-language" content="ja">
    	<title>' . $xoopsConfig['sitename'] . '</title>
			</head><body>';

        $usercookie = '';

        $redirect_page = '';

        if (isset($HTTP_COOKIE_VARS[$xoopsConfig['usercookie']])) {
            $usercookie = $HTTP_COOKIE_VARS[$xoopsConfig['usercookie']];
        }

        if (isset($_GET['xoops_redirect'])) {
            $redirect_page = htmlspecialchars(trim($_GET['xoops_redirect']), ENT_QUOTES);
        }

        echo '<div align="center">';

        if ('' != $emsg) {
            switch ($emsg) {
                case 'noact':
                    $msg = _US_NOACTTPADM;
                    break;
                case 'incorrect':
                    $msg = _US_INCORRECTLOGIN;
                    break;
                case 'noperm':
                    $msg = _NOPERM;
                    break;
                default:
                    $msg = _NOPERM;
                    break;
            }

            echo '<font color="#FF0000">' . $msg . '</font>';
        }

        echo '<h3>LOG IN</h3>
  			<form action="mobile_user.php" method="post">
    			' . _USERNAME . ' <input type="text" name="uname" size="10" maxlength="15" value="' . $usercookie . '"><br>
					' . _PASSWORD . ' <input type="password" name="pass" size="10" maxlength="15"><br>
    			<input type="hidden" name="op" value="login">
    			<input type="hidden" name="xoops_redirect" value="' . $redirect_page . '">
    			<input type="submit" value="' . _LOGIN . '">
  			</form>';

        echo '<hr>
  		' . $xoopsConfig['sitename'] . '<br>
			</div>';

        echo '</body></html>';

        exit();
    } elseif ($xoopsUser) {
        header('Location: ' . XOOPS_ROOT_PATH . '/mobile.php');

        exit;
    }

    exit();
}

if ('login' == $op) {
    var_dump($xoops_redirect);

    require_once XOOPS_URL . '/include/checklogin.php';

    exit();
}

if ('logout' == $op) {
    $HTTP_SESSION_VARS = [];

    session_destroy();

    if ($xoopsConfig['use_mysession'] && '' != $xoopsConfig['session_name']) {
        setcookie($xoopsConfig['session_name'], '', time() - 3600, '/', '', 0);
    }

    // clear autologin cookies
    setcookie('autologin_uname', '', time() - 3600, '/', '', 0); // GIJ
    setcookie('autologin_pass', '', time() - 3600, '/', '', 0);

    // clear entry from online users table

    if (is_object($xoopsUser)) {
        $onlineHandler = xoops_getHandler('online');

        $onlineHandler->destroy($xoopsUser->getVar('uid'));
    }

    //	$message = _US_LOGGEDOUT.'<br>'._US_THANKYOUFORVISIT;

    //	redirect_header('mobile_user.php', 1, $message);

    header('Location: ' . XOOPS_ROOT_PATH . '/mobile.php');

    //	header("Location: mobile_user.php");

    exit;
}
