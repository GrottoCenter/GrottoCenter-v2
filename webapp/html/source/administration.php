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
 * @copyright Copyright (c) 2009-1912 Cl�ment Ronzon
 * @license http://www.gnu.org/licenses/agpl.txt
 */
include("../conf/config.php");
include("../func/function.php");
include("declaration.php");
if (!allowAccess(appli_view_all)){ 
  exit();
}
$frame = "filter";
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
    <script type="text/javascript" src="<?php echo getScriptJS(__FILE__); ?>"></script>
<?php
  	include("application_".$_SESSION['language'].".php");
  	include("mailfunctions_".$_SESSION['language'].".php");
?>
    <?php echo getMetaTags(); ?>
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=690<convert></title><!--Administration-->
    <link rel="stylesheet" type="text/css" href="../css/filter.css" />
    <link rel="stylesheet" type="text/css" href="../css/portlet.css" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
    <script type="text/javascript" src="../scripts/classeGCTest.js"></script>
<?php
//Variables initialisation
$type = (isset($_GET['type'])) ? $_GET['type'] : '';
$Name = '';
//$Is_current = '';
$Version = '';
$Url = '';
$Contact = '';
$Host = '';
$Timer_min = '';
$Availability = '';
$Estimated_reopening_time = '';
$Creation = '';
$Revision = '';
$Authors = '';
$Authors_contact = '';
$Thanks = '';
$Copyright = '';
$Comments = '';
$save_failed = false;
$isNew = '';
$id = '';

if (allowAccess(appli_edit_all)) {
  //Save the application :
  if ($type == "application") {
    if (isset($_POST['save']) || isset($_POST['update'])){
      $Name = (isset($_POST['Name'])) ? $_POST['Name'] : '';
      //$Is_current = (isset($_POST['Is_current'])) ? $_POST['Is_current'] : '';
      $Version = (isset($_POST['Version'])) ? $_POST['Version'] : '';
      $Url = (isset($_POST['Url'])) ? $_POST['Url'] : '';
      $Contact = (isset($_POST['Contact'])) ? $_POST['Contact'] : '';
      $Host = (isset($_POST['Host'])) ? $_POST['Host'] : '';
      $Timer_min = (isset($_POST['Timer_min'])) ? $_POST['Timer_min'] : '';
      $Availability = (isset($_POST['Availability'])) ? $_POST['Availability'] : '';
      $Estimated_reopening_time = (isset($_POST['Estimated_reopening_time'])) ? $_POST['Estimated_reopening_time'] : '';
      $Creation = (isset($_POST['Creation'])) ? $_POST['Creation'] : '';
      $Revision = (isset($_POST['Revision'])) ? $_POST['Revision'] : '';
      $Authors = (isset($_POST['Authors'])) ? $_POST['Authors'] : '';
      $Authors_contact = (isset($_POST['Authors_contact'])) ? $_POST['Authors_contact'] : '';
      $Thanks = (isset($_POST['Thanks'])) ? $_POST['Thanks'] : '';
      $Copyright = (isset($_POST['Copyright'])) ? $_POST['Copyright'] : '';
      $Comments = (isset($_POST['Comments'])) ? $_POST['Comments'] : '';
      if (isset($_POST['save'])) {
        $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_application` ";
        $sql .= " SET ";
        $sql .= "Is_current = 'NO' ";
        $sql .= "WHERE Is_current = 'YES' ";
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`T_application` ";
        $sql .= "(Name, Is_current, Version, Url, Contact, Host, Timer_min, Availability, Estimated_reopening_time, Creation, Revision, Authors, Authors_contact, Thanks, Copyright, Id_comments) VALUES (";
        $sql .= returnDefault($Name, 'text').", ";
        $sql .= returnDefault('YES', 'text').", ";
        $sql .= returnDefault($Version, 'text').", ";
        $sql .= returnDefault($Url, 'text').", ";
        $sql .= returnDefault($Contact, 'text').", ";
        $sql .= returnDefault($Host, 'text').", ";
        $sql .= returnDefault($Timer_min, 'float').", ";
        $sql .= returnDefault($Availability, 'text').", ";
        $sql .= returnDefault($Estimated_reopening_time, 'text').", ";
        $sql .= returnDefault($Creation, 'text').", ";
        $sql .= "Now(), ";
        $sql .= returnDefault($Authors, 'text').", ";
        $sql .= returnDefault($Authors_contact, 'text').", ";
        $sql .= returnDefault($Thanks, 'text').", ";
        $sql .= returnDefault($Copyright, 'text').", ";
        $sql .= returnDefault($Comments, 'int').") ";
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
      } else {
        $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_application` ";
        $sql .= " SET ";
        $sql .= "Name = ".returnDefault($Name, 'text').", ";
        $sql .= "Version = ".returnDefault($Version, 'text').", ";
        $sql .= "Url = ".returnDefault($Url, 'text').", ";
        $sql .= "Contact = ".returnDefault($Contact, 'text').", ";
        $sql .= "Host = ".returnDefault($Host, 'text').", ";
        $sql .= "Timer_min = ".returnDefault($Timer_min, 'float').", ";
        $sql .= "Availability = ".returnDefault($Availability, 'text').", ";
        $sql .= "Estimated_reopening_time = ".returnDefault($Estimated_reopening_time, 'text').", ";
        $sql .= "Creation = ".returnDefault($Creation, 'text').", ";
        $sql .= "Revision = Now(), ";
        $sql .= "Authors = ".returnDefault($Authors, 'text').", ";
        $sql .= "Authors_contact = ".returnDefault($Authors_contact, 'text').", ";
        $sql .= "Thanks = ".returnDefault($Thanks, 'text').", ";
        $sql .= "Copyright = ".returnDefault($Copyright, 'text').", ";
        $sql .= "Id_comments = ".returnDefault($Comments, 'int')." ";
        $sql .= "WHERE Is_current = 'YES' ";
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
      }
    }
  }
}
 
if (allowAccess(right_edit_all)) { 
  //Save the right :
  if ($type == "right_edit") {
    if (isset($_POST['save'])){
      $save_failed = true;
      $Name = (isset($_POST['right_name'])) ? $_POST['right_name'] : '';
      $Comments = (isset($_POST['right_comments'])) ? $_POST['right_comments'] : '';
      $list = (isset($_POST['g_list'])) ? $_POST['g_list'] : '';
      $isNew = (isset($_POST['is_new'])) ? $_POST['is_new'] : '';
      $id = (isset($_POST['right_id'])) ? $_POST['right_id'] : '';
      if ($isNew == "True") {
        $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`T_right` ";
        $sql .= "(`Name`, `Comments`)";
        $sql .= " VALUES (";
        $sql .= returnDefault($Name, 'text').", ";
        $sql .= returnDefault($Comments, 'text').") ";
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        $nid = $req['mysql_insert_id'];
      } else {
        $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_right` ";
        $sql .= " SET ";
        $sql .= "Name = ".returnDefault($Name, 'text').", ";
        $sql .= "Comments = ".returnDefault($Comments, 'text')." ";
        $sql .= "WHERE Id = ".$id;
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_group_right` ";
      	$sql .= "WHERE `Id_right` = ".$id;
      	$req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        $nid = $id;
      }
      if ($list != "") {
        $arrList = split('[|]+', $list);
        $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`J_group_right` (`Id_right`, `Id_group`) VALUES ";
        foreach($arrList as $value) {
          $sql .= "(".$nid.", ".$value."), ";
        }
        $sql = substr($sql,0,strlen($sql)-2);
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
      }
      $save_failed = false;
      $type = "right";
    } else {
      if (isset($_GET['id'])) {
        $id = (isset($_GET['id'])) ? $_GET['id'] : '';
        if ($id != "") {
          $sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`T_right` WHERE Id = ".$id;
          $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
          $Name = $data[0]['Name'];
          $Comments = $data[0]['Comments'];
          $isNew = "False";
        } else {
          $type = "right";
        }
      } else {
        $isNew = "True";
      }
    }
  }
}
  
