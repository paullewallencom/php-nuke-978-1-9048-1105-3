<?php

if (!defined('ADMIN_FILE')) {
	die ("Access Denied");
}

global $admin_file;
adminmenu("".$admin_file.".php?op=UserSubs", ""._USERSUBMISSIONS."", "usersubs.gif");

?>