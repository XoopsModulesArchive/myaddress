<?php

require_once '../../../include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/modules/myaddress/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/myaddress/include/read_configs.php';

global $xoopsDB;

if ($xoopsUser) {
    $xoopsModule = XoopsModule::getByDirname('myaddress');

    if (!$xoopsUser->isAdmin($xoopsModule->mid())) {
        redirect_header(XOOPS_URL . '/', 3, _NOPERM);

        exit();
    }
} else {
    redirect_header(XOOPS_URL . '/', 3, _NOPERM);

    exit();
}
//if ( file_exists("../language/".$xoopsConfig['language']."/main.php") ) {
//	include "../language/".$xoopsConfig['language']."/main.php";
//} else {
//	include "../language/english/main.php";
//}