if (allowAccess(group_edit_all)) {
  //Save the group :
  if ($type == "group_edit") {
    if (isset($_POST['save'])){
      $save_failed = true;
      $Name = (isset($_POST['group_name'])) ? $_POST['group_name'] : '';
      $Comments = (isset($_POST['group_comments'])) ? $_POST['group_comments'] : '';
      $rlist = (isset($_POST['r_list'])) ? $_POST['r_list'] : '';
      $clist = (isset($_POST['c_list'])) ? $_POST['c_list'] : '';
      $isNew = (isset($_POST['is_new'])) ? $_POST['is_new'] : '';
      $id = (isset($_POST['group_id'])) ? $_POST['group_id'] : '';
      if ($isNew == "True") {
        $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`T_group` ";
        $sql .= "(`Name`, `Comments`)";
        $sql .= " VALUES (";
        $sql .= returnDefault($Name, 'text').", ";
        $sql .= returnDefault($Comments, 'text').") ";
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        $nid = $req['mysql_insert_id'];
      } else {
        $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_group` ";
        $sql .= " SET ";
        $sql .= "Name = ".returnDefault($Name, 'text').", ";
        $sql .= "Comments = ".returnDefault($Comments, 'text')." ";
        $sql .= "WHERE Id = ".$id;
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_group_right` ";
      	$sql .= "WHERE `Id_group` = ".$id;
      	$req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_caver_group` ";
      	$sql .= "WHERE `Id_group` = ".$id;
      	$req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        $nid = $id;
      }
      if ($rlist != "") {
        $arrList = split('[|]+', $rlist);
        $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`J_group_right` (`Id_group`, `Id_right`) VALUES ";
        foreach($arrList as $value) {
          $sql .= "(".$nid.", ".$value."), ";
        }
        $sql = substr($sql,0,strlen($sql)-2);
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
      }
      if ($clist != "") {
        $arrList = split('[|]+', $clist);
        $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`J_caver_group` (`Id_group`, `Id_caver`) VALUES ";
        foreach($arrList as $value) {
          $sql .= "(".$nid.", ".$value."), ";
        }
        $sql = substr($sql,0,strlen($sql)-2);
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
      }
      $save_failed = false;
      $type = "group";
    } else {
      if (isset($_GET['id'])) {
        $id = (isset($_GET['id'])) ? $_GET['id'] : '';
        if ($id != "") {
          $sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`T_group` WHERE Id = ".$id;
          $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
          $Name = $data[0]['Name'];
          $Comments = $data[0]['Comments'];
          $isNew = "False";
        } else {
          $type = "group";
        }
      } else {
        $isNew = "True";
      }
    }
  }
}
  
if (allowAccess(group_delete_all)) {
  //Delete a group
  if ($type == "group_delete") {
    $did = (isset($_GET['did'])) ? $_GET['did'] : '';
    if ($did != "") {
      $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`T_group` ";
      $sql .= "WHERE Id = ".$did;
      $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
      $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_group_right` ";
    	$sql .= "WHERE `Id_group` = ".$did;
    	$req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
      $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_caver_group` ";
    	$sql .= "WHERE `Id_group` = ".$did;
    	$req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
      $delete_failed = false;
    } else {
      $delete_failed = true;
    }
    $type = "group";
  }
}
  
if (allowAccess(caver_edit_all)) {
  //Save the user :
  if ($type == "caver_edit") {
    $save_failed = true;
    if (isset($_POST['save'])){
      $list = (isset($_POST['g_list'])) ? $_POST['g_list'] : '';
      $id = (isset($_POST['caver_id'])) ? $_POST['caver_id'] : '';
      $caver_contact = (isset($_POST['p_caver_contact'])) ? $_POST['p_caver_contact'] : '';
      $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_caver` SET `Contact` = ".returnDefault($caver_contact, 'text')." WHERE Id = ".$id;
      $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
      $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_caver_group` ";
    	$sql .= "WHERE `Id_caver` = ".$id;
    	$req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
      if ($list != "") {
        $arrList = split('[|]+', $list);
        $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`J_caver_group` (`Id_caver`, `Id_group`) VALUES ";
        foreach($arrList as $value) {
          $sql .= "(".$id.", ".$value."), ";
        }
        $sql = substr($sql,0,strlen($sql)-2);
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
      }
      $save_failed = false;
      $type = "caver";
    } else {
      $save_failed = false;
      if (isset($_GET['id'])) {
        $id = (isset($_GET['id'])) ? $_GET['id'] : '';
        if ($id != "") {
          $sql = "SELECT IF(CONCAT_WS(' ',Surname,Name)='',Login,CONCAT_WS(' ',Surname,Name)) As name, Contact FROM `".$_SESSION['Application_host']."`.`T_caver` WHERE Id = ".$id;
          $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
          $Name = $data[0]['name'];
          $caver_contact = $data[0]['Contact'];
        } else {
          $type = "caver";
        }
      } else {
        $type = "caver";
      }
    }
  }
  if ($type == "caver_prop") {
    $activated = (isset($_GET['Activated'])) ? $_GET['Activated'] : '';
    $banned = (isset($_GET['Banned'])) ? $_GET['Banned'] : '';
    $id = (isset($_GET['id'])) ? $_GET['id'] : '';
    $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_caver` SET ";
    $sql .= "Activated = ".returnDefault($activated, "text").", ";
    $sql .= "Banned = ".returnDefault($banned, "text")." ";
    $sql .= "WHERE `Id` = ".$id;
    $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
    $save_failed = false;
    $type = "caver";
  }
}
?>
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
    //Gona need those functions : switchDOM();
    var namesArray = [];
<?php
    switch ($type) {
    	case "menu":
?>
    function menuBeforeLoad() {
      parent.setFilterSize(35);
    }
    
    function menuOnLoad() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      var oHtml = document.getElementsByTagName('HTML')[0];
      parent.setFilterSizeTight(oHtml);
    }
<?php
    	break;
    	case "right":
?>
    function rightBeforeLoad() {
      parent.setFilterSize(35);
    }
    
    function rightOnLoad() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      var oHtml = document.getElementsByTagName('HTML')[0];
      parent.setFilterSizeTight(oHtml);
    }
    
    function rightNew() {
      self.location.href = "administration_<?php echo $_SESSION['language']; ?>.php?type=right_edit";
    }
    
    function rightEdit(oForm) {
      var oRadio = oForm.radio_list;
      var id = getRadioValue(oRadio);
      if (id) {
        self.location.href = "administration_<?php echo $_SESSION['language']; ?>.php?type=right_edit&id=" + id;
      }
    }
    
    function rightRefresh(oForm) {
      oForm.submit();
    }
<?php
    	break;
    	case "right_edit":
?>
    function rightEditBeforeLoad() {
      parent.setFilterSize(35);
    }
    
    function rightEditOnLoad() {
      var oForm;
      oForm = document.right_edit;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      parent.setFilterSize(getMaxWidthInput(oForm),"px");
      var oHtml = document.getElementsByTagName('HTML')[0];
      parent.setFilterSizeTight(oHtml);
      namesArray = loadNames("right");
      checkThisName(oForm.right_name, 'name_pic');
    }
    
    function newCancel() {
      self.location.href = "administration_<?php echo $_SESSION['language']; ?>.php?type=right";
    }
    
    function rightEditSubmit(event) {
      var oForm = document.right_edit;
      var rightSource = toAbsURL(rightPic);
      var oField = xtdGetElementById('name_pic');
      var sMessage = "<convert>#label=713<convert> <convert>#label=714<convert> | / \\ ' \" & + <convert>#label=46<convert> ¨";//Le nom du droit //doit être unique, de 2 à 68 caractères sauf //et
      createTest(oField.name, oField.src, rightSource, "==", sMessage, true);
      if (!testForm()) {
        stopSubmit(event);
      } else {
      	doChallengeList(oForm.g_myList,oForm.g_list);
      }
    }
<?php
    	break;
    	case "group":
?>
    function groupBeforeLoad() {
      parent.setFilterSize(35);
    }
    
    function groupOnLoad() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      var oHtml = document.getElementsByTagName('HTML')[0];
      parent.setFilterSizeTight(oHtml);
    }
    
    function groupNew() {
      self.location.href = "administration_<?php echo $_SESSION['language']; ?>.php?type=group_edit";
    }
    
    function groupEdit(oForm) {
      var oRadio = oForm.radio_list;
      var id = getRadioValue(oRadio);
      if (id) {
        self.location.href = "administration_<?php echo $_SESSION['language']; ?>.php?type=group_edit&id=" + id;
      }
    }
    
    function groupRefresh(oForm) {
      oForm.submit();
    }
    
    function groupDelete(oForm) {
      var oRadio = oForm.radio_list;
      var id = getRadioValue(oRadio);
      if (id) {
        self.location.href = "administration_<?php echo $_SESSION['language']; ?>.php?type=group_delete&did=" + id;
      }
    }
<?php
    	break;
    	case "group_edit":
