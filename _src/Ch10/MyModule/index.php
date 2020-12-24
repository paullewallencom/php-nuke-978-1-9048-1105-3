<?php

/************************************************************************/
/* PHP-NUKE: Web Portal System                                          */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2005 by Francisco Burzi                                */
/* http://phpnuke.org                                                   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if (!defined('MODULE_FILE')) {
	die ("You can't access this file directly...");
}

require_once("mainfile.php");
$module_name = basename(dirname(__FILE__));

$pagetitle = "- My Module";


function show_other() {
	global $prefix, $db, $sitename;
	global $admin, $multilingual, $module_name, $admin_file;
	include("header.php");
	title("$sitename: My Module");
	OpenTable();
	echo "And this is the other page of My Module.<br><br>Click <a href=\"modules.php?name=$module_name\">here</a> to return to the home page of the module.<br><br>";

	CloseTable();
	include("footer.php");
}

function show_welcome() {
	global $prefix, $db, $sitename;
	global $admin, $multilingual, $module_name, $admin_file;
	include("header.php");
	title("$sitename: My Module");
	OpenTable();
	echo "Welcome to My Module.<br><br>Click <a href=\"modules.php?name=$module_name&op=other\">here</a> to see another part of the module.<br><br>";

	CloseTable();
	include("footer.php");
}

switch($op) {

	case "other":
	show_other();
	break;


	default:
	show_welcome();
	break;

}

?>