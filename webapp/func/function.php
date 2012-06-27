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
 * @copyright Copyright (c) 2009-2012 Cl�ment Ronzon
 * @license http://www.gnu.org/licenses/agpl.txt
 */
function resetConvertedFiles($filesArray)
{
  $destPath = "html";
  $deletedFilesArray = $filesArray["fileName"];
  if ($d = opendir($destPath)) {
    while (false !== ($file = readdir($d))) {
      if ($file != '.' && $file != '..' && in_array(substr($file, 0, strlen($file)-7).".php", $deletedFilesArray)) {
      	if (@unlink($destPath."/".$file)) {
          echo start_comment."File ".$destPath."/".$file." unlinked !".end_comment."\n";
      	}
      }
    }
  }
}

function convertFiles($lang, $filesArray)
{
  //HTML files
  $sourcePath = "html/source";
  $destPath = "html";
  foreach($filesArray["fileName"] as $sourceIndex => $sourceFile) {
    convertFile($sourceFile, $sourcePath, $destPath, $filesArray["frame"][$sourceIndex], $lang);
  }
  //Webservices
  $sourcePath = "html/webservices/source";
  $destPath = "html/webservices";
  convertFile("getPropertiesPlain.php", $sourcePath, $destPath, "loader", $lang);
	convertFile("getSearchResultPlain.php", $sourcePath, $destPath, "general", $lang);
}

function userIsConnected()
{
  return (isset($_SESSION['user_connected'])) ? $_SESSION['user_connected'] : false;
}

function logToFile($content, $filename)
{
  $path = substr(__FILE__, 0, strlen(__FILE__)-17)."log/";
  $handle = fopen($path.$filename.".".date("Y-m-d", time()).".log", "a+b");
  $date = date("r", time());
  fwrite($handle, $date.$content."\n");
  fclose($handle);
}

function convertFile($sourceFile, $sourcePath, $destPath, $frame, $lang)
{
  // Tag traducted are :
  // <convert>#label=000<convert>
  // Where 000 is the id of the label in T_label table
  $mainSeparator = "<convert>";
  $secSeparator = "#label=";
  // Preparing for live label translation ... in further version ... "translation.php" is located in "html/source/essais" folder
  $secSeparatorT = "#trslb=";
  $startPsn = strlen($secSeparator);
  $destFile = getFileName($sourceFile)."_".$lang.".".getFileExtension($sourceFile);
  //If the destination file dose not exists or is outdated
  /*echo $sourcePath."/".$sourceFile."<br />";
  echo $destPath."/".$destFile."<br />";*/
  if (!fileExists($destPath."/".$destFile,"r") || (fileExists($destPath."/".$destFile,"r") && (getFilemtime($sourcePath."/".$sourceFile) >= getFilemtime($destPath."/".$destFile)))) {
    echo start_comment.$destFile." refreshed".end_comment."\n";
    //get the labels array
    $labelArray = getLabelArray($frame, $lang);
    //Create or replace the destination file
    $handleW = @fopen($destPath."/".$destFile, "w");
    $handleR = @fopen($sourcePath."/".$sourceFile, "r");
    if ($handleR && $handleW) {
      while (!feof($handleR)) {
        $buffer = fgets($handleR, 4096);
        //If there is something to convert
        if (strpos($buffer, $mainSeparator) !== false) {
          //Get the elements
          $data = explode($mainSeparator,$buffer);
          $buffer = "";
          for ($i=0;$i<count($data);$i++) { //for ($i=0;$i<=count($data);$i++) {
            //If there is a label code
            if (strpos($data[$i], $secSeparator) !== false || strpos($data[$i], $secSeparatorT) !== false) {
              $translation_supported = (strpos($data[$i], $secSeparatorT) !== false);
              //Get the label code and replace it
              $data[$i] = substr($data[$i],$startPsn,strlen($data[$i])-$startPsn);
              if ($translation_supported) {
                $data[$i] = "<?php if (allowAccess(translation_view_all)) { ?"."><span onclick=\"JavaScript:openWindow('translation_".$lang.".php?id=".$data[$i]."', '', 800, 600);\" class=\"translation_label\"><?php } ?".">".$labelArray[$data[$i]]."<?php if (allowAccess(translation_view_all)) { ?"."></span><?php } ?".">";
              } else {
                $data[$i] = isset($labelArray[$data[$i]]) ? $labelArray[$data[$i]] : null;
              }
            }
            //Fill the buffer
            $buffer .= $data[$i];
          }
        }
        //Write the result into the destination file
        fwrite($handleW, $buffer);
      }
      fclose($handleR);
      fclose($handleW);
    }else{
        echo "error yiyi8797";
    }
  }
}

function getFilemtime($url)
{
  if (fileExists($url)) {
    return @filemtime($url);
  } else {
    return 0;
  }
}

function getHistoSrc($sql,$column)
{
  $array = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  $data = array();
  $argData = "";
  $max = 0;
  for($i=0;$i<$array['Count'];$i++) {
    $data[$array[$i][$column]] += 1;
    if($max<$array[$i][$column]){
      $max = $array[$i][$column];
    }
  }
  for($i=0;$i<=$max;$i++) {
    if(empty($data[$i])){
      $data[$i] = 0;
    }
    $argData .= $data[$i]."|";
  }
  $argData = substr($argData,0,strlen($argData)-1);
  return "../images/gen/getChart.php?type=histo&amp;data=".$argData;
}

function getFileName($file)
{
  $fileArray = explode(".",$file);
  return $fileArray[0];
}

function getFileExtension($file)
{
  $fileArray = explode(".",$file);
  return isset($fileArray[1]) ? $fileArray[1] : null;
}

function fileExists($url)
{
/*  if (@fclose(@fopen($url, 'r'))) {
    return true;
  } else {
    return false;
  }*/
/*  return ($ch = curl_init($url)) ? @curl_close($ch) || true : false;
*/
  return file_exists(realpath($url));
}

function getScriptJS($localFile) {
  $langSeparator = "_";
  $prop = pathinfo($localFile);
  $destFile = getFileName($prop['basename']);
  if (strpos($prop['basename'], $langSeparator) !== false) {
    $destFile = substr($destFile, 0, strrpos($destFile, $langSeparator));
  }
  $destPath = "../scripts/generated/".$destFile.".js";
  return $destPath;
}

function appProp()
{
  if (strpos(__FILE__, 'clementronzon') !== false) {
		$bdd = "clementronzon";
	} else {
		$bdd = "grottoce";
	}
  $sql = "SELECT * FROM `".$bdd."`.`T_application` WHERE `Is_current` = 'YES'";
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  return $data[0];
}

