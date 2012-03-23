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
include("../conf/config.php");
include("../func/function.php");
include("declaration.php");
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
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=224<convert></title><!--Contacter votre administrateur/modérateur.-->
    <link rel="stylesheet" type="text/css" href="../css/filter.css" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
<?php
  	//Init vars
    $type = (isset($_GET['type'])) ? $_GET['type'] : '';
    $send_failed = false;
    $body = '';
    $name = (isset($_SESSION['user_login'])) ? $_SESSION['user_login'] : '';
    $contact = (isset($_SESSION['user_contact'])) ? $_SESSION['user_contact'] : '';
    $subject = (isset($_GET['subject'])) ? urldecode(stripslashes($_GET['subject'])) : '';
    $category = (isset($_GET['category'])) ? urldecode(stripslashes($_GET['category'])) : '';
    $bad_name = (isset($_GET['name'])) ? urldecode(stripslashes($_GET['name'])) : '';
    $helpId = array("message" => 5);

    //Send the mail
    if (isset($_POST['send'])){
      $send_failed = true;
      $name = (isset($_POST['m_name'])) ? $_POST['m_name'] : '';
      $contact = (isset($_POST['m_contact'])) ? $_POST['m_contact'] : '';
      $subject = (isset($_POST['m_subject'])) ? $_POST['m_subject'] : '';
      $bad_name = (isset($_POST['m_sec_subject'])) ? $_POST['m_sec_subject'] : '';
      $category = (isset($_POST['m_category'])) ? $_POST['m_category'] : '';
      $body = (isset($_POST['m_body'])) ? $_POST['m_body'] : '';
      $real_mail = (isset($_POST['m_real_mail'])) ? $_POST['m_real_mail'] : '';
      if (formIsValid()) {
        $sql = "SELECT `".$_SESSION['language']."_admin_id` AS Admin_id FROM `".$_SESSION['Application_host']."`.`T_message_subject` WHERE `Subject` = '".$subject."'";
        $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
        $admin_id = "";
        if($data['Count'] > 0) {
          $admin_id = $data[0]['Admin_id'];
        }
        if ($subject == "bad_content" || $subject == "restore_element") {
          $subject .= "; Category: ".$category;
        }
        if ($bad_name != "") {
          $subject .= "; Id: ".$bad_name;
        }
        sendMessageToWM($admin_id,$contact,$real_mail,$name,$subject,nl2br($body));
        if ($category == 'topography' && $bad_name != '') {
          $sql = "SELECT Id FROM T_status WHERE Name = 'canceled'";
          $status_id = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
          $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_topography` SET Enabled = 'NO' WHERE Id_request = ".$bad_name;
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $user_login = ($_SESSION['user_login'] == '') ? "guest" : $_SESSION['user_login'];
          $date_stamp = '---- <convert>#label=230<convert>: '.$user_login.' '.date("Y-m-d H:i:s").' ----';
          $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_request` SET Id_status = ".returnDefault($status_id[0]['Id'], 'id').", Comments = ".returnDefault($date_stamp."\n".$body, 'text')." WHERE Id = ".$bad_name;
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          trackAction("edit_request",$bad_name,"T_request");
          sendRequestMail($bad_name);
        }
        $send_failed = false;
      }
    }
    
    function formIsValid() {
		  $string = (isset($_POST['m_check'])) ? $_POST['m_check'] : '';
	    $check = True;
      $check = $check && ((md5(getIp().strtolower($string)) == $_SESSION['userCheck']) || USER_IS_CONNECTED);
      return $check;
    }
    
    if (!USER_IS_CONNECTED) {
	   deleteImage();
	  }
	  
    switch ($_SESSION['home_page']) {
      case "overview":
        $back_src = "filter_".$_SESSION['language'].".php";
      break;
      case "home":
      default:
        if (USER_IS_CONNECTED) {
          $back_src = "filter_".$_SESSION['language'].".php";
        } else {
          $back_src = "connection_".$_SESSION['language'].".php?type=login";
        }
      break;
    }
    
    $readonly = '';
    if (USER_IS_CONNECTED) {
      $readonly = 'readonly="readonly"';
    }
