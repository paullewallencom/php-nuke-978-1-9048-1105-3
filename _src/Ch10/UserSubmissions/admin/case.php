<?php
if (!defined('ADMIN_FILE')) {
	die ("Access Denied");
}


$module_name = "UserSubmissions";
include_once("modules/$module_name/admin/language/lang-".$currentlang.".php");

switch($op) {

    case "UserSubDelete":
    case "UserSubEdit":
    case "UserSubs":
    case "UserEncycloAccept":
    case "UserSubAccept":
    include ("modules/$module_name/admin/index.php");
    break;

}

?>