function activateAccount($id, $code)
{
  $return = 0;
  if ($id != "" && $code != "") {
    $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_caver` SET `Activated` = 'YES' WHERE `Id` = ".$id." AND `Activation_code` = '".$code."'";
    $req = execSQL($sql, "function", __FILE__, __FUNCTION__);
    $return = $req['mysql_affected_rows'];
  }
  return $return;
}


function sessionCount()
{
  if ($d = opendir(session_save_path())) {
    $count = 0;
    //session.gc_maxlifetime = 1440s (24 min)
    $session_maxlifetime = ini_get('session.gc_maxlifetime'); //session.gc_maxlifetime is the life time of the server's session (session.cache_expire is for the cookies one)
    while (false !== ($file = readdir($d))) {
      if ($file != '.' && $file != '..') {
      	if ( time() - getFilemtime(session_save_path().'/'.$file) < $session_maxlifetime ) { //filemtime = UNIX timestamp of the last modification of the file
        	$count++;
        }
      }
    }
  }
  return $count;
}

/*function sessionIsActive($time) {
  $session_maxlifetime = ini_get('session.gc_maxlifetime');
  if ( time() - $time < $session_maxlifetime ) {
  	return true;
  } else {
    return false;
  }
}*/

function sessionIsActive($caverId)
{
  if ($d = opendir(session_save_path())) {
    $count = 0;
    $session_maxlifetime = ini_get('session.gc_maxlifetime');
    while (false !== ($file = readdir($d))) {
      $path = session_save_path().'/'.$file;
      if ($file != '.' && $file != '..' && readSessionVar($path, "user_id") == $caverId) {
      	if (time() - getFilemtime($path) < $session_maxlifetime ) {
        	return true;
        }
      }
    }
  }
  return false;
}

/*Deprecated*/
function getConnectedCaversArray()
{
  return readSessionsVar("user_id");
}

function set_quotes(&$item, $key, $quote)
{
  $item = $quote.$item.$quote;
}

/*Deprecated, use implode() !!! */
function concat_WS($array, $separator) 
{
  $concat = "";
  for ($i=0;$i<count($array);$i++) {
    if (trim($array[$i]) != "") {
      $concat .= $array[$i].$separator;
    }
  }
  if ($concat != "") {
    $concat = substr($concat,0,strlen($concat)-strlen($separator));
  }
  return $concat;
}

/*Deprecated since 2.0*/
function echoInnerObject($innerArray)
{
  foreach($innerArray as $innerLine) {
    if ($innerLine <> "") {
      echo "innerObject = innerObject + \"".addslashes($innerLine)."\";\n";
    }
  }
}

function getInnerLine($dataToCheck,$data,$label = "",$link = "",$unit = "",$cplData = "",$cplLabel = "",$cplUnit = "")
{
  if (isset($dataToCheck)) {
    if ($link != "") {
      $ldata = "<a href=\"".$link."\">".$data."</a>";
    } else {
      $ldata = $data;
    }
    if ($label != "") {
      $line = "<span class=\"details_label\">".$label."</span> <span class=\"details_data\">".$ldata."</span> <span class=\"details_unit\">".$unit."</span>";
    } else {
      $line = "<span class=\"details_data\">".$ldata."</span><span class=\"details_unit\"> ".$unit."</span>";
    }
    if($cplData != "") {
      $line .= "<span class=\"details_add\"> (".$cplLabel." ".$cplData." ".$cplUnit.")</span>";
    }
    if ($label != "") {
      $line = "<div class=\"detail_line\">".$line."</div>";
    }
    /*if ($label != "") {
      $line .= "<br />\n";
    }*/
    return $line;
  } else {
    return "";
  }
}

function convertYN($valueYN,$Yes,$No)
{
  if ($valueYN == "YES") {
    return $Yes;
  } else {
    return $No;
  }  
}

/*function throwSessions()
{
  if ($d = opendir(session_save_path())) {
    $count = 0;
    while (false !== ($file = readdir($d))) {
      if ($file != '.' && $file != '..' && $file != "sess_".session_id()) {
        if ($unlinked = @unlink(session_save_path()."/".$file)) {
          $count++;
        }
      }
    }
  }
  return $count;
}*/

function throwSessions()
{
  if ($d = opendir(session_save_path())) {
    $count = 0;
    while (false !== ($file = readdir($d))) {
      if ($file != '.' && $file != '..' && $file != "sess_".session_id()) {
        if (resetSessionVar(session_save_path()."/".$file,"Application_data_set")) {
          $count++;
        }
      }
    }
  }
  return $count;
}

function resetSessionVar($filePath, $sessionVar)
{
  $mainSeparator = ";";
  $content = "";
  $return = false;
  //Open the file
  $handleR = @fopen($filePath, "r");
  if ($handleR) {
    while (!feof($handleR)) {
      $buffer = fgets($handleR, 4096);
      //if (strpos($buffer, $sessionVar) !== false) {
        $data = explode($mainSeparator,$buffer);
        for ($i=0;$i<=count($data);$i++) {
          if (strpos($data[$i], $sessionVar) === false) {
            $content .= $data[$i].$mainSeparator;
          }
        }
      //}
    }
    fclose($handleR);
    $handleW = @fopen($filePath, "w");
    if ($handleR) {
      fwrite($handleW, $content);
      fclose($handleW);
      $return = true;
    }
  }
  return $return;
}

function readSessionVar($filePath, $sessionVar)
{
  $mainSeparator = "\{|;\}|;";
  $secSeparator = '|';
  $thirdSeparator = ':';
  $buffer = "";
  $arrayName = "";
  $flag = false;
  $handleR = @fopen($filePath, "r");
  if ($handleR) {
    while (!feof($handleR)) {
      $buffer .= fgets($handleR, 4096);
    }
  }
  @fclose($handleR);
  $data = split($mainSeparator,$buffer);
  for ($i=0;$i<=count($data);$i++) {
    $field = explode($secSeparator,$data[$i]);
    if ($flag && count($field) == 1) {
      $fieldValue .= $field[0];
    } else {
      $fieldName = $field[0];
      $fieldCaract = explode($thirdSeparator, $field[1]);
      $fieldType = $fieldCaract[0];
      if ($fieldType == "a") {
        $flag = true;
        $arrayName = $fieldName;
      } else {
        if ($flag) {
          if ($arrayName == $sessionVar) {
            return $fieldValue;
          }
          $flag = false;
        }
      }
      if (count($fieldCaract) > 2) {
        $fieldLength = $fieldCaract[1];
        $fieldValue = nl2br($fieldCaract[2]);
      } else {
        $fieldLength = "";
        $fieldValue = nl2br($fieldCaract[1]);
      }
      if ($fieldType == "s") {
        $fieldValue = substr($fieldValue, 1, strlen($fieldValue)-2);
      }
    }
    if($fieldType != "a") {
      if ($fieldName == $sessionVar) {
        return $fieldValue;
      }
    }
  }
  return "";
}

function readSessionsVar($sessionVar)
{
  $array = array();
  if ($d = opendir(session_save_path())) {
    $session_maxlifetime = ini_get('session.gc_maxlifetime');
    while (false !== ($file = readdir($d))) {
      $path = session_save_path().'/'.$file;
      if ($file != '.' && $file != '..') {
      	if (time() - getFilemtime($path) < $session_maxlifetime ) {
          $value = readSessionVar($path, $sessionVar);
          if ($value != "") {
            $array[$path] = $value;
        	}
        }
      }
    }
  }
  return array_unique($array);
}

function throwMySession()
{
  if ($d = opendir(session_save_path())) {
    $file = session_save_path()."/sess_".session_id();
    if (file_exists($file)) {
      return @unlink($file);
    }
  }
}

function getReferentCavers($leader_group_id)
{
  $sql = "SELECT Id_caver FROM J_caver_group WHERE Id_group = ".$leader_group_id;
  $array = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  $leaders = array();
  for ($i=0;$i<=$array['Count'];$i++) {
    $leaders[] .= $array[$i]['Id_caver']; 
  }
  return array_unique($leaders);
}

function reportError($error, $file, $frame, $function, $comment)
{
  $error_number = mysql_errno();
  $msg = '<script type="text/javascript">';
  $msg .= getCDataTag(true);
  $msg .= 'self.location.href = "'.$_SESSION['Application_url'].'/html/error_'.$_SESSION['language'].'.php?frame='.$frame.'";';
  $msg .= getCDataTag(false);
  $msg .= '</script>';
  $error_id = insertError($error, $file, $frame, $function, $comment);
  sendErrorMail($error_id, $frame);
  return $msg.start_comment.$error." Err # ".$error_number.end_comment;
}

function insertError($error, $file, $frame, $function, $comment)
{
  $error = addslashes($error);
  $comment = addslashes($comment);
  $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`T_error` ";
  $sql .= "(`Id_caver`, `Date`, `Error`, `File`, `Frame`, `Function`, `Comment`) VALUES ";
  $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
  $sql .= "(".returnDefault($userId,'text');
  $sql .= ",Now(),";
  $sql .= returnDefault($error,'text').",";
  $sql .= returnDefault($file,'text').",";
  $sql .= returnDefault($frame,'text').",";
  $sql .= returnDefault($function,'text').",";
  $sql .= returnDefault($comment,'text').")";
  $connect_db = connect();
  $req = mysql_query($sql) or die(print('Erreur SQL !<br />'.$sql.'<br />'.mysql_error()));
  $id = mysql_insert_id($connect_db);
  close($connect_db);
  return $id;
}

function insertWarning($warning, $frame, $comment)
{
  $warning = addslashes($warning);
  $comment = addslashes($comment);
  $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`T_warning` ";
  $sql .= "(`Id_caver`, `Date`, `Warning`, `Frame`, `Comment`) VALUES ";
  $sql .= "(".returnDefault($_SESSION['user_id'],'text');
  $sql .= ",Now(),";
  $sql .= returnDefault($warning,'text').",";
  $sql .= returnDefault($frame,'text').",";
  $sql .= returnDefault($comment,'text').")";
  $req = execSQL($sql, "function", __FILE__, __FUNCTION__);
  $id = $req['mysql_insert_id'];
  return $id;
}

function sendErrorMail($error_id, $frame)
{
  $data = getObjectData($error_id, "T_error");
	$date = date('l dS \of F Y h:i:s A');
	$mail_dest = getAdminContact();
	$subject = " ".$frame." ERROR !";
  $mail_body = $data;
	$mail_body .= "\nUser id : ";
	$mail_body .= $_SESSION['user_id']."\n";
	$mail_body .= "Date time : ";
	$mail_body .= $date."\n";
	$mail_body .= "<b>Error id : ".$error_id."</b>\n";
	$mail_body .= "<b>Source frame : ";
	$mail_body .= $frame."</b>\n";
	$mail_body .= print_r(getDataFromSQL("SHOW PROCESSLIST", __FILE__, "function", __FUNCTION__), true)."\n";
	$mail_body .= print_r(explode('  ', mysql_stat()), true)."\n";
	sendMail($mail_dest,$subject,nl2br($mail_body));
}

function takeOver($category,$id)
{
  $return = false;
  $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_".$category."` SET Locked = 'YES', Date_locked = Now(), Id_locker = ".$_SESSION['user_id']." WHERE (Locked = 'NO' OR Id_locker = ".$_SESSION['user_id'].") AND Id = ".$id;//, Date_reviewed = Now()
  $req = execSQL($sql, "function", __FILE__, __FUNCTION__);
  if ($req['mysql_affected_rows'] == 1) {
    $return = true;
  }
  return $return;
}

function backOver($category,$id)
{
  $return = false;
  $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_".$category."` SET Locked = 'NO', Id_locker = ".$_SESSION['user_id']." WHERE Locked = 'YES' AND Id = ".$id;//, Date_reviewed = Now()
  $req = execSQL($sql, "function", __FILE__, __FUNCTION__);
  if ($req['mysql_affected_rows'] == 1) {
    $return = true;
  }
  return $return;
}

function trackAction($type, $id, $names)
{
	if (!is_array($names)) {
		$names = array('table' => array($names), 'column' => array('Id'));
	}
	$tableName = $names['table'][0];
	$column_id = $names['column'][0];
  $main_separator = "_";
  $type_array = explode($main_separator,$type);
  $mail_subject = "Tracker : ";
	
	$sql = "SELECT * ";
	$sql .= "FROM `".$_SESSION['Application_host']."`.`".$tableName."` ";
	$sql .= "WHERE `".$column_id."` = ".$id;
	$dataArray = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  if ($type_array[1] != "user") {
    $author_name = $_SESSION['user_login'];
    if ($dataArray[0]['Name'] != "") {
      $object_name = $dataArray[0]['Name'];
    } else {
      $object_name = $dataArray[0]['Title'];
    }
  } else {
    $author_name = $dataArray[0]['Login'];
    $object_name = "";
  }
	$change_codes = array("insert" => 1, "edit" => 2, "pwd" => 2, "approve" => 2, "cancel" => 2, "delete" => 3);
	$change_code = (array_key_exists($type_array[0], $change_codes)) ? $change_codes[$type_array[0]] : 0;
	$data = "";
	foreach($names['table'] as $key => $table) {
		$data .= getObjectData($id, $table, $names['column'][$key], $change_code);
	}
  $mail_body = $data;
  switch ($type_array[0]) {
    case "delete":
      $mail_subject .= "a ".$type_array[1]." has been deleted : ".$object_name." by ".$author_name.".";
    break;
    case "insert":
      $mail_subject .= "a new ".$type_array[1]." has been added : ".$object_name." by ".$author_name.".";
    break;
    case "edit":
      $mail_subject .= "a ".$type_array[1]." has been changed : ".$object_name." by ".$author_name.".";
    break;
    case "pwd":
      $mail_subject .= "a new password has been sent : ".$object_name." by ".$author_name.".";
    break;
    case "approve":
      $mail_subject .= "a ".$type_array[1]." has been approved : ".$object_name." by ".$author_name.".";
    break;
    case "cancel":
      $mail_subject .= "a ".$type_array[1]." has been removed : ".$object_name." by ".$author_name.".";
    break;
    default:
      $mail_body = "Function trackAction, type = default : ".$id."|".$tableName;
      $mail_subject .= "tracker error !!";
    break;
  }
  $warning_id = insertWarning($mail_subject, "", $mail_body);
  $mail_body .= "\n <b>Warning Id : ".$warning_id."</b>";
  $mail_dest = $_SESSION['Application_mail'];//getAdminContact();
  sendMail($mail_dest,$mail_subject,$mail_body,"","",false);
}

function getObjectData($id, $tableName, $column_id='Id', $change_code=0)
{
  /*$change_code:
			0: undefined
			1: insertion
			2: update
			3: deletion
	*/
  $separator = "</td><td>";
	$separator_h = "</th><th>";
  $sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`".$tableName."` WHERE `".$column_id."` = ".$id;
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
	$back_sql = "";
	$html = "";
  if($data['Count'] > 0) {
		for($i=0;$i<$data['Count'];$i++) {
			$returnColumn = "";
			$returnValue = "";
			switch($change_code) {
				case 2:
					$back_sql .= "UPDATE `".$_SESSION['Application_host']."`.`".$tableName."` SET ";
					break;
				case 3:
					$back_sql .= "INSERT INTO `".$_SESSION['Application_host']."`.`".$tableName."` VALUES (";
					break;
			}
			foreach($data[$i] as $key => $value) {
				if($change_code == 2) {
					$back_sql .= "`".$key."`=";
				}
				if ($value == '') {
					$back_sql.= "NULL, ";
					$value = '&nbsp;';
				} else {
					$back_sql.= "'".$value."', ";
				}
				$returnColumn .= $key.$separator_h;
				$returnValue .= $value.$separator;
			}
			$back_sql = substr($back_sql,0,strlen($back_sql)-2);
			switch($change_code) {
				case 2:
					$back_sql .= " WHERE `".$column_id."` = ".$id.";<br />";
					break;
				case 3:
					$back_sql .= ");<br />";
					break;
			}
			if ($i == 0) {
				$html .= "<table border=\"1\">";
				$returnColumn = substr($returnColumn,0,strlen($returnColumn)-4);
				$html .= "<tr><th>".$returnColumn."</tr>";
			}
			$returnValue = substr($returnValue,0,strlen($returnValue)-4);
			$html .= "<tr><td>".$returnValue."</tr>";
			if ($i == ($data['Count'] - 1)) {
				$html .= "</table>";
			}
			if ($change_code > 1 && $i == ($data['Count'] - 1)) {
				$html .= "<div><b>Recovery script:</b><br />".$back_sql."</div>";
			}
		}
  }
	return $html;
}

function getAdminContact()
{
  $groupedMail = "";
  $groupId = 1; //For administrators
  $sql = "SELECT GROUP_CONCAT(DISTINCT cr.Contact ORDER BY cr.Contact SEPARATOR ', ') AS List FROM `".$_SESSION['Application_host']."`.`T_caver` cr INNER JOIN `".$_SESSION['Application_host']."`.`V_caver_right` v ON cr.Id = v.Id_caver WHERE v.Id_group = ".$groupId;
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  $groupedMail = $data[0]['List'];
  return $groupedMail;
}

function getModContact()
{
  $groupedMail = "";
  $groupId = 2; //For moderators
  $sql = "SELECT GROUP_CONCAT(DISTINCT cr.Contact ORDER BY cr.Contact SEPARATOR ', ') AS List FROM `".$_SESSION['Application_host']."`.`T_caver` cr INNER JOIN `".$_SESSION['Application_host']."`.`V_caver_right` v ON cr.Id = v.Id_caver WHERE v.Id_group = ".$groupId;
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  $groupedMail = $data[0]['List'];
  return $groupedMail;
}

function getContactForMessage($caver_id)
{
  $contact = "";
  $sql = "SELECT Contact FROM `".$_SESSION['Application_host']."`.`T_caver` WHERE Id = ".$caver_id;
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  $contact = $data[0]['Contact'];
  return $contact;
}

function generatePassword($length=9, $strength=0) {
    $vowels = 'aeuy';
    $consonants = 'bdghjmnpqrstvz';
    if ($strength >= 1) {
        $consonants .= 'BDGHJLMNPQRSTVWXZ';
    }
    if ($strength >= 2) {
        $vowels .= "AEUY";
    }
    if ($strength >= 4) {
        $consonants .= '23456789';
    }
    if ($strength >= 8) {
        $consonants .= '10cCfFiIkKoO';//'@#$%';
    }

    $password = '';
    $alt = time() % 2;
    for ($i = 0; $i < $length; $i++) {
        if ($alt == 1) {
            $password .= $consonants[(rand() % strlen($consonants))];
            $alt = 0;
        } else {
            $password .= $vowels[(rand() % strlen($vowels))];
            $alt = 1;
        }
    }
    return $password;
}

/*function utf8_htmlspecialchars($value)
{
   return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}*/

function utf8_htmlspecialchars_decode($value)
{
   return htmlspecialchars_decode($value, ENT_QUOTES);
}

function doChallengeResponse($login,$password)
{
  $str = $login."*".$password;
  return md5($str);
}

function getDataFromSQL($sql, $file, $frame, $function)
{
  $connect_db = connect();
//	$set_timeformat = "SET time_format = '".$_SESSION['user_timeformat']."';";
	$set_timezone = "";
	if (isset($_SESSION['user_utcoffset']) && $_SESSION['user_utcoffset'] != "") {
		$set_timezone = "SET time_zone = '".$_SESSION['user_utcoffset']."'";
		$req = mysql_query($set_timezone) or die(reportError(mysql_error(), $file, $frame, $function, 'Erreur SQL : '.$sql));
	}
  $req = mysql_query($sql) or die(reportError(mysql_error(), $file, $frame, $function, 'Erreur SQL : '.$sql));
  $i = 0;
  $array = array();
  while($data = mysql_fetch_assoc($req)) {
    foreach($data as $key => $value) {
      $array[$i][$key] = $value;
    }
    $i++;
  }
  mysql_free_result($req);
  close($connect_db);
  $array["Count"] = $i;
  return $array;
}

function getArrayFromData($data, $column) {
  $array = array();
  for($i=0;$i<$data['Count'];$i++) {
    $array[] .= $data[$i][$column];
  }
  return $array;
}

function getUserRights($userId) {
  if ($userId == "") {
    $sql = "SELECT tgr.Id_right FROM J_group_right tgr INNER JOIN T_group gr ON gr.Id = tgr.Id_group WHERE gr.Name = 'Visitor'";
  } else {
    $sql = "SELECT Id_right FROM V_caver_right WHERE Id_caver = '".$userId."' ";
  }
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  return getArrayFromData($data, "Id_right");
}

function allowAccess($placeId) {
  if ($_SESSION['user_rights'] == "") {
    return false;
  }
  return in_array($placeId, $_SESSION['user_rights']);
}

function getLabelArray($frame, $lang)
{
  if ($lang == "" || $lang == Select_default) {
    $lang = "En";
  }
  $array = array();
  $sql = "SELECT Id, ".$lang." AS Label FROM `".$_SESSION['Application_host']."`.`T_label` ";
  $sql .= "WHERE Frame = '".$frame."' ";
  $sql .= "OR Frame = 'general' ";
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  for($i=0;$i<$data['Count'];$i++) {
    $array[$data[$i]['Id']] = $data[$i]['Label'];
  }
  return $array;
}

function getCountries($langSelected)
{
  $array = array();
  $sql = "SELECT Iso, En_name, ".$langSelected."_name AS Name FROM `".$_SESSION['Application_host']."`.`T_country` ";
  $sql .= "ORDER BY Name";
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  return $data;
}

function getCountry($langSelected,$iso)
{
  $sql = "SELECT ".$langSelected."_name AS Name FROM `".$_SESSION['Application_host']."`.`T_country` ";
  $sql .= "WHERE Iso = '".$iso."'";
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  if($data['Count'] > 0) {
    return $data[0]['Name'];
  } else {
    return "";
  }
}

function getAvailableLanguages()
{
  $array = array();
  $LangArray = array();
  $sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`T_label` ";
  $sql .= "WHERE Id IN (1, 2) ";
  $sql .= "ORDER BY Id";
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  //$j = 0;
  for($i=0;$i<$data['Count'];$i++) {
    $j = 0;
    foreach($data[$i] as $key=>$value) {
      if ($key != 'Id' && $key != 'Frame') {
        $array[$i][$j] = $value; //$array[$j][] .= $value;
        $j = $j + 1;
      }
    }
    //$j = $j + 1;
  }
  foreach($array[1] as $key => $shortLang) {
    $largeLang = $array[0][$key];
    $LangArray[$shortLang] = $largeLang;
  }
  return $LangArray;
}

function checkLang($clientLang)
{
  $clientLang = ucfirst(strtolower(trim($clientLang)));
  $availLang = getAvailableLanguages();
  if ($availLang[$clientLang] == "") { //This language is not available
    $clientLang = "En";
  }
  return $clientLang;
}

function getCountryCodeByIP($gip_ip)
{
  $dirroot = substr(__FILE__, 0, strlen(__FILE__) - 12);
  include($dirroot."geoip/geoip.inc");
  include($dirroot."geoip/geoipcity.inc");
  $gip = geoip_open($dirroot."geoip/GeoLiteCity.dat",GEOIP_STANDARD);
  $code = geoip_country_code_by_addr($gip, $gip_ip);
  geoip_close($gip);
  return $code;
  /*$ip2 = IPAddress2IPNumber($gip_ip);
  $handle = fopen("http://ip-to-country.directi.com/country/name/".$ip2, 'r');
  $country = fgets($handle, 4096);
  fclose($handle);
  return $country;*/
  //Translate Ip to number
  /*$ip2 = IPAddress2IPNumber($gip_ip);    
  //Build the webservice's url
  $url = "/get-country/?ip=".$ip2."&user=guest&pass=guest";
  //Open the socket
  $fp = fsockopen("http://ip-to-country.directi.com", 80, &$errno, &$errstr, 30);
  //Errors handeling
  if (!$fp) {
    //Echo error
  } else {
    //Send http get request
    fputs($fp, "GET $url HTTP/1.0\r\nHost: ip-to-country.com\r\n\r\n");
    //Wait for data
    while (!feof($fp)) {
    //Extract data
    $response .= fgets($fp,128);
    }
    //Close connection
    fclose($fp);
    //Return result
    return $response;
  }*/
}

function IPAddress2IPNumber($dotted) {
  $dotted = preg_split( "/[.]+/", $dotted);
  $ip = (double) ($dotted[0]*16777216)+($dotted[1]*65536)+($dotted[2]*256)+($dotted[3]);
  return $ip;
}

function IPNumber2IPAddress($number) {
  $a = ($number/16777216)%256;
  $b = ($number/65536)%256;
  $c = ($number/256)%256;
  $d = ($number)%256;
  $dotted = $a.".".$b.".".$c.".".$d;
  return $dotted;
}

function getOptionCountry($langSelected, $countrySelected, $title)
{
  $langSelected = checkLang($langSelected);
  $countryArray = getCountries($langSelected);
  $options = "<option id=\"default_".rand()."\" value=\"".Select_default."\"";
  if ($countrySelected == "") {
    $options .= " selected=\"selected\"";
  }
  $options .= ">".$title."</option>\n";
  for($i=0;$i<$countryArray['Count'];$i++) {
    $ISO = $countryArray[$i]["Iso"];
//    $enName = $countryArray[$i]["En_name"];
    $enName = strtr($countryArray[$i]["En_name"],' ,().\'','_______');
    $Name = $countryArray[$i]["Name"];
    $options .= "<option id=\"".$enName."\" value=\"".$ISO."\"";
    if ($countrySelected == $ISO) {
      $options .= " selected=\"selected\"";
    }
    $options .= ">".$Name."</option>\n";
  }
  return $options;
}

function getOptions()
{
  $arg_list = func_get_args();
  $sql = $arg_list[0];
  $msg = $arg_list[1];
  $selected = $arg_list[2];
  $comparedCol = $arg_list[3];
  $numargs = func_num_args();
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  $array = array();
  $i = 0;
  for($k=0;$k<$data['Count'];$k++) {
    for ($j = 3; $j < $numargs; $j++) {
      $columnName = $arg_list[$j];
      $array[$i][$columnName] = $data[$k][$columnName];
    }
    $i = $i + 1;
  }
  $lines = $i;
  $options = "";
  if ($msg != "") {
    $options .= "<option id=\"default_".rand()."\" value=\"".Select_default."\"";
    if ($selected == "" || $selected == Select_default) {
      $options .= " selected=\"selected\"";
    }
    $options .= ">".$msg."</option>\n";
  }
  for($i = 0; $i < $lines; $i++) {
    $options .= "<option ";
    $is_selected = false;
    if (is_array($selected)) {
      $is_selected = in_array($array[$i][$comparedCol],$selected);
    } else {
      $is_selected = ($array[$i][$comparedCol] == $selected);
    }
    if ($is_selected) {
      $options .= "selected=\"selected\" ";
    }
    for($j = 3; $j < $numargs - 1; $j++) {
      $columnName = $arg_list[$j];
      //$options .= " ".strtolower($columnName)." =\"".$array[$i][$columnName]."\"";
      $options .= " ".$columnName." =\"".$array[$i][$columnName]."\"";
    }
    $columnName = $arg_list[$j];
    $options .= ">".$array[$i][$columnName]."</option>\n";
  }
  return $options;
}

function groupOptions($options,$groupBy) {
  $mainSeparator = "<option";
  $startSeparator = $groupBy." =\"";
  $endSeparator = "\"";
  $optionsArray = explode($mainSeparator, $options);
  $groupByValue = "";
  $tmpGroupStart = "";
  $tmpGroupEnd = "</optgroup>";
  $optionsStr = "";
  $groupOpened = false;
  for($i = 0; $i < count($optionsArray); $i++) {
    if ($optionsArray[$i] != "") {
      $start = strpos($optionsArray[$i],$startSeparator) + strlen($startSeparator);
      if (strpos($optionsArray[$i],$startSeparator) === false) {
        $optionsStr .= $mainSeparator.$optionsArray[$i];
      } else {
        $length = strpos($optionsArray[$i],$endSeparator,$start) - $start;
        if ($groupByValue != substr($optionsArray[$i],$start,$length)) {
          if ($groupOpened) {
            $optionsStr .= $tmpGroupEnd;
          }
          $groupByValue = substr($optionsArray[$i],$start,$length);
          $tmpGroupStart = "<optgroup label=\"".$groupByValue."\">";
        } else {
          $tmpGroupStart = "";
        }
        $optionsStr .= $tmpGroupStart.$mainSeparator.$optionsArray[$i];
        $groupOpened = true;
        if (!(($i + 1) < count($optionsArray)) && $groupOpened) {
          $optionsStr .= $tmpGroupEnd;
          $groupOpened = false;
        }
      }
    }
  }
  return $optionsStr;
}

function getOptionLanguage($langSelected)
{
  $LangArray = getAvailableLanguages();
  $options = "<option id=\"Default\" value=\"".Select_default."\"";
  if ($langSelected == "" || $langSelected == Select_default) {
    $options .= " selected=\"selected\"";
  }
  $options .= ">I speak ...</option>\n";
  foreach($LangArray as $shortLang => $largeLang) {
    $options .= "<option value=\"".$shortLang."\"";
    if ($langSelected == $shortLang) {
      $options .= " selected=\"selected\"";
    }
    $options .= ">".$largeLang."</option>\n";
  }
  return $options;
}

function connectUser($login, $password, $string)
{
  $activated = false;
  $banned = true;
  $connected = false;
  $registered = false;
  $data = array();
  if(md5(getIp().strtolower($string)) == $_SESSION['userCheck'] || !$_SESSION['do_check']) {
    $sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`T_caver`";
    $sql .= " WHERE Login = '".$login."' AND Password ='".getCryptedPwd($login,$password)."' ";
    $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
    if ($data['Count'] > 0){
      $registered = true;
      $banned = ($data[0]['Banned'] == "YES");
      $activated = ($data[0]['Activated'] == "YES");
    } else {
      $banned = false;
      $activated = true;
    }
  }
  $connected = ($registered && !$banned && $activated);
  //Set the session
  setSession($connected, $data[0]);
  if ($connected) {
    //Update the date of last connection for this user
    $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_caver` ";
    $sql .= "SET Date_last_connection = Now(), ";
    $sql .= "Ip = '".getIp()."', ";
    $sql .= "Browser = '".getBrowserData()."', ";
    $sql .= "Connection_counter = Connection_counter + 1 ";
    $sql .= "WHERE Id = ".$_SESSION['user_id'];
    $req = execSQL($sql, "function", __FILE__, __FUNCTION__);
  }
  $return = array('Connected' => $connected, 'Activated' => $activated, 'Banned' => $banned, 'Registered' => $registered);
  return $return;
}

