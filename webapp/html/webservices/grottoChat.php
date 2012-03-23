<?php
/**
 * This file is part of GrottoCenter.
 *
 * GrottoCenter is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * GrottoCenter is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with GrottoCenter.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright Copyright (c) 2009-2012 Clément Ronzon
 * @license http://www.gnu.org/licenses/agpl.txt
 */
include("../../conf/config.php");
include("../../func/function.php");
include("../declaration.php");
header("Content-type: text/plain");
$frame = "function";
$action = (isset($_GET['action'])) ? $_GET['action'] : '';
$guest = (isset($_GET['guest'])) ? $_GET['guest'] : '';
$guest = urldecode(stripslashes($guest));
$delay = (isset($_GET['delay'])) ? $_GET['delay'] : 5000;
$delay = ($delay + 0)/1000; //en ms => s
$id = (isset($_GET['id'])) ? $_GET['id'] : '';
$message = (isset($_GET['message'])) ? $_GET['message'] : '';
$message = strip_tags(urldecode(stripslashes($message)));
$actionWhiteList = array("list", "refresh", "send");
if (!in_array($action, $actionWhiteList) || !allowAccess(chat_all)) {
  exit();
}
$answer = "";
define(ROOM_FILE_PATH, substr(__FILE__, 0, strlen(__FILE__)-31)."chat/room_".$id.".txt", true);
define(MAX_LINES, 500);
define(DELIMITER, "<d/>", true);
define(SLEEP_DELAY, 2*$delay);
define(TIME_OUT_DELAY, 24*$delay);
define(TIME_INDEX, 0);
define(GUEST_INDEX, 1);
define(MESSAGE_INDEX, 2);
define(PICTURE_INDEX, 3);
define(UNIX_TIME_STAMP, time());

function addStyle($n, $style)
{
  return "<span class=\"".$style."\">".$n."</span>";
}

function cropFile($filePath, $lineStamp)
{
  $fileArray = @file($filePath, FILE_TEXT);
  if (!$fileArray) {
    return false;
  }
  $lineCount = count($fileArray);
  if ($lineCount <= $lineStamp) {
    return true;
  }
  $lineOffset = $lineCount - $lineStamp;
  $array = array_slice($fileArray, $lineOffset, $lineStamp);
  $lines = implode("", $array);
  $handleW = @fopen($filePath, "w");
  if (!$handleW) {
    return false;
  }
  @fwrite($handleW, $lines);
  @fclose($handleW);
  return true;
}

function processMessagesArray($array)
{
  sort($array, SORT_STRING);
  $localGuest = "";
  $localMessage = "";
  $localTimeStamp = 0;
  $returnedArray = array();
  foreach ($array as $key => $value) {
    $addLine = "";
    $lineArray = explode(DELIMITER, $value);
    $localMessage = $lineArray[MESSAGE_INDEX];
    $localPicture = $lineArray[PICTURE_INDEX];
    if (abs($localTimeStamp - $lineArray[TIME_INDEX]) > TIME_OUT_DELAY) {
      $localTimeStamp = $lineArray[TIME_INDEX];
      $returnedArray[count($returnedArray)] = addStyle("Sent at ".date("h:i:s A", $localTimeStamp)." on ".date("l", $localTimeStamp), "lastdate");
    }
    if ($lineArray[GUEST_INDEX] != $localGuest) {
      $localGuest = $lineArray[GUEST_INDEX];
      $addLine = "<hr/>";
      if ($localPicture != "") {
        $addLine .= "<img src=\"../upload/avatars/".$localPicture."\" class=\"avatar\" alt=\"avatar\"/>";
      }
      $addLine .= addStyle($localGuest." : ", "username")."<br />";
    } else {
      //$addLine = "&nbsp;&nbsp;&nbsp;";
    }
    $addLine .= addStyle($localMessage, "usermessage");
    $returnedArray[count($returnedArray)] = $addLine;
    if ((abs(UNIX_TIME_STAMP - $lineArray[TIME_INDEX]) > TIME_OUT_DELAY) && ($key == (count($array) - 1))) {
      $localTimeStamp = $lineArray[TIME_INDEX];
      $returnedArray[count($returnedArray)] = addStyle("Sent at ".date("h:i:s A", $localTimeStamp)." on ".date("l", $localTimeStamp), "lastdate");
    } 
  } 
  return $returnedArray;
}

function processGuestsArray($activities, $nicknames)
{
  asort($nicknames, SORT_STRING);
  $localGuest = "";
  $localTimeStamp = 0;
  $_SESSION['chat_guests'] = (isset($_SESSION['chat_guests']))? array_unique($_SESSION['chat_guests']) : array();
  $returnedArray = array();
  foreach ($nicknames as $key => $localGuest) {
    $addLine = "";
    $localTimeStamp = $activities[$key];
    if ($localTimeStamp == "") {
      $offset = SLEEP_DELAY;
    } else {
      $offset = abs(UNIX_TIME_STAMP - $localTimeStamp);
    }
    if ($offset <= SLEEP_DELAY) {
      if (!in_array($localGuest, $_SESSION['chat_guests'])) {
        $addLine = "+ ".addStyle($localGuest, "entering_username");
        $_SESSION['chat_guests'][] = $localGuest;
      } else {
        if ($localGuest == $_SESSION['user_nickname']) {
          $usernameStyle = "username_myself";
        } else {
          $usernameStyle = "username";
        }
        $addLine = addStyle($localGuest, $usernameStyle);
      }
    } elseif (in_array($localGuest, $_SESSION['chat_guests'])) {
      if ($localGuest != $_SESSION['user_nickname']) {
        $addLine = "- ".addStyle($localGuest, "leaving_username");
      }
      unset($_SESSION['chat_guests'][array_search($localGuest, $_SESSION['chat_guests'])]);
    }
    if ($addLine != "") {
      $returnedArray[count($returnedArray)] = $addLine;
    }
  }
  return $returnedArray;
}

switch ($action) {
  case "list":
    if ($delay != "") {
      $lastActivityArray = readSessionsVar("user_lastactivitydate");
      $nicknameArray = readSessionsVar("user_nickname");
      $list = processGuestsArray($lastActivityArray, $nicknameArray);
      $answer = implode("<br />", $list);
    }
    break;
  case "refresh":
    if ($id != "") {
      $_SESSION['user_lastactivitydate'] = UNIX_TIME_STAMP;
      $roomArray = @file(ROOM_FILE_PATH, FILE_TEXT | FILE_SKIP_EMPTY_LINES);
      $roomArray = processMessagesArray($roomArray);
      $answer = implode("<br />", $roomArray);
    }
    break;
  case "send":
    if ($id != "" && $message != "" && $guest == $_SESSION['user_nickname']) {
      $avatar = ($_SESSION['user_file'] == "") ? "default_avatar.png" : $_SESSION['user_file'];
      $lineArray = array(TIME_INDEX => UNIX_TIME_STAMP, GUEST_INDEX => $guest, MESSAGE_INDEX => replaceBannedWords($message), PICTURE_INDEX => $avatar);
      $line = implode(DELIMITER, $lineArray)."\n";
      $handleW = @fopen(ROOM_FILE_PATH, "a+b");
      if (!$handleW) {
        $answer = 0;
        break;
      }
      @fwrite($handleW, $line);
      @fclose($handleW);
      if (!cropFile(ROOM_FILE_PATH, MAX_LINES)) {
        $answer = 0;
        break;
      }
      $answer = 1;
    } else {
      $answer = 0;
    }
    break;
  default:
    exit();
}

echo $answer;
?>