?>
    function groupEditBeforeLoad() {
      parent.setFilterSize(35);
    }
    
    function groupEditOnLoad() {
      var oForm;
      oForm = document.group_edit;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      parent.setFilterSize(getMaxWidthInput(oForm),"px");
      var oHtml = document.getElementsByTagName('HTML')[0];
      parent.setFilterSizeTight(oHtml);
      namesArray = loadNames("group");
      checkThisName(oForm.group_name, 'name_pic');
    }
    
    function newCancel() {
      self.location.href = "administration_<?php echo $_SESSION['language']; ?>.php?type=group";
    }
    
    function groupEditSubmit(event) {
      var oForm = document.group_edit;
      var rightSource = toAbsURL(rightPic);
      var oField = xtdGetElementById('name_pic');
      var sMessage = "<convert>#label=715<convert> <convert>#label=714<convert> | / \\ ' \" & + <convert>#label=46<convert> ¨";//Le nom du groupe //doit être unique, de 2 à 68 caractères sauf //et
      createTest(oField.name, oField.src, rightSource, "==", sMessage, true);
      if (!testForm()) {
        stopSubmit(event);
      } else {
      	doChallengeList(oForm.r_myList,oForm.r_list);
      	doChallengeList(oForm.c_myList,oForm.c_list);
      }
    }
<?php
    	break;
    	case "caver":
?>
    function caverBeforeLoad() {
      parent.setFilterSize(35);
    }
    
    function caverOnLoad() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      var oHtml = document.getElementsByTagName('HTML')[0];
      parent.setFilterSizeTight(oHtml);
    }
    
    function caverEdit(oForm) {
      var oRadio = oForm.radio_list;
      var id = getRadioValue(oRadio);
      if (id) {
        self.location.href = "administration_<?php echo $_SESSION['language']; ?>.php?type=caver_edit&id=" + id;
      }
    }
    
    function caverChangeProp(oForm) {
      var oRadio, id, prop;
      oRadio = oForm.radio_list;
      id = getRadioValue(oRadio);
      prop = "";
      if (id) {
        if (oForm.Activated.checked) {
          prop = prop + "&Activated=YES";
        } else {
          prop = prop + "&Activated=NO";
        }
        if (oForm.Banned.checked) {
          prop = prop + "&Banned=YES";
        } else {
          prop = prop + "&Banned=NO";
        }
        self.location.href = "administration_<?php echo $_SESSION['language']; ?>.php?type=caver_prop&id=" + id + prop;
      }
    }
    
    function caverRefresh(oForm) {
      oForm.submit();
    }
<?php
    	break;
    	case "caver_edit":
?>
    function caverEditBeforeLoad() {
      parent.setFilterSize(35);
    }
    
    function caverEditOnLoad() {
      var oForm;
      oForm = document.caver_edit;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      checkMail(oForm.p_caver_contact, 'mail_pic');
      parent.setFilterSize(getMaxWidthInput(oForm),"px");
      var oHtml = document.getElementsByTagName('HTML')[0];
      parent.setFilterSizeTight(oHtml);
    }
    
    function newCancel() {
      self.location.href = "administration_<?php echo $_SESSION['language']; ?>.php?type=caver";
    }
    
    function caverEditSubmit(event) {
      var oForm = document.caver_edit;
      doChallengeList(oForm.g_myList,oForm.g_list);
    }
<?php
    	break;
    	case "application":
?>
    function applicationBeforeLoad() {
      parent.setFilterSize(35);
    }
    
    function applicationOnLoad() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      var oHtml = document.getElementsByTagName('HTML')[0];
      parent.setFilterSizeTight(oHtml);
    }
<?php
    	break;
    	case "mail":
?>
    function mailBeforeLoad() {
      parent.setFilterSize(35);
    }
    
    function mailOnLoad() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      var oHtml = document.getElementsByTagName('HTML')[0];
      parent.setFilterSizeTight(oHtml);
    }
<?php
    	break;
    }
?>
    function checkThisName(oObject, namePic) {
      checkName(oObject, namePic, "caver_long", "<?php echo $Name; ?>", namesArray, true);
    }
  	
  	function groupRemove() {
      var oOptions, i, oForm;
      oForm = document.forms[0]
      oOptions = oForm.g_myList.options;
      for (i = 0; i < oOptions.length; i = i + 1) {
        if (oOptions[i].selected) {
          oOptions[i] = null;
          i--;
        }
      }
    }
  
  	function groupAdd() {
      var windowName, url;
      windowName = "<convert>#label=612<convert>";//Choisissez une ou plusieurs entrées à ajouter
      //url = "<?php echo $_SESSION['Application_url']; ?>/html/pick_window_<?php echo $_SESSION['language']; ?>.php?type=group&callback=addGroup";
      url = "pick_window_<?php echo $_SESSION['language']; ?>.php?type=group&callback=addGroup";
      openWindow(url, windowName, 690, 520);
  	}
  	
  	function addGroup(oForm) {
      var uForm = document.forms[0];
      addOptionsFromSelection(oForm, uForm.g_myList);
    }
  	
  	function rightRemove() {
      var oOptions, i, oForm;
      oForm = document.forms[0]
      oOptions = oForm.r_myList.options;
      for (i = 0; i < oOptions.length; i = i + 1) {
        if (oOptions[i].selected) {
          oOptions[i] = null;
          i--;
        }
      }
    }
  
  	function rightAdd() {
      var windowName, url;
      windowName = "<convert>#label=612<convert>";//Choisissez une ou plusieurs entrées à ajouter
      //url = "<?php echo $_SESSION['Application_url']; ?>/html/pick_window_<?php echo $_SESSION['language']; ?>.php?type=right&callback=addRight";
      url = "pick_window_<?php echo $_SESSION['language']; ?>.php?type=right&callback=addRight";
      openWindow(url, windowName, 690, 520);
  	}
  	
  	function addRight(oForm) {
      var uForm = document.forms[0];
      addOptionsFromSelection(oForm, uForm.r_myList);
    }
  	
  	function caverRemove() {
      var oOptions, i, oForm;
      oForm = document.forms[0]
      oOptions = oForm.c_myList.options;
      for (i = 0; i < oOptions.length; i = i + 1) {
        if (oOptions[i].selected) {
          oOptions[i] = null;
          i--;
        }
      }
    }
  
  	function caverAdd() {
      var windowName, url;
      windowName = "<convert>#label=612<convert>";//Choisissez une ou plusieurs entrées à ajouter
      //url = "<?php echo $_SESSION['Application_url']; ?>/html/pick_window_<?php echo $_SESSION['language']; ?>.php?type=caver&callback=addCaver";
      url = "pick_window_<?php echo $_SESSION['language']; ?>.php?type=caver&callback=addCaver";
      openWindow(url, windowName, 690, 520);
  	}
  	
  	function addCaver(oForm) {
      var uForm = document.forms[0];
      addOptionsFromSelection(oForm, uForm.c_myList);
    }