function setSession($status, $data = ""){
  $_SESSION['user_connected'] = $status;
  if ($status) {
    $_SESSION['user_id'] = $data['Id'];
    $_SESSION['user_name'] = $data['Name'];
    $_SESSION['user_surname'] = $data['Surname'];
    $_SESSION['user_login'] = $data['Login'];
    $_SESSION['user_nickname'] = $data['Nickname'];
    $_SESSION['user_last_connection'] = cDate($data['Date_last_connection'], false);
    $_SESSION['user_country'] = $data['Country'];
    $_SESSION['user_region'] = $data['Region'];
    $_SESSION['user_city'] = $data['City'];
    $_SESSION['user_postal'] = $data['Postal_code'];
    $_SESSION['user_address'] = $data['Address'];
    $_SESSION['user_birth'] = cDate($data['Date_birth'], false);
    $_SESSION['user_contact'] = $data['Contact'];
    $_SESSION['user_initiation'] = $data['Year_initiation'];
    $_SESSION['user_language'] = $data['Language'];
    $_SESSION['language'] = $_SESSION['user_language'];
    $_SESSION['user_public'] = $data['Contact_is_public'];
    $_SESSION['user_hover'] = $data['Show_links'];
    $_SESSION['user_detail_level'] = $data['Detail_level'];
    $_SESSION['user_latitude'] = $data['Latitude'];
    $_SESSION['user_longitude'] = $data['Longitude'];
    $_SESSION['user_default_lat'] = $data['Default_latitude'];
    $_SESSION['user_default_lng'] = $data['Default_longitude'];
    $_SESSION['user_default_zoom'] = $data['Default_zoom'];
    $_SESSION['user_message'] = $data['Custom_message'];
    $_SESSION['user_facebook'] = $data['Facebook'];
    $_SESSION['user_file'] = $data['Picture_file_name'];
    $_SESSION['user_banned'] = $data['Banned'];
    $_SESSION['user_news'] = $data['Alert_for_news'];
		$_SESSION['user_utcoffset'] = $data['Utc_offset'];
		$_SESSION['user_timezone'] = $data['Id_time_zone'];
//		$_SESSION['user_timeformat'] = $data['Time_format'];
    $_SESSION['user_rights'] = getUserRights($_SESSION['user_id']);
    $_SESSION['user_lastactivitydate'] = 0;
  }
}

function cDate($date, $for_sql) {
  if ($date != "") {
    if ($for_sql) {
      $separator = "-";
      // $date is formated like mm/dd/yyyy
      list($month, $day, $year) = explode("/", $date);
      // $date has to be formated like yyyy-mm-dd
      return trim($year).$separator.trim($month).$separator.trim($day);
    } else {
      $separator = "/";
      // $date is formated like yyyy-mm-dd xxxxxxx...
      list($year, $month, $day) = explode("-", substr($date,0,10));
      // $date has to be formated like mm/dd/yyyy
      return trim($month).$separator.trim($day).$separator.trim($year)." ".trim(substr($date,11,strlen($date)));
    }
  } else {
    return "";
  }
}

function timeToStr($dateTime, $format="") {
  if ($format=="") {
    $format = "d M y\, H:i";
  }
  return date($format,strtotime($dateTime));
}

function getNow()
{
  $sql = "SELECT NOW() AS DTNOW";
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  if ($data['Count'] > 0){
    $retour = $data[0]['DTNOW'];
  }
  return $retour;
}

function getSinceDateFromDT($value, $labels)
{
  $sql = "SELECT ";
  $sql .= "TIMESTAMPDIFF(YEAR, STR_TO_DATE('".$value."', '%m/%d/%Y %H:%i:%s'), NOW()) AS YEAR, ";
  $sql .= "TIMESTAMPDIFF(MONTH, STR_TO_DATE('".$value."', '%m/%d/%Y %H:%i:%s'), NOW()) AS MONTH, ";
  $sql .= "TIMESTAMPDIFF(WEEK, STR_TO_DATE('".$value."', '%m/%d/%Y %H:%i:%s'), NOW()) AS WEEK, ";
  $sql .= "TIMESTAMPDIFF(DAY, STR_TO_DATE('".$value."', '%m/%d/%Y %H:%i:%s'), NOW()) AS DAY, ";
  $sql .= "TIMESTAMPDIFF(HOUR, STR_TO_DATE('".$value."', '%m/%d/%Y %H:%i:%s'), NOW()) AS HOUR, ";
  $sql .= "TIMESTAMPDIFF(MINUTE, STR_TO_DATE('".$value."', '%m/%d/%Y %H:%i:%s'), NOW()) AS MINUTE, ";
  $sql .= "TIMESTAMPDIFF(SECOND, STR_TO_DATE('".$value."', '%m/%d/%Y %H:%i:%s'), NOW()) AS SECOND ";
  return getSinceDate($sql, $labels);
}

function getSinceDateFromD($value, $labels)
{
  $sql = "SELECT ";
  $sql .= "TIMESTAMPDIFF(YEAR, STR_TO_DATE('".$value."', '%m/%d/%Y'), NOW()) AS YEAR, ";
  $sql .= "TIMESTAMPDIFF(MONTH, STR_TO_DATE('".$value."', '%m/%d/%Y'), NOW()) AS MONTH, ";
  $sql .= "TIMESTAMPDIFF(WEEK, STR_TO_DATE('".$value."', '%m/%d/%Y'), NOW()) AS WEEK, ";
  $sql .= "TIMESTAMPDIFF(DAY, STR_TO_DATE('".$value."', '%m/%d/%Y'), NOW()) AS DAY";
  return getSinceDate($sql, $labels);
}

function getSinceDateFromSQL($table, $field, $id, $labels)
{
  $sql = "SELECT ";
  $sql .= "TIMESTAMPDIFF(YEAR, ".$field.", NOW()) AS YEAR, ";
  $sql .= "TIMESTAMPDIFF(MONTH, ".$field.", NOW()) AS MONTH, ";
  $sql .= "TIMESTAMPDIFF(WEEK, ".$field.", NOW()) AS WEEK, ";
  $sql .= "TIMESTAMPDIFF(DAY, ".$field.", NOW()) AS DAY, ";
  $sql .= "TIMESTAMPDIFF(HOUR, ".$field.", NOW()) AS HOUR, ";
  $sql .= "TIMESTAMPDIFF(MINUTE, ".$field.", NOW()) AS MINUTE, ";
  $sql .= "TIMESTAMPDIFF(SECOND, ".$field.", NOW()) AS SECOND ";
  $sql .= "FROM `".$_SESSION['Application_host']."`.`".$table."` ";
  $sql .= "WHERE Id = ".$id;
  return getSinceDate($sql, $labels);
}

function getSinceDate($sql, $labels)
{
  $return = "";
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  if ($data['Count'] > 0){
    if (isset($data[0]['YEAR']) && $data[0]['YEAR'] > 0) {
      $return = $data[0]['YEAR']." ".$labels[0];//"years ago";
    } elseif(isset($data[0]['MONTH']) && $data[0]['MONTH'] > 0 ) {
      $return = $data[0]['MONTH']." ".$labels[1];//month(s) ago";
    } elseif(isset($data[0]['WEEK']) && $data[0]['WEEK'] > 0 ) {
      $return = $data[0]['WEEK']." ".$labels[2];//week(s) ago";
    } elseif(isset($data[0]['DAY']) && $data[0]['DAY'] > 0 ) {
      $return = $data[0]['DAY']." ".$labels[3];//day(s) ago";
    } elseif(isset($data[0]['HOUR']) && $data[0]['HOUR'] > 0 ) {
      $return = $data[0]['HOUR']." ".$labels[4];//hour(s) ago";
    } elseif(isset($data[0]['MINUTE']) && $data[0]['MINUTE'] > 0 ) {
      $return = $data[0]['MINUTE']." ".$labels[5];//minute(s) ago";
    } elseif(isset($data[0]['SECOND']) && $data[0]['SECOND'] > 0 ) {
      $return = $data[0]['SECOND']." ".$labels[6];//second(s) ago";
    }
  }
  return $return;
}

function sendMail($mail_dest,$subject,$mail_body,$from_email = "",$add_cc_email = "", $send_a_copy = true)
{
  $mail_subject = "[".$_SESSION['Application_name']."]"." ".$subject;
  if ($from_email == "") {
    $from_email = $_SESSION['Application_mail'];
  }
  $reply_email = $from_email;
  $header_date = date("D, j M Y H:i:s");//-0600"); // avec offset horaire
  
  //HTML Content
  $css = 'body
          {
            font-family: arial, sans-serif;
            font-size: 9pt;
            background-color: white; 
            color: black;
          }
          
          a
          {
            text-decoration: none;
            color: #34558A;
            border-bottom: dotted 1px #458EFF;
          }
          
          a:hover
          {
            color: #FFA713;
            border-bottom: dotted 1px #FFA713;
          }';
  $html_text_body = getDoctype(false).'<html '.getHTMLTagContent().'><head>'.getMetaTags().'<style type="text/css">'.$css.'</style><title>'.$subject.'</title></head><body>'.$mail_body.'</body></html>';
  
  //TXT Content
  $plain_text_body = HTML2TxtMail($mail_body);
  
  $mime_boundary = md5(uniqid(mt_rand()));
  $mime_boundary_header = chr(34).$mime_boundary.chr(34);
  
  $mail_header = 'From: '.$from_email."\n";
  if ($add_cc_email != "") {
    $mail_header .= 'Cc: '.$add_cc_email."\n";
  }
  if ($send_a_copy) {
    $mail_header .= 'Bcc: '.$_SESSION['Application_mail']."\n";
  }
  $mail_header .= 'Reply-To: '.$reply_email."\n";
  $mail_header .= 'Return-Path: '.$reply_email."\n";
  $mail_header .= 'MIME-Version: 1.0'."\n";
  $mail_header .= 'Content-Type: multipart/alternative;'."\n";
  $mail_header .= '     boundary='.$mime_boundary."\n";
  $mail_header .= 'X-Mailer: PHP/'.phpversion()."\n" ;
  $mail_header .= 'Date: '.$header_date."\n";
  
  //-----------------------------------------------
  // TXT
  //-----------------------------------------------
  $mail_body = 'This is a multi-part message in MIME format.'."\n\n";
  
  $mail_body .= '--'.$mime_boundary."\n";
  $mail_body .= 'Content-Type: text/plain; charset=UTF-8'."\n";
  $mail_body .= 'Content-Transfer-Encoding: 8bit'."\n\n";
  $mail_body .= $plain_text_body."\n\n";
  
  //-----------------------------------------------
  // HTML
  //-----------------------------------------------
  $mail_body .= '--'.$mime_boundary."\n";
  $mail_body .= 'Content-Type: text/html; charset=UTF-8'."\n";
  $mail_body .= 'Content-Transfer-Encoding: 8bit'."\n\n";
  $mail_body .= $html_text_body."\n\n";
  
  $mail_body .= '--'.$mime_boundary.'--'."\n";
  return mail($mail_dest,$mail_subject,$mail_body,$mail_header);
}

