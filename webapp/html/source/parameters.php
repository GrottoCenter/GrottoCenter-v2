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
include("../func/phpBBinterface.php");
if (!allowAccess(caver_edit_himself) || !($_SESSION['home_page'] == "overview")) { 
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
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=409<convert></title>
    <link rel="stylesheet" type="text/css" href="../css/filter.css" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
<?php  	
  	//Capture the action type :
    $type = (isset($_GET['type'])) ? $_GET['type'] : '';
    $tmp_key = rand(1,8);
    $helpId = array("account" => 10, "avatar" => 11, "password" => 12, "entry" => 13, "grotto" => 14, "delete" => 15);
    
    if (allowAccess(caver_edit_himself)){ 
      //Save the current viewport
      if ($type == "default") {
        $lat = (isset($_GET['lat'])) ? $_GET['lat'] : '';
        $lng = (isset($_GET['lng'])) ? $_GET['lng'] : '';
        $zoom = (isset($_GET['zoom'])) ? $_GET['zoom'] : '';
  
        $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_caver` ";
  	    $sql .= "SET `Default_latitude` = ".$lat.", ";
  	    $sql .= "`Default_longitude` = ".$lng.", ";
  	    $sql .= "`Default_zoom` = ".$zoom." ";
  	    $sql .= "WHERE `Id` = ".$_SESSION['user_id'];
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
      	$type = "menu";
      }
      
      if ($type == "menu") {
        $sql = "SELECT Relevance FROM `".$_SESSION['Application_host']."`.`T_caver` WHERE Id = ".$_SESSION['user_id'];
        $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
        $points_counter = $data[0]['Relevance'];
      }
  
      //Save the parameters :
  	  if (isset($_POST['save'])){
  	    $name = (isset($_POST['p_caver_name'])) ? $_POST['p_caver_name'] : '';
  	    $surname = (isset($_POST['p_caver_surname'])) ? $_POST['p_caver_surname'] : '';
  	    //$login = (isset($_POST['p_caver_login'])) ? $_POST['p_caver_login'] : '';
  	    $nickname = (isset($_POST['p_caver_nickname'])) ? $_POST['p_caver_nickname'] : '';
  	    $country = (isset($_POST['p_caver_country'])) ? $_POST['p_caver_country'] : '';
  	    $region = (isset($_POST['p_caver_region'])) ? $_POST['p_caver_region'] : '';
  	    $city = (isset($_POST['p_caver_city'])) ? $_POST['p_caver_city'] : '';
  	    $facebook = (isset($_POST['p_caver_facebook'])) ? $_POST['p_caver_facebook'] : '';
  	    $postal = (isset($_POST['p_caver_postal'])) ? $_POST['p_caver_postal'] : '';
  	    $address = (isset($_POST['p_caver_address'])) ? $_POST['p_caver_address'] : '';
  	    $birth = (isset($_POST['p_caver_birth'])) ? $_POST['p_caver_birth'] : '';
  	    $birth = cDate($birth,true);
  	    $contact = (isset($_POST['p_caver_contact'])) ? $_POST['p_caver_contact'] : '';
  	    $initiation = (isset($_POST['p_caver_initiation'])) ? $_POST['p_caver_initiation'] : '';
  			$language = (isset($_POST['p_caver_language'])) ? $_POST['p_caver_language'] : '';
        if ($language == "" || $language == Select_default) {
          $language = $_SESSION['language'];
        }
  	    $public = (isset($_POST['p_caver_public'])) ? $_POST['p_caver_public'] : '';
  	    $alert_for_news = (isset($_POST['p_caver_news'])) ? $_POST['p_caver_news'] : '';
  	    $show_links = (isset($_POST['p_caver_links'])) ? $_POST['p_caver_links'] : '';
  	    $detail_level = (isset($_POST['p_caver_detail_level'])) ? $_POST['p_caver_detail_level'] : '';
  	    $latitude = (isset($_POST['p_caver_latitude'])) ? $_POST['p_caver_latitude'] : '';
  	    $longitude = (isset($_POST['p_caver_longitude'])) ? $_POST['p_caver_longitude'] : '';
  	    $custom_message = (isset($_POST['p_caver_message'])) ? $_POST['p_caver_message'] : '';
  	    $picture_file_name = (isset($_POST['p_caver_file'])) ? $_POST['p_caver_file'] : '';
				
  	    $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_caver` ";
  	    $sql .= "SET `Name` = ".returnDefault($name, 'Name').", ";
  	    //$sql .= "`Login` = ".returnDefault($login, 'text').", ";
  	    $sql .= "`Nickname` = ".returnDefault($nickname, 'text').", ";
        $sql .= "`Surname` = ".returnDefault($surname, 'text').", ";
        $sql .= "`Country` = ".returnDefault($country, 'list').", ";
        $sql .= "`Region` = ".returnDefault($region, 'text').", ";
        $sql .= "`City` = ".returnDefault($city, 'text').", ";
        $sql .= "`Facebook` = ".returnDefault($facebook, 'text').", ";
        $sql .= "`Postal_code` = ".returnDefault($postal, 'int').", ";
        $sql .= "`Address` = ".returnDefault($address, 'text').", ";
        $sql .= "`Date_birth` = ".returnDefault($birth, 'text').", ";
        $sql .= "`Contact` = ".returnDefault($contact, 'text').", ";
        $sql .= "`Year_initiation` = ".returnDefault($initiation, 'int').", ";
        $sql .= "`Language` = ".returnDefault($language, 'list').", ";
        $sql .= "`Contact_is_public` = ".returnDefault($public, 'text').", ";
        $sql .= "`Alert_for_news` = ".returnDefault($alert_for_news, 'checkbox').", ";
        $sql .= "`Show_links` = ".returnDefault($show_links, 'checkbox').", ";
        $sql .= "`Detail_level` = ".returnDefault($detail_level, 'list').", ";
        $sql .= "`Latitude` = ".returnDefault($latitude, 'latlng').", ";
        $sql .= "`Longitude` = ".returnDefault($longitude, 'latlng').", ";
        $sql .= "`Custom_message` = ".returnDefault($custom_message, 'text')." ";
  	    $sql .= "WHERE `Id` = ".$_SESSION['user_id'];
  	    $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
  	    $sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`T_caver` ";
  	    $sql .= "WHERE `Id` = ".$_SESSION['user_id'];
  	    $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
        if ($data['Count'] > 0){
  	     // Renew the session variables :
          setSession(true, $data[0]);
  	    } else {
  	      //Reset the session
  	      setSession(false);
        }
  	  }
  	  
  	  if (isset($_GET['avatar_changed'])) {
        $avatarFile = (isset($_GET['avatar_file'])) ? $_GET['avatar_file'] : '';
        $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_caver` SET Picture_file_name = '".$avatarFile."' ";
  	    $sql .= "WHERE `Id` = ".$_SESSION['user_id'];
  	    $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
  	    if ($req['mysql_affected_rows'] == 0) {
          $avatarFile = "";
          $upload_error = "error while updating profile. Please contact your administrator.";
        } else {
          $_SESSION['user_file'] = $avatarFile;
        }
      }
      
      if ($type == "avatar" && $avatarFile == "") {
        $sql = "SELECT Picture_file_name FROM `".$_SESSION['Application_host']."`.`T_caver` WHERE `Id` = ".$_SESSION['user_id'];
        $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
        $avatarFile = $data[0]['Picture_file_name'];
      }
  	  
      //Save the pwd :
  	  if (isset($_POST['save_pwd'])){
        $password = (isset($_POST['c_caver_password'])) ? $_POST['c_caver_password'] : '';
        $new_password = (isset($_POST['c_caver_new_password'])) ? $_POST['c_caver_new_password'] : '';
        $key = (isset($_POST['c_key'])) ? $_POST['c_key'] : '';
        $password = crypt_xor(stripslashes($password), $key);
        $new_password = crypt_xor(stripslashes($new_password), $key);
        $login = addslashes($_SESSION['user_login']);
        $sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`T_caver` ";
  	    $sql .= "WHERE `Id` = ".$_SESSION['user_id']." ";
  	    $sql .= "AND `Password` = '".getCryptedPwd($login,$password)."'";
  	    $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
        if ($data['Count'] > 0){
          $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_caver` ";
          $sql .= "SET `Password` = ".returnDefault(getCryptedPwd($login,$new_password), 'text')." ";
          $sql .= "WHERE `Id` = ".$_SESSION['user_id'];
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          if (!defined('NO_PHPBB_INSTALLED')) {
            chgPwdphpBBuser($login, $new_password);
          }
          $pwd_saved = true;
  	    } else {
          $pwd_saved = false;
        }
  	  }
  	}
  	  
  	if (allowAccess(caver_delete_himself)){
      //Delete an account :
  	  if (isset($_POST['delete_user'])){
        $password = (isset($_POST['d_caver_password'])) ? $_POST['d_caver_password'] : '';
        $key = (isset($_POST['d_key'])) ? $_POST['d_key'] : '';
        $password = crypt_xor(stripslashes($password), $key);
        $login = (isset($_POST['d_caver_login'])) ? $_POST['d_caver_login'] : '';
        $sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`T_caver` ";
  	    $sql .= "WHERE `Id` = ".$_SESSION['user_id']." ";
  	    $sql .= "AND `Password` = '".getCryptedPwd($login,$password)."' ";
  	    $sql .= "AND `Login` = '".$login."'";
  	    $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
        if ($data['Count'] > 0){
          trackAction("delete_user",$_SESSION['user_id'],"T_caver");
          $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`T_caver` ";
          $sql .= "WHERE `Id` = ".$_SESSION['user_id'];
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_entry_caver` ";
          $sql .= "WHERE `Id_caver` = ".$_SESSION['user_id'];
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_grotto_caver` ";
          $sql .= "WHERE `Id_caver` = ".$_SESSION['user_id'];
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_caver_group` ";
          $sql .= "WHERE `Id_caver` = ".$_SESSION['user_id'];
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $delete_failed = false;
  	    } else {
          $delete_failed = true;
        }
  	  }
  	}
  	  
  	if (allowAccess(caver_edit_himself)){ 
  	  //Save the entry list
  	  if (isset($_POST['save_list_entries'])){
        $entry_saved = false;
        $list = (isset($_POST['e_list'])) ? $_POST['e_list'] : '';
        //Reset the user's entries
        $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_entry_caver` ";
        $sql .= "WHERE `Id_caver` = ".$_SESSION['user_id'];
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        //If he chose some, store the entries
        if ($list != "") {
          $arrList = split('[|]+', $list);
          $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`J_entry_caver` (`Id_caver`, `Id_entry`) VALUES ";
          foreach($arrList as $value) {
            $sql .= "(".$_SESSION['user_id'].", ".$value."), ";
          }
          $sql = substr($sql,0,strlen($sql)-2);
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $entry_saved = true;
        }
      }
  	  
  	  //Save the grotto list
  	  if (isset($_POST['save_list_grottos'])){
        $grotto_saved = false;
        $list = (isset($_POST['g_list'])) ? $_POST['g_list'] : '';
        //Reset the user's grottos
        $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_grotto_caver` ";
        $sql .= "WHERE `Id_caver` = ".$_SESSION['user_id'];
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        //If he chose some, store the grottos
        if ($list != "") {
          $arrList = split('[|]+', $list);
          $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`J_grotto_caver` (`Id_caver`, `Id_grotto`) VALUES ";
          foreach($arrList as $value) {
            $sql .= "(".$_SESSION['user_id'].", ".$value."), ";
          }
          $sql = substr($sql,0,strlen($sql)-2);
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $grotto_saved = true;
        }
      }
    }
	  
?>
    <script type="text/javascript" src="../scripts/classeGCTest.js"></script>
<?php
if ($type == "account") {
    /*<script type="text/javascript" src="../scripts/geoNames.js"></script>
    <script type="text/javascript" src="http://ws.geonames.org/export/geonamesData.js"></script>
    <script type="text/javascript" src="http://ws.geonames.org/export/jsr_class.js"></script>*/
?>
    <script type="text/javascript" src="../scripts/calendar.js"></script>
<?php
}
?>
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
    //Gona need those functions : switchDOM();
    var namesArray = new Array();
    <?php include("../scripts/events.js"); ?>

<?php
switch ($type) {
	case "menu":
?>
    function menuBeforeLoad() {
      parent.setFilterSize(20);
    }

    function menuOnLoad() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
    }
    
    function menuDefault() {
      saveDefaultPosition();
      var lat = getDefaultLat();
      var lng = getDefaultLng();
      var zoom = getDefaultZoom();
      self.location='parameters_<?php echo $_SESSION['language']; ?>.php?type=default&lat=' + lat.toString() + '&lng=' + lng.toString() + '&zoom=' + zoom.toString();
    }
<?php
	break;
	case "account":
?>
    function paramBeforeLoad() {
      parent.setFilterSize(46);
      if (mySite.overview) {
        mySite.overview.hideId("reload");
      }
    }
    
    function paramOnLoad() {
      var oForm = document.data_user;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      if (mySite.details) {
        if (mySite.details.switchDetails) {
          mySite.details.switchDetails(true);
        }
      }
      parent.setFilterSize(getMaxWidthInput(oForm),"px");
      var oHtml = document.getElementsByTagName('HTML')[0];
      parent.setFilterSizeTight(oHtml);
      blockFields(oForm);
      disableField(xtdGetElementById('edit'),false);
      checkMail(oForm.p_caver_contact, 'mail_pic');
      namesArray = loadNames("caver_nick");
      checkThisName(oForm.p_caver_nickname);
      <?php if (isset($_POST['save'])) { ?>// && $_SESSION['home_page'] == "overview"
      reload();
      openMe(<?php echo $_SESSION['user_id']; ?>, "caver");
      <?php } ?>
    }
    
    function paramOnUnload() {
      if (mySite.overview) {
        blockMe(<?php echo $_SESSION['user_id']; ?>, "caver");
        mySite.overview.showId("reload");
      }
      mySite.details.switchDetails(false);
    }
    
    function paramSubmit(event) {
      if (!userIsOk(document)) {
        stopSubmit(event);
      } else {
        var oForm = document.data_user;
        if (mySite.overview) {
          blockMe(<?php echo $_SESSION['user_id']; ?>, "caver");
          var marker = parent.overview.getMarker(<?php echo $_SESSION['user_id']; ?>, "caver");
          doChallengeCoordinates(oForm.p_caver_latitude, oForm.p_caver_longitude, marker);
        }
        return true;
      }
    }
    
    function paramCancel() {
      var oForm = document.data_user;
      if (mySite.overview) {
        blockMe(<?php echo $_SESSION['user_id']; ?>, "caver");
        //setMapPosition();
        reload(false);
      }
      self.location.href = "parameters_<?php echo $_SESSION['language']; ?>.php?type=account";
      showId("close");
    }
      
    function paramEdit() {
      var oForm = document.data_user;
      //getMapPosition();
      freeFields(oForm);
      disableField(xtdGetElementById('edit'),true);
      if (mySite.overview) {
        openMe(<?php echo $_SESSION['user_id']; ?>, "caver", false);
        freeMe(<?php echo $_SESSION['user_id']; ?>, "caver");
      }
      hideId("close");
    }
    
    function checkThisName(oObject) {
      checkName(oObject, "nick_pic", "caver", "<?php echo $_SESSION['user_nickname']; ?>", namesArray, true);
    }
    
    var address_level;
    
    function paramLocate(byCoords) {
      address_level = 4; //Address = 4, City = 3, Region = 2, Country = 1
      var oForm = document.data_user;
      if (byCoords) {
        moveMarker(new mySite.overview.GLatLng(strToFloat(oForm.p_caver_latitude.value), strToFloat(oForm.p_caver_longitude.value)));
      } else {
      	getCoordsByDirection(getAddress('p_', 'caver', address_level), moveMarker);
      }
    }
    
    function moveMarker(gLatLng) {
      if (gLatLng) {
        moveMarkerTo(<?php echo $_SESSION['user_id']; ?>, 'caver', gLatLng.lat(), gLatLng.lng());
        openMe(<?php echo $_SESSION['user_id']; ?>, "caver", false);
        setLocations(gLatLng.lat(), gLatLng.lng());
      } else {
        if (address_level > 0) {
          address_level --;
          getCoordsByDirection(getAddress('p_', 'caver', address_level), moveMarker);
        }
      }
    }

    function recieveLocation(lat, lng) {
      setLocations(lat, lng);
      paramLocate(true);
    }
    
    function setLocations(lat, lng) {
      var oForm = document.data_user;
      oForm.p_caver_latitude.value = lat;
      oForm.p_caver_longitude.value = lng;
    }
    
    function writeLink(oField) {
      var href, text, idValue, oText;
      idValue = "facebook_link";
      href = xtdGetElementById(idValue);
      text = "http://www.facebook.com/" + oField.value;
      oText = document.createTextNode(text);
      if (href == null) {
        href = document.createElement("a");
        href.appendChild(oText);
        href.setAttribute("target", "_blank");
        href.setAttribute("id", idValue);
      } else {
        href.replaceChild(oText, href.firstChild);
      }
      href.setAttribute("href", text);
      href.setAttribute("title", text);
      oField.parentNode.appendChild(href);
    }
<?php
	break;
	case "avatar":
?>
    function avatarBeforeLoad() {
      parent.setFilterSize(25);
    }
    
    function avatarOnLoad() {
      var oForm = document.avatar_user;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      parent.setFilterSize(getMaxWidthInput(oForm),"px");
      var oHtml = document.getElementsByTagName('HTML')[0];
      parent.setFilterSizeTight(oHtml);
    }
<?php
	break;
	case "password":
?>
    function pwdBeforeLoad() {
      parent.setFilterSize(30);
    }
    
    function pwdOnLoad() {
      var oForm = document.pwd_user;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      parent.setFilterSize(getMaxWidthInput(oForm),"px");
      var oHtml = document.getElementsByTagName('HTML')[0];
      parent.setFilterSizeTight(oHtml);
    }
    
    function pwdSubmit(event) {
      var oForm = document.pwd_user;
      oForm.c_caver_password.value = c(oForm.c_caver_password.value, oForm.c_key.value);
      oForm.c_caver_new_password.value = c(oForm.c_caver_new_password.value, oForm.c_key.value);
      if (!userIsOk(document)) {
        stopSubmit(event);
      } else {
        oForm.c_caver_new_password2.value = "";
        return true;
      }
    }
<?php
	break;
	case "entry":
?>
    function entryBeforeLoad() {
      parent.setFilterSize(25);
    }
    
    function entryOnLoad() {
      var oForm = document.entries_user;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      parent.setFilterSize(getMaxWidthInput(oForm),"px");
      var oHtml = document.getElementsByTagName('HTML')[0];
      parent.setFilterSizeTight(oHtml);
      <?php if (isset($_POST['save_list_entries'])) {echo "reload();";} ?>// && $_SESSION['home_page'] == "overview"
    }
    
    function entryRemove() {
      var oForm = document.entries_user;
      var oOptions = oForm.e_myList.options;
      for (var i=0; i<oOptions.length; i++) {
        if (oOptions[i].selected) {
          oOptions[i] = null;
          i--;
        }
      }
    }
    
    function entryAdd() {
      var windowName, url;
      windowName = "<convert>#label=612<convert>";//Choisissez une ou plusieurs entrÃ©es Ã  ajouter
      //url = "<?php echo $_SESSION['Application_url']; ?>/html/pick_window_<?php echo $_SESSION['language']; ?>.php?type=entry&callback=addEntry";
      url = "pick_window_<?php echo $_SESSION['language']; ?>.php?type=entry&callback=addEntry";
      openWindow(url, windowName, 990, 520);
    }
    
    function addEntry(oForm) {
      var uForm = document.entries_user;
      addOptionsFromSelection(oForm, uForm.e_myList);
    }
    
    function entrySubmit() {
      var oForm = document.entries_user;
      doChallengeList(oForm.e_myList,oForm.e_list);
      return true;
    }
<?php
	break;
	case "grotto":
?>
    function grottoBeforeLoad() {
      parent.setFilterSize(25);
    }
    
    function grottoOnLoad() {
      var oForm = document.grottos_user;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      parent.setFilterSize(getMaxWidthInput(oForm),"px");
      var oHtml = document.getElementsByTagName('HTML')[0];
      parent.setFilterSizeTight(oHtml);
      <?php if (isset($_POST['save_list_grottos'])) {echo "reload();";} ?>// && $_SESSION['home_page'] == "overview"
    }
    
    function grottoRemove() {
      var oForm = document.grottos_user;
      var oOptions = oForm.g_myList.options;
      for (var i=0; i<oOptions.length; i++) {
        if (oOptions[i].selected) {
          oOptions[i] = null;
          i--;
        }
      }
    }
    
    function grottoAdd() {
      var windowName, url;
      windowName = "<convert>#label=645<convert>";//Choisissez un ou plusieurs clubs Ã  ajouter
      //url = "<?php echo $_SESSION['Application_url']; ?>/html/pick_window_<?php echo $_SESSION['language']; ?>.php?type=grotto&callback=addGrotto";
      url = "pick_window_<?php echo $_SESSION['language']; ?>.php?type=grotto&callback=addGrotto";
      openWindow(url, windowName, 690, 520);
    }
    
    function addGrotto(oForm) {
      var uForm = document.grottos_user;
      addOptionsFromSelection(oForm, uForm.g_myList);
    }
    
    function grottoSubmit() {
      var oForm = document.grottos_user;
      doChallengeList(oForm.g_myList,oForm.g_list);
      return true;
    }
<?php
	break;
	case "delete":
?>
    function deleteBeforeLoad(failed) {
      if (failed) {
        parent.setFilterSize(30);
      }    	
    }
    
    function deleteOnLoad(failed) {
      var oForm = document.del_user;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      if (failed) {
        parent.setFilterSize(getMaxWidthInput(oForm),"px");
        var oHtml = document.getElementsByTagName('HTML')[0];
        parent.setFilterSizeTight(oHtml);
      } else {
        alert("<convert>#label=410<convert>");//Votre compte a Ã©tÃ© supprimÃ©.\nVous Ãªtes dÃ©connectÃ© Ã  prÃ©sent.
        this.parent.parent.location.href = "../index.php?logout=true";
      }    	
    }
    
    function deleteSubmit(event) {
      var oForm = document.del_user;
      if (confirm("<convert>#label=411<convert>")) { //Etes vous sÃ»r de vouloir supprimer votre compte ?
        oForm.d_caver_password.value = c(oForm.d_caver_password.value, oForm.d_key.value);
      } else {
        stopSubmit(event);
      }
    }
<?php
	break;
	default:
?>
    function defaultBeforeLoad() {
      parent.resetFilterSize();
    }
    
    function defaultOnLoad() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
    }
<?php
	break;
}
?>
    function selectOnClick(e, oSelect) {
      var Id = oSelect.options[oSelect.selectedIndex].value;
      document.body.focus();
      var Category = "<?php echo $type; ?>";
      openMe(Id, Category, false);
      detailMarker(e, Category, Id, '<?php echo $_SESSION['language']; ?>', false);
    }
    
    function entryNew() {
      self.location.href = "entry_<?php echo $_SESSION['language']; ?>.php?type=edit";
    }
    
    function grottoNew() {
      self.location.href = "grotto_<?php echo $_SESSION['language']; ?>.php?type=edit";
    }
    
    function backToMenu() {
      self.location.href = "parameters_<?php echo $_SESSION['language']; ?>.php?type=menu";
    }
<?php
    switch ($type) {
    	case "menu":
?>
      menuBeforeLoad();
<?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:menuOnLoad();">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
    <div class="menu">
		  <?php echo getTopMenu(getCloseBtn("filter_".$_SESSION['language'].".php","<convert>#label=371<convert>").'<div class="frame_title">'.setTitle("parameters_".$_SESSION['language'].".php?type=menu", "filter", "<convert>#label=375<convert>", 2).'</div><!--Mes paramÃ¨tres-->'); ?>
      <ul>
<?php
if (allowAccess(caver_edit_himself)) {
?>
        <li class ="sub_menu">
          <a href="JavaScript:menuDefault();"><convert>#label=412<convert><!--Enregistrer cette vue comme vue par dÃ©faut--></a>
        </li>
        <li class ="sub_menu">
          <a href="parameters_<?php echo $_SESSION['language']; ?>.php?type=account"><convert>#label=413<convert><!--GÃ©rer mon compte--></a>
        </li>
        <li class ="sub_menu">
          <a href="parameters_<?php echo $_SESSION['language']; ?>.php?type=avatar"><convert>#label=584<convert><!--Mon avatar--></a>
        </li>
        <li class ="sub_menu">
          <a href="parameters_<?php echo $_SESSION['language']; ?>.php?type=password"><convert>#label=414<convert><!--Changer de mot de passe--></a>
        </li>
        <li class ="sub_menu">
          <a href="parameters_<?php echo $_SESSION['language']; ?>.php?type=entry"><convert>#label=415<convert><!--Liste de mes cavitÃ©s--></a>
        </li>
        <li class ="sub_menu">
          <a href="parameters_<?php echo $_SESSION['language']; ?>.php?type=grotto"><convert>#label=416<convert><!--Liste de mes clubs--></a>
        </li>
<?php
}
if (allowAccess(caver_delete_himself)) {
?>
        <li class ="sub_menu">
          <a href="parameters_<?php echo $_SESSION['language']; ?>.php?type=delete" style="color:red;"><convert>#label=417<convert><!--Supprimer mon compte--></a>
        </li>
<?php
}
?>
      </ul>
      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><th colspan="2"><?php echo getHelpTopic($helpId[$type], "<convert>#label=23<convert>"); ?></th></tr>
        <tr><td width="170" class="label">
	      	<convert>#label=753<convert><!--Vous avez-->
        </td><td class="field">
    		  <?php echo $points_counter; ?> <convert>#label=756<convert><!--Points ! -->
    		</td></tr><tr><td width="170" class="label">
	      	<convert>#label=192<convert><!--Identifiant de connexion-->
        </td><td class="field">
    		  <?php echo $_SESSION['user_login']; ?>
    		</td></tr>
    	</table>
      <?php echo getBotMenu(); ?>
    </div>
<?php
			break;
    	case "account":
        if (!allowAccess(caver_edit_himself)) {
          exit();
        }
?>
    paramBeforeLoad();
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:paramOnLoad();" onunload="JavaScript:paramOnUnload();">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
		<?php echo getCloseBtn("JavaScript:backToMenu();","<convert>#label=371<convert>"); ?><!-- Fermer -->
		<div class="frame_title"><?php echo setTitle("parameters_".$_SESSION['language'].".php?type=account", "filter", "<convert>#label=418<convert>", 3); ?></div><!--Mon compte-->
  	<form id="data_user" name="data_user" method="post" action="">
      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><th colspan="2"><?php echo getHelpTopic($helpId[$type], "<convert>#label=23<convert>"); ?></th></tr>
			  <tr><td width="170" class="label">
          <label for="edit" style="color:red;font-weight:bold;">
            <convert>#label=419<convert><!--Cliquez pour commencer-->
          </label>
        </td><td class="field">
          <input onclick="JavaScript:paramEdit();" class="button1" type="button" id="edit" name="edit" value="<convert>#label=53<convert>" /><!--Modifier-->
        </td></tr><tr><td width="170" class="label">
          <label for="p_caver_login">
            <convert>#label=192<convert><!--Identifiant de connexion-->
          </label>
        </td><td class="field">
          <?php echo $_SESSION['user_login']; ?>
        </td></tr><tr><td width="170" class="label">
          <label for="p_caver_nickname">
            <img src="../images/icons/FlagRequired.gif" alt="*" /><img src="../images/icons/FlagUnique.gif" alt="!" /><convert>#label=578<convert><!--Pseudonyme--><sup>1</sup>
          </label>
        </td><td class="field">
          <input class="input1" type="text" id="p_caver_nickname" name="p_caver_nickname" value="<?php echo $_SESSION['user_nickname']; ?>" size="20" maxlength="68" onkeyup="JavaScript:checkThisName(this);" />
          <img class="status1" name="nick_pic" id="nick_pic" src="../images/icons/wrong.png" alt="image" />
        </td></tr><tr><td width="170" class="label">
          <label for="p_caver_contact">
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=197<convert><!--E-mail de contact--><sup>2</sup>
          </label>
        </td><td class="field">
          <input class="input1" type="text" id="p_caver_contact" name="p_caver_contact" value="<?php echo $_SESSION['user_contact']; ?>" size="20" maxlength="40" onkeyup="JavaScript:checkMail(this, 'mail_pic');" />
          <img class="status1" name="mail_pic" id="mail_pic" src="../images/icons/wrong.png" alt="image" />
        </td></tr><tr><td width="170" class="label">
          <label for="p_caver_name">
            <convert>#label=199<convert><!--Nom-->
          </label>
        </td><td class="field">
          <input class="input1" type="text" id="p_caver_name" name="p_caver_name" value="<?php echo $_SESSION['user_name']; ?>" size="15" maxlength="36" />
        </td></tr><tr><td width="170" class="label">
          <label for="p_caver_surname">
            <convert>#label=200<convert><!--PrÃ©nom-->
          </label>
        </td><td class="field">
          <input class="input1" type="text" id="p_caver_surname" name="p_caver_surname" value="<?php echo $_SESSION['user_surname']; ?>" size="15" maxlength="32" />
        </td></tr><tr><td width="170" class="label">
          <label for="p_caver_country">
            <convert>#label=98<convert><!--Pays-->
          </label>
        </td><td class="field">
          <select class="select2" name="p_caver_country" id="p_caver_country">
<?php
          echo getOptionCountry($_SESSION['user_language'], $_SESSION['user_country'],"<convert>#label=99<convert>");//Sélectionnez un pays...
?>
          </select>
        </td></tr><tr><td width="170" class="label">
          <label for="p_caver_region">
            <convert>#label=100<convert><!--RÃ©gion-->
          </label>
        </td><td class="field">
          <input class="input1" type="text" id="p_caver_region" name="p_caver_region" value="<?php echo $_SESSION['user_region']; ?>" size="15" maxlength="32" />
          <i><convert>#label=665<convert><!--Ex : RhÃ´ne (69)--></i>
        </td></tr><tr><td width="170" class="label">
          <label for="p_caver_postal">
            <convert>#label=145<convert><!--Code postal-->
          </label>
        </td><td class="field">
          <input class="input1" type="text" id="p_caver_postal" name="p_caver_postal" value="<?php echo $_SESSION['user_postal']; ?>" size="5" maxlength="5" />
        </td></tr><tr><td width="170" class="label">
          <label for="p_caver_city">
            <convert>#label=101<convert><!--Ville-->
          </label>
        </td><td class="field">
          <input class="input1" type="text" id="p_caver_city" name="p_caver_city" value="<?php echo $_SESSION['user_city']; ?>" size="15" maxlength="32" /><!-- onfocus="JavaScript:cityOnFocus(this,'suggestBoxElement');" onblur="JavaScript:closeSuggestBox();"-->
          <!--span style="position:absolute;top:20px;left:0px;z-index:25;visibility:hidden;" id="suggestBoxElement"></span-->
        </td></tr><tr><td width="170" class="label">
          <label for="p_caver_address">
            <convert>#label=102<convert><!--Adresse-->
          </label>
        </td><td class="field">
          <input class="input1" type="text" id="p_caver_address" name="p_caver_address" value="<?php echo $_SESSION['user_address']; ?>" size="20" maxlength="128" />
        </td></tr>
<?php
//if ($_SESSION['home_page'] == "overview") {
?>
        <tr><td width="170" class="label">
          <label for="p_caver_locate">
            <convert>#label=201<convert><!--Indiquer ma position sur la carte--><sup>3</sup>
          </label>
        </td><td class="field">
          <input class="button1" type="button" id="p_caver_locate" name="p_caver_locate" onclick="JavaScript:paramLocate(false);" value="<convert>#label=108<convert>" /><!--Cliquez ici-->
        </td></tr>
<?php
//}
?>
        <tr><td width="170" class="label">
          <label for="p_caver_birth">
            <convert>#label=202<convert><!--Date de naissance-->
          </label>
        </td><td class="field">
          <input class="input1" type="text" id="p_caver_birth" name="p_caver_birth" value="<?php echo $_SESSION['user_birth']; ?>" size="10" maxlength="10" readonly="readonly" />
          <a href="JavaScript:showCalendar(document.data_user.p_caver_birth,'MM/DD/yyyy','<convert>#label=653<convert>')"><img src="../images/icons/cal.gif" alt="<convert>#label=652<convert>" title="<convert>#label=652<convert>" style="cursor:pointer;vertical-align:text-top;border:0px none;" /><!--Click Here to use a calendar--><!--Choose date--></a>
          <i><convert>#label=203<convert><!--MM/JJ/AAAA--></i>
        </td></tr><tr><td width="170" class="label">
          <label for="p_caver_initiation">
            <convert>#label=204<convert><!--AnnÃ©e d'initiation Ã  la spÃ©lÃ©o-->
          </label>
        </td><td class="field">
          <input class="input1" type="text" id="p_caver_initiation" name="p_caver_initiation" value="<?php echo $_SESSION['user_initiation']; ?>" size="4" maxlength="4" />
          <i><convert>#label=110<convert><!--AAAA--></i>
        </td></tr><tr><td width="170" class="label">
          <label for="p_caver_language">			      
            <convert>#label=205<convert><!--Langue-->
          </label>
        </td><td class="field">
          <select class="select2" name="p_caver_language" id="p_caver_language">
<?php
            echo getOptionLanguage($_SESSION['user_language']);
?>
          </select>
        </td></tr><tr><td width="170" class="label">
          <label for="p_caver_public0">			      	
            <convert>#label=206<convert><!--Mon profil est visible (e-mail etc...)-->
          </label>
        </td><td class="field">
          <input class="input1" type="radio" id="p_caver_public0" name="p_caver_public" value="0" style="border: none;" <?php if ($_SESSION['user_public']==0) { echo 'checked="checked"'; } ?> />
          <convert>#label=207<convert><!--jamais.-->
        </td></tr><tr><td width="170" class="label">
          <label for="p_caver_public1">
          </label>
        </td><td class="field">
          <input class="input1" type="radio" id="p_caver_public1" name="p_caver_public" value="1" style="border: none;" <?php if ($_SESSION['user_public']==1) { echo 'checked="checked"'; } ?> />
          <convert>#label=208<convert><!--par les inscrits Ã  GrottoCenter.-->
        </td></tr><tr><td width="170" class="label">
          <label for="p_caver_public2">
          </label>
        </td><td class="field">
          <input class="input1" type="radio" id="p_caver_public2" name="p_caver_public" value="2" style="border: none;" <?php if ($_SESSION['user_public']==2) { echo 'checked="checked"'; } ?> />
          <convert>#label=209<convert><!--par tout le monde. (dÃ©conseillÃ©)-->
        </td></tr><tr><td width="170" class="label">
          <label for="p_caver_facebook">
            <img class="status1" name="facebook_pic" id="facebook_pic" src="http://www.facebook.com/favicon.ico" alt="image" />
            <convert>#label=788<convert><!--Nom d'utilisateur Facebook--><span class="new_feature"><convert>#label=537<convert><!--Nouveau !--></span>
          </label>
        </td><td class="field">
          <input class="input1" type="text" id="p_caver_facebook" name="p_caver_facebook" value="<?php echo $_SESSION['user_facebook']; ?>" size="20" maxlength="68" onkeyup="JavaScript:writeLink(this);" /><br />
          <?php echo getFacebookATag($_SESSION['user_facebook']); ?>
        </td></tr><tr><td width="170" class="label">
          <label for="p_caver_message">
            <convert>#label=541<convert><!--Message perso./liens-->
          </label>
        </td><td class="field">
          <textarea class="input1" id="p_caver_message" name="p_caver_message" style="width:100%" rows="3" cols="" wrap="soft"><?php echo $_SESSION['user_message'];?></textarea>
        </td></tr><tr><td width="170" class="label">
          <label for="p_caver_news">
            <convert>#label=561<convert><!--Je souhaite Ãªtre tenu informÃ© des nouveautÃ©s par e-mail-->
          </label>
        </td><td class="field">
          <input class="input1" style="border:0px none;" type="checkbox" id="p_caver_news" name="p_caver_news" <?php if($_SESSION['user_news']=="YES"){echo "checked=\"checked\"";} ?> />
        </td></tr><tr><td width="170" class="label">
          <label for="p_caver_links">
            <convert>#label=621<convert><!--Afficher les liens au survol-->
          </label>
        </td><td class="field">
          <input class="input1" style="border:0px none;" type="checkbox" id="p_caver_links" name="p_caver_links" <?php if($_SESSION['user_hover']=="YES"){echo "checked=\"checked\"";} ?> />
        </td></tr><tr><td width="170" class="label">
          <label for="p_caver_detail_level">
            <convert>#label=649<convert><!--Niveau de dÃ©tail--> <convert>#label=651<convert><!--de l'affichage-->
          </label>
        </td><td class="field">
          <select name="p_caver_detail_level" id="p_caver_detail_level" size="1">
<?php
$delta = 5;
for ($i=5; $i<=Max_detail_level; $i = $i + $delta) {
  $limit_selected = "";
  if ($i == $_SESSION['user_detail_level'] + 0.0) {
    $limit_selected = "selected=\"selected\" ";
  }
  echo "<option value=\"".$i."\" ".$limit_selected.">".$i."</option>";
  if ($i == 20) {
    $delta = 10;
  }
  if ($i == 100) {
    $delta = 50;
  }
  if ($i == 300) {
    $delta = 100; 
  }
}
?>
          </select><i>(<convert>#label=650<convert><!--picto./catÃ©gorie-->).</i>
        </td></tr><tr><td width="170" class="label">
          <label for="save">
          </label>
        </td><td class="field">
          <input type="hidden" id="p_caver_latitude" name="p_caver_latitude" value="<?php echo $_SESSION['user_latitude'];?>" />
          <input type="hidden" id="p_caver_longitude" name="p_caver_longitude" value="<?php echo $_SESSION['user_longitude'];?>" />
          <input type="hidden" id="c_response" name="c_response" value="" />
          <input onclick="JavaScript:paramSubmit(event);" class="button1" type="submit" id="save" name="save" value="<convert>#label=76<convert>" /><!--Valider-->
        </td></tr><tr><td width="170" class="label">
          <label for="cancel">
          </label>
        </td><td class="field">
          <input onclick="JavaScript:paramCancel();" class="button1" type="button" id="cancel" name="cancel" value="<convert>#label=77<convert>" /><!--Annuler-->
        </td></tr><tr><td colspan="2">
          <div class="notes">
            <?php echo getTopBubble(); ?>
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=78<convert><!--Champs nÃ©cessaires.--><br />
            <img src="../images/icons/FlagUnique.gif" alt="!" /><convert>#label=736<convert><!--Doit Ãªtre unique.--><br />
            <!--sup>1</sup> Votre pseudonyme doit Ãªtre unique de 3 Ã  8 caractÃ¨res, sans accentuation ni caractÃ¨res spÃ©ciaux.<br-->
            <sup>1</sup> <convert>#label=579<convert><!--Votre pseudonyme--> <convert>#label=580<convert><!--doit Ãªtre composÃ© d'au moins 3 caractÃ¨res sauf--> <b>|</b> <b>/</b> <b>\</b> <b>"</b> <b>#</b> <b>&amp;</b> <b>+</b> <convert>#label=46<convert><!--et--> <b>Â¨</b><br />
            <sup>2</sup> <convert>#label=214<convert><!--Votre e-mail doit Ãªtre un e-mail valide.--> <br />
<?php
//if ($_SESSION['home_page'] == "overview") {
?>
            <sup>3</sup> <convert>#label=126<convert><!--Faites glisser le marqueur sur la carte avec votre souris pour le positionner plus prÃ©cisÃ©ment !-->
<?php
//}
?>
            <?php echo getBotBubble(); ?>
          </div>
<?php
if ($_SESSION['home_page'] != "overview") {
?>
          <div class="info">
            <?php echo getTopBubble(); ?>
            <convert>#label=630<convert> <convert>#label=632<convert><!--Pour pouvoir vous positionner gÃ©ographiquement, veuillez passer en mode 'carte' en cliquant sur le petit globe, en haut Ã  droite.<br />Attention, ceci aura pour effet de recharger la page et de perdre les Ã©ventuelles modifications.-->
            <?php echo getBotBubble(); ?>
          </div>
<?php
}
?>
        </td></tr>
      </table>
    </form>
<?php
			break;
			case "avatar":
        if (!allowAccess(caver_edit_himself)) {
          exit();
        }
        include("../func/upload_restrictions.php");
        $upload_error = (isset($_GET['error'])) ? $_GET['error'] : '';
        $upload_error = urldecode($upload_error);
?>
    avatarBeforeLoad();
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:avatarOnLoad();">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
		<?php echo getCloseBtn("JavaScript:backToMenu();","<convert>#label=371<convert>"); ?><!-- Fermer -->
	  <div class="frame_title"><?php echo setTitle("parameters_".$_SESSION['language'].".php?type=avatar", "filter", "<convert>#label=584<convert>", 3); ?></div><!--Mon avatar-->
<?php
          if ($upload_error != "") {
?>
	  <div class="error"><?php echo getTopBubble(); ?><convert>#label=62<convert><!--Erreur, veuillez recommencer !--><br />
      Error : <?php echo $upload_error; ?>
    <?php echo getBotBubble(); ?></div>
<?php
          }
?>
    <form id="avatar_user" name="avatar_user" method="post" action="../upload/avatars/avatar.php" enctype="multipart/form-data">
      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><th colspan="2"><?php echo getHelpTopic($helpId[$type], "<convert>#label=23<convert>"); ?></th></tr>
<?php if ($avatarFile != "") { ?>
			  <tr><td width="170" class="label" colspan="2" style="text-align:left;">
          <convert>#label=587<convert><!--Avatar actuel--> :
        </td></tr><tr><td colspan="2">
          <img class="gross_avatar" src="../upload/avatars/<?php echo $avatarFile."?nocache=".rand(); ?>" alt="avatar" />
        </td></tr><tr><td class="field" colspan="2">
          <input type="hidden" id="upload_type" name="upload_type" value="delete_avatar" />
      	  <input type="submit" class="button1" id="delete" name="delete" value="<convert>#label=55<convert>" /><!--Supprimer-->
      	</td></tr><tr><td class="field" colspan="2">
      	  <input type="button" class="button1" id="cancel" name="cancel" onclick="JavaScript:backToMenu();" value="<convert>#label=77<convert>" /><!--Annuler-->
      	</td></tr>
<?php } else { ?>
        <tr><td width="170" class="label" colspan="2" style="text-align:left;">
          <convert>#label=588<convert><!--Vous n'avez pas encore d'avatar-->.
        </td></tr><tr><td colspan="2">
          <img class="gross_avatar" src="../upload/avatars/default_avatar.png" alt="avatar" />
        </td></tr><tr><td width="170" class="label">
		      <label for="filename">
		      	<img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=585<convert><sup>1</sup><!--Fichier-->
		      </label>
		    </td><td class="field">
		     	<input class="input1" type="file" id="filename" name="filename" value="<?php echo $_FILES['filename']; ?>" size="20" accept="image/*" />
		    </td></tr><tr><td class="field" colspan="2">
          <input type="hidden" id="upload_type" name="upload_type" value="add_avatar" />
	      	<input type="submit" class="button1" id="upload" name="upload" value="<convert>#label=586<convert>" /><!--Envoyer le fichier-->
	      </td></tr><tr><td class="field" colspan="2">
          <input type="button" class="button1" id="cancel" name="cancel" onclick="JavaScript:backToMenu();" value="<convert>#label=77<convert>" /><!--Annuler-->
        </td></tr><tr><td colspan="2">
          <div class="notes">
            <?php echo getTopBubble(); ?>
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=78<convert><!--Champs nÃ©cessaires.--><br />
            <sup>1</sup> <?php echo '('.implode(', ', $upload_restrictions_ext_array['add_avatar']).')'; ?> <?php echo round($upload_restrictions_size_array['add_avatar']/1000, 2).'Ko.'; ?> <convert>#label=67<convert><!--Max.-->
            <!--<convert>#label=589<convert>--><!--Choisissez un fichier image (*.jpg, *.bmp, *.png…) de 30Ko maximum.-->
            <?php echo getBotBubble(); ?>
          </div>
        </td></tr>
<?php } ?>
      </table>
      <input type="hidden" id="source_manager" name="source_manager" value="../../html/parameters_<?php echo $_SESSION['language']; ?>.php?type=<?php echo $type; ?>&uploaded=true" />
    </form>
<?php
			break;
			case "password":
        if (!allowAccess(caver_edit_himself)) {
          exit();
        }
?>
    pwdBeforeLoad();
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:pwdOnLoad();">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
		<?php echo getCloseBtn("JavaScript:backToMenu();","<convert>#label=371<convert>"); ?><!-- Fermer -->
		<div class="frame_title"><?php echo setTitle("parameters_".$_SESSION['language'].".php?type=password", "filter", "<convert>#label=420<convert>", 3); ?></div><!--Mon mot de passe-->
<?php
        if (isset($_POST['save_pwd'])){
          if ($pwd_saved) {
?>
    <div class="warning"><?php echo getTopBubble(); ?><convert>#label=421<convert><!--Mot de passe sauvegardÃ© avec succÃ¨s !--><?php echo getBotBubble(); ?></div>
<?php
          } else {
?>
    <div class="error"><?php echo getTopBubble(); ?><convert>#label=422<convert><!--Erreur de mot de passe, veuillez recommencer !--><?php echo getBotBubble(); ?></div>
<?php
          }
        }
?>
  	<form id="pwd_user" name="pwd_user" method="post" action="" onsubmit="JavaScript:pwdSubmit(event);">
      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><th colspan="2"><?php echo getHelpTopic($helpId[$type], "<convert>#label=23<convert>"); ?></th></tr>
        <tr><td width="170" class="label">
          <label for="c_caver_password">
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=423<convert><!--Mot de passe actuel-->
          </label>
        </td><td class="field">
          <input class="input1" type="password" id="c_caver_password" name="c_caver_password" value="" size="10" maxlength="32" /><!--onKeyUp="JavaScript:checkPassword(this);"/-->
        </td></tr><tr><td width="170" class="label">
          <label for="c_caver_password2">
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=424<convert><!--Nouveau mot de passe-->
          </label>
        </td><td class="field">
          <input class="input1" type="password" id="c_caver_new_password" name="c_caver_new_password" value="" size="10" maxlength="32" onkeyup="JavaScript:checkPwd(this, 'pwd_pic');" /><!--onKeyUp="JavaScript:checkPassword(this);"/-->
          <img class="status1" name="pwd_pic" id="pwd_pic" src="../images/icons/wrong.png" alt="image" />
        </td></tr><tr><td width="170" class="label">
          <label for="c_caver_password2">
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=198<convert><!--Confirmez votre mot de passe-->
          </label>
        </td><td class="field">
          <input class="input1" type="password" id="c_caver_new_password2" name="c_caver_new_password2" value="" size="10" maxlength="32" onkeyup="JavaScript:checkPwd2(this, xtdGetElementById('c_caver_new_password'), 'pwd2_pic');" /><!--onKeyUp="JavaScript:checkPassword2(this, xtdGetElementById('c_caver_new_password'));"/-->
          <img class="status1" name="pwd2_pic" id="pwd2_pic" src="../images/icons/wrong.png" alt="image" />
        </td></tr><tr><td width="170" class="label">
          <label for="save_pwd">
          </label>
        </td><td class="field">
          <input type="hidden" id="c_key" name="c_key" value="<?php echo $tmp_key; ?>" />
          <input type="hidden" id="c_caver_login" name="c_caver_login" value="<?php echo $_SESSION['user_login']; ?>" />
          <input class="button1" type="submit" id="save_pwd" name="save_pwd" value="<convert>#label=76<convert>" /><!--Valider-->
        </td></tr><tr><td width="170" class="label">
          <label for="cancel_pwd">
          </label>
        </td><td class="field">
          <input class="button1" type="button" id="cancel_pwd" name="cancel_pwd" onclick="JavaScript:backToMenu();" value="<convert>#label=77<convert>" /><!--Annuler-->
        </td></tr><tr><td colspan="2">
          <div class="notes">
            <?php echo getTopBubble(); ?>
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=78<convert><!--Champs nÃ©cessaires.--><br />
            <convert>#label=215<convert><!--Votre mot de passe doit Ãªtre composÃ© d'au moins 8 caractÃ¨res sauf--> <b>|</b> <b>/</b> <b>\</b> <b>"</b> <b>#</b> <b>&amp;</b> <b>+</b> <b>Â¨</b> <convert>#label=216<convert><!--et les espaces.-->
            <?php echo getBotBubble(); ?>
          </div>
        </td></tr>
      </table>
    </form>
<?php
			break;
    	case "entry":
        if (!allowAccess(caver_edit_himself)) {
          exit();
        }
?>
    entryBeforeLoad();
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:entryOnLoad();">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
		<?php echo getCloseBtn("JavaScript:backToMenu();","<convert>#label=371<convert>"); ?><!-- Fermer -->
		<div class="frame_title"><?php echo setTitle("parameters_".$_SESSION['language'].".php?type=entry", "filter", "<convert>#label=425<convert>", 3); ?></div><!--Mes cavitÃ©s-->
<?php
          if ($entry_saved) {
?>
    <div class="warning"><?php echo getTopBubble(); ?><convert>#label=426<convert><!--Votre liste a Ã©tÃ© sauvegardÃ©e avec succÃ¨s !--><?php echo getBotBubble(); ?></div>
<?php
          }
?>
  	<form id="entries_user" name="entries_user" method="post" action="">
      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><th colspan="2"><?php echo getHelpTopic($helpId[$type], "<convert>#label=23<convert>"); ?></th></tr>
			  <tr><td class="label" colspan="2" style="text-align:left;">
          <label for="e_myList" style="text-align:left;">
            <b><convert>#label=427<convert><!--Les cavitÃ©s que j'ai visitÃ©es--> :</b>
          </label>
        </td></tr><tr><td class="field" colspan="2">
          <select style="width:270px;" name="e_myList" id="e_myList" size="10" multiple="multiple" onclick="JavaScript:selectOnClick(event, this);" ondblclick="JavaScript:entryRemove();">
<?php
          $sql = "SELECT ey.Id AS value, ey.Name AS text "; 
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_entry` ey ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_entry_caver` ec ON ec.Id_entry = ey.Id ";
          $sql .= "WHERE ec.Id_caver = ".$_SESSION['user_id']." ";
          $sql .= "ORDER BY text";
          $msg = "";
          $comparedCol = "value";
          $selected = "#";
          $textCol = "text";
          echo getOptions($sql, $msg, $selected, $comparedCol, $textCol);
?>
          </select>
        </td></tr><tr><td class="field" colspan="2">
          <input type="button" class="button1" id="add" name="add" value="<convert>#label=429<convert>" onclick="JavaScript:entryAdd();" /><!--Ajouter Ã  ma liste...-->
        </td></tr><tr><td class="field" colspan="2">
          <input type="button" class="button1" id="remove" name="remove" value="<convert>#label=428<convert>" onclick="JavaScript:entryRemove();" /><!--Retirer de ma liste-->
        </td></tr><tr><td class="field" colspan="2">
          <input type="hidden" id="e_list" name="e_list" />
          <input type="submit" class="button1" id="save_list_entries" name="save_list_entries" value="Enregistrer" onclick="JavaScript:entrySubmit();" />
        </td></tr><tr><td class="field" colspan="2">  
          <input type="button" class="button1" id="cancel" name="cancel" onclick="JavaScript:backToMenu();" value="<convert>#label=77<convert>" /><!--Annuler-->
        </td></tr>
<?php //if ($_SESSION['home_page'] == "overview") { ?>
        <tr><td colspan="2">
          <div class="notes">
            <?php echo getTopBubble(); ?>
            <convert>#label=95<convert><!--Si l'entrÃ©e que vous cherchez n'est pas dans--> <?php echo $_SESSION['Application_name']; ?>, <a href="JavaScript:entryNew();"><convert>#label=96<convert><!--crÃ©ez la--></a> !
            <?php echo getBotBubble(); ?>
          </div>
        </td></tr>
<?php //} ?>
      </table>
    </form>
<?php
			break;
			case "grotto":
        if (!allowAccess(caver_edit_himself)) {
          exit();
        }
?>
    grottoBeforeLoad();
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:grottoOnLoad();">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
		<?php echo getCloseBtn("JavaScript:backToMenu();","<convert>#label=371<convert>"); ?><!-- Fermer -->
		<div class="frame_title"><?php echo setTitle("parameters_".$_SESSION['language'].".php?type=grotto", "filter", "<convert>#label=431<convert>", 3); ?></div><!--Mes clubs-->
<?php
          if ($grotto_saved) {
?>
    <div class="warning"><?php echo getTopBubble(); ?><convert>#label=426<convert><!--Votre liste a Ã©tÃ© sauvegardÃ©e avec succÃ¨s !--><?php echo getBotBubble(); ?></div>
<?php
          }
?>
  	<form id="grottos_user" name="grottos_user" method="post" action="">
      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><th colspan="2"><?php echo getHelpTopic($helpId[$type], "<convert>#label=23<convert>"); ?></th></tr>
			  <tr><td class="label" colspan="2" style="text-align:left;">
          <label for="g_myList">
            <b><convert>#label=432<convert><!--Les clubs auquels je suis inscrit--> :</b>
          </label>
        </td></tr><tr><td class="field" colspan="2">
          <select style="width:270px;" name="g_myList" id="g_myList" size="10" multiple="multiple" onclick="JavaScript:selectOnClick(event, this);" ondblclick="JavaScript:grottoRemove();">
<?php
          $sql = "SELECT go.Id AS value, go.Name AS text "; 
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_grotto` go ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_grotto_caver` gc ON gc.Id_grotto = go.Id ";
          $sql .= "WHERE gc.Id_caver = ".$_SESSION['user_id']." ";
          $sql .= "ORDER BY text";
          $msg = "";
          $comparedCol = "value";
          $selected = "#";
          $textCol = "text";
          echo getOptions($sql, $msg, $selected, $comparedCol, $textCol);
?>
          </select>
        </td></tr><tr><td class="field" colspan="2">
          <input type="button" class="button1" id="add" name="add" value="<convert>#label=429<convert>" onclick="JavaScript:grottoAdd();" />
        </td></tr><tr><td class="field" colspan="2">
          <input type="button" class="button1" id="remove" name="remove" value="<convert>#label=428<convert>" onclick="JavaScript:grottoRemove();" />
        </td></tr><tr><td class="field" colspan="2">
          <input type="hidden" id="g_list" name="g_list" />
          <input type="submit" class="button1" id="save_list_grottos" name="save_list_grottos" value="Enregistrer" onclick="JavaScript:grottoSubmit();" />
        </td></tr><tr><td class="field" colspan="2">
          <input type="button" class="button1" id="cancel" name="cancel" onclick="JavaScript:backToMenu();" value="<convert>#label=77<convert>" /><!--Annuler-->
        </td></tr>
<?php //if ($_SESSION['home_page'] == "overview") { ?>
        <tr><td colspan="2">
          <div class="notes">
            <?php echo getTopBubble(); ?>
            <convert>#label=434<convert><!--Si le club que vous cherchez n'est pas dans--> <?php echo $_SESSION['Application_name']; ?>, <a href="JavaScript:grottoNew();"><convert>#label=435<convert><!--crÃ©ez le--></a> !
            <?php echo getBotBubble(); ?>
          </div>
        </td></tr>
<?php //} ?>
      </table>
    </form>
<?php
			break;
			case "delete":
        if (!allowAccess(caver_delete_himself)) {
          exit();
        }
        if (!isset($_POST['delete_user']) || $delete_failed) {
?>
    deleteBeforeLoad(true);
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:deleteOnLoad(true);">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
		<?php echo getCloseBtn("JavaScript:backToMenu();","<convert>#label=371<convert>"); ?><!-- Fermer -->
		<div class="frame_title"><?php echo setTitle("parameters_".$_SESSION['language'].".php?type=delete", "filter", "<convert>#label=436<convert>", 3); ?></div><!--Supprimer mon compte-->
<?php
          if ($delete_failed){
?>
    <div class="error"><?php echo getTopBubble(); ?><convert>#label=437<convert><!--Erreur de mot de passe ou d'identifiant, veuillez recommencer !--><?php echo getBotBubble(); ?></div>
<?php
          }
?>
    <div class="warning" style="height: 5.4em;"><?php echo getTopBubble(); ?>
      <convert>#label=438<convert><!--ATTENTION ! ApprÃ¨s avoir cliquÃ© sur le bouton "Supprimer mon compte", toutes les donnÃ©es relatives Ã  votre profil seront dÃ©finitivement perdues !-->
    <?php echo getBotBubble(); ?></div>
  	<form id="del_user" name="del_user" method="post" action="">
      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><th colspan="2"><?php echo getHelpTopic($helpId[$type], "<convert>#label=23<convert>"); ?></th></tr>
        <tr><td width="170" class="label">
          <label for="d_caver_login">
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=192<convert><!--Identifiant--> :
          </label>
        </td><td class="field">
          <input class="input1" type="text" id="d_caver_login" name="d_caver_login" value="" size="10" maxlength="20" />
        </td></tr><tr><td width="170" class="label">
          <label for="d_caver_password">
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=193<convert><!--Mot de passe--> :
          </label>
        </td><td class="field">
          <input class="input1" type="password" id="d_caver_password" name="d_caver_password" value="" size="10" maxlength="32" />
        </td></tr><tr><td width="170" class="label">
          <label for="delete_user">
          </label>
        </td><td class="field">
          <input type="hidden" id="d_key" name="d_key" value="<?php echo $tmp_key; ?>" />
          <input class="button1" onclick="JavaScript:deleteSubmit(event);" type="submit" id="delete_user" name="delete_user" value="<convert>#label=439<convert>" />
        </td></tr><tr><td width="170" class="label">
          <label for="delete_cancel">
          </label>
        </td><td class="field">
          <input class="button1" onclick="JavaScript:backToMenu();" type="button" id="delete_cancel" name="delete_cancel" value="<convert>#label=77<convert>" /><!--Annuler-->
        </td></tr><tr><td colspan="2">
          <div class="notes">
            <?php echo getTopBubble(); ?>
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=78<convert><!--Champs nÃ©cessaires.-->
            <?php echo getBotBubble(); ?>            
          </div>
        </td></tr>
      </table>
    </form>
<?php
        } else {
          if (!$delete_failed) {
?>
    deleteBeforeLoad(false);
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:deleteOnLoad(false);">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
<?php
          }
        }
			break;
    	default:
?>
    defaultBeforeLoad();
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:defaultOnLoad();">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
		<?php echo getCloseBtn("JavaScript:backToMenu();","<convert>#label=371<convert>"); ?><!-- Fermer -->
		<div class="frame_title"><?php echo setTitle("parameters_".$_SESSION['language'].".php?type=", "filter", "<convert>#label=80<convert>", 2); ?></div><!--Erreur-->
		<div class="error"><?php echo getTopBubble(); ?><convert>#label=81<convert><!--Cas non traitÃ© !--><?php echo getBotBubble(); ?></div>
<?php
    	break;
    }
?>
    <?php echo getBotFrame(); ?>
<?php
    $virtual_page = "parameters/".$type."/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>