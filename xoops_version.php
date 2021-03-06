<?php

// ------------------------------------------------------------------------- //
//                      MyAddress - XOOPS addressbook                        //
//                        <http://forum.kuri3.net>                          //
// ------------------------------------------------------------------------- //
if (!defined('XOOPS_ROOT_PATH')) {
    exit;
}

$modversion['name'] = _MI_MYADDRESS_NAME;
$modversion['version'] = 1.13;
$modversion['description'] = _MI_MYADDRESS_DESC;
$modversion['author'] = 'hodaka hodaka@kuri3.net';
$modversion['credits'] = '';
$modversion['help'] = '';
$modversion['license'] = '';
$modversion['official'] = 0;
$modversion['image'] = 'images/myaddress_logo.gif';
$modversion['dirname'] = 'myaddress';

// Sql file (must contain sql generated by phpMyAdmin or phpPgAdmin)
// All tables should not have any prefix!
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
//$modversion['sqlfile']['postgresql'] = "sql/pgsql.sql";

// Tables created by sql file (without prefix!)
$modversion['tables'][0] = 'myaddress_cat';
$modversion['tables'][1] = 'myaddress_addressbook';
$modversion['tables'][2] = 'myaddress_company';
$modversion['tables'][3] = 'myaddress_zipcode';
$modversion['tables'][4] = 'myaddress_relations';

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

// Blocks

// Menu
global $xoopsUser;
$modversion['hasMain'] = 1;

if (isset($xoopsUser) && is_object($xoopsUser)) {
    $modversion['sub'][1]['name'] = _MI_TEXT_SMNAME1;

    $modversion['sub'][1]['url'] = 'download.php';
}

// Config
$modversion['config'][0]['name'] = 'myaddress_mode';
$modversion['config'][0]['title'] = '_MI_MYADDRESS_DEFAULTMODE';
$modversion['config'][0]['description'] = '_MI_MYADDRESS_DEFAULTMODEDSC';
$modversion['config'][0]['formtype'] = 'select';
$modversion['config'][0]['valuetype'] = 'int';
$modversion['config'][0]['default'] = 1;
$modversion['config'][0]['options'] = [
    '_MI_MYADDRESS_DEFAULTMODENAME' => 1,
'_MI_MYADDRESS_DEFAULTMODECOMPANY' => 2,
];

$modversion['config'][1]['name'] = 'myaddress_numperpage';
$modversion['config'][1]['title'] = '_MI_MYADDRESS_NUMPERPAGE';
$modversion['config'][1]['description'] = '_MI_MYADDRESS_NUMPERPAGEDSC';
$modversion['config'][1]['formtype'] = 'select';
$modversion['config'][1]['valuetype'] = 'int';
$modversion['config'][1]['default'] = 10;
$modversion['config'][1]['options'] = ['5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30, '50' => 50];

$modversion['config'][2]['name'] = 'myaddress_catorder';
$modversion['config'][2]['title'] = '_MI_MYADDRESS_CATORDER';
$modversion['config'][2]['description'] = '_MI_MYADDRESS_CATORDERDSC';
$modversion['config'][2]['formtype'] = 'select';
$modversion['config'][2]['valuetype'] = 'int';
$modversion['config'][2]['default'] = 1;
$modversion['config'][2]['options'] = [
    '_MI_MYADDRESS_CATORDERCID' => 1,
'_MI_MYADDRESS_CATORDERTITLE' => 2,
];

// Templates
$modversion['templates'][1]['file'] = 'myaddress_header.html';
$modversion['templates'][1]['description'] = 'main header';
$modversion['templates'][2]['file'] = 'myaddress_categories.html';
$modversion['templates'][2]['description'] = 'show categories';
$modversion['templates'][3]['file'] = 'myaddress_searchnav.html';
$modversion['templates'][3]['description'] = 'show search navigation';
$modversion['templates'][4]['file'] = 'myaddress_footer.html';
$modversion['templates'][4]['description'] = 'main footer';
$modversion['templates'][5]['file'] = 'myaddress_index.html';
$modversion['templates'][5]['description'] = 'main index.html';
$modversion['templates'][6]['file'] = 'myaddress_addressbook_main.html';
$modversion['templates'][6]['description'] = 'edit addressbook main';
$modversion['templates'][7]['file'] = 'myaddress_addressbook.html';
$modversion['templates'][7]['description'] = 'edit addressbook';
$modversion['templates'][8]['file'] = 'myaddress_edit_companyprofile.html';
$modversion['templates'][8]['description'] = 'edit company profile';
$modversion['templates'][9]['file'] = 'myaddress_show_companyprofile.html';
$modversion['templates'][9]['description'] = 'show company profile';
$modversion['templates'][10]['file'] = 'myaddress_show_employees.html';
$modversion['templates'][10]['description'] = 'show employees list';
$modversion['templates'][11]['file'] = 'myaddress_edit_employeeprofile.html';
$modversion['templates'][11]['description'] = 'edit employee profile';
$modversion['templates'][12]['file'] = 'myaddress_drawcompany_main.html';
$modversion['templates'][12]['description'] = 'show and edit company main';
$modversion['templates'][13]['file'] = 'myaddress_searchresults.html';
$modversion['templates'][13]['description'] = 'show search results';
$modversion['templates'][14]['file'] = 'myaddress_viewcat.html';
$modversion['templates'][14]['description'] = 'show the view of categories';
$modversion['templates'][15]['file'] = 'myaddress_get_company_in_box.html';
$modversion['templates'][15]['description'] = 'SELECT company FROM candidates';
$modversion['templates'][16]['file'] = 'myaddress_company_in_box.html';
$modversion['templates'][16]['description'] = 'show search results of company';
$modversion['templates'][17]['file'] = 'myaddress_relations.html';
$modversion['templates'][17]['description'] = 'configure relations table';
$modversion['templates'][18]['file'] = 'myaddress_searchform.html';
$modversion['templates'][18]['description'] = 'for advanced search';
$modversion['templates'][19]['file'] = 'myaddress_results.html';
$modversion['templates'][19]['description'] = 'show advanced search results';