function sendMail_bak($mail_dest,$subject,$mail_body,$from_email = "",$add_cc_email = "", $send_a_copy = true)
{
  srand((double)microtime()*1000000);
  $boundary = md5(uniqid(rand()));
  $mail_subject = "[".$_SESSION['Application_name']."]"." ".$subject;
  if ($from_email == "") {
    $from_email = $_SESSION['Application_mail'];
  }
  $header_date = date("D, j M Y H:i:s");//-0600"); // avec offset horaire
  $mail_header = "";
  $mail_header .= "MIME-Version: 1.0\n";
  $mail_header .= "Content-Type: multipart/alternative;boundary=\"".$boundary."\"\n";//$mail_header .= "content-type: text/html; charset=utf-8\n";
  //$mail_header .= "content-type: text/html; charset=utf-8\n";
  $mail_header .= "From: $from_email \n"; // Adresse expediteur
  if ($add_cc_email != "") {
    $mail_header .= "Cc: ".$add_cc_email."\n"; // Copies
  }
  if ($send_a_copy) {
    $mail_header .= "Bcc: ".$_SESSION['Application_mail']."\n"; // Copies cachées
  }
  $mail_header .= "Reply-To: $from_email \n"; // Adresse de retour
  $mail_header .= "X-Mailer: PHP/".phpversion()."\n" ;
  $mail_header .= "Date: $header_date";
  
  //CSS
  $css = "body
          {
            font-family: arial, sans-serif;
            font-size: 9pt;
            background-color: white; 
            color: black;
          }
          
          a
          {
            text-decoration: none;
            color: #34558A;
            border-bottom: dotted 1px #458EFF;
          }
          
          a:hover
          {
            color: #FFA713;
            border-bottom: dotted 1px #FFA713;
          }";
          
  //Format the mail
  $html_mail_body = "<html ".getHTMLTagContent()."><head><style type=\"text/css\">".$css."</style><title>".$subject."</title></head><body>".$mail_body."</body></html>";
  $mail_body = "\nThis message is in MIME format. Since your mail reader does not understand this format, some or all of this message may not be legible.\n--".$boundary."\nContent-Type: text/plain;charset=\"UTF-8\"\n\n".HTML2TxtMail($mail_body)."\n\n";
  $mail_body .= "\n--".$boundary."\nContent-type: text/html; charset=\"UTF-8\"\n\n".$html_mail_body."\n\n";
  mail($mail_dest,$mail_subject,$mail_body,$mail_header);
  return True;
}

function sendMail2($mail_dest,$subject,$mail_body,$from_email = "",$add_cc_email = "", $send_a_copy = true)
{ 
  $mail_subject = "[".$_SESSION['Application_name']."]"." ".$subject;
  if ($from_email == "") {
    $from_email = $_SESSION['Application_mail'];
  }
  $reply_email = $from_email;
  $header_date = date("D, j M Y H:i:s");//-0600"); // avec offset horaire
  
  //HTML Content
  $css = 'body
          {
            font-family: arial, sans-serif;
            font-size: 9pt;
            background-color: white; 
            color: black;
          }
          
          a
          {
            text-decoration: none;
            color: #34558A;
            border-bottom: dotted 1px #458EFF;
          }
          
          a:hover
          {
            color: #FFA713;
            border-bottom: dotted 1px #FFA713;
          }';
  //$html_mail_body = '<html '.getHTMLTagContent().'><head><style type="text/css">'.$css.'</style><title>'.$subject.'</title></head><body>'.$mail_body.'</body></html>';
  $mail_body = "TEST";
  $html_mail_body = '<html '.getHTMLTagContent().'><head><title>'.$subject.'</title></head><body>'.$mail_body.'</body></html>';
  
  //TXT Content
  $txt_mail_body = HTML2TxtMail($html_mail_body);
  
  $pipe = '-----='.md5(uniqid(mt_rand()));
  
  $mail_header = 'From: '.$from_email."\n";
  if ($add_cc_email != "") {
    $mail_header .= 'Cc: '.$add_cc_email."\n";
  }
  if ($send_a_copy) {
    $mail_header .= 'Bcc: '.$_SESSION['Application_mail']."\n";
  }
  $mail_header .= 'Reply-To: '.$reply_email."\n";
  $mail_header .= 'Return-Path: '.$reply_email."\n";
  $mail_header .= 'MIME-Version: 1.0'."\n";
  $mail_header .= 'Content-Type: multipart/alternative; boundary="'.$pipe.'"';
  $mail_header .= 'X-Mailer: PHP/'.phpversion()."\n" ;
  $mail_header .= 'Date: '.$header_date;
  
  //-----------------------------------------------
  // TXT
  //-----------------------------------------------
  $mail_body = 'This is a multi-part message in MIME format.'."\n\n";
  
  $mail_body .= '--'.$pipe.'--'."\n";
  $mail_body .= 'Content-Type: text/plain; charset="UTF-8"'."\n";
  $mail_body .= 'Content-Transfer-Encoding: 8bit'."\n\n";
  $mail_body .= $txt_mail_body."\n\n";
  
  //-----------------------------------------------
  // HTML
  //-----------------------------------------------
  $mail_body .= '--'.$pipe.'--'."\n";
  $mail_body .= 'Content-Type: text/html; charset="UTF-8"'."\n";
  $mail_body .= 'Content-Transfer-Encoding: 8bit'."\n\n";
  $mail_body .= $html_mail_body."\n\n";
  
  $mail_body .= '--'.$pipe.'--'."\n";
  
  return mail($mail_dest,$mail_subject,$mail_body,$mail_header);
}

function HTML2TxtMail($HTMLBody)
{
  $TxtBody = str_replace("<br />", "\n", $HTMLBody);
  $TxtBody = str_replace("<p>", "\n", $TxtBody);
  $TxtBody = str_replace("</p>", "\n", $TxtBody);
  $TxtBody = str_replace("<ul>", "\n", $TxtBody);
  $TxtBody = str_replace("</ul>", "\n", $TxtBody);
  $TxtBody = str_replace("<li>", "\n    * ", $TxtBody);
  $TxtBody = str_replace("<hr />", "-------------------------------------------------------------\n", $TxtBody);
  $TxtBody = strip_tags($TxtBody);
  return $TxtBody;
}

function getIp(){
  if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
  	$IP = $_SERVER['HTTP_X_FORWARDED_FOR'] ;
  } elseif(isset($_SERVER['HTTP_CLIENT_IP'])) {
  	$IP = $_SERVER['HTTP_CLIENT_IP'] ;
  } else {
  	$IP = $_SERVER['REMOTE_ADDR'] ;
  }
  return $IP;
}

function getBrowserData() {
  return str_replace("'","\"",$_SERVER['HTTP_USER_AGENT']);
}

function LoadGif ($imgname) {
    $im = @imagecreatefromgif ($imgname); /* Tentative d'ouverture */
    if (!$im) { /* Test d'Ã©chec */
        $im = imagecreatetruecolor (150, 30); /* CrÃ©ation d'une image vide */
        $bgc = imagecolorallocate ($im, 255, 255, 255);
        $tc = imagecolorallocate ($im, 0, 0, 0);
        imagefilledrectangle ($im, 0, 0, 150, 30, $bgc);
        /* Affichage d'un message d'erreur */
        imagestring ($im, 1, 5, 5, "Error $imgname", $tc);
    }
    return $im;
}

function createImage($length, $size){
  $string_a = array("A","B","C","D","E","F","G","H","J","K",
  "L","M","N","P","R","S","T","U","V","W","X","Y","Z",
  "2","3","4","5","6","7","8","9");
  $keys = array_rand($string_a, $length);
  $string = "";
  foreach($keys as $n=>$v){
    $string .= $string_a[$v];
  }
  $number = rand(1,7);
  $im = LoadGif("../images/security/security_background".$number.".gif");
  $colour = imagecolorallocate($im, rand(0,255), rand(0,255), rand(0,255));
  $font = 'lhandw';
  $angle = rand(-20,20);
  $fileRand = md5(getIp().strtolower($string));
  putenv('GDFONTPATH='.realpath('.'));//"GDFONTPATH=".realpath("."));//"..")."/fonts/");
  imagettftext($im, $size, $angle, 30, 35, $colour, $font, $string);
  imagegif($im,$fileRand.".gif");
  return $fileRand;
}

function createImagedText($string="--", $font_size=5, $bgColorHex="ffffff", $fgColorHex="000000"){
  //default: --, 5pt, black text, white background
  $x_pos = 5;
  $y_pos = 5;
  $w_array = array(1 => 5, 2 => 6, 3 => 7, 4 => 8, 5 => 9); 
  $h_array = array(1 => 7, 2 => 12, 3 => 11, 4 => 13, 5 => 13);
  $bgRed = hexdec(substr($bgColorHex, 0, 2));
  $bgGreen = hexdec(substr($bgColorHex, 2, 2));
  $bgBlue = hexdec(substr($bgColorHex, -2));
  $fgRed = hexdec(substr($fgColorHex, 0, 2));
  $fgGreen = hexdec(substr($fgColorHex, 2, 2));
  $fgBlue = hexdec(substr($fgColorHex, -2));
  $width = strlen($string)*$w_array[$font_size]+2*$x_pos;
  $height = $h_array[$font_size]+2*$y_pos; 
  $im = imagecreate($width, $height);
  $bgColor = imagecolorallocate($im, $bgRed, $bgGreen, $bgBlue);
  imagecolortransparent($im, $bgColor);
  $text_color = imagecolorallocate($im, $fgRed, $fgGreen, $fgBlue);
  imagestring($im, $font_size, $x_pos, $y_pos,  $string, $text_color);
  return $im;
}

function deleteImage($fileName = ""){
  if ($fileName != "") {
    if (fileExists($fileName.".gif")) {
      @unlink($fileName.".gif");
      return True;
    }
  } elseif (isset($_SESSION['userCheck'])) {
    if (fileExists($_SESSION['userCheck'].".gif")) {
      @unlink($_SESSION['userCheck'].".gif");
    }
    unset($_SESSION['userCheck']);
    return True;
  } else {
    return False;
  }
}

function deleteOldImages()
{
  $path = "../html";
  $ext = "gif";
  $count = 0;
  //session.gc_maxlifetime = 1440s (24 min)
  $session_maxlifetime = ini_get('session.gc_maxlifetime'); //session.gc_maxlifetime is the life time of the server's session (session.cache_expire is for the cookies one)
  if ($d = opendir($path)) {
    while (false !== ($file = readdir($d))) {
      $file_ext = strtolower(getFileExtension($file)); 
      if ($file != '.' && $file != '..' && $file_ext == $ext) {
        if (time() - getFilemtime($path.'/'.$file) > $session_maxlifetime) {
          if ($unlinked = @unlink($path."/".$file)) {
            $count++;
          }
        }
      }
    }
  }
  return $count;
}

function unlockOldCategories()
{
  $count = 0;
  $tables = array("T_entry","T_grotto","T_cave","T_massif","T_url","T_description","T_location","T_rigging","T_bibliography","T_comment","T_history","T_news");
  foreach($tables as $table) {
    $sql = "UPDATE `".$_SESSION['Application_host']."`.`".$table."` SET Locked = 'NO' WHERE Locked = 'YES' AND TIMESTAMPDIFF(DAY, Date_locked, NOW()) > 0";
    $req = execSQL($sql, "function", __FILE__, __FUNCTION__);
    $count += $req['mysql_affected_rows'];
  }
  return $count;
}