?>
    <script type="text/javascript" src="../scripts/classeGCTest.js"></script>
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
    //Gona need those functions: openWindow
    
    function defaultonload() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
    }
    
    function messageBeforeLoad() {
      if (parent.setFilterSize) {
        parent.setFilterSize(490,"px");
      }
    }
    
    function messageOnLoad() {
      var oForm = document.message;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      if (oForm) {
        if (parent.setFilterSize) {
          parent.setFilterSize(getMaxWidthInput(oForm),"px");
        }
      }
      var oHtml = document.getElementsByTagName("HTML")[0];
      if (parent.setFilterSizeTight) {
        parent.setFilterSizeTight(oHtml);
      }
      if (oForm) {
        checkName(oForm.m_contact, "name_pic", "caver2", "", undefined, false);
        checkMail(oForm.m_contact, "mail_pic");
        displayLength(oForm.m_body, 'm_length');
        displayCategory(oForm.m_subject);
        displayNames(oForm.m_category);
      }
    }
    
    function msgOnKeyUp(oObject) {
      limitLength(oObject, 1500);
      displayLength(oObject, 'm_length');
    }
    
    function subjectOnChange(oSelect) {
      displayCategory(oSelect);
    }
    
    function categoryOnChange(oSelect) {
      displayNames(oSelect);
    }
    
    function displayCategory(oSelect) {
      var oOption = oSelect.options[oSelect.selectedIndex];
      var oForm = document.message;
      if (oOption.id == "optn_id_2" || oOption.id == "optn_id_5") { //Bad_content or Restore_element
        showId('div_category');
      } else {
        hideId('div_category');
      }
    }
    
    function displayNames(oSelect) {
<?php
if ($_SESSION['home_page'] == "overview") {
?>
      var oOption = oSelect.options[oSelect.selectedIndex];
      var oForm = document.message;
      var namesArray = getMarkersByCategory(oOption.value);
      var emptyObject = new Object();
      emptyObject.id = "<?php echo Select_default; ?>";
      emptyObject.category = oOption.value;
      emptyObject.Text = "Autre élément";
      namesArray.push(emptyObject);
<?php
}
?>
    }
      
    function messageCancel() {
      document.location.href = "<?php echo $back_src; ?>";
    }
    
    function messageSubmit(event) {
      var oForm = document.message;
      var rightSource = toAbsURL(rightPic);
      var oField = xtdGetElementById('name_pic');
      var sMessage = "<convert>#label=225<convert> <convert>#label=240<convert> | / \\ ' \" & + <convert>#label=46<convert> ¨";//Votre nom //doit être unique, de 3 à 36 caractères sauf //et
      createTest(oField.name, oField.src, rightSource, "==", sMessage, true);
      oField = xtdGetElementById('mail_pic');
      sMessage = "<convert>#label=214<convert>";//Votre e-mail doit être un e-mail valide.
      createTest(oField.name, oField.src, rightSource, "==", sMessage, true);
      oField = oForm.m_subject;
      sMessage = "<convert>#label=226<convert>";//La zone \"Objet\" doit être renseignée.
      createTest(oField.name, oField.value, "<?php echo Select_default; ?>", "!=", sMessage, true);
      if (!testForm()) {
        stopSubmit(event);
      }
    }
      
