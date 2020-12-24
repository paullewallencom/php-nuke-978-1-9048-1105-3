<?php

/************************************************************************/
/* PHP-NUKE: Web Portal System                                          */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2002 by Francisco Burzi                                */
/* http://phpnuke.org                                                   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
/*         Additional security & Abstraction layer conversion           */
/*                           2003 chatserv                              */
/*      http://www.nukefixes.com -- http://www.nukeresources.com        */
/************************************************************************/


    if ( !defined('BLOCK_FILE') ) 
    {
      Header("Location: ../index.php");
      die();
    }

global $prefix, $db;

$today = date('d-m-Y');

$sql = "SELECT * from ".$prefix."_dinoportal_dotd WHERE day='$today'";

$result = $db->sql_query($sql);
$content = "";
$dino_title = "";
$image = "";

 $numrows = $db->sql_numrows($result);
 if ($numrows)
 {
    $row = $db->sql_fetchrow($result);
    $dino_title = $row['title'];
    $image = $row['image'];
 }
 else
 {
    $filename = "blocks/dotd_list.txt";
    $possibles =@ file($filename);

    if ($possibles)
    {
        $choice = rand(1, count($possibles));
        $imp = explode("," , $possibles[$choice-1]);
        $dino_title = $imp[0];
        $image = $imp[1];
        $sql = "INSERT INTO ".$prefix."_dinoportal_dotd(day,title,image) 
                VALUES ('$today', '$dino_title', '$image')";
        $result = $db->sql_query($sql);
    }
    $choice = rand(1, count($possibles));

    $imp = explode("," , $possibles[$choice-1]);
    $dino_title = $imp[0];
    $image = $imp[1];    
 }
if ($dino_title)
{
$content = "Today's dinosaur is:<br><center><b>$dino_title</b><center><br>";
    $content .= "<center><img src=\"$image\" alt=\"$dino_title\"></center><br>";
}	
?>