function banBadCarvers()
{
  $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_caver` SET Banned = 'YES' WHERE Relevance < -100 AND Banned = 'NO'";
  $req = execSQL($sql, "function", __FILE__, __FUNCTION__);
  $count = $req['mysql_affected_rows'];
  return $count;
}

/*function getCopyright()
{
  return "Â© Copyright ".$_SESSION['Application_name'].", 2008. All rights reserved.";
}*/

function getLicensePicture($imgType, $title="")
{
  $imgSrc = "";
  switch($imgType) {
    case 1:
      $imgSource = "http://i.creativecommons.org/l/by-nc/3.0/88x31.png";
      break;
    case 2:
      $imgSource = "http://creativecommons.org/images/public/somerights20.png";
      break;
    case 3:
    case 4:
      $imgSource = "http://i.creativecommons.org/l/by-nc/3.0/80x15.png";
      break;
  }
  switch($imgType) {
    case 1:
    case 2:
    case 3:
      $licensePic = "<a href=\"http://creativecommons.org/licenses/by-nc/3.0/deed.".strtolower($_SESSION['language'])."\" class=\"nothing\" target=\"_blank\">";//rel=\"license\" 
      $licensePic .= " <img alt=\"Creative Commons License\" style=\"border-width:0\" src=\"".$imgSource."\"/>";
      $licensePic .= "</a>";
      break;
    case 4:
      $licensePic = "<a href=\"http://creativecommons.org/licenses/by-nc/3.0/deed.".strtolower($_SESSION['language'])."\" title=\"".$title."\" class=\"nothing\" target=\"_blank\">";// rel=\"license\"
      $licensePic .= " <img alt=\"Creative Commons License\" style=\"border-width:0\" src=\"".$imgSource."\"/>";
      $licensePic .= "</a>";
      break;
  }
  return $licensePic;
}

function returnDefault($val, $type)
{
	$val = strip_tags($val);
  switch ($type) {
		case "Name":
		  if ($val == '') {return 'DEFAULT';} else {return "'".ucwords(strtolower($val))."'";}
		break;
		case "text":
		  if ($val == '') {return 'DEFAULT';} else {return "'".$val."'";}//nl2br($val)."'";}
		break;
		case "url":
		  if ($val == '') {return 'DEFAULT';} else {
        // match protocol://address/path/
        $val = ereg_replace("[a-zA-Z]+://([.]?[a-zA-Z0-9_/-])*", "\\0", $val);
        // match www.something
        $val = ereg_replace("(^| )(www([.]?[a-zA-Z0-9_/-])*)", "http://\\2", $val);
        return "'".$val."'";
      }
		break;
		case "list":
		  if ($val == "00") {return 'DEFAULT';} else {return "'".$val."'";}
		break;
		case "id":
		  if ($val == "00" || $val == "") {return 'DEFAULT';} else {return $val;}
		break;
		case "int":
		  if ($val == "") {return 'DEFAULT';} else {return (0 + str_replace(",", ".", $val));}
		break;
		case "float":
		  if ($val == "") {return 'DEFAULT';} else {return (0.0 + str_replace(",", ".", $val));}
		break;
		case "latlng":
		  if ($val == "") {return 'DEFAULT';} else {return str_replace(",", ".", $val);}
		break;
		case "checkbox":
		  if ($val == 'on') {return "'YES'";} else {return "'NO'";}
		break;
		case "inv_checkbox":
		  if ($val == 'on') {return "'NO'";} else {return "'YES'";}
		break;
		case "checkboxarray":
		  if ($val == 'on') {return "YES";} else {return "NO";}
		break;
		case "time":
		  if ($val == "") {
        return 'DEFAULT';
      } else {
        $val_array = explode(":",$val);
        $val_array[0] = 0 + $val_array[0];
        $val_array[1] = 0 + $val_array[1];
        $val_array[2] = 0 + $val_array[2];
        return "'".$val_array[0].":".$val_array[1]."'";//.":".$val_array[2]."'";
      }
		break;
		default:
		  return 'NULL';
		break;
  }
}

function getLanguagesForMetaTags()
{
  if (!isset($_SESSION['Languages_for_meta_tags'])) {
    //Get the languages
    $sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`T_label` WHERE Id = 155";
    $languages = "";
    $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
    if($data['Count'] > 0) {
      foreach($data[0] as $key => $value) {
        if ($key != "Id" && $key != "Frame") {
          $languages .= $value.",";
        }
      }
    }
    $_SESSION['Languages_for_meta_tags'] = substr($languages,0,strlen($languages)-1);
  }
  return $languages = $_SESSION['Languages_for_meta_tags'];
}

function getMetaDescription()
{
  if (!isset($_SESSION['Content_for_meta_tags']) && isset($_SESSION['language']) && $_SESSION['language'] != '') {
  	$maxChars = 200; //200 characters max for browsers
  	$content = "";
  	$keywords = "";
    $sql = "SELECT ".$_SESSION['language']." FROM `".$_SESSION['Application_host']."`.`T_label` WHERE Id=598";
    $content = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
    $content = $content[0][$_SESSION['language']];
    $sql = "SELECT ".$_SESSION['language']." FROM `".$_SESSION['Application_host']."`.`T_label` WHERE Id=599";
    $keywords = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
    $keywords = $keywords[0][$_SESSION['language']];
    //$content = substr($content,0,$maxChars-1);
  	$meta = "<meta name=\"Description\" content=\"".$content."\" />\n";
    $meta .= "<meta name=\"abstract\" content=\"".$content."\" />\n";
    $meta .= "<meta name=\"keywords\" content=\"".$keywords."\" />\n";
    $_SESSION['Content_for_meta_tags'] = $meta;
  }
  $meta = isset($_SESSION['Content_for_meta_tags']) ? $_SESSION['Content_for_meta_tags'] : '';
  return $meta;
}

function getMetaTags()
{
  $languages = getLanguagesForMetaTags();
  $languageTag = "<meta http-equiv=\"content-language\" content=\"".$languages."\" />\n";//fr-FR
  $languageTag .= "<meta name=\"language\" content=\"".$languages."\" />\n";
  $categoryTag = "<meta name=\"category\" content=\"sports\" />\n";
  $classificationTag = "<meta name=\"classification\" content=\"07\" />\n";
  $typeTag = "<meta name=\"type\" content=\"40\" />\n"; //Carte geographique
  $authorTag = "<meta name=\"author\" content=\"".$_SESSION['Application_authors']."\" />\n";
  $publisherTag = "<meta name=\"publisher\" content=\"".$_SESSION['Application_authors']."\" />\n";
  $copyrightTag = "<meta name=\"copyright\" content=\"".$_SESSION['Application_copyright']."\" />\n";
  $creationTag = "<meta name=\"date-creation-yyyymmdd\" content=\"".str_replace("-","",$_SESSION['Application_creation'])."\" />\n";
  $revisionTag = "<meta name=\"date-revision-yyyymmdd\" content=\"".str_replace("-","",$_SESSION['Application_revision'])."\" />\n";
  $descriptionTag = getMetaDescription();
  $distributionTag = "<meta name=\"distribution\" content=\"global\" />\n";
  $offset = 40 * 60 * 60;
  $ExpStr = gmdate("D, d M Y H:i:s", time() + $offset)." GMT";
  $expiresTag = "<meta name=\"Expires\" content=\"".$ExpStr."\" />\n";
  $generatorTag = "<meta name=\"generator\" content=\"PSPad, Pain.NET\" />\n";
  $urlTag = "<meta name=\"identifier-url\" content=\"".$_SESSION['Application_url']."\" />\n";
  $replytoTag = "<meta name=\"reply-to\" content=\"".$_SESSION['Application_mail']."\" />\n";
  $revisitafterTag = "<meta name=\"revisit-after\" content=\"15 days\" />\n";
  $ratingTag = "<meta name=\"rating\" content=\"general\" />\n";
  $robotsTag = "<meta name=\"robots\" content=\"index, follow\" />\n";
  $contenttypeTag = "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />\n";
	//$script_imagepngfix_Tag = "<script type=\"text/javascript\" src=\"../scripts/iepngfix/iepngfix_tilebg.js\"></script>\n";
  
  $meta = "";
  $meta .= $contenttypeTag;
  $meta .= $languageTag;
  $meta .= $categoryTag;
  $meta .= $classificationTag;
  $meta .= $typeTag;
  $meta .= $authorTag;
  $meta .= $publisherTag;
  $meta .= $copyrightTag;
  $meta .= $creationTag;
  $meta .= $revisionTag;
  $meta .= $descriptionTag;
  $meta .= $distributionTag;
  $meta .= $expiresTag;
  $meta .= $generatorTag;
  $meta .= $urlTag;
  $meta .= $replytoTag;
  $meta .= $revisitafterTag;
  $meta .= $ratingTag;
  $meta .= $robotsTag;
	//$meta .= $script_imagepngfix_Tag;
  return $meta;
}

function getHTMLTagContent()
{
  return "xml:lang=\"".$_SESSION['language']."\" xmlns=\"http://www.w3.org/1999/xhtml\"";
}

/*function getGAnalytics()
{
  $ganalytics = "<script type=\"text/javascript\">\n";
  //$ganalytics .= "//".start_comment."\n";
  $ganalytics .= "  var gaJsHost = ((\"https:\" == document.location.protocol) ? \"https://ssl.\" : \"http://www.\");\n";
  $ganalytics .= "  document.write(unescape(\"%3Cscript src='\" + gaJsHost + \"google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E\"));\n";
  //$ganalytics .= "//".end_comment."\n";
  $ganalytics .= "</script>\n";
  $ganalytics .= "<script type=\"text/javascript\">\n";
  //$ganalytics .= "//".start_comment."\n";
  $ganalytics .= "  var pageTracker = _gat._getTracker(\"UA-4684361-1\");\n";
  $ganalytics .= "  pageTracker._initData();\n";
  $ganalytics .= "  pageTracker._trackPageview();\n";
  //$ganalytics .= "//".end_comment."\n";
  $ganalytics .= "</script>\n";
  
  return $ganalytics;
}*/

function defaultZero($value)
{
  if ($value == "") {
  	return 0;
  }
  return $value;
}

function listEntries($tableException = "", $idException = "") {
  $sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`V_list_entry` ";
  if ($tableException != "") {
    $sql .= "WHERE entryId NOT IN (SELECT Id_entry FROM `".$_SESSION['Application_host']."`.`".$tableException."` WHERE NOT Id_cave = '".$idException."') ";
  }
  $sql .= "ORDER BY entryId";
  $list = array();
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  for($i=0;$i<$data['Count'];$i++) {
    $list["entryId"][] .= $data[$i]['entryId'];
    $list["entryName"][] .= $data[$i]['entryName'];
    $list["Is_public"][] .= $data[$i]['Is_public'];
    $list["Country"][] .= $data[$i]['Country'];
    $list["caverId"][] .= defaultZero($data[$i]['caverId']);
    $list["caveId"][] .= defaultZero($data[$i]['Id_cave']);
    $list["grottoId"][] .= defaultZero($data[$i]['Id_grotto']);
    $list["massifId"][] .= defaultZero($data[$i]['Id_massif']);
  }
  return $list;
}

function listGrottos($sql) {
  $list = array();
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  for($i=0;$i<$data['Count'];$i++) {
    $list["grottoId"][] .= $data[$i]['grottoId'];
    $list["grottoName"][] .= $data[$i]['grottoName'];
    $list["caverId"][] .= $data[$i]['caverId'];
  }
  return $list;
}

function countByCategory($category)
{
  $sql = "SELECT COUNT(*) AS count FROM `".$_SESSION['Application_host']."`.`T_".$category."`";
  if ($category == "entry" && !userIsConnected()) {
  	$sql .= " WHERE Is_public = 'YES'";
  }
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  if($data['Count'] > 0) {
    $count = $data[0]['count'];
  }
  return $count;
}

function countByCountry($category, $country)
{
  $sql = "SELECT COUNT(*) AS count FROM `".$_SESSION['Application_host']."`.`T_".$category."`";
  if ($country == "") {
  	$sql .= " WHERE Country IS NULL";
  } else {
  	$sql .= " WHERE Country = '".$country."'";
  }
  if ($category == "entry" && !userIsConnected()) {
  	$sql .= " AND Is_public = 'YES'";
  }
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  if($data['Count'] > 0) {
    $count = $data[0]['count'];
  }
  return $count;
}

function countByMassif($country, $massifId)
{
  $sql = "SELECT COUNT(*) AS count FROM `".$_SESSION['Application_host']."`.`V_filter_entry` WHERE Id_massif = ".$massifId." AND Country = '".$country."'";
  if (!userIsConnected()) {
  	$sql .= " AND Is_public = 'YES'";
  }
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  if($data['Count'] > 0) {
    $count = $data[0]['count'];
  }
  return $count;
}

function countByCave($country, $massifId, $caveId)
{
  $sql = "SELECT COUNT(*) AS count FROM `".$_SESSION['Application_host']."`.`V_filter_entry` WHERE Id_massif = ".$massifId." AND Id_cave = ".$caveId." AND Country = '".$country."'";
  if (!userIsConnected()) {
  	$sql .= " AND Is_public = 'YES'";
  }
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  if($data['Count'] > 0) {
    $count = $data[0]['count'];
  }
  return $count;
}

function setTitle($url, $frame, $title, $layerNumber)
{
  $title_pipe = "<span class=\"title_pipe\"> > </span>";
  if (!isset($_SESSION[$frame.'_title']) || $_SESSION[$frame.'_title'] == "") {
    $_SESSION[$frame.'_title'] = "<a href=\"".$url."\" title=\"".$title."\" class=\"a_title\">".$title."</a>";
  } else {
    $title_array = explode($title_pipe,$_SESSION[$frame.'_title']);
    if ($layerNumber <= count($title_array)) {
      $_SESSION[$frame.'_title'] = "";
      for($i=0;$i<$layerNumber-1;$i++) {
        $_SESSION[$frame.'_title'] .= $title_array[$i].$title_pipe;
      }
      $_SESSION[$frame.'_title'] = substr($_SESSION[$frame.'_title'],0,strlen($_SESSION[$frame.'_title'])-strlen($title_pipe));
    }
    if ($_SESSION[$frame.'_title'] == "") {
      $_SESSION[$frame.'_title'] = "<a href=\"".$url."\" title=\"".$title."\" class=\"a_title\">".$title."</a>";
    } else {
      $_SESSION[$frame.'_title'] .= $title_pipe."<a href=\"".$url."\" title=\"".$title."\" class=\"a_title\">".$title."</a>";
    }
  }
  return $_SESSION[$frame.'_title'];
}

function getCloseBtn($url,$title)
{
  $rnd = rand();
  return "<div class=\"close\" id=\"close_".$rnd."\"><a href=\"".$url."\" class=\"nothing\"><img src=\"../images/icons/close.png\" alt=\"".$title."\" title=\"".$title."\" style=\"border:0px none;\" /></a></div>";
}

function getNoScript($sentence,$help)
{
  return "<noscript><div class=\"error\">".$sentence."<br /><a href=\"http://www.google.com/support/bin/answer.py?answer=23852\">".$help."</a></div></noscript>";
}

function getDoctype($forFrame)
{
  if ($forFrame) {
  	return "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">";
  } else {
    return "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
  }
}

function fact($n)
{
  if($n === 0) { 
    return 1;
  } else {
    return $n*fact($n-1);
  }
}

function summFirstNaturals($n)
{
  if ($n === 0) {
    return 0;
  } else {
    return (($n*($n+1))/2);
  }
}

function countEntries($entry_id = "") {
  if ($entry_id != Select_default && $entry_id != "") {
    $sql = "SELECT COUNT(*) AS Count ";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`J_cave_entry` ";
    $sql .= "WHERE Id_cave IN (SELECT Id_cave FROM `".$_SESSION['Application_host']."`.`J_cave_entry` ce2 WHERE ce2.Id_entry = ".$entry_id.") ";
    //$sql = "SELECT COUNT(*) AS Count FROM `".$_SESSION['Application_host']."`.`J_cave_entry` WHERE Id_cave = ".$cave_id;
    $array = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
    return $array[0]['Count'];
  } else {
    return 0;
  }
}

function limitValue($value, $min, $max)
{
  if ($value > $max) {
    return $max;
  }
  if ($value < $min) {
    return $min;
  }
  return $value;
}

function get_include_contents($url) {
  ob_start();
  include $url;
  $content = ob_get_contents();
  ob_end_clean();
  return $content;
}

function getRef($text) {
  $openingSeparator = "[ref]";
  $closingSeparator = "[/ref]";
  $buffer = "";
  $references = "";
  $refNum = 1;
  if (strpos($text, $openingSeparator) !== false) {
    //Start the references division
    $references = "<ol class=\"references\">";
    //Get the elements
    $data = explode($openingSeparator,$text);
    for ($i=0;$i<=count($data);$i++) {
      //If there is a label code
      if (strpos($data[$i], $closingSeparator) !== false) {
        //Get the parts
        $ref = explode($closingSeparator,$data[$i]);
        //Get a randomized number
        $randNum = rand();
        //Store the reference
        $references .= "<li id=\"cite_note-".$randNum."\">";
        $references .= "  <span>";
        $references .= "    <a href=\"#cite_ref-".$randNum."\" class=\"reference\">↑</a>";
        $references .= "  </span>";
        $references .= "  ".$ref[0];
        $references .= "</li>";
        //Store the text
        $buffer .= "<a href=\"#cite_note-".$randNum."\" class=\"reference\" ><sup id=\"cite_ref-".$randNum."\">".$refNum."</sup></a>".$ref[1];
        //Increment the numbering
        $refNum ++;
      } else {
        //Store the text
        $buffer .= $data[$i];
      }
    }
    //Ends the references division
    $references = $references."</ol>";
  } else {
    $buffer = $text;
  }
  if ($references != "") {
  	return $buffer."<br /><hr />".$references;
  } else {
    return $buffer;
  }
}

function getCDataTag($isOpeningTag)
{ 
  if($isOpeningTag) {
    return "//<![CDATA[\n";
  } else {
    return "//]]>\n";
  }
}

function getBadWords()
{
  //Fr
  $badWords = 'connard|connerie|merde|merdeu|enfoiré|enfoirée|connard|merde|salaud|salope|saloppe|con|conne|pute|putain|enculé|enculer|chier|';
  //En
  $badWords .= 'asshole|hore|bitch|fuck|fucking|slut|cunt|motherfucker|shit|';
  //Es
  $badWords .= 'cabrón|cabron|cabrona|carajo|puta|puto|chinga|chingar|pendejo|pendeja|joto|jota|coño|mierda|';
  //De
  $badWords .= 'Arschloch|Mist|Fotze';
  return $badWords;
}

function replaceBannedWords($txt)
{
  $badWords = getBadWords();
  $replacedBy = "****";
  $txt = preg_replace("#(^|[[:punct:][:space:][:blank:]])+(".$badWords.")s?x?(se)?(ses)?($|[[:punct:][:space:][:blank:]])+#si", " ".$replacedBy." ", $txt);
  return $txt;
}

/*function replaceBannedWords($txt)
{
  $wordsArray = preg_split("/[:blank::ctrl::punct::space:]+/", $txt) ;
  $bannedArray = explode('|', getBadWords());
  foreach ($wordsArray as $word) {
    foreach($bannedArray as $banned){
      $similar = 0 ;
      $ret = similar_text($word, $banned, $similar);
      if ($similar >= 80) {
        return false;
      }
    }
  }
  return true ;
}*/


//http://www.weirdog.com/blog/php/l_anti_spam_fastoche.html
function wd_spamScore($body, $smallStr=false, $words=NULL, $starters=NULL)
{
    // score >= 1 - The message doesn't look like spam
    // score == 0 - The message should be put to moderation
    // score < -10 - The message is most certainly spam
    $score = 0;
    // put our body in lower case for checking
    $body = strtolower($body);
    
    // how many links are in the body ?
    $n = max(array(substr_count($body, "http"), substr_count($body, "href"), substr_count($body, "ftp")));
    if ($n > 2) {
        // more than 2 : -1 point per link
        $score -= $n;
    } else {
        // less than 2 : +2 points
        $score += 2;
    }

    // now remove links
    // html style: <a> <a/>
    $body = preg_replace('#\<a\s.+\<\/a\>#', NULL, $body);
    // bb style: [url] [/url]
    $body = preg_replace('#\[url.+\/url\]#', NULL, $body);
    // removing addresses: http://
    $body = preg_replace('#http://[^\s]+#', NULL, $body);

    // how large is the body ?
    $l = strlen($body);
    if (($l > 20 || $smallStr) && $n == 0) {
        // More than 20 characters and there's no links : +2 points
        $score += 2;
    } else if ($l < 10) { //20
        // Less than 20 characters : -1 point
        $score--;
    }
    
    // Keyword search
    if (empty($words)) {
        $words = array();
    }
    $words += array('levitra', 'viagra', 'casino', 'free sex', 'porn', 'win', 'pills', 'cializ', 'hot', 'xxx');
    $words += explode('|', getBadWords());
    foreach ($words as $word) {
        $n = substr_count($body, $word);
        if (!$n) {
            continue;
        }
        $score -= $n;
    }
    
    // Body starts with...
    if (empty($starters)) {
        $starters = array();
    }
    $starters += array('interesting', 'sorry', 'nice', 'cool', 'hi');
    foreach ($starters as $word) {
        $pos = strpos($body, $word.' ');
        if ($pos === false) {
            continue;
        }
        if ($pos > 10) {
            continue;
        }
        $score -= 10;
        break;
    }
    
    if (!$smallStr) {
      // How many different words are used ?
      $count = str_word_count($body);
      if ($count < 5) { //10
          $score -= 5;
      }
    }
    
    return $score;
}

function getDiffStr($string1, $string2)
{
  @similar_text($string1,$string2,$p);
  return (1 - $p);
}

function getScore($newStr="", $oldStr="", $isModif=false, $smallStr=false)
{
  $score += wd_spamScore($newStr,$smallStr);
  if ($isModif) {
    $oldScore += wd_spamScore($oldStr,$smallStr);
    $score = $score - $oldScore;
  }
  return $score;
}

function updateCaverRelevance($score, $caverId)
{
  if ($score != 0) {
    $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_caver` ";
    if ($score > 0) {
      $sql .= "SET Relevance = Relevance +".$score." ";
    } else {
      $sql .= "SET Relevance = Relevance ".$score." ";
    }
    $sql .= "WHERE Id = ".$caverId;
    $req = execSQL($sql, "function", __FILE__, __FUNCTION__);
    if ($req['mysql_affected_rows'] == 1) {
      return true;
    } else {
      return false;
    }
  } else {
    return true;
  }
}