<?php
    switch ($type) {
    	case "menu":
        if (!allowAccess(appli_view_all)) {
          exit();
        }
?>
    menuBeforeLoad();
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:menuOnLoad();">
    <?php echo getTopFrame(); ?>
		<div class="menu">
      <?php echo getTopMenu(getCloseBtn("filter_".$_SESSION['language'].".php","<convert>#label=371<convert>").'<div class="frame_title">'.setTitle("administration_".$_SESSION['language'].".php?type=menu", "filter", "<convert>#label=691<convert>", 2).'</div><!--Menu d\'administration-->'); ?>
      <ul>
<?php
    if (allowAccess(right_view_all)) {
?>
        <li class ="sub_menu">
          <a href="administration_<?php echo $_SESSION['language']; ?>.php?type=right"><convert>#label=692<convert><!--Gestion des droits--></a>
        </li>
<?php
    }
    if (allowAccess(group_view_all)) {
?>
        <li class ="sub_menu">
          <a href="administration_<?php echo $_SESSION['language']; ?>.php?type=group"><convert>#label=693<convert><!--Gestion des groupes--></a>
        </li>
<?php
    }
    if (allowAccess(caver_view_all)) {
?>
        <li class ="sub_menu">
          <a href="administration_<?php echo $_SESSION['language']; ?>.php?type=caver"><convert>#label=694<convert><!--Gestion des utilisateurs--></a>
        </li>
<?php
    }
    if (allowAccess(appli_view_all)) {
?>
        <li class ="sub_menu">
          <a href="administration_<?php echo $_SESSION['language']; ?>.php?type=application"><convert>#label=695<convert><!--Paramètres de l'application--></a>
        </li>
<?php
    }
    if (allowAccess(appli_view_all)) {
?>
        <li class ="sub_menu">
          <a href="administration_<?php echo $_SESSION['language']; ?>.php?type=mail"><convert>#label=699<convert><!--Liste des mails--></a>
        </li>
<?php
    }
    if (allowAccess(cache_refresh_all)) {
?>
        <li class ="sub_menu">
          <a href="../index.php?refreshCache=True" target="_top">Rafraichir le cache HTML</a>
        </li>
<?php
    }
?>
      </ul>
      <?php echo getBotMenu(); ?>
    </div>
<?php
    	break;
    	case "right":
        if (!allowAccess(right_view_all)) {
          exit();
        }
?>
    rightBeforeLoad();
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:rightOnLoad();">
    <?php echo getTopFrame(); ?>
		<?php echo getCloseBtn("administration_".$_SESSION['language'].".php?type=menu","<convert>#label=371<convert>"); ?>
		<div class="frame_title"><?php echo setTitle("administration_".$_SESSION['language'].".php?type=right", "filter", "<convert>#label=692<convert>", 3); ?></div><!--Gestion des droits-->
<?php
      if (isset($_POST['save']) && !$save_failed){
?>
    <div class="warning"><?php echo getTopBubble(); ?><convert>#label=701<convert> <convert>#label=50<convert><!--Le droit a été enregistré avec succès !--><?php echo getBotBubble(); ?></div>
<?php
      }
?>

<?php
    $sql = "SELECT DISTINCT ";
    $sql .= "T_right.Id AS `0`, ";
    $sql .= "T_right.Name AS `1` ";//Nom du droit
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_right` ";
		$columns_params = array(
			0 => "T_right*Id|Id",
			1 => "T_right*Name|<convert>#label=702<convert>",
		);
    $links = array();
    $input_type = array(
                'type' => 'radio',
                'conditions' => array());
    $style = array();
    $default_order = 2;
    $length_page = 10;
    $records_by_page_array = array(5, 10, 15, 20, 25, 30, 40, 50);
    $records_by_page = (isset($_POST['records_by_page'])) ? $_POST['records_by_page'] : 15;
    $filter_form = "automatic_form";
    $list_form = "result_form";
    $result = getRowsFromSQL($sql, $columns_params, $links, $records_by_page, $filter_form, $list_form, $_POST, $input_type, $style, $default_order, true, true, "");
    $resource_id = $result['resource_id'];
    $filter_fields = getFilterFields($sql,$columns_params,$_POST,$filter_form,"<convert>#label=542<convert>",true, $resource_id);//Tous
    $rows = $result['rows'];
    $total_count = $result['total_count'];
    $local_count = $result['local_count'];
    $count_page = ceil($total_count/$records_by_page);
    $current_page = (isset($_POST['current'])) ? $_POST['current'] : 1;
    $order = (isset($_POST['order'])) ? $_POST['order'] : '';
    $by = (isset($_POST['by'])) ? $_POST['by'] : $default_order;
    $navigator = "";
    if ($total_count > 0) {
      $navigator = getPageNavigator($length_page, $current_page, $count_page, $filter_form);
    }
?>
    <div>
      <form id="<?php echo $filter_form; ?>" name="<?php echo $filter_form; ?>" method="post" action="administration_<?php echo $_SESSION['language']; ?>.php?type=<?php echo $type; ?>">
        <table border="0" cellspacing="0" cellpadding="0" id="filter_set">
          <tr><td colspan="2"><convert>#label=601<convert><!--Pour rechercher une partie d'un mot utiliser le caractère *, ex: *erre pourrais retourner Pierre ou Terre etc.--></td></tr>
          <?php echo $filter_fields; ?>
        </table>
        <input type="hidden" name="current" value="" />
        <input type="hidden" name="order" value="<?php echo $order; ?>" />
        <input type="hidden" name="by" value="<?php echo $by; ?>" />
        <input type="submit" name="submit_filter" class="button1" value="<convert>#label=602<convert>" /><!--Filtrer-->
        <input type="submit" name="reset_filter" class="button1" value="<convert>#label=603<convert>" onclick="JavaScript:resetForm(this.form);" /><!--Tout afficher-->
        <input type="button" name="reset" class="button1" value="<convert>#label=604<convert>" onclick="JavaScript:resetForm(this.form);" /><!--Effacer-->
        <br /><select class="select2" name="records_by_page" id="records_by_page" onchange="JavaScript:this.form.submit();">
          <?php echo getOptionsFromArray($records_by_page_array,"",$records_by_page); ?>
        </select> <convert>#label=664<convert><!--Lignes par page-->.
      </form>
    </div>
    <?php if ($local_count >= $records_by_page) { ?>
    <div class="navigator">
      <?php echo $navigator; ?>
    </div>
    <?php } ?>
    <div>
      <form id="<?php echo $list_form; ?>" name="<?php echo $list_form; ?>" method="post" action="">
        <table border="0" cellspacing="1" cellpadding="0" id="result_table">
          <?php if ($total_count > 0) { echo $rows; } else { ?><convert>#label=622<convert><!--Aucun résultat n'est disponible--><?php } ?>
        </table>
        <div class="navigator">
          <?php echo $navigator; ?>
        </div>
        <div>
          <convert>#label=605<convert><!--Nb total de résultats--> : <?php echo $total_count; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<convert>#label=606<convert><!--Nb total de pages--> : <?php echo $count_page; ?><br />
        </div>
<?php if (allowAccess(right_edit_all)) { ?>
        <input type="button" class="button1" id="edit_right" name="edit_right" value="<convert>#label=53<convert>" onclick="JavaScript:rightEdit(this.form);" /><!--Modifier--><br />
        <input type="button" class="button1" id="new_right" name="new_right" value="<convert>#label=54<convert>" onclick="JavaScript:rightNew();" /><!--Nouveau--><br />
<?php } ?>
        <input type="button" class="button1" id="refresh_right" name="refresh_right" value="<convert>#label=56<convert>" onclick="JavaScript:rightRefresh(document.<?php echo $filter_form; ?>);" /><!--Rafraîchir-->
      </form>
    </div>
<?php
    	break;
    	case "right_edit":
        if (!allowAccess(right_edit_all)) {
          exit();
        }
?>
    rightEditBeforeLoad();
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:rightEditOnLoad();">
    <?php echo getTopFrame(); ?>
		<?php echo getCloseBtn("JavaScript:newCancel();","<convert>#label=371<convert>"); ?>
		<div class="frame_title"><?php echo setTitle("administration_".$_SESSION['language'].".php?type=right_edit", "filter", "<convert>#label=61<convert>", 4); ?></div><!--Création / Modification-->
<?php
          if ($save_failed) {
?>
		<div class="error"><?php echo getTopBubble(); ?><convert>#label=62<convert><!--Erreur, veuillez recommencer !--><?php echo getBotBubble(); ?></div><br /><br />
<?php
          }
?>
  	<form id="right_edit" name="right_edit" method="post" action="" onsubmit="JavaScript:rightEditSubmit(event);">
    	<table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><td width="170" class="label">
	      	<label for="right_name">
	      		<img src="../images/icons/FlagRequired.gif" alt="*" /><img src="../images/icons/FlagUnique.gif" alt="!" /><convert>#label=702<convert><!--Nom du droit-->
	      	</label>
	      </td><td class="field">
      		<input class="input1" type="text" id="right_name" name="right_name" value="<?php echo $Name; ?>" size="15" maxlength="68" onkeyup="JavaScript:checkThisName(this, 'name_pic');" />
      		<img class="status1" name="name_pic" id="name_pic" src="../images/icons/wrong.png" alt="image" />
      	</td></tr><tr><td width="170" class="label">
		      <label for="right_comments">
		      	<convert>#label=638<convert><!--Commentaires-->
					</label>
				</td><td class="field">
          <input class="input1" type="text" id="right_comments" name="right_comments" value="<?php echo $Comments; ?>" size="30" maxlength="1000" />
        </td></tr><tr><td class="label" colspan="2" style="text-align:left;">
          <label for="g_myList">
            <b><convert>#label=712<convert><!--Les groupes associés à ce droit--> :</b>
          </label>
	      </td></tr><tr><td class="field" colspan="2">
          <select style="width:270px;" name="g_myList" id="g_myList" size="10" multiple="multiple" ondblclick="JavaScript:groupRemove();">
<?php
if ($isNew == "False") {
          $sql = "SELECT gp.Id AS value, gp.Name AS text ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_group` gp ";
          $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_group_right` gr ON gr.Id_group = gp.Id ";
          $sql .= "WHERE gr.Id_right = ".$id." ";
          $sql .= "ORDER BY text ";
          $msg = "";
          $comparedCol = "value";
          $selected = "#";
          $textCol = "text";
          echo getOptions($sql, $msg, $selected, $comparedCol, $textCol);
} ?>
          </select>
		    </td></tr><tr><td class="field" colspan="2">
          <input type="button" class="button1" id="grpAdd" name="grpAdd" value="<convert>#label=74<convert>" onclick="JavaScript:groupAdd();" /><!--    Ajouter à ma liste  /\-->
		    </td></tr><tr><td class="field" colspan="2">
          <input type="button" class="button1" id="grpRemove" name="grpRemove" value="<convert>#label=73<convert>" onclick="JavaScript:groupRemove();" /><!--\/  Retirer de ma liste    -->
		    </td></tr><tr><td class="field" colspan="2">
          <input type="hidden" id="g_list" name="g_list" />
      		<input type="hidden" id="is_new" name="is_new" value="<?php echo $isNew; ?>" />
      		<input type="hidden" id="right_id" name="right_id" value="<?php echo $id; ?>" />
      	  <input class="button1" type="submit" id="save" name="save" value="<convert>#label=76<convert>" /><!--Valider-->
		    </td></tr><tr><td class="field" colspan="2">
      	  <input class="button1" onclick="JavaScript:newCancel();" type="button" id="cancel" name="cancel" value="<convert>#label=77<convert>" /><!--Annuler-->
        </td></tr><tr><td colspan="2">
          <div class="notes">
            <?php echo getTopBubble(); ?>
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=78<convert><!--Champs nécessaires.--><br />
            <img src="../images/icons/FlagUnique.gif" alt="!" /><convert>#label=736<convert><!--Doit être unique.--><br />
            <convert>#label=79<convert><!--Double-cliquez sur l'élément que vous souhaitez retirer pour gagner du temps !-->
            <?php echo getBotBubble(); ?>
          </div>
        </td></tr>
      </table>
    </form>
<?php
    	break;
    	case "group":
        if (!allowAccess(group_view_all)) {
          exit();
        }
?>
    groupBeforeLoad();
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:groupOnLoad();">
    <?php echo getTopFrame(); ?>
		<?php echo getCloseBtn("administration_".$_SESSION['language'].".php?type=menu","<convert>#label=371<convert>"); ?>
		<div class="frame_title"><?php echo setTitle("administration_".$_SESSION['language'].".php?type=group", "filter", "<convert>#label=693<convert>", 3); ?></div><!--Gestion des groupes-->
<?php
      if (isset($_POST['save']) && !$save_failed){
?>
    <div class="warning"><?php echo getTopBubble(); ?><convert>#label=705<convert> <convert>#label=50<convert><!--Le groupe a été enregistré avec succès !--><?php echo getBotBubble(); ?></div>
<?php
      }
      if (isset($did)) {
        if ($delete_failed){
?>
  	<div class="error"><?php echo getTopBubble(); ?><convert>#label=840<convert><!--Vous n'êtes pas autorisé à supprimer--> <convert>#label=705<convert><!--Le groupe--><?php echo getBotBubble(); ?></div>
<?php
        } else {
?>
    <div class="warning"><?php echo getTopBubble(); ?><convert>#label=705<convert> <convert>#label=609<convert><!--Le groupe a été supprimé avec succès !--><?php echo getBotBubble(); ?></div>
<?php
        }
      }
?>

<?php
    $sql = "SELECT DISTINCT ";
    $sql .= "T_group.Id AS `0`, ";
    $sql .= "T_group.Name AS `1`, ";//Nom du groupe
    $sql .= "COUNT(J_caver_group.Id_caver) AS `2` ";//Nombre d'utilisateurs
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_group` ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_caver_group` ON J_caver_group.Id_group = T_group.Id ";
    $sql .= "GROUP BY T_group.Id ";
		$columns_params = array(
			0 => "[hidden]|[hidden]Id",
			1 => "T_group*Name|<convert>#label=706<convert>",
			2 => "[hidden]|<convert>#label=707<convert>"
		);
    $links = array();
    $input_type = array(
                'type' => 'radio',
                'conditions' => array());
    $style = array();
    $default_order = 2;
    $length_page = 10;
    $records_by_page_array = array(5, 10, 15, 20, 25, 30, 40, 50);
    $records_by_page = (isset($_POST['records_by_page'])) ? $_POST['records_by_page'] : 15;
    $filter_form = "automatic_form";
    $list_form = "result_form";
    $result = getRowsFromSQL($sql, $columns_params, $links, $records_by_page, $filter_form, $list_form, $_POST, $input_type, $style, $default_order, true, true, "");
    $resource_id = $result['resource_id'];
    $filter_fields = getFilterFields($sql,$columns_params,$_POST,$filter_form,"<convert>#label=542<convert>",true, $resource_id);//Tous
    $rows = $result['rows'];
    $total_count = $result['total_count'];
    $local_count = $result['local_count'];
    $count_page = ceil($total_count/$records_by_page);
    $current_page = (isset($_POST['current'])) ? $_POST['current'] : 1;
    $order = (isset($_POST['order'])) ? $_POST['order'] : '';
    $by = (isset($_POST['by'])) ? $_POST['by'] : $default_order;
    $navigator = "";
    if ($total_count > 0) {
      $navigator = getPageNavigator($length_page, $current_page, $count_page, $filter_form);
    }
?>
    <div>
      <form id="<?php echo $filter_form; ?>" name="<?php echo $filter_form; ?>" method="post" action="administration_<?php echo $_SESSION['language']; ?>.php?type=<?php echo $type; ?>">
        <table border="0" cellspacing="0" cellpadding="0" id="filter_set">
          <tr><td colspan="2"><convert>#label=601<convert><!--Pour rechercher une partie d'un mot utiliser le caractère *, ex: *erre pourrais retourner Pierre ou Terre etc.--></td></tr>
          <?php echo $filter_fields; ?>
        </table>
        <input type="hidden" name="current" value="" />
        <input type="hidden" name="order" value="<?php echo $order; ?>" />
        <input type="hidden" name="by" value="<?php echo $by; ?>" />
        <input type="submit" name="submit_filter" class="button1" value="<convert>#label=602<convert>" /><!--Filtrer-->
        <input type="submit" name="reset_filter" class="button1" value="<convert>#label=603<convert>" onclick="JavaScript:resetForm(this.form);" /><!--Tout afficher-->
        <input type="button" name="reset" class="button1" value="<convert>#label=604<convert>" onclick="JavaScript:resetForm(this.form);" /><!--Effacer-->
        <br /><select class="select2" name="records_by_page" id="records_by_page" onchange="JavaScript:this.form.submit();">
          <?php echo getOptionsFromArray($records_by_page_array,"",$records_by_page); ?>
        </select> <convert>#label=664<convert><!--Lignes par page-->.
      </form>
    </div>
    <?php if ($local_count >= $records_by_page) { ?>
    <div class="navigator">
      <?php echo $navigator; ?>
    </div>
    <?php } ?>
    <div>
      <form id="<?php echo $list_form; ?>" name="<?php echo $list_form; ?>" method="post" action="">
        <table border="0" cellspacing="1" cellpadding="0" id="result_table">
          <?php if ($total_count > 0) { echo $rows; } else { ?><convert>#label=622<convert><!--Aucun résultat n'est disponible--><?php } ?>
        </table>
        <div class="navigator">
          <?php echo $navigator; ?>
        </div>
        <div>
          <convert>#label=605<convert><!--Nb total de résultats--> : <?php echo $total_count; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<convert>#label=606<convert><!--Nb total de pages--> : <?php echo $count_page; ?><br />
        </div>
<?php if (allowAccess(group_edit_all)) { ?>
        <input type="button" class="button1" id="edit_group" name="edit_group" value="<convert>#label=53<convert>" onclick="JavaScript:groupEdit(this.form);" /><!--Modifier--><br />
        <input type="button" class="button1" id="new_group" name="new_group" value="<convert>#label=54<convert>" onclick="JavaScript:groupNew();" /><!--Nouveau--><br />
<?php }
if (allowAccess(group_delete_all)) { ?>
        <input type="button" class="button1" id="del_group" name="del_group" value="<convert>#label=55<convert>" onclick="JavaScript:groupDelete(this.form);" /><!--Supprimer--><br />
<?php } ?>
        <input type="button" class="button1" id="refresh_group" name="refresh_group" value="<convert>#label=56<convert>" onclick="JavaScript:groupRefresh(document.<?php echo $filter_form; ?>);" /><!--Rafraîchir-->
      </form>
    </div>
<?php
    	break;
    	case "group_edit":
        if (!allowAccess(group_edit_all)) {
          exit();
        }
?>
    groupEditBeforeLoad();
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:groupEditOnLoad();">
    <?php echo getTopFrame(); ?>
		<?php echo getCloseBtn("administration_".$_SESSION['language'].".php?type=group","<convert>#label=371<convert>"); ?>
		<div class="frame_title"><?php echo setTitle("administration_".$_SESSION['language'].".php?type=group_edit", "filter", "<convert>#label=61<convert>", 4); ?></div><!--Création / Modification-->
<?php
          if ($save_failed) {
?>
		<div class="error"><?php echo getTopBubble(); ?><convert>#label=62<convert><!--Erreur, veuillez recommencer !--><?php echo getBotBubble(); ?></div><br /><br />
<?php
          }
?>
  	<form id="group_edit" name="group_edit" method="post" action="" onsubmit="JavaScript:groupEditSubmit(event);">
    	<table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><td width="170" class="label">
	      	<label for="group_name">
	      		<img src="../images/icons/FlagRequired.gif" alt="*" /><img src="../images/icons/FlagUnique.gif" alt="!" /><convert>#label=706<convert><!--Nom du groupe-->
	      	</label>
	      </td><td class="field">
      		<input class="input1" type="text" id="group_name" name="group_name" value="<?php echo $Name; ?>" size="15" maxlength="68" onkeyup="JavaScript:checkThisName(this, 'name_pic');" />
      		<img class="status1" name="name_pic" id="name_pic" src="../images/icons/wrong.png" alt="image" />
      	</td></tr><tr><td width="170" class="label">
		      <label for="group_comments">
		      	<convert>#label=638<convert><!--Commentaires-->
					</label>
				</td><td class="field">
          <input class="input1" type="text" id="group_comments" name="group_comments" value="<?php echo $Comments; ?>" size="30" maxlength="1000" />
        </td></tr><tr><td class="label" colspan="2" style="text-align:left;">
          <label for="r_myList">
            <b><convert>#label=716<convert><!--Les droits associés à ce groupe--> :</b>
          </label>
	      </td></tr><tr><td class="field" colspan="2">
          <select style="width:270px;" name="r_myList" id="r_myList" size="10" multiple="multiple" ondblclick="JavaScript:rightRemove();">
<?php
if ($isNew == "False") {
          $sql = "SELECT r.Id AS value, r.Name AS text ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_right` r ";
          $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_group_right` gr ON gr.Id_right = r.Id ";
          $sql .= "WHERE gr.Id_group = ".$id." ";
          $sql .= "ORDER BY text ";
          $msg = "";
          $comparedCol = "value";
          $selected = "#";
          $textCol = "text";
          echo getOptions($sql, $msg, $selected, $comparedCol, $textCol);
} ?>
          </select>
		    </td></tr><tr><td class="field" colspan="2">
          <input type="button" class="button1" id="rgtAdd" name="rgtAdd" value="<convert>#label=74<convert>" onclick="JavaScript:rightAdd();" /><!--    Ajouter à ma liste  /\-->
		    </td></tr><tr><td class="field" colspan="2">
          <input type="button" class="button1" id="rgtRemove" name="rgtRemove" value="<convert>#label=73<convert>" onclick="JavaScript:rightRemove();" /><!--\/  Retirer de ma liste    -->
		    </td></tr><tr><td class="label" colspan="2" style="text-align:left;">
          <label for="c_myList">
            <b><convert>#label=717<convert><!--Ressources du groupe--> :</b>
          </label>
	      </td></tr><tr><td class="field" colspan="2">
          <select style="width:270px;" name="c_myList" id="c_myList" size="10" multiple="multiple" ondblclick="JavaScript:caverRemove();">
<?php
if ($isNew == "False") {
          $sql = "SELECT c.Id AS value, CONCAT_WS(' &gt; ',c.Login,c.Nickname) AS text ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_caver` c ";
          $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_caver_group` cg ON cg.Id_caver = c.Id ";
          $sql .= "WHERE cg.Id_group = ".$id." ";
          $sql .= "ORDER BY text ";
          $msg = "";
          $comparedCol = "value";
          $selected = "#";
          $textCol = "text";
          echo getOptions($sql, $msg, $selected, $comparedCol, $textCol);
} ?>
          </select>
		    </td></tr><tr><td class="field" colspan="2">
          <input type="button" class="button1" id="cvrAdd" name="cvrAdd" value="<convert>#label=74<convert>" onclick="JavaScript:caverAdd();" /><!--    Ajouter à ma liste  /\-->
		    </td></tr><tr><td class="field" colspan="2">
          <input type="button" class="button1" id="cvrRemove" name="cvrRemove" value="<convert>#label=73<convert>" onclick="JavaScript:caverRemove();" /><!--\/  Retirer de ma liste    -->
		    </td></tr><tr><td class="field" colspan="2">
          <input type="hidden" id="r_list" name="r_list" />
          <input type="hidden" id="c_list" name="c_list" />
      		<input type="hidden" id="is_new" name="is_new" value="<?php echo $isNew; ?>" />
      		<input type="hidden" id="group_id" name="group_id" value="<?php echo $id; ?>" />
      	  <input class="button1" type="submit" id="save" name="save" value="<convert>#label=76<convert>" /><!--Valider-->
		    </td></tr><tr><td class="field" colspan="2">
      	  <input class="button1" onclick="JavaScript:newCancel();" type="button" id="cancel" name="cancel" value="<convert>#label=77<convert>" /><!--Annuler-->
        </td></tr><tr><td colspan="2">
          <div class="notes">
            <?php echo getTopBubble(); ?>
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=78<convert><!--Champs nécessaires.--><br />
            <img src="../images/icons/FlagUnique.gif" alt="!" /><convert>#label=736<convert><!--Doit être unique.--><br />
            <convert>#label=79<convert><!--Double-cliquez sur l'élément que vous souhaitez retirer pour gagner du temps !-->
            <?php echo getBotBubble(); ?>
          </div>
        </td></tr>
      </table>
    </form>
<?php
    	break;
    	case "caver":
        if (!allowAccess(caver_view_all)) {
          exit();
        }
?>
    caverBeforeLoad();
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:caverOnLoad();">
    <?php echo getTopFrame(); ?>
		<?php echo getCloseBtn("administration_".$_SESSION['language'].".php?type=menu","<convert>#label=371<convert>"); ?>
		<div class="frame_title"><?php echo setTitle("administration_".$_SESSION['language'].".php?type=caver", "filter", "<convert>#label=694<convert>", 3); ?></div><!--Gestion des utilisateurs-->
<?php
      if (isset($_POST['save']) && !$save_failed){
?>
    <div class="warning"><?php echo getTopBubble(); ?><convert>#label=704<convert> <convert>#label=50<convert><!--L'utilisateur a été enregistré avec succès !--><?php echo getBotBubble(); ?></div>
<?php
      }
?>
<?php
    $sql = "SELECT DISTINCT ";
    $sql .= "T_caver.Id AS `0`, ";
    $sql .= "T_caver.Login AS `1`, ";//Identifiant de connexion
    $sql .= "T_caver.Nickname AS `2`, ";//Pseudo
    $sql .= "T_caver.Surname AS `3`, ";//Prénom
    $sql .= "T_caver.Name AS `4`, ";//Nom
    $sql .= "IF(T_caver.Contact_is_public in (1,2),T_caver.Contact,NULL) AS `5`, ";//E-mail
    $sql .= "GROUP_CONCAT(DISTINCT T_group.Name ORDER BY T_group.Name SEPARATOR ', ') AS `6`, ";//Groupes
    $sql .= "T_country.".$_SESSION['language']."_name AS `7`, "; //Pays
    $sql .= "T_caver.Language AS `8`, ";//Langue
    $sql .= "GROUP_CONCAT(DISTINCT T_grotto.Name ORDER BY T_grotto.Name SEPARATOR ', ') AS `9`, ";//Clubs
    $sql .= "IF(T_caver.Activated='NO','<convert>#label=627<convert>','<convert>#label=626<convert>') AS `10`, ";//Compte actif
    $sql .= "T_caver.Connection_counter AS `11`, ";//Nombre de connexions
    $sql .= "IF(T_caver.Banned='NO','<convert>#label=627<convert>','<convert>#label=626<convert>') AS `12`, ";//Banni
    $sql .= "T_caver.Relevance AS `13` ";//Pertinence
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_caver` ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_country` ON T_country.Iso = T_caver.Country ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_grotto_caver` ON J_grotto_caver.Id_caver = T_caver.Id ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_grotto` ON T_grotto.Id = J_grotto_caver.Id_grotto ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_caver_group` ON J_caver_group.Id_caver = T_caver.Id ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_group` ON T_group.Id = J_caver_group.Id_group ";
    $sql .= "GROUP BY T_caver.Id ";
		$columns_params = array(
			0 => "[hidden]|[hidden]Id",
			1 => "T_caver*Login|<convert>#label=192<convert>",
			2 => "T_caver*Nickname|<convert>#label=578<convert>",
			3 => "T_caver*Surname|<convert>#label=200<convert>",
			4 => "T_caver*Name|<convert>#label=199<convert>",
			5 => "IF(T_caver*Contact_is_public@in@(1,2),T_caver*Contact,NULL)|<convert>#label=146<convert>",
			6 => "T_group*Id|<convert>#label=703<convert>|SELECT Id AS value, Name AS text FROM ".$_SESSION['Application_host'].".T_group ORDER BY text",
			7 => "T_caver*Country|<convert>#label=98<convert>|SELECT Iso AS value,".$_SESSION['language']."_name AS text FROM ".$_SESSION['Application_host'].".T_country ORDER BY text",
			8 => "T_caver*Language|<convert>#label=205<convert>",
			9 => "T_grotto*Name|<convert>#label=386<convert>",
			10 => "IF(T_caver*Activated='NO','<convert>#label=627<convert>','<convert>#label=626<convert>')|<convert>#label=708<convert>|<convert>#label=626<convert>;<convert>#label=627<convert>",
			11 => "T_caver*Connection_counter|<convert>#label=711<convert>",
			12 => "IF(T_caver*Banned='NO','<convert>#label=627<convert>','<convert>#label=626<convert>')|<convert>#label=709<convert>|<convert>#label=626<convert>;<convert>#label=627<convert>",
			13 => "T_caver*Relevance|<convert>#label=710<convert>"
		);
    $links = array (
            5 => array(
                'conditions' =>  array(),
                'parameters' => array(
                                '<email>' => 5),
                'link' => "mailto:<email>",
                'target' => ''));
    $input_type = array(
                'type' => 'radio',
                'conditions' => array());
    $style = array();
    $default_order = 2;
    $length_page = 10;
    $records_by_page_array = array(5, 10, 15, 20, 25, 30, 40, 50);
    $records_by_page = (isset($_POST['records_by_page'])) ? $_POST['records_by_page'] : 15;
    $filter_form = "automatic_form";
    $list_form = "result_form";
    $result = getRowsFromSQL($sql, $columns_params, $links, $records_by_page, $filter_form, $list_form, $_POST, $input_type, $style, $default_order, true, true, "");
    $resource_id = $result['resource_id'];
    $filter_fields = getFilterFields($sql,$columns_params,$_POST,$filter_form,"<convert>#label=542<convert>",true, $resource_id);//Tous
    $rows = $result['rows'];
    $total_count = $result['total_count'];
    $local_count = $result['local_count'];
    $count_page = ceil($total_count/$records_by_page);
    $current_page = (isset($_POST['current'])) ? $_POST['current'] : 1;
    $order = (isset($_POST['order'])) ? $_POST['order'] : '';
    $by = (isset($_POST['by'])) ? $_POST['by'] : $default_order;
    $navigator = "";
    if ($total_count > 0) {
      $navigator = getPageNavigator($length_page, $current_page, $count_page, $filter_form);
    }
?>
    <div>
      <form id="<?php echo $filter_form; ?>" name="<?php echo $filter_form; ?>" method="post" action="administration_<?php echo $_SESSION['language']; ?>.php?type=<?php echo $type; ?>">
        <table border="0" cellspacing="0" cellpadding="0" id="filter_set">
          <tr><td colspan="2"><convert>#label=601<convert><!--Pour rechercher une partie d'un mot utiliser le caractère *, ex: *erre pourrais retourner Pierre ou Terre etc.--></td></tr>
          <?php echo $filter_fields; ?>
        </table>
        <input type="hidden" name="current" value="" />
        <input type="hidden" name="order" value="<?php echo $order; ?>" />
        <input type="hidden" name="by" value="<?php echo $by; ?>" />
        <input type="submit" name="submit_filter" class="button1" value="<convert>#label=602<convert>" /><!--Filtrer-->
        <input type="submit" name="reset_filter" class="button1" value="<convert>#label=603<convert>" onclick="JavaScript:resetForm(this.form);" /><!--Tout afficher-->
        <input type="button" name="reset" class="button1" value="<convert>#label=604<convert> " onclick="JavaScript:resetForm(this.form);" /><!--Effacer-->
        <br /><select class="select2" name="records_by_page" id="records_by_page" onchange="JavaScript:this.form.submit();">
          <?php echo getOptionsFromArray($records_by_page_array,"",$records_by_page); ?>
        </select> <convert>#label=664<convert><!--Lignes par page-->.
      </form>
    </div>
    <?php if ($local_count >= $records_by_page) { ?>
    <div class="navigator">
      <?php echo $navigator; ?>
    </div>
    <?php } ?>
    <div>
      <form id="<?php echo $list_form; ?>" name="<?php echo $list_form; ?>" method="post" action="">
        <table border="0" cellspacing="1" cellpadding="0" id="result_table">
          <?php if ($total_count > 0) { echo $rows; } else { ?><convert>#label=622<convert><!--Aucun résultat n'est disponible--><?php } ?>
        </table>
        <div class="navigator">
          <?php echo $navigator; ?>
        </div>
        <div>
          <convert>#label=605<convert><!--Nb total de résultats--> : <?php echo $total_count; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<convert>#label=606<convert><!--Nb total de pages--> : <?php echo $count_page; ?><br />
        </div>
<?php if (allowAccess(caver_edit_all)) { ?>
        <input type="button" class="button1" id="edit_caver" name="edit_caver" value="<convert>#label=53<convert>" onclick="JavaScript:caverEdit(this.form);" /><!--Modifier--><br />
        <input type="checkbox" class="input1" style="border:0px none;" id="Activated" name="Activated" checked="checked" /> <convert>#label=708<convert><br />
        <input type="checkbox" class="input1" style="border:0px none;" id="Banned" name="Banned" /> <convert>#label=709<convert><br />
        <input type="button" class="button1" id="change_caver" name="change_caver" value="<convert>#label=724<convert>" onclick="JavaScript:caverChangeProp(this.form);" /><!--Appliquer--><br />              
<?php } ?>
        <input type="button" class="button1" id="refresh_caver" name="refresh_caver" value="<convert>#label=56<convert>" onclick="JavaScript:caverRefresh(document.<?php echo $filter_form; ?>);" /><!--Rafraîchir-->
      </form>
    </div>
<?php
    	break;
    	case "caver_edit":
        if (!allowAccess(caver_edit_all)) {
          exit();
        }
?>
    caverEditBeforeLoad();
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:caverEditOnLoad();">
    <?php echo getTopFrame(); ?>
		<?php echo getCloseBtn("administration_".$_SESSION['language'].".php?type=caver","<convert>#label=371<convert>"); ?>
		<div class="frame_title"><?php echo setTitle("administration_".$_SESSION['language'].".php?type=caver_edit", "filter", "<convert>#label=61<convert>", 4); ?></div><!--Création / Modification-->
<?php
          if ($save_failed) {
?>
		<div class="error"><?php echo getTopBubble(); ?><convert>#label=62<convert><!--Erreur, veuillez recommencer !--><?php echo getBotBubble(); ?></div><br /><br />
<?php
          }
?>
  	<form id="caver_edit" name="caver_edit" method="post" action="" onsubmit="JavaScript:caverEditSubmit(event);">
    	<table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><td class="label" colspan="2" style="text-align:center;line-height:30px;">
          <b><u><?php echo $Name; ?></u></b>
      	</td></tr><tr><td width="170" class="label">
          <label for="p_caver_contact">
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=197<convert><!--E-mail de contact-->
          </label>
        </td><td class="field">
          <input class="input1" type="text" id="p_caver_contact" name="p_caver_contact" value="<?php echo $caver_contact; ?>" size="20" maxlength="40" onkeyup="JavaScript:checkMail(this, 'mail_pic');" />
          <img class="status1" name="mail_pic" id="mail_pic" src="../images/icons/wrong.png" alt="image" />
        </td></tr><tr><td class="label" colspan="2" style="text-align:left;">
          <label for="g_myList">
            <b><convert>#label=718<convert><!--Cette ressource fait partie des groupes suivants--> :</b>
          </label>
	      </td></tr><tr><td class="field" colspan="2">
          <select style="width:270px;" name="g_myList" id="g_myList" size="10" multiple="multiple" ondblclick="JavaScript:groupRemove();">
<?php
          $sql = "SELECT gp.Id AS value, gp.Name AS text ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_group` gp ";
          $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_caver_group` cg ON cg.Id_group = gp.Id ";
          $sql .= "WHERE cg.Id_caver = ".$id." ";
          $sql .= "ORDER BY text ";
          $msg = "";
          $comparedCol = "value";
          $selected = "#";
          $textCol = "text";
          echo getOptions($sql, $msg, $selected, $comparedCol, $textCol);
?>
          </select>
		    </td></tr><tr><td class="field" colspan="2">
          <input type="button" class="button1" id="grpAdd" name="grpAdd" value="<convert>#label=74<convert>" onclick="JavaScript:groupAdd();" /><!--    Ajouter à ma liste  /\-->
		    </td></tr><tr><td class="field" colspan="2">
          <input type="button" class="button1" id="grpRemove" name="grpRemove" value="<convert>#label=73<convert>" onclick="JavaScript:groupRemove();" /><!--\/  Retirer de ma liste    -->
		    </td></tr><tr><td class="label" colspan="2" style="text-align:left;">
          <label for="r_myList">
            <i><convert>#label=719<convert><!--Droits de cette ressource--> :</i>
          </label>
	      </td></tr><tr><td class="field" colspan="2">
          <select style="width:270px;" name="r_myList" id="r_myList" size="10" readonly="readonly">
<?php
          $sql = "SELECT Id_right AS value, CONCAT(Name_right, ' [', Name_group, ']') AS text ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`V_caver_right` ";
          $sql .= "WHERE Id_caver = ".$id." ";
          $sql .= "ORDER BY text ";
          $msg = "";
          $comparedCol = "value";
          $selected = "#";
          $textCol = "text";
          echo getOptions($sql, $msg, $selected, $comparedCol, $textCol);
?>
          </select>
		    </td></tr><tr><td class="field" colspan="2">
          <input type="hidden" id="g_list" name="g_list" />
      		<input type="hidden" id="caver_id" name="caver_id" value="<?php echo $id; ?>" />
      	  <input class="button1" type="submit" id="save" name="save" value="<convert>#label=76<convert>" /><!--Valider-->
		    </td></tr><tr><td class="field" colspan="2">
      	  <input class="button1" onclick="JavaScript:newCancel();" type="button" id="cancel" name="cancel" value="<convert>#label=77<convert>" /><!--Annuler-->
        </td></tr><tr><td colspan="2">
          <div class="notes">
            <?php echo getTopBubble(); ?>
            <convert>#label=79<convert><!--Double-cliquez sur l'élément que vous souhaitez retirer pour gagner du temps !-->
            <?php echo getBotBubble(); ?>
          </div>
        </td></tr>
      </table>
    </form>
<?php
    	break;
    	case "application":
        if (!allowAccess(appli_view_all)) {
          exit();
        }
?>
    applicationBeforeLoad();
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:applicationOnLoad();">
    <?php echo getTopFrame(); ?>
		<?php echo getCloseBtn("administration_".$_SESSION['language'].".php?type=menu","<convert>#label=371<convert>"); ?>
		<div class="frame_title"><?php echo setTitle("administration_".$_SESSION['language'].".php?type=application", "filter", "<convert>#label=695<convert>", 3); ?></div><!--Paramètres de l'application-->
  	<form id="application" name="application" method="post" action="">
      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
<?php
$sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`T_application` WHERE Is_current = 'YES'";
$data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
if ($data['Count'] > 0) {
  foreach ($data[0] as $key => $value) {
    if ($key!= "Is_current" && $key != "Revision") {
?>
        <tr><td width="170" class="label">
	      	<label for="<?php echo $key; ?>">
	      		<?php echo $key; ?>
	      	</label>
	      </td><td class="field">
      		  <input class="input1" type="text" id="<?php echo $key; ?>" name="<?php echo $key; ?>" value="<?php echo $value; ?>" size="35"/>
      	</td></tr>
<?php
    }
  }
  if (allowAccess(appli_edit_all)) { ?>
        <tr><td width="170" class="label">
          <label for="save">
          </label>
        </td><td class="field">
	      	<input class="button1" type="submit" id="update" name="update" value="<convert>#label=53<convert>" /><!--Modifier--><br />
	      	<input class="button1" type="submit" id="save" name="save" value="<convert>#label=54<convert>" /><!--Nouveau-->
	      </td></tr>
<?php
  }
  if (allowAccess(appli_delete_all)) { ?>
        <tr><td colspan="2" class="label" style="text-align:left;">
          <a href="banner_<?php echo $_SESSION['language']; ?>.php?Reset_application_vars=True&amp;suspend=<?php echo $_SESSION['user_login']; ?>" target="banner">
            <convert>#label=696<convert><!--Forcer la mise à jour de toutes les sessions-->.
          </a>
	      </td></tr>
<?php
  }
}
?>
      </table>
    </form>
<?php
    	break;
    	case "mail":
        if (!allowAccess(appli_view_all)) {
          exit();
        }
?>
    mailBeforeLoad();
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:mailOnLoad();">
    <?php echo getTopFrame(); ?>
		<?php echo getCloseBtn("administration_".$_SESSION['language'].".php?type=menu","<convert>#label=371<convert>"); ?>
		<div class="frame_title"><?php echo setTitle("administration_".$_SESSION['language'].".php?type=mail", "filter", "<convert>#label=699<convert>", 3); ?></div><!--Liste des mails-->
  	<form id="mail" name="mail" method="post" action="">
      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
<?php
  $sql = "SELECT DISTINCT Language, CONCAT('\"',IF(CONCAT_WS(' ', Surname, Name) = '',Nickname, CONCAT_WS(' ', Surname, Name)),'\" &lt;',Contact,'&gt;, ') AS Contacts FROM `".$_SESSION['Application_host']."`.`T_caver` WHERE Alert_for_news = 'YES' ORDER BY Language";
  $dataArray = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
  $mailing_list = "";
  $mailing_language = $dataArray[0]['Language'];
  for($i=0;$i<$dataArray["Count"];$i++) {
    $mailing_list .= $dataArray[$i]['Contacts'];
    if ($dataArray[$i+1]['Language'] != $mailing_language) {
?>
        <tr><td colspan="2" class="label" style="text-align:left;">
          <label for="news_<?php echo $mailing_language; ?>">
            <convert>#label=697<convert><!--Mailing list for news--> - <?php echo $mailing_language; ?> :
          </label>
		    </td></tr><tr><td colspan="2" class="field">
	      	<textarea class="input1" id="news_<?php echo $mailing_language; ?>" name="news_<?php echo $mailing_language; ?>" style="width:100%" rows="5" readonly="readonly"><?php echo $mailing_list; ?></textarea>
	      </td></tr>
<?php
      $mailing_list = "";
      $mailing_language = $dataArray[$i+1]['Language'];
    }
  }
?>
<?php
  $sql = "SELECT DISTINCT Language, CONCAT('\"',IF(CONCAT_WS(' ', Surname, Name) = '',Nickname, CONCAT_WS(' ', Surname, Name)),'\" &lt;',Contact,'&gt;, ') AS Contacts FROM `".$_SESSION['Application_host']."`.`T_caver` ORDER BY Language";
  $dataArray = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
  $mailing_list = "";
  $mailing_language = $dataArray[0]['Language'];
  for($i=0;$i<$dataArray["Count"];$i++) {
    $mailing_list .= $dataArray[$i]['Contacts'];
    if ($dataArray[$i+1]['Language'] != $mailing_language) {
?>
        <tr><td colspan="2" class="label" style="text-align:left;">
          <label for="all_<?php echo $mailing_language; ?>">
            <convert>#label=698<convert><!--All users--> - <?php echo $mailing_language; ?> :
          </label>
		    </td></tr><tr><td colspan="2" class="field">
      	  <textarea class="input1" id="all_<?php echo $mailing_language; ?>" name="all_<?php echo $mailing_language; ?>" style="width:100%" rows="5" readonly="readonly"><?php echo $mailing_list; ?></textarea>
	      </td></tr>
<?php
      $mailing_list = "";
      $mailing_language = $dataArray[$i+1]['Language'];
    }
  }
?>
      </table>
    </form>
<?php
			break;
    	default:
?>
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:defaultOnload();">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
  	<?php echo getCloseBtn("filter_".$_SESSION['language'].".php","<convert>#label=371<convert>"); ?>
  	<div class="frame_title"><?php echo setTitle("#", "filter", "<convert>#label=80<convert>", 2); ?></div><!--Erreur-->
  	<div class="error"><?php echo getTopBubble(); ?><convert>#label=81<convert><!--Cas non traité !--><?php echo getBotBubble(); ?></div>
<?php
    	break;
    }
?>
    <?php echo getBotFrame(); ?>
<?php
    $virtual_page = "administration/".$type."/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>