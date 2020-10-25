<?php

// ------------------------------------------------------------------------- //
//                      MyAddress - XOOPS addressbook                        //
//                        <http://forum.kuri3.net>                          //
// ------------------------------------------------------------------------- //

include 'header.php';

$zipkey = $_GET['zip'] ?? '';
$adrs1 = (isset($_SESSION) && isset($_SESSION['adrs1'])) ? $_SESSION['adrs1'] : '';
$adrs2 = (isset($_SESSION) && isset($_SESSION['adrs2'])) ? $_SESSION['adrs2'] : '';
$form = $_GET['form'] ?? 'address';

xoops_header(false);

echo '
<SCRIPT LANGUAGE="JavaScript">
<!--
function insertAddress(form) {
	for(i=0 ; i<form.length-1 ; i++)	{
		if(form.zipcode[i].checked) {';
if ('address' == $form) {
    echo '
			opener.document.address.myzipcode.value = form.zipcode[i].value;
			opener.document.address.myaddress1.value = form.address1[i].value;
			opener.document.address.myaddress2.value = form.address2[i].value;';
} else {
    echo '
			opener.document.company.czipcode.value = form.zipcode[i].value;
			opener.document.company.caddress1.value = form.address1[i].value;
			opener.document.company.caddress2.value = form.address2[i].value;';
}
echo "
			opener.focus();
			window.close();
		}
	}
}
//-->
</SCRIPT>\n";

echo "
</HEAD>
<BODY>\n";

// search zipcode table again
if ('' != $zipkey) {
    $ziparray = zip_to_address($zipkey);
} elseif ('' != $adrs1 || '' != $adrs2) {
    $ziparray = address_to_zip($adrs1, $adrs2);
} else {
    exit;
}

echo "<FORM NAME='addressForm'>
<DIV ALIGN='RIGHT'><INPUT VALUE='" . _CLOSE . "' TYPE='button' onclick='javascript:window.close();'></DIV>\n";

echo '<CENTER><H2>' . _MD_MYADDRESS_PLURAL . "</H2></CENTER>\n";

if (!isset($ziparray) || 0 == count($ziparray)) {
    echo "<B><FONT COLOR='red'>[" . _MD_MYADDRESS_NOFOUND . "]</FONT></B>\n";
} else {
    foreach ($ziparray as $zip) {
        $strzip = (string)$zip['zipcode'];

        $strzip = mb_substr($strzip, 0, 3) . '-' . mb_substr($strzip, 3, 4);

        echo "<P><INPUT TYPE='radio' name='zipcode' VALUE='" . $strzip . "'>" . $strzip . ' ' . $zip['pref'] . ' ' . $zip['address2'];

        echo "<INPUT TYPE='HIDDEN' NAME='address1' VALUE='" . $zip['pref'] . "'>";

        echo "<INPUT TYPE='HIDDEN' NAME='address2' VALUE='" . $zip['address2'] . "'>\n";
    }
}

echo "<CENTER><P><INPUT TYPE='button' VALUE= '" . _SEND . "' onClick='insertAddress(this.form)'>";

echo "&nbsp;&nbsp;<INPUT TYPE='button' VALUE= '" . _CLOSE . "' onClick='javascript:window.close();'></P></CENTER></FORM>\n";
echo "</BODY></HTML>\n";