function sortByThread($array)
{
  $data = array();
  $data["Count"] = $array["Count"];
  for($i=0;$i<$array["Count"];$i++) {
    if($array[$i]['Id_answered'] == "" || isOrphan($array[$i]['Id_answered'],$array)) {
      $data[] = array_merge($array[$i],array('Thread_status' => 0));
      $childs = getChildsThread($array[$i]['Id'],$array,0);
      if(isset($childs) && is_array($childs) && count($childs)) {
        $data = array_merge($data,$childs);
      }
    }
  }
  return $data;
}

function isOrphan($fathers_id,$array)
{
  $return = true;
  for($i=0;$i<$array["Count"];$i++) {
    if ($array[$i]['Id'] == $fathers_id) {
      $return = false;
    	break;
    }
  }
  return $return;
}

function getChildsThread($fathers_id,$array,$fathers_status)
{
  $data = array();
  for($i=0;$i<$array["Count"];$i++) {
    if($array[$i]['Id_answered'] == $fathers_id) {
      $childs_status = $fathers_status + 1;
      $data[] = array_merge($array[$i],array('Thread_status' => $childs_status));
      $childs = getChildsThread($array[$i]['Id'],$array,$childs_status);
      if(isset($childs) && is_array($childs) && count($childs)) {
        $data = array_merge($data,$childs);
      }
    }
  }
  return $data;
}

function getEnumArray($table_field) {
  $t_f_array = explode("*",$table_field);
  $table = $t_f_array[0];
  $field = $t_f_array[1];
  $sql = "SHOW FIELDS FROM ".$table." WHERE Field = '".$field."' ";
  $resultArray = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  $enumArray = explode("','",substr($resultArray[0]['Type'],6,strlen($resultArray[0]['Type'])-8));
  sort($enumArray);
  return $enumArray;
}

function getOptionsFromArray($array,$default_value,$selected_value) {
  if ($default_value != "") {
    $options = "<option value=\"".Select_default."\" ";
    if ($selected_value == Select_default || $selected_value == "") {
      $options .= "selected=\"selected\" ";
    }
    $options .= ">".$default_value."</option>";
  }
  foreach($array as $element) {
    $options .= "<option value=\"".$element."\" ";
    $is_selected = false;
    if (is_array($selected_value)) {
      $is_selected = in_array($element,$selected_value);
    } else {
      $is_selected = ($element == $selected_value);
    }
    if ($is_selected) {
      $options .= "selected=\"selected\" ";
    }
    $options .= ">".$element."</option>";
  }
  return $options;
}

function unhtmlentities($string)
{
    $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
    $string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
    $trans_tbl = array_flip($trans_tbl);
    return strtr($string, $trans_tbl);
}

function idEncode($string)
{
  return str_replace("%",":_:",urlencode($string));
}

function idDecode($string)
{
  return urldecode(str_replace(":_:","%",$string));
}

function getFilterFields($sql, $columns_params, $POST_vars, $filter_form, $msg_all, $small=false, $resource=false, $default_values = array())
{
	$time_start = startMetro();
  $default_size_text = 20;
  $default_size_number = 10;
  $field_set_array = array();
  // Check if it takes into account the default values or the POST values
  $buttons = array("submit_filter","reset_filter","reset","overview_filter","current","order","by","records_by_page","PHPSESSID");
  $get_post_values = false;
  foreach($buttons as $key => $value) {
    if (isset($POST_vars[$value])) {
      $get_post_values = true;
      break;
    }
  }
  // other
  if (isset($POST_vars['reset_filter'])) {
    foreach($POST_vars as $key => $value) {
      if (!is_array($value)) {
        unset($POST_vars[$key]);
      }
    }
  }
  //end of other
  if ($get_post_values) {
    $values_array = $POST_vars;
  } else {
    $values_array = $default_values;
  }
  // End of check if it takes into account the default values or the POST values
  $connect_db = connect();
  if (!$resource) {
    if (strpos($sql, " LIMIT ") !== false) {
      $head_sql = substr($sql, 0, strpos($sql, " LIMIT "));
    } else {
      $head_sql = $sql;
    }
    $head_sql .= " LIMIT 0 ";
    $resource = mysql_query($head_sql) or die(reportError(mysql_error(),__FILE__, "function", __FUNCTION__, 'Erreur SQL : '.$sql));
  }
$time_start_b = startMetro();
  $fields = mysql_num_fields($resource); //CHRONOPHAGE?
endMetro($time_start_b, __FUNCTION__." head query");
  $column_side = 1;
  for ($i=0; $i < $fields; $i++) {
    $new_field = "";
    $names = $columns_params[$i]; //mysql_field_name($resource, $i);
    $name_array = explode("|",$names);
    $name = $name_array[0];
    $label = $name_array[1];
    $sub_querry = $name_array[2];
    $multiple_size = $name_array[3];
    if (strpos($name, "[hidden]") === false) {
      $len = mysql_field_len($resource, $i);
      $type = mysql_field_type($resource, $i);
      $flags = mysql_field_flags($resource, $i);
      if (strpos($flags, "not_null") !== false) {
        $mandatory = "*";
      } else {
        $mandatory = "";
      }
      if ($sub_querry != "" || strpos($flags, "enum") !== false) {
        $multiple = "";
        $mult_suffix = "";
        if ($multiple_size > 0) {
          $multiple = "size=\"".$multiple_size."\" multiple=\"multiple\"";
          $mult_suffix = "[]"; //The POST var must know that it's an array of values !! ... this is the trick ! On name attribute only !!!
          $msg_all = "";
        }
        $new_field .= "<select class=\"select2\" id=\"".idEncode($name)."\" name=\"".idEncode($name).$mult_suffix."\" ".$multiple.">\n";
        $selected = $values_array[idEncode($name)];
        if ($sub_querry != "") {
          if (strpos($sub_querry, ";") !== false) {
            $enum_array = explode(";",$sub_querry);
            $new_field .= getOptionsFromArray($enum_array,$msg_all,$selected);
          } else {
            $comparedCol = "value";
            $textCol = "text";
            $new_field .= getOptions($sub_querry, $msg_all, $selected, $comparedCol, $textCol);
          }
        } else {
          $enum_array = getEnumArray($name);
          $new_field .= getOptionsFromArray($enum_array,$msg_all,$selected);
        }
        $new_field .= "</select>";
      } else {
        switch ($type) {
          case "string":
            $new_field .= "<input class=\"input1\" id=\"".idEncode($name)."\" name=\"".idEncode($name)."\" value=\"".$values_array[idEncode($name)]."\" ";
            $new_field .= "type=\"text\" ";
            $len = $len/3;
            $new_field .= "size=\"".min($default_size,0+$len)."\" maxlength=\"".$len."\" />";
          break;
          case "int":
          case "real":
            $name_from = idEncode(">=|".$name);
            $name_to = idEncode("<=|".$name);
            $new_field .= "<input class=\"input1\" id=\"".$name_from."\" name=\"".$name_from."\" value=\"".$values_array[$name_from]."\" type=\"text\" size=\"".$default_size_number."\" />";
            $new_field .= " &le; x &le; <input class=\"input1\" id=\"".$name_to."\" name=\"".$name_to."\" value=\"".$values_array[$name_to]."\" type=\"text\" size=\"".$default_size_number."\" />";
            $name = "<=|".$name;
          break;
          case "datetime":
          case "date":
            $name_from = idEncode(">=|".$name);
            $name_to = idEncode("<=|".$name);
            $new_field .= "<input class=\"input1\" id=\"".$name_from."\" name=\"".$name_from."\" value=\"".$values_array[$name_from]."\" type=\"text\" size=\"10\" maxlength=\"10\" readonly=\"readonly\" /> <a href=\"JavaScript:showCalendar(document.".$filter_form.".elements['".$name_from."'],'yyyy-MM-DD','Pick a date')\"><img src=\"../images/icons/cal.gif\" alt=\"Click Here to use a calendar\" title=\"Click Here to use a calendar\" style=\"cursor:pointer;vertical-align:text-top;border:0px none;\" /></a>";
            $new_field .= " &le; x &le; <input class=\"input1\" id=\"".$name_to."\" name=\"".$name_to."\" value=\"".$values_array[$name_to]."\" type=\"text\" size=\"10\" maxlength=\"10\" readonly=\"readonly\" /> <a href=\"JavaScript:showCalendar(document.".$filter_form.".elements['".$name_to."'],'yyyy-MM-DD','Pick a date')\"><img src=\"../images/icons/cal.gif\" alt=\"Click Here to use a calendar\" title=\"Click Here to use a calendar\" style=\"cursor:pointer;vertical-align:text-top;border:0px none;\" /></a>";
            $name = "<=|".$name;
          break;
          default:
        	break;
        }
      }
      $field_set = "<tr><td class=\"label\">\n";
      $field_set .= "<label class=\"label_portlet\" for=\"".idEncode($name)."\">\n";
      $field_set .= str_replace("[hidden]","",$label)."\n";
			$field_set .= "</label>\n";
      $field_set .= "</td>\n";
      $field_set .= "<td class=\"field\">\n";
      $field_set .= $new_field."\n";
      $field_set .= "</td></tr>\n";
      $field_set_array[] .= $field_set;
    }
  }
  $field_set = "<tr><td>\n<table border=\"0\" cellspacing=\"1\" cellpadding=\"0\">\n";
  for ($i=0; $i<count($field_set_array); $i++) {
    $field_set .= $field_set_array[$i];
    if ($i == (ceil(count($field_set_array)/2)-1) && !$small) {
      $field_set .= "</table>\n</td>\n<td>\n<table border=\"0\" cellspacing=\"1\" cellpadding=\"0\">\n";
    }
  }
  $field_set .= "</table>\n</td></tr>\n";
  mysql_free_result($resource);
  close($connect_db);
	endMetro($time_start, __FUNCTION__);
  return $field_set;
}

function getPageNavigator($length, $current_page, $count, $form)
{
	$time_start = startMetro();
  $navigator = "";
  if ($current_page == "") {
    $current_page = 1;
  }
  if ($count > 1) {
    $start = (ceil($current_page/$length)-1)*$length+1;
    $end = $start+$length-1;
    $from_last = (ceil($count/$length)-1)*$length+1;
    $is_on_start = ($current_page == $start);
    $is_on_end = ($current_page == $end);
    $is_first = ($current_page == 1);
    $is_last = ($current_page == $count);
    if ($is_first) {
    	$navigator .= "<img src=\"../images/icons/PaginateFirstDis.gif\" alt=\"&lt;&lt;\" /> <img src=\"../images/icons/PaginatePreviousDis.gif\" alt=\"&lt;\" /> ";
    } else {
      $navigator .= "<a href=\"JavaScript:document.".$form.".current.value='1';document.".$form.".submit();\" class=\"nothing\"><img src=\"../images/icons/PaginateFirst.gif\" alt=\"&lt;&lt;\" /></a> ";
      if ($is_on_start) {
        $navigator .= "<a href=\"JavaScript:document.".$form.".current.value='".($start-1)."';document.".$form.".submit();\" class=\"nothing\">";
      } else {
        $navigator .= "<a href=\"JavaScript:document.".$form.".current.value='".($current_page-1)."';document.".$form.".submit();\" class=\"nothing\">";
      }
      $navigator .= "<img src=\"../images/icons/PaginatePrevious.gif\" alt=\"&lt;\" /></a> ";
    }
    for ($i=$start;$i<=min($end,$count);$i++) {
      if ($i == $current_page) {
        $navigator .= $i." ";
      } else {
        $navigator .= "<a href=\"JavaScript:document.".$form.".current.value='".$i."';document.".$form.".submit();\">".$i."</a> ";
      }
    }
    if ($is_last) {
      $navigator .= "<img src=\"../images/icons/PaginateNextDis.gif\" alt=\"&gt;\" /> <img src=\"../images/icons/PaginateLastDis.gif\" alt=\"&gt;&gt;\" />";
    } else {
      if ($is_on_end) {
        $navigator .= "<a href=\"JavaScript:document.".$form.".current.value='".($start+$length)."';document.".$form.".submit();\" class=\"nothing\">";
      } else {
        $navigator .= "<a href=\"JavaScript:document.".$form.".current.value='".($current_page+1)."';document.".$form.".submit();\" class=\"nothing\">";
      }
      $navigator .= "<img src=\"../images/icons/PaginateNext.gif\" alt=\"&gt;\" /></a> ";
      $navigator .= "<a href=\"JavaScript:document.".$form.".current.value='".$count."';document.".$form.".submit();\" class=\"nothing\"><img src=\"../images/icons/PaginateLast.gif\" alt=\"&gt;&gt;\" /></a>";
    }
  }
	endMetro($time_start, __FUNCTION__);
  return $navigator;
}

function getWhereClause($POST_vars, $category="", $sql="") {
  //Take into account the filter
  $where_clause = "";
	$where_value_flag = false;
  if (isset($POST_vars['submit_filter']) || isset($POST_vars['order']) || isset($POST_vars['current']) || isset($POST_vars['overview_filter']) || isset($POST_vars['records_by_page'])) {
    foreach($POST_vars as $key => $value) {
      if (!is_array($value)) {
        $value = trim($value);
      }
      $buttons = array("submit_filter","reset_filter","reset","overview_filter","current","order","by","records_by_page","PHPSESSID");
      if (!in_array($key,$buttons) && ($value != Select_default || $value === "0") && $value != "") {
        $key = idDecode($key);
        if (strpos($key, "|") !== false) {
          $key_array = explode("|",$key);
          $operator = $key_array[0];
          $key = $key_array[1];
          $value = $value + 0.0;
          $last_operator = "";
        } else {
          if (is_array($value)) {
            $myValue = "";
            $array_for_walk = $value;
            array_walk($array_for_walk,'set_quotes',"'");
            $myValue = implode(",",$array_for_walk);
            $value = $myValue;
            $operator = "in (";
            $last_operator = ")";
          } else {
            $value = str_replace("*", "%", $value);
            $operator = "like '";
            $last_operator = "%'";
          }
        }
        $where_clause .= str_replace("@", " ",str_replace("*", ".", $key))." ".$operator.$value.$last_operator." AND ";
      }
    }
		$where_value_flag = true;
  }
	/*if (!$where_value_flag) {
		$where_clause .= "1 = 0 AND ";
	}*/
  if (!userIsConnected()) {
    if (strpos($sql, "T_entry.") !== false || strpos($sql, ".`T_entry`") !== false) {
      if (strpos($sql, "T_entry.Is_public = 'YES'") === false) {
        $where_clause .= "(T_entry.Is_public IS NULL OR T_entry.Is_public = 'YES') AND ";
      }
    }
  }
  if ($category != "") {
    if (isset($_SESSION[$category.'_load_conditions']) && $_SESSION[$category.'_load_conditions'] != "") {
      $where_clause .= $_SESSION[$category.'_load_conditions']." AND ";
    }
  }
  if ($where_clause != "") {
    $where_clause = substr($where_clause,0,strlen($where_clause)-5);
  }
  return $where_clause;
}

