<?php

if (!defined('ADMIN_FILE')) {
	die ("Access Denied");
}


$module_name = "UserSubmissions";

$aid = substr($aid, 0,25);
$query = $db->sql_query("SELECT title, admins FROM ".$prefix."_modules WHERE title='$module_name'");

$row = $db->sql_fetchrow($query);

$admins = $row['admins'];


$auth_user = 0;

$query2 = $db->sql_query("SELECT name, radminsuper FROM ".$prefix."_authors WHERE aid='$aid'");


$row2 = $db->sql_fetchrow($query2);
$name = $row2['name'];
$radminsuper = $row2['radminsuper'];


if ($row2)
{
  if (stristr(",".$admins, ",".$name.",") )
     $auth_user = 1;
}


if ($radminsuper == 1 || $auth_user == 1) 
{
	
function UserSubs()
{
	global $db, $prefix;
	global $admin_file;
	
	include("header.php");
	GraphicAdmin();
	$contenttypes = array('encyclo'=>"Encyclopedia");
	title(_USERSUBADMIN);
	
	OpenTable();
	
	echo "<center>";
	$sql = "select id, title, type, user_id, user_name,  UNIX_TIMESTAMP(date) AS theDate from ".$prefix."_usersubmissions order by date ASC";
	//$sql = "select id, title, type, user_id, user_name,  date FROM ".$prefix."_usersubmissions order by date desc";
	
	$result = $db->sql_query($sql);
	
	echo "<table border=1>";
	echo "<tr><td>ID</td><td>Submission Type</td>
	<td>Title</td><td>User</td><td>Date</td>
	<td colspan=2>Functions</td></tr>";
	while($row = $db->sql_fetchrow($result))
	{
		$type = $row['type'];
		$id = $row['id'];
		$title = $row['title'];
		echo "<tr><td>$id</td>";
		echo "<td align=\"center\">".$contenttypes[$type]."</td>";
		echo "<td>$title</td>";
		echo "<td>".$row[user_name]."</td>";
		echo "<td>".date("l dS of F Y h:i:s A", $row['theDate'])."</td>";
		echo "<td><a href=\"".$admin_file.".php?op=UserSubApprove&sid=$id\"> <img  src=\"images/unban.gif\" alt=\"Approve\"   title=\"Approve\" border=0></a></td>";

		echo "<td><a href=\"".$admin_file.".php?op=UserSubEdit&sid=$id\"><img src=\"images/edit.gif\" alt=\"Edit\" title=\"Edit\" border=\"0\"></a></td>";
		echo "<td><a href=\"".$admin_file.".php?op=UserSubDelete&sid=$id\"><img src=\"images/delete.gif\" alt=\"Delete\" title=\"Delete\" border=\"0\"></a></td>";
		echo "</tr>";
	}
	echo "</table>";
	echo "</center>";
	CloseTable();
	include("footer.php");
	
	
}
function UserSubApprove($sid)
{
		global $db, $prefix;
		
		$sid = intval($sid);
		if ($sid<0) exit;
		
		include("header.php");
		GraphicAdmin();
		
		OpenTable();
		approve($sid);
		
		echo "The submission has been approved.";
		CloseTable();
		include("footer.php");
		
	
}
function UserSubDelete($sid)
{
	$sid = intval($sid);
	
	if (!$sid) return;
	
	removeFromPending($sid);
	Header("Location: ".$admin_file.".php?op=UserSubs");
	
	
}
function UserSubEdit($sid)
{
	global $admin_file, $db,$prefix;
		
	$sid = intval($sid);
	
	$sql = "select * from ".$prefix."_usersubmissions where id=$sid";	
	$result = $db->sql_query($sql);	
	$row = $db->sql_fetchrow($result);		
	if (!$row)
	  Header("Location: ".$admin_file.".php?op=UserSubs");
	
	
	$arry = unserialize($row['data']);
	$row['data'] = $arry;
	$type = $row['type'];

	switch($type) 
	{
		case "encyclo":
			$ok = editEncycloEntry($sid, $row);

			break;
		default:
		  Header("Location: ".$admin_file.".php?op=UserSubs");
	
	}	
	
}
function editEncycloEntry($sid, $row)
{
	global $db, $prefix, $admin_file;
	
	$usr_eid = $row['parent_id'];
		
	$eid = intval($eid);
	$data = $row['data'];
	$usr_text = $data['text'];
	$usr_title = $data['title'];
	
	include("header.php");
	GraphicAdmin();
			
	OpenTable();
		
	echo "  <form action=\"".$admin_file.".php?op=UserSubs\" method=\"post\">\n
			<p><b>Title:</b><br>\n
			<input name=\"usr_title\" size=\"50\" type=\"text\" value=\"$usr_title\">\n<br>
			<br>
			<b>Term Text:</b><br>
			If you want multiple pages you can write <b>&lt;!--pagebreak--&gt;</b> where you
			want to cut.<br>
			<textarea name=\"usr_text\" cols=\"60\" rows=\"20\">$usr_text</textarea><br>
			<br>
			<b>Encyclopedia:</b><br>
			<select name=\"usr_eid\">";
	
		$sql = "SELECT eid, title FROM " . $prefix . "_encyclopedia WHERE active='1'";
	
	
	$result = $db->sql_query($sql);
	while ($row2 = $db->sql_fetchrow($result))
	{
	$eid = intval($row2['eid']);
	$title = stripslashes($row2['title']);
	echo "<option value=\"$eid\" ";
	
	if ($eid == $usr_eid) 
	  echo "selected";
	
	echo ">$title</option>\n";
	}
	  echo "</select>\n<br><br>";
	  
	 echo "<br><br>";
	
	 echo "<input name=\"user_name\" value=\"".$row['user_name']."\" type=\"hidden\">\n";
	 echo "<input name=\"user_id\" value=\"".$row['user_id']."\" type=\"hidden\">\n";
	 echo "<input name=\"sid\" value=\"$sid\" type=\"hidden\">\n";
	 echo " <input name=\"op\" value=\"UserSubAccept\" type=\"hidden\">";
	echo "<input value=\"Accept\" type=\"submit\">";
	echo "<a href=\"".$admin_file.".php?op=UserSubDelete&sid=$sid\">Delete</a>";

echo "<a href=\"".$admin_file.".php?op=UserSubs\">Ignore</a>";
echo "</form>";
CloseTable();
include("footer.php");
}
function approve($sid)
{
	
	global $db,$prefix;
	
	$sql = "select * from ".$prefix."_usersubmissions where id=$sid";
	$conn = $db->sql_query($sql);
	$row = $db->sql_fetchrow($conn);
	//if (!$row)
	//	Header("Location: modules.php?name=UserSubmissions");
	$arry = unserialize($row['data']);
	$row['data'] = $arry;
	$type = $row['type'];
	switch($type) 
	{
		case "encyclo":
			$ok = insertEncycloEntry($row);
			if ($ok) removeFromPending($sid);
			break;
	}
}
function removeFromPending($sid)
{
	global $db, $prefix;
	$sql = "delete from ".$prefix."_usersubmissions where id=$sid";
	$db->sql_query($sql);
	
	
}
function insertEncycloEntry( $row)
{
	global $db, $prefix;
	
	$eid = $row['parent_id'];
	
	$eid = intval($eid);
	$data = $row['data'];
	$text = $data['text'];
	$title = $data['title'];
	
	$text = stripslashes(FixQuotes($text));
	$title = stripslashes(FixQuotes($title));
	
	$text .= "<br><b>Submitted by: ".$row['user_name']."<b><br>";
	$db->sql_query("insert into ".$prefix."_encyclopedia_text values(NULL, '$eid', '$title', '$text', '0')");
   
  	return true;
  	
	
	
}


function UserEncycloAccept( $sid, $title, $text, $eid, $user_name, $user_id)
{
	global $db, $prefix, $admin_file;
	
	
	$sid = intval($sid);
	
	$eid = intval($eid);
	
	if ($sid<1 || $eid<1) 
	  Header("Location: ".$admin_file.".php?op=UserSubs");
	
	$text .= "<br><b>Submitted by: ".$user_name."<b><br>";
	
	$text = stripslashes(FixQuotes($text));
	$title = stripslashes(FixQuotes($title));
	
	$db->sql_query("insert into ".$prefix."_encyclopedia_text values(NULL, '$eid', '$title', '$text', '0')");
   
   
   	removeFromPending($sid);
   	
  	Header("Location: ".$admin_file.".php?op=UserSubs");
	
	
	
}
switch($op) {
    case "UserSubEdit":
    UserSubEdit($sid);
    break;
    case "UserSubDelete":
    UserSubDelete($sid);
    break;
    case "UserEncycloAccept":
    UserSubAccept($sid, $usr_title, $usr_text, $usr_eid, $user_name, $user_id );
    break;
    case "UserSubs":
    UserSubs();
    break;
    
}
} 
 else {
	include("header.php");
	GraphicAdmin();
	OpenTable();
	echo "<center><b>"._ERROR."</b><br><br>You do not have administration permission for module \"$module_name\"</center>";
	CloseTable();
	include("footer.php");
}
?>