<?php

//--------------------------------------------------------------------//
//              MyAddress maintenance table relations                 //
//--------------------------------------------------------------------//
include 'admin_header.php';
require XOOPS_ROOT_PATH . '/header.php';

$op = isset($_POST['edit']) ? 'edit' : (isset($_POST['add']) ? 'add' : '');

$PHP_SELF = $_SERVER['PHP_SELF'];
$selgrp = isset($_POST['selgrp']) ? (int)$_POST['selgrp'] : (isset($_GET['selgrp']) ? (int)$_GET['selgrp'] : 0);

$myts = MyTextSanitizer::getInstance();

$xoopsOption['template_showaddressbook'] = 'myaddress_relations.html';

switch ($op) {
    case 'add':
        $title = $myts->addSlashes($_POST['title']);
        $weight = (int)$_POST['weight'];
        $gid = (int)$_POST['selgrp'];
        $newid = $xoopsDB->genId($table_relations . '_rid_seq');

        $sql = sprintf("INSERT INTO %s ( rid, title, weight, gid ) VALUES ( %u, '%s', '%s', %u )", $table_relations, $newid, $title, $weight, $gid);
        $result = $xoopsDB->query($sql) || die('DB error: ADD relations table.');

        redirect_header("$PHP_SELF?selgrp=$selgrp", 2, _AM_MYADDRESS_ADDINGREL);
        break;
    case 'edit':
        if (isset($_POST['rid']) && is_array($_POST['rid'])) {
            while (list($key, $val) = each($_POST['rid'])) {
                if (isset($_POST['del']) && is_array($_POST['del']) && in_array($val, $_POST['del'], true)) {
                    deleteRel($val);
                } else {
                    updateRel($val, $_POST['title'][$key], $_POST['weight'][$key]);
                }
            }
        }

        redirect_header("$PHP_SELF?selgrp=$selgrp", 2, _AM_MYADDRESS_UPDATINGREL);
        exit;
        break;
    default:
        $myTpl = new XoopsTpl();
        $memberHandler = xoops_getHandler('member');
        $group_list = $memberHandler->getGroupList();

        xoops_cp_header();
        OpenTable();

        echo "<H4 STYLE='text-align:left;'>" . _AM_MYADDRESS_RELATIONS . '</H4><BR>';

        $group_sel = _AM_MYADDRESS_GROUP . " <SELECT SIZE=\"1\" NAME=\"selgrp\" ONCHANGE=\"location='" . XOOPS_URL . "/modules/myaddress/admin/relations.php?selgrp='+this.options[this.selectedIndex].value\">";

        foreach ($group_list as $k => $v) {
            // exclude if group_id < 4

            //			if($k > 3) {

            $sel = '';

            if ($k == $selgrp) {
                $sel = ' selected="selected"';
            }

            $group_sel .= '<OPTION VALUE="' . $k . '"' . $sel . '>' . $v . '</OPTION>';

            //			}
        }
        $group_sel .= '</SELECT> ';
        echo $group_sel;

        $myTpl->assign('url', $PHP_SELF);
        $myTpl->assign('selgrp', $selgrp);

        $sql = "SELECT rid, title, weight FROM $table_relations WHERE gid=$selgrp ORDER BY weight";
        $result = $xoopsDB->query($sql);

        // assign header
        $myTpl->assign('lang_title', _AM_MYADDRESS_TITLER);
        $myTpl->assign('lang_weight', _AM_MYADDRESS_WEIGHT);
        $myTpl->assign('lang_delete', _AM_MYADDRESS_DELETER);
        $myTpl->assign('lang_send', _AM_MYADDRESS_SEND);
        $myTpl->assign('lang_add', _AM_MYADDRESS_ADDR);

        while (list($rid, $title, $weight) = $xoopsDB->fetchRow($result)) {
            $myrelationdata['rid'] = (int)$rid;

            $myrelationdata['title'] = htmlspecialchars($title, ENT_QUOTES | ENT_HTML5);

            $myrelationdata['weight'] = (int)$weight;

            $myTpl->append('myreldata', $myrelationdata);
        }
        $myTpl->display('db:myaddress_relations.html');

        CloseTable();
        break;
}
/*
function addRel() {
    global $xoopsDB;
    global $myts;
    global $table_relations;

    $title = $myts->addSlashes($_POST['title']);
    $weight = intval($_POST['weight']);
    $gid = intval($_POST['selgrp']);
    $newid = $xoopsDB->genId( $table_relations."_rid_seq" );

    $sql = sprintf("INSERT INTO %s ( rid, title, weight, gid ) VALUES ( %u, '%s', '%s', %u )", $table_relations, $newid, $title, $weight, $gid );
    $result = $xoopsDB->query( $sql ) || die( "DB error: ADD relations table." );

    redirect_header( "$PHP_SELF?selgrp=$selgrp", 2, _AM_MYADDRESS_ADDINGREL );
}

function editRel() {
    global $selgrp;

    foreach ($_POST['rid'] as $rid) {
        if (isset($_POST['del']) && is_array($_POST['del']) && in_array($_POST['del'], $rid)) {
            deleteRel( $rid );
        } else {
            updateRel( $rid, $title[], $weight[] );
        }
    }
    redirect_header( "$PHP_SELF?selgrp=$selgrp", 2, _AM_MYADDRESS_UPDATINGREL );
    exit;
}
*/
function deleteRel($rid)
{
    global $xoopsDB;

    global $table_relations;

    $result = $xoopsDB->query("DELETE FROM $table_relations WHERE rid=$rid") || die('DB error: DELETE relations table.');
}

function updateRel($rid, $title = '', $weight = 0)
{
    global $xoopsDB;

    global $myts;

    global $table_relations;

    $title = $myts->addSlashes($title);

    $weight = (int)$weight;

    $sql = "UPDATE $table_relations SET title='$title', weight=$weight WHERE rid=$rid";

    $result = $xoopsDB->query($sql) || die('DB error: UPDATE relations table.');
}