function getRowsFromSQL($sql, $columns_params, $links, $records_by_page, $filter_form, $list_form, $POST_vars, $input_type, $style, $default_order, $enable_order, $enable_limit, $category="", $default_values=array(), $images=array())
{
    $resource = null;
	$time_start = startMetro();
  //Stores the Group by statement
  $where_clause = "";
  $group_by_statement = "GROUP BY";
  if (strpos($sql,$group_by_statement) !== false) {
    $group_by_clause = substr($sql, strpos($sql,$group_by_statement), strlen($sql)-strpos($sql,$group_by_statement));
    $sql = substr($sql, 0, strpos($sql,$group_by_statement));
  } else {
    $group_by_clause = "";
  }
  // Check if it takes into account the default values or the POST values
  $buttons = array("submit_filter","reset_filter","reset","overview_filter","current","order","by","records_by_page","PHPSESSID");
  $get_post_values = false;
  foreach($buttons as $key => $value) {
    if (isset($POST_vars[$value])) {
      $get_post_values = true;
      break;
    }
  }
  if ($get_post_values) {
    $values_array = $POST_vars;
  } else {
    $values_array = $default_values;
  }
  //Take into account the filter
  $where_clause = getWhereClause($values_array, $category, $sql);
  if ($where_clause != "") {
    if (strpos($sql, "WHERE") === false) {
			$sql .= " WHERE ".$where_clause." ";
			/*if (strpos($sql, "ORDER BY") === false) {
				$sql .= " WHERE ".$where_clause." ";
			} else {
				$sql_array = explode("ORDER BY", $sql);
				$sql_order = array_pop($sql_array);
				$sql_select = implode("ORDER BY", $sql_array);
				$sql = $sql_select." WHERE ".$where_clause." ORDER BY ".$sql_order;
			}*/
    } else {
			$sql .= " AND ".$where_clause." ";
			/*$sql_array = explode("WHERE", $sql);
			$sql_where = array_pop($sql_array);
			$sql_select = implode("WHERE", $sql_array);
			$sql = $sql_select." WHERE ".$where_clause." AND ".$sql_where;*/
    }
  }
  $sql .= " ".$group_by_clause;
//echo '<!--' . $sql . '-->';
  //Construction of headers  
  $order_by = (isset($values_array['by'])) ? $values_array['by'] : '';
  if ($order_by == "") {
    $order_by = $default_order;
  }
  $order_by = $order_by+0;//str_replace("\\","",urldecode($order_by));
  $order = (isset($values_array['order'])) ? $values_array['order'] : '';
  $row_set = "<tr>\n";
  switch ($input_type['type']) {
    case "checkbox":
      $row_set .= "<th><a href=\"JavaScript:checkAll(document.".$list_form.");\" class=\"nothing\" title=\"".$input_type['title']."\" ><img src=\"../images/icons/IcoCheckAll.gif\" alt=\"All\" /></a></th>\n";
    break;
    case "radio":
      $row_set .= "<th>".$input_type['title']."</th>\n";
    break;
    default:
    break;
  }
  if (strpos($sql, " LIMIT ") !== false) {
    $head_sql = substr($sql, 0, strpos($sql, " LIMIT "));
  } else {
    $head_sql = $sql;
  }
/*
  $head_select = "SELECT";
  $select_pos = strpos($head_sql, $head_select) + strlen($head_select);
  $head_sql = substr($head_sql, $select_pos, strlen($head_sql)-$select_pos);
  $head_sql = $head_select." SQL_CALC_FOUND_ROWS ".$head_sql." LIMIT 0,0";
  $count_sql = "SELECT FOUND_ROWS() AS Count";
*/
  $from_pos = strpos($head_sql, 'FROM');
  $count_sql = substr($head_sql, $from_pos, strlen($head_sql)-$from_pos);
	$count_sql = "SELECT COUNT(*) AS `Count` ".$count_sql;
$time_start_b = startMetro();
  $connect_db = connect();
endMetro($time_start_b, __FUNCTION__." connect");
echo '<!--'.$count_sql.'-->';
/*
$time_start_b = startMetro();
  $resource = mysql_query($head_sql) or die(reportError(mysql_error(),__FILE__, "function", __FUNCTION__, 'Erreur SQL : '.$head_sql));
endMetro($time_start_b, __FUNCTION__." head query"); //CHRONOPHAGE
*/
/*
$time_start_b = startMetro();
  $count_res = mysql_query($count_sql) or die(reportError(mysql_error(),__FILE__, "function", __FUNCTION__, 'Erreur SQL : '.$count_sql));
endMetro($time_start_b, __FUNCTION__." count query");
$time_start_b = startMetro();
  $count = mysql_fetch_assoc($count_res);
endMetro($time_start_b, __FUNCTION__." count fetch");
$time_start_b = startMetro();
  $count = $count['Count'];
  mysql_free_result($count_res);
endMetro($time_start_b, __FUNCTION__." free result");
*/

$time_start_b = startMetro();
	$count = getDataFromSQL($count_sql, __FILE__, 'function', __FUNCTION__);
endMetro($time_start_b, __FUNCTION__." count query");
	$count = ($count['Count'] > 1) ? $count['Count'] : $count[0]['Count'];
$time_start_b = startMetro();
  //$resource = mysql_unbuffered_query($sql) or die(reportError(mysql_error(),__FILE__, "function", __FUNCTION__, 'Erreur SQL : '.$sql));
  $columns_nb = count($columns_params);//mysql_num_fields($resource);
endMetro($time_start_b, __FUNCTION__." num fields");
$time_start_b = startMetro();
//  $count = mysql_num_rows($resource);
  for($i=0;$i<$columns_nb;$i++) {
//    $field = mysql_field_name($resource,$i);
    $columns[$i] = $columns_params[$i];
    $field_array = explode("|",$columns[$i]);
//    $field_code = str_replace("*", ".", $field_array[0]);
    $field_name = $field_array[1];
    if (strpos($field_name,'[hidden]') === false) {
      if ($order_by == ($i+1) && $order == "ASC") {
        $next_order = "DESC";
      } else {
        $next_order = "ASC";
      }
      if ($order_by == ($i+1)) {
        if ($order == "ASC") {
          $class = "bg_asc";
        } else {
          $class = "bg_desc";
        }
        $a_class = "class=\"".$class."\" ";
        $th_class = "class=\"ordered\" ";
      } else {
        $a_class = "";
        $th_class = "";
      }
      if ($enable_order) {
        $row_set .= "<th ".$th_class."><a href=\"JavaScript:document.".$filter_form.".order.value='".$next_order."';document.".$filter_form.".by.value='".($i+1)."';document.".$filter_form.".submit();\" ".$a_class.">".$field_name."</a></th>\n";
      } else {
        $row_set .= "<th>".$field_name."</th>\n";
      }
    }
  }
endMetro($time_start_b, __FUNCTION__." columns headers");
$time_start_b = startMetro();
  //mysql_free_result($resource);
  close($connect_db);
endMetro($time_start_b, __FUNCTION__." close db");
  $row_set .= "</tr>\n";
  if ($count > 0) {
    //Take into account the order
    if ($order_by != "" && $order_by <= $columns_nb && $enable_order) {
//      $order_by = explode("|",$order_by);
//      $order_by = str_replace("*", ".", $order_by[0]);
      $sql .= " ORDER BY ".$order_by." ".$order;
    }
    //Limit the result to the current page
    $current_page = (isset($values_array['current'])) ? $values_array['current'] : '';
    if ($current_page == "") {
      $current_page = 1;
    }
    $from_record = ($current_page-1)*$records_by_page;
    $to_record = $records_by_page;
    if ($enable_limit) {
      $sql .= " LIMIT ".$from_record.", ".$to_record." ";
    }
    //Construction of data rows
    $time_start_b = startMetro();
    echo '<!--'.$sql.'-->';
    $values = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
    endMetro($time_start_b, __FUNCTION__." values query"); //CHRONOPHAGE
    for($i=0;$i<$values["Count"];$i++) {
      //New Row
      $row_set .= "<tr>\n";
      //Check the condition disable the input
      $do_enable = true;
      foreach($input_type['conditions'] as $i_key => $i_value) {
        if ($values[$i][$i_key] != $i_value) {
          $do_enable = false;
          break;
        }
      }
      //Input style
      switch ($input_type['type']) {
        case "checkbox":
          $row_set .= "<td><input type=\"checkbox\" ";
          if (!$do_enable) {
            $row_set .= "disabled=\"disabled\" ";
          }
          $row_set .= "class=\"input1\" style=\"border: none;\" id=\"_".$values[$i][0]."\" name=\"".$values[$i][1]."\" /></td>\n";
        break;
        case "radio":
          $row_set .= "<td><input type=\"radio\" ";
          if (!$do_enable) {
            $row_set .= "disabled=\"disabled\" ";
          }
          $row_set .= "class=\"input1\" style=\"border: none;\" id=\"_".$values[$i][0]."\" name=\"radio_list\" value=\"".$values[$i][0]."\" /></td>\n";
        break;
        default:
        break;
      }
      //Add cells
      for($j=0;$j<$columns_nb;$j++) {
        $column = $columns[$j];
        $column_name = explode("|",$column);
        $column_name = $column_name[1];
        if (strpos($column_name,'[hidden]') === false) {
          //New cell
          $row_set .= "<td>";
          $this_value = htmlentities($values[$i][$j], ENT_QUOTES, 'UTF-8');
          if ($this_value == "") {
            //Empty cell
            $row_set .= "&nbsp;";
          } else {
            //Check the style
            $style_class_o = "";
            $style_class_c = "";
            $style_tag = "";
            if (isset($style[$j])) {
              $style_tag = $style[$j]['tag'];
            }
            if ($style_tag != "") {
              $do_style = true;
              foreach($style[$j]['conditions'] as $s_key => $s_value) {
                if ($values[$i][$s_key] != $s_value) {
                  $do_style = false;
                  break;
                }
              }
              if ($do_style) {
                //Apply the class to the value
                $style_class_o = "<".$style_tag." class=\"".$style[$j]['class']."\">";
                $style_class_c = "</".$style_tag.">";
              }
            }
            $row_set .= $style_class_o;
            if (strpos($this_value,"[|]") !== false) {
              $this_value_array = explode("[|]", $this_value);
              for($k=0;$k<count($this_value_array);$k++) {
                if ($this_value_array[$k] != "") {
                  if ($k == 0) {
                    $myValue = getLinkedValue(getImagedValue(nl2br($this_value_array[$k]), $images[$j], $values[$i]), $links[$j], $values[$i]);
                  } else {
//                    $myValue = nl2br(htmlentities($this_value_array[$k], ENT_QUOTES, 'UTF-8'));
                    $myValue = nl2br($this_value_array[$k]);
                  }
                  $row_set .= "<div class=\"sub_cell_".$k."\">".$myValue."</div>";
                }
              }
            } else {
              $getlink_links_array = array();
              $getlink_values_array = array();
              $getimage_images_array = array();
              $getimage_values_array = array();
              if (isset($links[$j])) {
                $getlink_links_array = $links[$j];
              }
              if (isset($images[$j])) {
                $getimage_images_array = $images[$j];
              }
              if (isset($values[$i])) {
                $getlink_values_array = $values[$i];
                $getimage_values_array = $values[$i];
              }
              $myValue = getLinkedValue(getImagedValue(nl2br($this_value), $getimage_images_array, $getimage_values_array), $getlink_links_array, $getlink_values_array);
              $row_set .= $myValue;
            }
            $row_set .= $style_class_c;
          }
          $row_set .= "</td>\n";
        }
      }
      $row_set .= "</tr>\n";
    }
  }
	endMetro($time_start, __FUNCTION__);
  //Return the result
  return array('rows' => $row_set, 'local_count' => $values['Count'], 'total_count' => $count, 'resource_id' => $resource, 'debug' => start_comment.$sql.end_comment);
}

function getLinkedValue($value, $linkArray, $valueArray) {
/*
echo '<pre>';
print_r($linkArray);
print_r($valueArray);
echo '</pre>';
*/
  $this_value = $value;
  if (is_array($linkArray)) {
    if (isset($linkArray['conditions'])) {
      //Check the condition to link the value
      $do_replace = true;
      foreach($linkArray['conditions'] as $c_key => $c_value) {
        if ($valueArray[$c_key] != $c_value) {
          $do_replace = false;
          break;
        }
      }
      if ($do_replace) {
        //Link the value replacing the write parameters
        $link = $linkArray['link'];
        $title = isset($linkArray['title']) ? $linkArray['title'] : null;
        $target = isset($linkArray['target']) ? $linkArray['target'] : null;
        $style = isset($linkArray['style']) ? $linkArray['style'] : null;
        $class = isset($linkArray['class']) ? $linkArray['class'] : null;
        foreach($linkArray['parameters'] as $p_key => $p_value) {
            $p_key = "/" . $p_key . "/i";
            $link = preg_replace($p_key,$valueArray[$p_value],$link);
          $title = preg_replace($p_key,$valueArray[$p_value],$title);
          $target = preg_replace($p_key,$valueArray[$p_value],$target);
          $style = preg_replace($p_key,$valueArray[$p_value],$style);
          $class = preg_replace($p_key,$valueArray[$p_value],$class);
        }
        $text_link = $this_value;
        $this_value = "<a ";
        if (substr($linkArray['target'], 0, 2) == "on") {
					$this_value .= "href=\"#\" ".$linkArray['target']."=\"".$link."\" ";
        } else {
					$this_value .= "href=\"".$link."\" ";
				}
				if ($linkArray['target'] != "" && substr($linkArray['target'], 0, 2) != "on") {
          $this_value .= "target=\"".$target."\" ";
        }
        if (isset($linkArray['title']) && $linkArray['title'] != "") {
          $this_value .= "title=\"".$title."\" ";
        }
        if (isset($linkArray['style']) && $linkArray['style'] != "") {
          $this_value .= "style=\"".$style."\" ";
        }
        if (isset($linkArray['class']) && $linkArray['class'] != "") {
          $this_value .= "class=\"".$class."\" ";
        }
        $this_value .= ">".$text_link."</a>";
      }
    }
  }
  return $this_value;
}

function getImagedValue($value, $imageArray, $valueArray) {
  $this_value = $value;
  if (is_array($imageArray)) {
    if (isset($imageArray['conditions'])) {
      //Check the condition to put an image on the value
      $do_replace = true;
      foreach($imageArray['conditions'] as $c_key => $c_value) {
        if ($valueArray[$c_key] != $c_value) {
          $do_replace = false;
          break;
        }
      }
      if ($do_replace) {
        //Put an image on the value replacing the write parameters
        $src = $imageArray['src'];
        $calss = $imageArray['class'];
        $alt = $imageArray['alt'];
        $style = $imageArray['style'];
        foreach($imageArray['parameters'] as $p_key => $p_value) {
          $src = eregi_replace($p_key,$valueArray[$p_value],$src);
          $calss = eregi_replace($p_key,$valueArray[$p_value],$calss);
          $alt = eregi_replace($p_key,$valueArray[$p_value],$alt);
          $style = eregi_replace($p_key,$valueArray[$p_value],$style);
        }
        $this_value = "<img src=\"".$src."\" ";
        if ($imageArray['class'] != "") {
          $this_value .= "class=\"".$calss."\" ";
        }
        if ($imageArray['style'] != "") {
          $this_value .= "style=\"".$style."\" ";
        }
        if ($imageArray['alt'] != "") {
          $this_value .= "alt=\"".$alt."\" ";
        }
        $this_value .= " />";
      }
    }
  }
  return $this_value;
}

function replaceLinks($text)
{
  // match protocol://address/path/
  $text = preg_replace("#(((ftp://)|(http(s?)://)){1}([a-zA-Z0-9\%\.\?\=\#\_\:\&\/\~\+\@\,\;\-])+)#", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $text);
  // match www.something
  $text = preg_replace("#([^/]){1}(www\.[a-zA-Z0-9\%\.\?\=\#\_\:\&\/\~\+\@\,\;\-]+){1}#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $text);
  // match email address
  $atom   = '[-a-z0-9!#$%&\'*+\\/=?^_`{|}~]';
  $domain = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)';
  $regex = '/(^| )('.$atom.'+'.'(\.'.$atom.'+)*'.'@'.'('.$domain.'{1,63}\.)+'.$domain.'{2,63})/';
  $text = preg_replace($regex, "\\1<a href=\"mailto:\\2\">\\2</a>", $text);
  
  return $text;
}

function br2nl($text)
{
  return preg_replace("/\<br\s*\/?\>/i", "", $text);
}

function crypt_xor($txt, $key)
{
  $utxt = "";
  for($i=0;$i<strlen($txt);$i++) {
    $utxt .= chr($key^ord($txt{$i}));
  }
  return $utxt;
}

function getCryptedPwd($login,$password)
{
  return addslashes(md5($login."*".$password));
}

