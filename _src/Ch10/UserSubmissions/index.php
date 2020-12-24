<?php


if (!defined('MODULE_FILE')) {
	die ("You can't access this file directly...");
}

define('INDEX_FILE', true);

$module_name = basename(dirname(__FILE__));
get_lang($module_name);
$pagetitle = "- $module_name";



function ShowTypesNoLanguages()
{
	global $module_name;
	include("header.php");
    	title("Submit a new Item");
	OpenTable();
	$u = get_user_object();
	if (!$u)
	{
		echo "<center>You need to be a registered user to submit or edit new items of content<center><br><br>";
		CloseTable();
		include("footer.php");
		exit;
	}
	echo "Choose the type of content to add<br><br>";

	echo "<ul>";
	echo "<li><a href=\"modules.php?name=$module_name&amp;op=add&amp;oid=1\">Submit a new Encyclopedia entry</a></li>";
	echo "</ul>";
	echo "<br>";
	CloseTable();
	include("footer.php");
}

function ShowTypes()
{
	global $module_name;
	include("header.php");
    	title(_SUBMITNEWITEM);
	OpenTable();
	$u = get_user_object();
	if (!$u)
	{
		echo "<center>"._YOUNEEDTOBEAUSER."<center><br><br>";
		CloseTable();
		include("footer.php");
		exit;
	}
	echo _CHOOSETYPE."<br><br>";

	echo "<ul>";
	echo "<li><a href=\"modules.php?name=$module_name&amp;op=add&amp;oid=1\">"._SUBMITNEWENCYCLOENTRY."</a></li>";
	echo "</ul>";
	echo "<br>";
	CloseTable();
	include("footer.php");
}


function ShowFormForInput($oid)
{
	global $pagetitle, $module_name;
	$oid = intval($oid);
	if ($oid==1)
	{

		$pagetitle = "- $module_name : "._ADDENYCLOENTRY;

		include("header.php");
		title (_ADDENYCLOENTRY);
		OpenTable();

		formatEncyclo();

		CloseTable();
		include("footer.php");
	}
	else
	  header("Redirect: modules.php?name=$module_name");

}
function AddEnyclopediaEntry($eid, $title, $text, $language)
{
	global $module_name;

	$eid = intval($eid);

	if ($title=="" || $text=="" || $eid<1)
	{
		$pagetitle = "- $module_name : "._ADDENYCLOENTRY;
		include("header.php");
		title (_ADDENYCLOENTRY);
		OpenTable();
		echo "You need to supply a title, some text, and select an Encylopedia!<br><br>";
		echo "[ <a href=\"javascript:history.go(-1)\">Go Back</a> ]<br><br>";

		CloseTable();
		include("footer.php");
		exit;
	}

	$storageArray = array('title'=>$title,
			      'text'=>$text, 'language'=>$language);

	storeSubmission("encyclo", $storageArray, $eid, $title);
	$pagetitle = "- $module_name : "._THANKYOUSUBMISSION;
	include("header.php");
	title (_THANKYOUSUBMISSION);
	OpenTable();
	echo "<center>"._THANKSTEXT."<br><br>";
	echo "[ <a href=\"modules.php?name=$module_name\">"._ADDMOREITEMS."</a> ]</center><br><br>";

	CloseTable();
	include("footer.php");
}

function formatEncyclo()
{
	global $db, $prefix;
	global $module_name;

	$u = get_user_object();
	if (!$u) exit;
	echo "  <form action=\"modules.php?name=$module_name\" method=\"post\">\n
		<p><b>Title:</b><br>\n
		<input name=\"usr_title\" size=\"50\" type=\"text\" value=\"\">\n<br>
		<br>
		<b>Term Text:</b><br>
		If you want multiple pages you can write <b>&lt;!--pagebreak--&gt;</b> where you
		want to cut.<br>
		<textarea name=\"usr_text\" cols=\"60\" rows=\"20\"></textarea><br>
		<br>
		<b>Encyclopedia:</b><br>
		<select name=\"usr_eid\">\n";

	$sql = "SELECT eid, title FROM " . $prefix . "_encyclopedia WHERE active='1'";


	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
	$eid = intval($row['eid']);
	$title = stripslashes($row['title']);
	echo "<option value=\"$eid\">$title</option>\n";

	//if ($eid == $usr_eid) echo "selected";
	//    echo ">$title</option>\n";
	}
	  echo "</select>\n<br><br>";

	echo " <input name=\"op\" value=\"add_encycloentry\" type=\"hidden\">
	<input value=\"Submit Item\" type=\"submit\"></p>
	</form>";

}

function storeSubmission($type, $storageArray, $parent_id, $title_field)
{
	global $prefix, $db, $module_name, $admin_file;
	global $nukeurl, $sitename, $adminmail;

	$u = get_user_object();
	if (!$u)
	{
		Header("Location: modules.php?name=$module_name");
	}
	$user_id = $u[0];
	$user_name = $u[1];

	$storage = serialize($storageArray);

	$storage = addslashes($storage);
	$title_field = addslashes($title_field);

	$sql = "INSERT INTO ".$prefix."_usersubmissions(type, parent_id, data, user_id, user_name, title)
	VALUES ('$type', '$parent_id', '$storage', '$user_id',  '$user_name', '$title_field')";

	$db->sql_query($sql);
	$lid = $db->sql_nextid();

	$mailBody = "A new piece of user-submitted content has been added to the site.<br><br>";

	$mailBody .= "Visit this link to check it out.<br><br>";

	$mailBody .= "<a href=\"$nukeurl/".$admin_file.".php?op=UserSubEdit&wid=$lid>Here</a>";

	$from = "$sitename <$adminmail>";
	$to = $adminmail;

	$subject = "A new piece of content has been submitted";

	// Use this function if you have access to a mail server
	//mail($to, $subject, $mailBody, "From: $from\nX-Mailer: PHP/" . phpversion());


}

function get_user_object()
{

   global $user;

   if (!$user) return false;

   if(!is_array($user))
   {
	$user2 = addslashes($user);
	$user2 = base64_decode($user2);

	$user2 = explode(":", $user2);
   }

   return $user2;



}

switch($op)
{

	case "add_encycloentry":
		AddEnyclopediaEntry($usr_eid, $usr_title, $usr_text, $usr_language);
		break;
   	case "add":
		SubmitContent($oid);
		break;
    	default:
    		ShowTypes();
    		break;
}
?>