<?php
if ($_SESSION['home_page'] == "overview") {
  include("../scripts/events.js");
}
?>
<?php
    switch ($type)
    {
    	case "message":
?>
    messageBeforeLoad(true);
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:messageOnLoad();">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
		<?php echo getCloseBtn($back_src,"<convert>#label=371<convert>"); ?><!-- Fermer -->
		<div class="frame_title"><?php echo setTitle("contact_".$_SESSION['language'].".php?type=message", "filter", "<convert>#label=230<convert>", 2); ?></div><!--Message-->
<?php

if (isset($_POST['send']) && !$send_failed){
?>
    <div class="warning"><?php echo getTopBubble(); ?><convert>#label=231<convert><!--Votre message a été envoyé avec succès !--><?php echo getBotBubble(); ?></div>
<?php
} else {
  if ($send_failed) {
?>
		<div class="error"><?php echo getTopBubble(); ?><convert>#label=62<convert><!--Erreur, veuillez recommencer !--><?php echo getBotBubble(); ?></div>
<?php
  }
?>
	  <form id="message" name="message" method="post" action="" onsubmit="JavaScript:messageSubmit(event);">
	  <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
      <tr><th colspan="2"><?php echo getHelpTopic($helpId[$type], "<convert>#label=23<convert>"); ?></th></tr>
      <tr><td width="170" class="label">
        <label class="label1" for="m_name">
          <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=225<convert><!--Votre nom--><sup>1</sup>
        </label>
      </td><td class="field">
        <input class="input1" type="text" id="m_name" name="m_name" value="<?php echo $name; ?>" size="15" maxlength="36" onkeyup="JavaScript:checkName(this, 'name_pic', 'caver2', '', undefined, false);" <?php echo $readonly; ?>/>
        <img class="status1" name="name_pic" id="name_pic" src="../images/icons/wrong.png" alt="status" />
      </td></tr><tr><td width="170" class="label">
        <label class="label1" for="m_contact">
          <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=232<convert><!--Votre e-mail--><sup>2</sup>
        </label>
      </td><td class="field">
        <input class="input1" type="text" id="m_contact" name="m_contact" value="<?php echo $contact; ?>" size="20" maxlength="40" onkeyup="JavaScript:checkMail(this, 'mail_pic');" <?php echo $readonly; ?>/>
        <img class="status1" name="mail_pic" id="mail_pic" src="../images/icons/wrong.png" alt="status" />
      </td></tr><tr><td width="170" class="label">
        <label class="label1" for="m_subject">
          <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=233<convert><!--Objet-->
        </label>
      </td><td class="field">
        <select class="select1" name="m_subject" id="m_subject" onchange="JavaScript:subjectOnChange(this);">
<?php
        $sql = "SELECT Subject as value, ".$_SESSION['language']."_subject AS text, CONCAT('optn_id_',Id) as id FROM `".$_SESSION['Application_host']."`.`T_message_subject` ORDER BY text";
        $msg = "<convert>#label=234<convert>";//Sélectionnez ...
        $comparedCol = "value";
        $textCol = "text";
        $selected = $subject;
        $idCol = "id";
        echo getOptions($sql, $msg, $selected, $comparedCol, $idCol, $textCol);
?>
        </select>
      </td></tr>
        
      <tr><td colspan="2">
    	<div id="div_category" style="margin:-2px 0px -2px -5px">
      	<table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
      	
          <tr><td width="170" class="label">
            <label class="label1" for="m_category">
              <convert>#label=235<convert><!--Catégorie-->
            </label>
          </td><td class="field">
            <select class="select1" name="m_category" id="m_category" onchange="JavaScript:categoryOnChange(this);">
<?php
            $sql = "SELECT Name AS value, ".$_SESSION['language']."_name AS text FROM `".$_SESSION['Application_host']."`.`T_category` ORDER BY text";
            $msg = "<convert>#label=236<convert>";//Autre catégorie
            $comparedCol = "value";
            $textCol = "text";
            $selected = $category;
            echo getOptions($sql, $msg, $selected, $comparedCol, $textCol);
?>
            </select>
          </td></tr>
        </table>
        </div>
        </td></tr>
        
        <tr><td width="170" class="label">
          <label class="label1" for="m_body" style="height:140px;">
            <convert>#label=229<convert><!--Votre message--><sup>3</sup>
          </label>
        </td><td class="field">
          <textarea class="input1" id="m_body" name="m_body" cols="40" rows="6" onkeyup="JavaScript:msgOnKeyUp(this);"><?php if (isset($_POST['send'])) { echo stripslashes($body); } ?></textarea>
          <br /><span id="m_length">0</span> <convert>#label=238<convert><!--caractères sur--> 1 500.
        </td></tr>
<?php
if (!USER_IS_CONNECTED) {
?>
        <tr><td width="170" class="label">
          <label class="label_img1" for="check">
          </label>
        </td><td class="field">
<?php
  $_SESSION['userCheck'] = createImage(6, 16);
?>
          <img class="image1" name="check" id="check" src="<?php echo $_SESSION['userCheck']; ?>.gif" alt="image" />
        </td></tr><tr><td width="170" class="label">
          <label class="label1" style="height:3.2em;" for="m_check">
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=194<convert><!--Texte contenu dans la grotte-->
          </label>
        </td><td class="field">
          <input class="input1" type="text" id="m_check" name="m_check" value="" size="6" maxlength="6" />
        </td></tr>
<?php
}
?>
        <tr><td width="170" class="label">
          <label class="label1" style="height: 2em;" for="send">
          </label>
        </td><td class="field">
          <input type="hidden" id="m_real_mail" name="m_real_mail" value="<?php echo $_SESSION['user_contact']; ?>" />
          <input type="hidden" id="m_sec_subject" name="m_sec_subject" value="<?php echo $bad_name; ?>" />
          <input class="button1" type="submit" id="send" name="send" value="<convert>#label=239<convert>" /><!--Envoyer-->
        </td></tr><tr><td width="170" class="label">
          <label class="label1" style="height: 2em;" for="cancel">
          </label>
        </td><td class="field">
          <input class="button1" type="button" id="cancel" name="cancel" onclick="JavaScript:messageCancel();" value="<convert>#label=77<convert>" /><!--Annuler-->
        </td></tr><tr><td colspan="2">
          <div class="notes">
            <?php echo getTopBubble(); ?>
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=78<convert><!--Champs nécessaires.--><br />
            <sup>1</sup> <convert>#label=225<convert><!--Votre nom--> <convert>#label=240<convert><!--doit être composé de 3 à 36 caractères sauf--> <b>|</b> <b>/</b> <b>\</b> <b>"</b> <b>#</b> <b>&amp;</b> <b>+</b> <convert>#label=46<convert><!--et--> <b>¨</b><br />
            <sup>2</sup> <convert>#label=214<convert><!--Votre e-mail doit être un e-mail valide.--><br />
            <sup>3</sup> <convert>#label=229<convert><!--Votre message--> <convert>#label=241<convert><!--ne doit pas dépasser--> 1 500 <convert>#label=242<convert><!--caractères-->.
            <?php echo getBotBubble(); ?>
          </div>
        </td></tr>
      </table>
    </form>
<?php
}
			break;
    	default:
?>
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:defaultonload();">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
  	<?php echo getCloseBtn($back_src,"<convert>#label=371<convert>"); ?><!-- Fermer -->
  	<div class="frame_title"><?php echo setTitle("#", "filter", "<convert>#label=80<convert>", 2); ?></div><!--Erreur-->
  	<div class="error"><?php echo getTopBubble(); ?><convert>#label=81<convert><!--Cas non traité !--><?php echo getBotBubble(); ?></div><br /><br />
<?php
    	break;
    }
?>
    <?php echo getBotFrame(); ?>
<?php
    $virtual_page = "contact/".$type."/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>