function formatSimpleTime($time)
{
  $time_separator = ":";
  $time_array = explode($time_separator,$time);
  $simple_time = ($time_array[0] != "" && $time_array[1] != "") ? $time_separator : "";
  $simple_time = $time_array[0].$simple_time.$time_array[1];
  return $simple_time;
}

function getTopFrame($fullScreen = true, $contentTdStyle = "")
{
  //return "<div class=\"frame\"><div class=\"frameTop\"><div class=\"frameTopLeft\"></div><div class=\"frameTopRight\"></div><div class=\"frameTopCenter\"></div></div><div class=\"frameMidLeft\"><div class=\"frameMidRight\"><div class=\"frameContent\">";
  $style = "";
  if (!$fullScreen) {
    $style = " style=\"height:0\" ";
  }
  return '<table border="0" cellspacing="0" cellpadding="0" class="frame"'.$style.'><tr class="frameTop"><td class="frameTopLeft"></td><td class="frameTopCenter"></td><td class="frameTopRight"></td></tr><tr class="frameMid"><td class="frameMidLeft">&nbsp;</td><td class="frameContent" style="'.$contentTdStyle.'">';
}

function getBotFrame()
{
  //return "</div></div></div><div class=\"frameClear\"></div><div class=\"frameBot\"><div class=\"frameBotLeft\"></div><div class=\"frameBotRight\"></div><div class=\"frameBotCenter\"></div></div></div>";
  return '</td><td class="frameMidRight">&nbsp;</td></tr><tr class="frameBot"><td class="frameBotLeft"></td><td class="frameBotCenter"></td><td class="frameBotRight"></td></tr></table>';
}

function getTopBubble()
{
  return '<table cellspacing="0" cellpadding="0" class="bubble"><tr><td class="btl"/><td class="btm">&nbsp;</td><td class="btr"/></tr><tr><td class="bcl">&nbsp;</td><td class="bcm">';
}

function getBotBubble()
{
  return '</td><td class="bcr">&nbsp;</td></tr><tr><td class="bbl"/><td class="bbm">&nbsp;</td><td class="bbr"/></tr></table>';
}

function getTopBox($bgColor, $bColor, $fgColor)
{
  return '<table style="background-color:#'.$bgColor.';border:1px solid #'.$bColor.';" cellspacing="3" cellpadding="5" border="0">
    <tr>
    <td valign="top" bgcolor="#'.$fgColor.'">';
}

function getBotBox()
{
  return '</td>
    </tr>
  </table>';
}

function getTopMenu($title)
{
  $rndId = rand();
  return '<table cellspacing="0" cellpadding="0" class="menu_box"><tr><td class="mbtl"/><td class="mbtm">&nbsp;</td><td class="mbtr"/></tr><tr><td class="mbtil">&nbsp;</td><td class="mbtim"><div class="mbtitle"><span onclick="JavaScript:switchDOM(\'menu'.$rndId.'\');" style="float:left;cursor:pointer;position:relative;top:2px;"><img src="../images/icons/minus.png" alt="-" /></span>'.$title.'</div></td><td class="mbtir">&nbsp;</td></tr><tr><td class="mbcl">&nbsp;</td><td><div id="menu'.$rndId.'" class="mbcm">';
}

function getBotMenu()
{
  return '</div></td><td class="mbcr">&nbsp;</td></tr><tr><td colspan="3" class="mbb">&nbsp;</td></tr></table>';
}

function explainSQL($sql)
{
  if (strpos($sql, "JOIN") !== false) {
    $sql_temp = str_replace(",", ",<br/>\n",$sql);
    $sql_temp = str_replace("SELECT", "SELECT<br/>\n",$sql_temp);
    $sql_temp = str_replace("FROM", "<br/>\nFROM",$sql_temp);
    $sql_temp = str_replace("LEFT OUTER JOIN", "<br/>\nLEFT OUTER JOIN",$sql_temp);
    $sql_temp = str_replace("INNER JOIN", "<br/>\nINNER JOIN",$sql_temp);
    $sql_temp = str_replace("WHERE", "<br/>\nWHERE",$sql_temp);
    $sql_temp = str_replace("ORDER", "<br/>\nORDER",$sql_temp);
    $sql = "EXPLAIN ".$sql;
    $connect_db = connect();
    $req = mysql_query($sql) or die(reportError(mysql_error(), __FILE__, "function", __FUNCTION__, 'Erreur SQL : '.$sql));
    $td_open = "<td>";
    $td_close = "</td>";
    $tr_open = "<tr>";
    $tr_close .= "</tr>";
    while($data = mysql_fetch_assoc($req)) {
      $returnColumn = $tr_open;
      $returnValue .= $tr_open;
      foreach($data as $key => $value) {
        $returnColumn .= $td_open.$key.$td_close;
        $returnValue .= $td_open.$value.$td_close;
      }
      $returnValue .= $tr_close;
      $returnColumn .= $tr_close;
    }
    mysql_free_result($req);
    close($connect_db);
    $mail_body = "SQL : <br/>\n".$sql_temp."<br/>\n<table border=\"1\">".$returnColumn.$returnValue."</table>"; 
    //echo start_comment.$mail_body.end_comment;
    sendMail("contact@grottocenter.org","Analyse SQL",$mail_body,"","",false);
  }
}

function execSQL($sql, $frame, $file, $function)
{
  $connect_db = connect();
  $req = mysql_query($sql) or die(reportError(mysql_error(),$file, $frame, $function, 'Erreur SQL : '.$sql));
  if ($req) {
    $id = mysql_insert_id($connect_db);
    $affected_rows = mysql_affected_rows($connect_db);
  }
  //mysql_free_result($req);
  close($connect_db);
  $array = array('mysql_query' => $req, 'mysql_insert_id' => $id, 'mysql_affected_rows' => $affected_rows);
  return $array;
}

function getAddThisButton($language, $id)
{
  $return = start_comment." AddThis Button BEGIN ".end_comment;
  $return .= "<script type=\"text/javascript\">";
  $return .= "var addthis_pub=\"".$id."\";"; //clemrz
  //$return .= "var addthis_brand = \"".$_SESSION['Application_name']."\";";
  $return .= "var addthis_header_color = \"#34558A\";";
  $return .= "var addthis_header_background = \"#ffffff\";";
  $return .= "var addthis_language = \"".strtolower($language)."\";";
  //$return .= "var addthis_options = 'favorites, facebook, email, twitter, google, more';";
  $return .= "</script>";
  $return .= "<span "; //a href=\"http://www.addthis.com/bookmark.php?v=20\"
  $return .= "onmouseover=\"return addthis_open(this, '', '".$_SESSION['Application_url']."', '".$_SESSION['Application_name']."')\" ";
  $return .= "onmouseout=\"addthis_close()\" ";
  $return .= "onclick=\"return addthis_sendto()\" style=\"cursor:pointer; vertical-align:top;\">";
  $return .= "  <img src=\"http://s7.addthis.com/static/btn/lg-share-".strtolower($language).".gif\" width=\"125\" height=\"16\" alt=\"SHARE THIS SITE !\" style=\"border:0\" />";
  $return .= "</span>"; //a
  $return .= "<script type=\"text/javascript\" src=\"http://s7.addthis.com/js/200/addthis_widget.js\"></script>";
  $return .= start_comment." AddThis Button END ".end_comment;
  return $return;
}

function cleanString($string)  
{  
  // Replace other special chars  
  $specialCharacters = array('#' => '','$' => '','%' => '','&' => '','@' => '','.' => '','€' => '','+' => '','=' => '','§' => '','\\' => '','/' => '');
  while (list($character, $replacement) = each($specialCharacters)) {  
    $string = str_replace($character, $replacement, $string);
  }
  $string = strtr($string, 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝÑàáâãäåçèéêëìíîïðòóôõöùúûüýÿñ', 'AAAAAACEEEEIIIIOOOOOUUUUYNaaaaaaceeeeiiiioooooouuuuyyn');
  // Remove all remaining other unknown characters
  $string = preg_replace('/[^a-zA-Z0-9\-]/', ' ', $string);
  $string = str_replace(' ', '-', $string);
  $string = preg_replace('/^[\-]+/', '', $string);
  $string = preg_replace('/[\-]+$/', '', $string);
  $string = preg_replace('/[\-]{2,}/', '-', $string);
  $string = strtolower($string);
  return $string;  
}

//http://www.asp-php.net/ressources/bouts_de_code.aspx?id=202
function BBCodeToHTML($t)
{
  $t = str_replace("[/]", "<hr width=\"100%\" size=\"1\" />", $t);
  $t = str_replace("[hr]", "<hr width=\"100%\" size=\"1\" />", $t);
  $t = str_replace("[b]", "<strong>", $t);
  $t = str_replace("[/b]", "</strong>", $t);
  $t = str_replace("[i]", "<em>", $t);
  $t = str_replace("[/i]", "</em>", $t);
  $t = str_replace("[u]", "<u>", $t);
  $t = str_replace("[/u]", "</u>", $t);
  $t = str_replace("[list]", "<ul>", $t);
  $t = str_replace("[/list]", "</ul>", $t);
  $t = preg_replace('`\[/list:u:[a-zA-Z0-9]+\]`i','</ul>', $t);
  $t = str_replace("[*]", "<li>", $t);
  $t = str_replace("[/*]", "</li>", $t);
  $t = str_replace("[center]", "<div style=\"text-align: center\">", $t);
  $t = str_replace("[/center]", "</div>", $t);
  $t = str_replace("[right]", "<div style=\"text-align: right\">", $t);
  $t = str_replace("[/right]", "</div>", $t);
  $t = str_replace("[justify]", "<div style=\"text-align: justify\">", $t);
  $t = str_replace("[/justify]", "</div>", $t);
  $t = str_replace("[/color]", "</span>", $t);
  $regCouleur = "\[color= ?(([[:alpha:]]+)|(#[[:digit:][:alpha:]]{6})) ?\]";
  $t = ereg_replace($regCouleur, "<span style=\"color: \\1\">", $t);
  $t = str_replace("[/size]", "</span>", $t);
  $regCouleur = "\[size= ?([[:digit:]]+) ?\]";
  $t = ereg_replace($regCouleur, "<span style=\"font-size: \\1px\">", $t);
  $regLienSimple = "\[url\] ?([^\[]*) ?\[/url\]";
  $regLienEtendu = "\[url ?=([^\[]*) ?] ?([^]]*) ?\[/url\]";
  if (ereg($regLienSimple, $t)) $t = ereg_replace($regLienSimple, "<a href=\"\\1\">\\1</a>", $t);
  if (ereg($regLienEtendu, $t)) $t = ereg_replace($regLienEtendu, "<a href=\"\\1\" target=\"_blank\">\\2</a>", $t);
  $regMailSimple = "\[email\] ?([^\[]*) ?\[/email\]";
  $regMailEtendu = "\[email ?=([^\[]*) ?] ?([^]]*) ?\[/email\]";
  if (ereg($regMailSimple, $t)) $t = ereg_replace($regMailSimple, "<a href=\"mailto:\\1\">\\1</a>", $t);
  if (ereg($regMailEtendu, $t)) $t = ereg_replace($regMailEtendu, "<a href=\"mailto:\\1\">\\2</a>", $t);
  $regImage = "\[img\] ?([^\[]*) ?\[/img\]";
  $regImageAlternatif = "\[img ?= ?([^\[]*) ?\]";
  if (ereg($regImage, $t)) $t = ereg_replace($regImage, "<img src=\"\\1\" alt=\"\" border=\"0\" />", $t);
  if (ereg($regImageAlternatif, $t)) $t = ereg_replace($regImageAlternatif, "<img src=\"\\1\" alt=\"\" border=\"0\" />", $t);
  return $t;
}

function getForumPosts($forum_id, $topic_id=0)
{
  $sql = "SELECT bbcode_uid, forum_id, topic_id, post_time, post_subject, post_text ";
  $sql .= "FROM forum_posts ";
  $sql .= "WHERE forum_id = ".$forum_id." ";
	if ($topic_id != 0) {
		$sql .= "AND topic_id = ".$topic_id." ";
	}
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  $post = array();
  for($i=0;$i<$data['Count'];$i++) {
		$uid = $data[$i]['bbcode_uid'];
		$post[$i]['forum_id'] = $data[$i]['forum_id'];
		$post[$i]['topic_id'] = $data[$i]['topic_id'];
    $post[$i]['date'] = $data[$i]['post_time'];
    $post[$i]['subject'] = $data[$i]['post_subject'];
    //$post[$i]['text'] = @html_entity_decode(nl2br(BBCodeToHTML(preg_replace('`\[([/a-z0-9=#*]+):[a-zA-Z0-9]+\]`i','[$1]', preg_replace('~&#([0-9]+);~e', 'chr("\\1")', preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $data[$i]['post_text']))))), ENT_QUOTES, "UTF-8");
		$tag_reg = "`\[([/a-z0-9=#*]+):(".$uid.")\]`i";
		$post[$i]['text'] = nl2br(BBCodeToHTML(preg_replace($tag_reg,'[$1]', preg_replace('~&#([0-9]+);~e', 'chr("\\1")', preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $data[$i]['post_text'])))));
  }
  $post['Count'] = $data['Count'];
  return $post;
}

function getHelpTopic($helpId, $helpTitle)
{
  $help = "";
  if ($helpId != '') {
    $sql = "SELECT Id_forum, Id_topic FROM J_help_topic WHERE Id_help = ".$helpId." AND Language = '".$_SESSION['language']."'";
    $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
    if ($data['Count'] != 0) {
      $url = "help_".$_SESSION['language'].".php?f=".$data[0]['Id_forum']."&amp;t=".$data[0]['Id_topic'];
      $windowName = "";
      $widht = 500;
      $height = 400;
      $help = '<span class="help"><a class="nothing" href="JavaScript:openWindow(\''.$url.'\', \''.$windowName.'\', '.$widht.', '.$height.');"><img src="../images/icons/help.png" alt="'.$helpTitle.'" style="border:0px none;" title="'.$helpTitle.'" /></a></span>';
    }
  }
  return $help;
}

function getFacebookATag($username, $withPic = false)
{
  if ($username == '') {
    return '';
  }
  $facebookRoot = "http://www.facebook.com/";
  $img = '<img style="border:0px none;" src="'.$facebookRoot.'favicon.ico" alt="facebook" title="facebook" />';
  $aTag = '<a href="'.$facebookRoot.$username.'" title="'.$facebookRoot.$username.'" target="_blank" id="facebook_link">';
  if ($withPic) {
    $aTag .= $img;
  } else {
    $aTag .= $facebookRoot.$username;
  }
  $aTag .= '</a>';
  return $aTag;
}

function getTocken($sess_var)
{
  $token = md5($_SERVER['HTTP_REFERER'].uniqid(mt_rand(), TRUE));
  $_SESSION[$sess_var] = $token;
  return $_SESSION[$sess_var];
}


function escapeCharsForReg($string)
{
  $returned_value = str_replace('[', '\\[', $string);
  $returned_value = str_replace(']', '\\]', $returned_value);
  $returned_value = str_replace('(', '\\(', $returned_value);
  $returned_value = str_replace(')', '\\)', $returned_value);
  $returned_value = str_replace('-', '\\-', $returned_value);
  $returned_value = str_replace('[', '\\[', $returned_value);
  $returned_value = str_replace('.', '\\.', $returned_value);
  $returned_value = str_replace('*', '\\*', $returned_value);
  $returned_value = str_replace('?', '\\?', $returned_value);
  $returned_value = str_replace('|', '\\|', $returned_value);
  $returned_value = str_replace('^', '\\^', $returned_value);
  $returned_value = str_replace('$', '\\$', $returned_value);
  return $returned_value;
}

function microtime2float()
{
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

function startMetro()
{
	return microtime2float();
}

function endMetro($time_start, $msg)
{
	$time_end = microtime2float();
	$time = $time_end - $time_start;
	echo "<!--".$msg.": ".$time."-->"."\n";	
}
/**USE:
my_function()
{
	$time_start = startMetro();
	...
	endMetro($time_start, __FUNCTION__);
}
**/

function getNewsletterMails()
{
//select distinct mail where ...
}

function getAllMails()
{
//select distinct mail.
}

//array_walk — Exécute une fonction sur chacun des éléments d'un tableau
/*
SELECT Category, Id, Id_entry
FROM `V_contributions`
WHERE Body LIKE '%<%'
OR Body LIKE '%>%'
ORDER BY 1 , 2
*/
?>