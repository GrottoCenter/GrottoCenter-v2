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
if (!allowAccess(grotto_view_all)){
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
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=127<convert></title>
    <link rel="stylesheet" type="text/css" href="../css/filter.css" />
    <link rel="stylesheet" type="text/css" href="../css/portlet.css" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
<?php
  	//Capture the action type :
    $type = (isset($_GET['type'])) ? $_GET['type'] : '';
    $parameters = "";
    $locked = false;
    $helpId = array("logo" => 7, "edit" => 8);

    if (isset($_GET['back'])) {
    	$backPage = (isset($_GET['back'])) ? $_GET['back'] : '';
    } else {
      $backPage = "grotto";
    }

    if (allowAccess(grotto_delete_all)) {
      //Delete the element
      if (isset($_POST['delete'])){
        $did = (isset($_POST['delete_id'])) ? $_POST['delete_id'] : '';
        if ($did != "") {
          trackAction("delete_grotto",$did,"T_grotto");
          $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`T_grotto` WHERE Id = ".$did;
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_grotto_caver` WHERE Id_grotto = ".$did;
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_grotto_entry` WHERE Id_grotto = ".$did;
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $delete_failed = false;
        } else {
          $delete_failed = true;
        }
        $type = "menu";
      }

      //Open Deleting window
      if ($type == "delete") {
        $did = (isset($_GET['did'])) ? $_GET['did'] : '';
        if (takeOver("grotto",$did) && $did != "") {
          $sql = "SELECT CONCAT(Name, ' (',CONCAT_WS(', ',Country, Region),')') AS Name FROM T_grotto WHERE Id = ".$did;
          $name = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
          $name = $name[0]['Name'];
          $parameters = "&cancel=True&cid=".$did."&ccat=grotto";
        } else {
          $locked = true;
          $type = "menu";
        }
      }
    }

    if (allowAccess(grotto_edit_all)) {
      //Save the new user's parameters :
      if ($type == "edit") {
        if (isset($_POST['save'])){
          $save_failed = true;
          $name = (isset($_POST['n_grotto_name'])) ? $_POST['n_grotto_name'] : '';
          $country = (isset($_POST['n_grotto_country'])) ? $_POST['n_grotto_country'] : '';
          $region = (isset($_POST['n_grotto_region'])) ? $_POST['n_grotto_region'] : '';
          $city = (isset($_POST['n_grotto_city'])) ? $_POST['n_grotto_city'] : '';
          $postal = (isset($_POST['n_grotto_postal'])) ? $_POST['n_grotto_postal'] : '';
          $address = (isset($_POST['n_grotto_address'])) ? $_POST['n_grotto_address'] : '';
          $contact = (isset($_POST['n_grotto_contact'])) ? $_POST['n_grotto_contact'] : '';
          $birth = (isset($_POST['n_grotto_birth'])) ? $_POST['n_grotto_birth'] : '';
          $president = (isset($_POST['n_grotto_president'])) ? $_POST['n_grotto_president'] : '';
          $vice_pres = (isset($_POST['n_grotto_vice_pres'])) ? $_POST['n_grotto_vice_pres'] : '';
          $treasurer = (isset($_POST['n_grotto_treasurer'])) ? $_POST['n_grotto_treasurer'] : '';
          $secretary = (isset($_POST['n_grotto_secretary'])) ? $_POST['n_grotto_secretary'] : '';
          $latitude = (isset($_POST['n_grotto_latitude'])) ? $_POST['n_grotto_latitude'] : '';
          $longitude = (isset($_POST['n_grotto_longitude'])) ? $_POST['n_grotto_longitude'] : '';
          $custom_message = (isset($_POST['n_grotto_message'])) ? $_POST['n_grotto_message'] : '';
          $list = (isset($_POST['e_list'])) ? $_POST['e_list'] : '';
          $isNew = (isset($_POST['is_new'])) ? $_POST['is_new'] : '';
          $id = (isset($_POST['grotto_id'])) ? $_POST['grotto_id'] : '';
          if ($isNew == "True") {
            $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`T_grotto` ";
            $sql .= "(`Id_author`, `Name`, `Country`, `Region`, `City`, `Postal_code`, `Address`, `Contact`, `Year_birth`, `Date_inscription`, `Id_president`, `Id_vice_president`, `Id_treasurer`, `Id_secretary`, `Latitude`, `Longitude`, `Custom_message`)";
            $sql .= " VALUES (";
            $sql .= $_SESSION['user_id'].", ";
            $sql .= returnDefault($name, 'text').", ";
            $sql .= returnDefault($country, 'list').", ";
            $sql .= returnDefault($region, 'text').", ";
            $sql .= returnDefault($city, 'text').", ";
            $sql .= returnDefault($postal, 'text').", ";
            $sql .= returnDefault($address, 'text').", ";
            $sql .= returnDefault($contact, 'text').", ";
            $sql .= returnDefault($birth, 'text').", ";
            $sql .= "Now(), ";
            $sql .= returnDefault($president, 'id').", ";
            $sql .= returnDefault($vice_pres, 'id').", ";
            $sql .= returnDefault($treasurer, 'id').", ";
            $sql .= returnDefault($secretary, 'id').", ";
            $sql .= returnDefault($latitude, 'latlng').", ";
            $sql .= returnDefault($longitude, 'latlng').", ";
            $sql .= returnDefault($custom_message, 'text').") ";
            $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
            $nid = $req['mysql_insert_id'];
            $status = "insert_grotto";
          } else {
            $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_grotto` ";
            $sql .= " SET ";
            $sql .= "Locked = 'NO', ";
            $sql .= "Id_reviewer = ".$_SESSION['user_id'].", ";
            $sql .= "Name = ".returnDefault($name, 'text').", ";
            $sql .= "Country = ".returnDefault($country, 'list').", ";
            $sql .= "Region = ".returnDefault($region, 'text').", ";
            $sql .= "City = ".returnDefault($city, 'text').", ";
            $sql .= "Postal_code = ".returnDefault($postal, 'text').", ";
            $sql .= "Address = ".returnDefault($address, 'text').", ";
            $sql .= "Contact = ".returnDefault($contact, 'text').", ";
            $sql .= "Year_birth = ".returnDefault($birth, 'text').", ";
            $sql .= "Date_reviewed = Now(), ";
            $sql .= "Id_president = ".returnDefault($president, 'id').", ";
            $sql .= "Id_vice_president = ".returnDefault($vice_pres, 'id').", ";
            $sql .= "Id_treasurer = ".returnDefault($treasurer, 'id').", ";
            $sql .= "Id_secretary = ".returnDefault($secretary, 'id').", ";
            $sql .= "Latitude = ".returnDefault($latitude, 'latlng').", ";
            $sql .= "Longitude = ".returnDefault($longitude, 'latlng').", ";
            $sql .= "Custom_message = ".returnDefault($custom_message, 'text')." ";
            $sql .= "WHERE Id = ".$id;
            $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
            $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_grotto_entry` ";
          	$sql .= "WHERE `Id_grotto` = ".$id;
            $status = "edit_grotto";
            $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
            $nid = $id;
          }
          trackAction($status,$nid,"T_grotto");
          if ($list != "") {
          	if ($isNew == "True") {
          		$onid = $nid;
          	} else {
          		$onid = $id;
          	}
            $arrList = split('[|]+', $list);
            $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`J_grotto_entry` (`Id_grotto`, `Id_entry`) VALUES ";
            foreach($arrList as $value) {
              $sql .= "(".$onid.", ".$value."), ";
            }
            $sql = substr($sql,0,strlen($sql)-2);
            $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          }
          $save_failed = false;
          $type = "menu";
        } else {
          if (isset($_GET['id'])) {
            $id = (isset($_GET['id'])) ? $_GET['id'] : '';
            if (takeOver("grotto",$id) && $id != "") {
              $sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`T_grotto` WHERE Id = ".$id;
              $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
              $name = $data[0]['Name'];
              $country = $data[0]['Country'];
              $region = $data[0]['Region'];
              $city = $data[0]['City'];
              $postal = $data[0]['Postal_code'];
              $address = $data[0]['Address'];
              $contact = $data[0]['Contact'];
              $birth = $data[0]['Year_birth'];
              $president = $data[0]['Id_president'];
              $vice_pres = $data[0]['Id_vice_president'];
              $treasurer = $data[0]['Id_treasurer'];
              $secretary = $data[0]['Id_secretary'];
              $latitude = $data[0]['Latitude'];
              $longitude = $data[0]['Longitude'];
              $custom_message = $data[0]['Custom_message'];
              $isNew = "False";
              $parameters = "&cancel=True&cid=".$id."&ccat=grotto";
            } else {
              $locked = true;
              $type = "menu";
            }
          } else {
            $isNew = "True";
            if(isset($_GET['nlat']) && isset($_GET['nlng'])) {
              $latitude = (isset($_GET['nlat'])) ? $_GET['nlat'] : '';
              $longitude = (isset($_GET['nlng'])) ? $_GET['nlng'] : '';
              $showMe = "True";
            } else {
              $showMe = "False";
            }
          }
        }
      }

  	  if ($type == "logo") {
        $id = (isset($_GET['id'])) ? $_GET['id'] : '';
        if (takeOver("grotto",$id) && $id != "") {
      	  if (isset($_GET['logo_changed']) && $_GET['logo_changed'] == "true") {
            $logo_file = (isset($_GET['logo_name'])) ? $_GET['logo_name'] : '';
            $logo_file = urldecode($logo_file);
            $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_grotto` SET ";
            $sql .= "Picture_file_name = ".returnDefault($logo_file,'text').", ";
            $sql .= "Id_reviewer = ".$_SESSION['user_id'].", ";
            $sql .= "Date_reviewed = Now() ";
            $sql .= "WHERE Id = ".$id;
            $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          }
          $sql = "SELECT Name, Picture_file_name FROM `".$_SESSION['Application_host']."`.`T_grotto` WHERE Id = ".$id;
          $result = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
          $logo_file = $result[0]['Picture_file_name'];
          $grotto_name = $result[0]['Name'];
          $parameters = "&cancel=True&cid=".$id."&ccat=grotto";
        } else {
          $locked = true;
          $type = "menu";
        }
      }
    }
?>
    <script type="text/javascript" src="../scripts/classeGCTest.js"></script>
<?php
if (false) {
?>
    <script type="text/javascript" src="../scripts/geoNames.js"></script>
    <script type="text/javascript" src="http://ws.geonames.org/export/geonamesData.js"></script>
    <script type="text/javascript" src="http://ws.geonames.org/export/jsr_class.js"></script>
<?php
}
?>
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
    var doCancel = true;
    var namesArray = new Array();
<?php include("../scripts/events.js"); ?>

<?php
switch ($type) {
	case "menu":
?>
    function menuBeforeLoad() {
      parent.setFilterSize(400,"px");
    }

    function menuOnLoad() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      var oHtml = document.getElementsByTagName('HTML')[0];
      parent.setFilterSizeTight(oHtml);
      <?php if (isset($_POST['delete'])) { ?> reload(false); <?php } ?>
    }

    function grottoEdit(oForm) {
      var oRadio = oForm.radio_list;
      var grottoId = getRadioValue(oRadio);
      if (grottoId) {
        openMe(grottoId, "grotto", false);
        self.location.href = "grotto_<?php echo $_SESSION['language']; ?>.php?type=edit&id=" + grottoId;
      }
    }

    function grottoLogo(oForm) {
      var oRadio = oForm.radio_list;
      var grottoId = getRadioValue(oRadio);
      if (grottoId) {
        openMe(grottoId, "grotto", false);
        self.location.href = "grotto_<?php echo $_SESSION['language']; ?>.php?type=logo&id=" + grottoId;
      }
    }

    function grottoRefresh(oForm) {
      //self.location.href = "grotto_<?php echo $_SESSION['language']; ?>.php?type=menu";
      oForm.submit();
    }

    function grottoDelete(oForm) {
      var oRadioArray = oForm.radio_list;
      var grottoId = getRadioValue(oRadioArray);
      deleteMarker("grotto", grottoId, "<?php echo $_SESSION['language']; ?>");
    }

    function grottoOnClick(e, id) {
      var category = "grotto";
      openMe(id, category, false);
      detailMarker(e, category, id, '<?php echo $_SESSION['language']; ?>', false);
    }
<?php
	break;
	case "delete":
?>
    function deleteOnLoad() {
      var oForm = document.delete_grotto;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      parent.setFilterSize(getMaxWidthInput(oForm),"px");
      var oHtml = document.getElementsByTagName('HTML')[0];
      parent.setFilterSizeTight(oHtml);
    }
<?php
	break;
	case "logo":
?>
    function logoOnLoad() {

    }

    function logoBeforeLoad() {

    }
<?php
	break;
	case "edit":
?>
    function newBeforeLoad(hasFailed) {
      if (hasFailed) {
        parent.setFilterSize(45);
        parent.overview.hideId("reload");
      } else {
        parent.setFilterSize(25);
      }
    }

    function newOnLoad(hasFailed) {
      var oForm = document.new_grotto;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      mySite.details.switchDetails(true);
      if (hasFailed) {
        parent.setFilterSize(getMaxWidthInput(oForm),"px");
        var oHtml = document.getElementsByTagName('HTML')[0];
        parent.setFilterSizeTight(oHtml);
        parent.overview.hideId('n_reload');
        <?php if ($isNew == "False") { ?>
        freeMe(<?php echo $id; ?>, "grotto");
        <?php } else { if ($showMe == "True") { ?>
        newShowMe(true);
        <?php } } ?>
        namesArray = loadNames("grotto");
        checkThisName(oForm.n_grotto_name, 'name_pic')
        checkMail(oForm.n_grotto_contact, "mail_pic");
      } else {
        reload(false);
      }
    }

    function selectOnClick(e, oSelect) {
      var Id = oSelect.options[oSelect.selectedIndex].value;
      document.body.focus();
      Category = "entry";
      openMe(Id, Category, false);
      detailMarker(e, Category, Id, '<?php echo $_SESSION['language']; ?>', false);
  	}

  	function entryRemove() {
      var oForm = document.new_grotto;
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
      windowName = "<convert>#label=612<convert>";//Choisissez une ou plusieurs entrées à ajouter
      //url = "<?php echo $_SESSION['Application_url']; ?>/html/pick_window_<?php echo $_SESSION['language']; ?>.php?type=entry&callback=addEntry";
      url = "pick_window_<?php echo $_SESSION['language']; ?>.php?type=entry&callback=addEntry";
      openWindow(url, windowName, 690, 520);
  	}

  	function addEntry(oForm) {
      var uForm = document.new_grotto;
      addOptionsFromSelection(oForm, uForm.e_myList);
    }

    /*function cityOnFocus(cityField, suggestBoxId) {
      var oForm = document.new_grotto;
      // Uses the geoNames library
      postalCodeLookup(suggestBoxId, oForm.n_grotto_postal, oForm.n_grotto_country, cityField);
      // Uses the geoNames library
    }*/

    function newSubmit(event) {
      var oForm = document.new_grotto;
      var rightSource = toAbsURL(rightPic);
      var oField = xtdGetElementById('name_pic');
      var sMessage = "<convert>#label=135<convert> <convert>#label=873<convert> | / \\ ' \" & + <convert>#label=46<convert> ¨";//Le nom du club //doit être composé de 2 à 36 caractères sauf //et
      createTest(oField.name, oField.src, rightSource, "==", sMessage, true);
      if (!testForm()) {
        stopSubmit(event);
      } else {
        <?php if ($isNew == "False") { ?>
        var marker = parent.overview.getMarker(<?php echo $id; ?>, "grotto");
        <?php } else { ?>
        var marker = parent.overview.marker_user;
        <?php } ?>
        doChallengeCoordinates(oForm.n_grotto_latitude, oForm.n_grotto_longitude, marker);
      	doChallengeList(oForm.e_myList,oForm.e_list);
        doCancel = false;
      }
    }

    var address_level;

    function newShowMe(byCoords) {
      address_level = 4; //Address = 4, City = 3, Region = 2, Country = 1
      var oForm = document.new_grotto;
      <?php if ($isNew == "True") { ?>
      if (byCoords) {
        showMarker(new mySite.overview.google.maps.LatLng(strToFloat(oForm.n_grotto_latitude.value), strToFloat(oForm.n_grotto_longitude.value)));
      } else {
        getCoordsByDirection(getAddress('n_', 'grotto', address_level), showMarker2);
      }
      <?php } else { ?>
      if (byCoords) {
        moveMarker(new mySite.overview.google.maps.LatLng(strToFloat(oForm.n_grotto_latitude.value), strToFloat(oForm.n_grotto_longitude.value)));
      } else {
        getCoordsByDirection(getAddress('n_', 'grotto', address_level), moveMarker2);
      }
      <?php } ?>
    }

    function showMarker(gLatLng) {
      if (gLatLng) {
        showMe(gLatLng, "grotto", false);
        setLocations(gLatLng.lat(), gLatLng.lng());
      } else {
        if (address_level > 0) {
          address_level --;
          getCoordsByDirection(getAddress('n_', 'grotto', address_level), showMarker2);
        }
      }
    }

    function showMarker2(geocoderResult) {
        if (geocoderResult[0]) {
            showMarker(geocoderResult[0].geometry.location);
        }
    }

    function moveMarker(gLatLng) {
      if (gLatLng) {
        moveMarkerTo('<?php echo $id; ?>', 'grotto', gLatLng.lat(), gLatLng.lng());
        openMe('<?php echo $id; ?>', "grotto", false); //, gLatLng);
        setLocations(gLatLng.lat(), gLatLng.lng());
      } else {
        if (address_level > 0) {
          address_level --;
          getCoordsByDirection(getAddress('n_', 'grotto', address_level), moveMarker2);
        }
      }
    }

    function moveMarker2(geocoderResult) {
        if (geocoderResult[0]) {
            moveMarker(geocoderResult[0].geometry.location);
        }
    }

    function recieveLocation(lat, lng) {
      setLocations(lat, lng);
      newShowMe(true);
    }

    function setLocations(lat, lng) {
      var oForm = document.new_grotto;
      oForm.n_grotto_latitude.value = lat;
      oForm.n_grotto_longitude.value = lng;
    }

    function checkThisName(oObject, namePic) {
      checkName(oObject, namePic, "grotto", "<?php echo $name; ?>", namesArray, false); //put the last parameter to false
      displayCloseNames(oObject, getMatchingValues(oObject.value, namesArray, "<?php echo $name; ?>"), '<convert>#label=844<convert>'); //Noms existants déjà en base :
    }

    function newOnBeforeUnload(event) {
      if (doCancel) {
        var msg = "<convert>#label=92<convert>";//Les modifications seront perdues !
        stopUnload(event, msg);
      }
    }

    function newOnUnload(hasFailed) {
      parent.overview.showId("reload");
      mySite.details.switchDetails(false);
      <?php if ($isNew == "False") { ?>
      blockMe(<?php echo $id; ?>, "grotto");
      <?php } else { ?>
      if (hasFailed) {
        parent.overview.removeAddress();
        parent.overview.showId('n_reload');
      }
      <?php } ?>
      if (hasFailed) {
        reload(false);
      }
    }
<?php
      break;
    	default:
?>
    function defaultOnload() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
    }
<?php
      break;
    }
?>
    function grottoNew() {
      self.location.href = "grotto_<?php echo $_SESSION['language']; ?>.php?type=edit";
    }

    function newCancel() {
      doCancel = false;
      self.location.href = "<?php echo $backPage; ?>_<?php echo $_SESSION['language']; ?>.php?type=menu<?php echo $parameters; ?>";
    }
<?php
    switch ($type)
    {
    	case "menu":
?>
    menuBeforeLoad(true);
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:menuOnLoad();">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
		<?php echo getCloseBtn("filter_".$_SESSION['language'].".php","<convert>#label=371<convert>"); ?>
		<div class="frame_title"><?php echo setTitle("grotto_".$_SESSION['language'].".php?type=menu", "filter", "<convert>#label=136<convert>", 2); ?></div><!--Menu des clubs-->
<?php
      if ($locked) {
?>
  	<div class="error"><?php echo getTopBubble(); ?><convert>#label=137<convert><!--Ce club--> <convert>#label=138<convert><!--est en cours de modification par un autre utilisateur, veuillez essayer plus tard !--><?php echo getBotBubble(); ?></div>
<?php
      } else {
        if (isset($_POST['save']) && !$save_failed){
?>
    <div class="warning"><?php echo getTopBubble(); ?><convert>#label=128<convert><!--Le club--> <convert>#label=139<convert><!--a été enregistré avec succès !--><?php echo getBotBubble(); ?></div>
<?php
        }
        if (isset($_POST['delete'])) {
          if ($delete_failed){
?>
  	<div class="error"><?php echo getTopBubble(); ?><convert>#label=840<convert><!--Vous n'êtes pas autorisé à supprimer--> <convert>#label=137<convert><!--ce club--><?php echo getBotBubble(); ?></div>
<?php
          } else {
?>
    <div class="warning"><?php echo getTopBubble(); ?><convert>#label=128<convert> <convert>#label=609<convert><!--Le club a été supprimé avec succès !--><?php echo getBotBubble(); ?></div>
<?php
          }
        }
      }
?>

<?php
    $sql = "SELECT DISTINCT ";
    $sql .= "T_grotto.Id AS `0`, ";
    $sql .= "IF((T_grotto.Locked = 'YES' AND NOT T_grotto.Id_locker = '".$_SESSION['user_id']."'),1,0) AS `1`, ";
    $sql .= "CONCAT_WS('',CONCAT_WS(' /!\\\\ ',T_grotto.Name,IF(T_grotto.Locked = 'YES','<convert>#label=42<convert>',NULL)),'[|]',IF((T_grotto.Locked = 'YES' AND T_grotto.Id_locker = '".$_SESSION['user_id']."'),'".str_replace("'","''","<convert>#label=575<convert>")."',NULL),'[|]',CONCAT_WS(', ',T_grotto.City,T_grotto.Region,T_country.".$_SESSION['language']."_name)) AS `2`, ";//Nom du club //Commune //Etat/Région, //Pays
    $sql .= "T_grotto.Name AS `3`, ";//Nom du club
    $sql .= "T_country.".$_SESSION['language']."_name AS `4`, ";//Pays
    $sql .= "T_grotto.Region AS `5`, ";//Etat/Région
    $sql .= "T_grotto.City AS `6`, ";//Commune
    $sql .= "IF((T_grotto.Locked = 'YES' AND T_grotto.Id_locker = '".$_SESSION['user_id']."'),1,0) AS `7` ";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_grotto` ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_country` ON T_country.Iso = T_grotto.Country ";
    $columns_params = array(
			0 => "[hidden]|[hidden]Id",
			1 => "[hidden]|[hidden]Locked",
			2 => "[hidden]|<convert>#label=144<convert><br /><convert>#label=101<convert>, <convert>#label=100<convert>, <convert>#label=98<convert>",
			3 => "T_grotto*Name|[hidden]<convert>#label=144<convert>",
			4 => "T_grotto*Country|[hidden]<convert>#label=98<convert>|SELECT Iso AS value,".$_SESSION['language']."_name AS text FROM T_country ORDER BY text",
			5 => "T_grotto*Region|[hidden]<convert>#label=100<convert>",
			6 => "T_grotto*City|[hidden]<convert>#label=101<convert>",
			7 => "[hidden]|[hidden]7"
		);
    //$param_link = "entry_".$_SESSION['language'].".php?type=edit&id=<Id>";
    $param_link = "JavaScript:grottoOnClick(event,'<Id>');";
    $links = array (
            2 => array(
                'conditions' =>  array(
                                1 => '0'),
                'parameters' => array(
                                '<Id>' => 0),
                'link' => $param_link,
                'target' => 'onclick'));
    $input_type = array(
                'type' => 'radio',
                'conditions' => array(
                    1 => '0'));
    $style = array(
            2 => array(
                'tag' => 'div',
                'class' => 'plt_warning',
                'conditions' => array(
                    7 => '1')));
    $default_order = 3;
?>
<?php
    $length_page = 10;
    $records_by_page_array = array(5, 10, 15, 20, 25, 30, 40, 50);
    $records_by_page = (isset($_POST['records_by_page'])) ? $_POST['records_by_page'] : 10;
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
    if ($total_count > 0) {
      $navigator = getPageNavigator($length_page, $current_page, $count_page, $filter_form);
    } else {
      $navigator = "";
    }
?>
    <div>
      <form id="<?php echo $filter_form; ?>" name="<?php echo $filter_form; ?>" method="post" action="grotto_<?php echo $_SESSION['language']; ?>.php?type=menu">
        <table border="0" cellspacing="0" cellpadding="0" id="filter_set">
          <tr><td colspan="2"><convert>#label=601<convert><!--Pour rechercher une partie d'un mot utiliser le caractère *, ex: *erre pourrais retourner Pierre ou Terre etc.--></td></tr>
          <?php echo $filter_fields; ?>
        </table>
        <input type="hidden" id="current" name="current" value="" />
        <input type="hidden" id="order" name="order" value="<?php echo $order; ?>" />
        <input type="hidden" id="by" name="by" value="<?php echo $by; ?>" />
        <input type="submit" id="submit_filter" name="submit_filter" class="button1" value="<convert>#label=602<convert>" /><!--Filtrer-->
        <input type="submit" id="reset_filter" name="reset_filter" class="button1" value="<convert>#label=603<convert>" onclick="JavaScript:resetForm(this.form);" /><!--Tout afficher-->
        <input type="button" id=""reset name="reset" class="button1" value="<convert>#label=604<convert>" onclick="JavaScript:resetForm(this.form);" /><!--Effacer-->
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
        <div class="notes">
          <?php echo getTopBubble(); ?>
          <convert>#label=142<convert><!--Si le club que vous cherchez n'est pas dans--> <?php echo $_SESSION['Application_name']; ?>, <a href="JavaScript:grottoNew();"><convert>#label=143<convert><!--créez le--></a> !
          <?php echo getBotBubble(); ?>
        </div>
<?php
    if (allowAccess(grotto_edit_all)) {
?>
        <input type="button" class="button1" id="edit_grotto" name="edit_grotto" value="<convert>#label=53<convert>" onclick="JavaScript:grottoEdit(this.form);" /><!--Modifier--><br />
        <input type="button" class="button1" id="logo_grotto" name="logo_grotto" value="<convert>#label=683<convert>" onclick="JavaScript:grottoLogo(this.form);" /><!--Logo--><span class="new_feature"><convert>#label=537<convert><!--Nouveau !--></span><br />
        <input type="button" class="button1" id="new_grotto" name="new_grotto" value="<convert>#label=54<convert>" onclick="JavaScript:grottoNew();" /><!--Nouveau--><br />
<?php
    }
    if (allowAccess(grotto_delete_all)) {
?>
        <input type="button" class="button1" id="del_grotto" name="del_grotto" value="<convert>#label=55<convert>" onclick="JavaScript:grottoDelete(this.form);" /><!--Supprimer--><br />
<?php
    }
?>
        <input type="button" class="button1" id="refresh_grotto" name="refresh_grotto" value="<convert>#label=56<convert>" onclick="JavaScript:grottoRefresh(document.<?php echo $filter_form; ?>);" /><!--Rafraîchir-->
      </form>
    </div>
<?php
			break;
    	case "delete":
        if (!allowAccess(grotto_delete_all)) {
          exit();
        }
?>
    //deleteBeforeLoad(true);
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:deleteOnLoad();">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
		<?php echo getCloseBtn("JavaScript:newCancel();","<convert>#label=371<convert>"); ?>
		<div class="frame_title"><?php echo setTitle("#", "filter", "<convert>#label=647<convert>", 3); ?></div><!--Suppression d'un club-->
		<form id="delete_grotto" name="delete_grotto" method="post" action="">
			<table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
			  <tr><td>
			    <div class="warning"><?php echo getTopBubble(); ?>
			      <convert>#label=44<convert> <convert>#label=134<convert> <?php echo $name; ?> ?<!--Etes vous sûr de vouloir supprimer le club-->
			    <?php echo getBotBubble(); ?></div>
			  </td></tr><tr><td class="field">
			    <input type="hidden" id="delete_id" name="delete_id" value="<?php echo $did; ?>" />
          <input type="submit" class="button1" id="delete" name="delete" value="<convert>#label=55<convert>" /><!--Supprimer-->
        </td></tr><tr><td class="field">
          <input class="button1" type="button" id="cancel" name="cancel" value="<convert>#label=77<convert>" onclick="JavaScript:newCancel();" /><!--Annuler-->
        </td></tr>
      </table>
    </form>
<?php
			break;
			case "logo":
        if (!allowAccess(grotto_edit_all)) {
          exit();
        }
        include("../func/upload_restrictions.php");
        $upload_error = (isset($_GET['error'])) ? $_GET['error'] : '';
        $upload_error = urldecode($upload_error);
?>
    logoBeforeLoad();
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:logoOnLoad();">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
		<?php echo getCloseBtn("JavaScript:newCancel();","<convert>#label=371<convert>"); ?><!-- Fermer -->
	  <div class="frame_title"><?php echo setTitle("grotto_".$_SESSION['language'].".php?type=logo", "filter", "<convert>#label=683<convert>", 3); ?></div><!--Logo du club-->
<?php
          if ($upload_error != "") {
?>
	  <div class="error"><?php echo getTopBubble(); ?><convert>#label=62<convert><!--Erreur, veuillez recommencer !--><br />
      Error : <?php echo $upload_error; ?>
    <?php echo getBotBubble(); ?></div>
<?php
          }
?>
    <form id="logo_grotto" name="logo_grotto" method="post" action="../upload/logos/logo.php" enctype="multipart/form-data">
      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><th colspan="2"><?php echo getHelpTopic($helpId[$type], "<convert>#label=23<convert>"); ?></th></tr>
        <tr><td width="170" class="label" colspan="2" style="text-align:center;">
          <?php echo $grotto_name; ?>
        </td></tr>
<?php if ($logo_file != "") { ?>
			  <tr><td width="170" class="label" colspan="2" style="text-align:left;">
          <convert>#label=684<convert><!--Logo actuel--> :
        </td></tr><tr><td colspan="2">
          <img class="gross_avatar" src="../upload/logos/<?php echo $logo_file."?nocache=".rand(); ?>" alt="logo" />
        </td></tr><tr><td class="field" colspan="2">
          <input type="hidden" id="upload_type" name="upload_type" value="delete_logo" />
          <input type="hidden" id="logo_file" name="logo_file" value="<?php echo $logo_file; ?>" />
      	  <input type="submit" class="button1" id="delete" name="delete" value="<convert>#label=55<convert>" /><!--Supprimer-->
      	</td></tr><tr><td class="field" colspan="2">
      	  <input type="button" class="button1" id="cancel" name="cancel" onclick="JavaScript:newCancel();" value="<convert>#label=77<convert>" /><!--Annuler-->
      	</td></tr>
<?php } else { ?>
        <tr><td width="170" class="label" colspan="2" style="text-align:left;">
          <convert>#label=685<convert><!--Ce club n'a pas encore de logo-->.
        </td></tr><tr><td colspan="2">
          <img class="gross_avatar" src="../upload/logos/default_logo.png" alt="logo" />
        </td></tr><tr><td width="170" class="label">
		      <label for="filename">
		      	<img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=585<convert><sup>1</sup><!--Fichier-->
		      </label>
		    </td><td class="field">
		     	<input class="input1" type="file" id="filename" name="filename" value="<?php echo $_FILES['filename']; ?>" size="20" accept="image/*" />
		    </td></tr><tr><td class="field" colspan="2">
          <input type="hidden" id="upload_type" name="upload_type" value="add_logo" />
          <input type="hidden" id="target_name" name="target_name" value="<?php echo $grotto_name."-".$id; ?>" />
	      	<input type="submit" class="button1" id="upload" name="upload" value="<convert>#label=586<convert>" /><!--Envoyer le fichier-->
	      </td></tr><tr><td class="field" colspan="2">
          <input type="button" class="button1" id="cancel" name="cancel" onclick="JavaScript:newCancel();" value="<convert>#label=77<convert>" /><!--Annuler-->
        </td></tr><tr><td colspan="2">
          <div class="notes">
            <?php echo getTopBubble(); ?>
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=78<convert><!--Champs nécessaires.--><br />
            <sup>1</sup> <?php echo '('.implode(', ', $upload_restrictions_ext_array['add_logo']).')'; ?> <?php echo round($upload_restrictions_size_array['add_logo']/1000, 2).'Ko.'; ?> <convert>#label=67<convert><!--Max.-->
            <!--<convert>#label=589<convert>--><!--15ko fichiers images-->
            <?php echo getBotBubble(); ?>
          </div>
        </td></tr>
<?php } ?>
      </table>
      <input type="hidden" id="source_manager" name="source_manager" value="../../html/grotto_<?php echo $_SESSION['language']; ?>.php?type=<?php echo $type; ?>&uploaded=true&id=<?php echo $id; ?>" />
    </form>
<?php
			break;
    	case "edit":
        if (!allowAccess(grotto_edit_all)) {
          exit();
        }
        if ($locked) {
        } else {
          if (!isset($_POST['save']) || $save_failed){
?>
    newBeforeLoad(true);
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onbeforeunload="JavaScript:newOnBeforeUnload(event);" onunload="JavaScript:newOnUnload(true);" onload="JavaScript:newOnLoad(true);">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
		<?php echo getCloseBtn("JavaScript:newCancel();","<convert>#label=371<convert>"); ?>
		<div class="frame_title"><?php echo setTitle("#", "filter", "<convert>#label=61<convert>", 3); ?></div>
<?php
          if ($save_failed) {
?>
		<div class="error"><?php echo getTopBubble(); ?><convert>#label=62<convert><!--Erreur, veuillez recommencer !--><?php echo getBotBubble(); ?></div><br /><br />
<?php
          }
?>
  	<form id="new_grotto" name="new_grotto" method="post" action="" onsubmit="JavaScript:newSubmit(event);">
    	<table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><th colspan="2"><?php echo getHelpTopic($helpId[$type], "<convert>#label=23<convert>"); ?></th></tr>
        <tr><td width="170" class="label">
	      	<label for="n_grotto_name">
	      		<img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=144<convert><!--Nom du club--><sup>1</sup>
	      	</label>
	      </td><td class="field">
      		<input class="input1" type="text" id="n_grotto_name" name="n_grotto_name" value="<?php echo $name; ?>" size="40" maxlength="100" onkeyup="JavaScript:checkThisName(this, 'name_pic');" />
      		<img class="status1" name="name_pic" id="name_pic" src="../images/icons/wrong.png" alt="image" />
      	</td></tr><tr><td width="170" class="label">
		      <label for="n_grotto_country">
		      	<convert>#label=98<convert><!--Pays-->
					</label>
				</td><td class="field">
          <select class="select2" name="n_grotto_country" id="n_grotto_country">
<?php
          echo getOptionCountry($_SESSION['language'], $country, "<convert>#label=99<convert>");//Sélectionnez un pays ...
?>
          </select>
        </td></tr><tr><td width="170" class="label">
					<label for="n_grotto_region">
		      	<convert>#label=100<convert><!--Région-->
		      </label>
	      </td><td class="field">
      	  <input class="input1" type="text" id="n_grotto_region" name="n_grotto_region" value="<?php echo $region; ?>"  size="25" maxlength="32" />
      	  <i><convert>#label=665<convert><!--Ex : Rhône (69)--></i>
		    </td></tr><tr><td width="170" class="label">
          <label for="n_grotto_postal">
		      	<convert>#label=145<convert><!--Code postal-->
          </label>
	      </td><td class="field">
      	  <input class="input1" type="text" id="n_grotto_postal" name="n_grotto_postal" value="<?php echo $postal; ?>" size="5" maxlength="5" />
		    </td></tr><tr><td width="170" class="label">
		      <label for="n_grotto_city">
		      	<convert>#label=101<convert><!--Ville-->
          </label>
	      </td><td class="field">
      	  <input class="input1" type="text" id="n_grotto_city" name="n_grotto_city" value="<?php echo $city; ?>"  size="40" maxlength="32" /><!-- onfocus="JavaScript:cityOnFocus(this,'suggestBoxElement');" onblur="JavaScript:closeSuggestBox();"-->
      	  <!--span style="position:absolute;top:20px;left:0px;z-index:25;visibility:hidden;" id="suggestBoxElement"></span-->
		    </td></tr><tr><td width="170" class="label">
          <label for="n_grotto_address">
		      	<convert>#label=102<convert><!--Adresse-->
          </label>
	      </td><td class="field">
      	  <input class="input1" type="text" id="n_grotto_address" name="n_grotto_address" value="<?php echo $address; ?>" size="40" maxlength="128" />
		    </td></tr><tr><td width="170" class="label">
          <label for="n_grotto_locate">
		      	<convert>#label=107<convert><!--Indiquer sa position sur la carte--><sup>2</sup>
          </label>
	      </td><td class="field">
      	  <input class="button1" type="button" id="n_grotto_locate" name="n_grotto_locate" onclick="JavaScript:newShowMe(false);" value="<convert>#label=108<convert>" /><!--Cliquez ici-->
		    </td></tr><tr><td width="170" class="label">
					<label for="n_grotto_contact">
		      	<convert>#label=146<convert><!--E-mail de contact--><sup>3</sup>
          </label>
	      </td><td class="field">
      	  <input class="input1" type="text" id="n_grotto_contact" name="n_grotto_contact" value="<?php echo $contact; ?>" size="40" maxlength="40" onkeyup="JavaScript:checkMail(this, 'mail_pic');" />
      	  <img class="status1" name="mail_pic" id="mail_pic" src="../images/icons/wrong.png" alt="image" />
		    </td></tr><tr><td width="170" class="label">
		      <label for="n_grotto_birth">
		      	<convert>#label=147<convert><!--Année de création-->
          </label>
	      </td><td class="field">
      	  <input class="input1" type="text" id="n_grotto_birth" name="n_grotto_birth" value="<?php echo $birth; ?>" size="4" maxlength="4" />
      	  <i><convert>#label=110<convert><!--AAAA--></i>
		    </td></tr><tr><td width="170" class="label">
          <label for="n_grotto_president">
		      	<convert>#label=148<convert><!--Président-->
          </label>
	      </td><td class="field">
          <select class="select2" name="n_grotto_president" id="n_grotto_president">
<?php
//                $sql = "SELECT Ifnull(co.".$_SESSION['language']."_name,'<convert>#label=512<convert>') AS country, ca.Id AS value, CONCAT(if(ca.Name is null AND ca.Surname is null,'',CONCAT(if(ca.Name is null,'',CONCAT(ca.Name, ' ')),if(ca.Surname is null,'',CONCAT(ca.Surname , ' ')),'<convert>#label=34<convert> ')), ca.Nickname) As NName "; //CONCAT(if(ca.Name is null,'',CONCAT(ca.Name, ' ')), if(ca.Surname is null,'',CONCAT(ca.Surname , ' <convert>#label=34<convert> ')), ca.Nickname)
          $sql = "SELECT Ifnull(co.".$_SESSION['language']."_name,'<convert>#label=512<convert>') AS country, ca.Id AS value, ca.Nickname As NName ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_caver` ca ";
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_country` co ON ca.Country = co.Iso ";
          $sql .= "ORDER BY country, NName ";
          $msg = "<convert>#label=149<convert>";//Sélectionnez un spéléologue ...
          $comparedCol = "value";
          $countryCol = "country";
          $textCol = "NName";
          $selected = $president;
          echo groupOptions(getOptions($sql, $msg, $selected, $comparedCol, $countryCol, $textCol),$countryCol);
?>
          </select>
		    </td></tr><tr><td width="170" class="label">
          <label for="n_grotto_vice_pres">
		      	<convert>#label=150<convert><!--Président suppléant-->
          </label>
	      </td><td class="field">
      	  <select class="select2" name="n_grotto_vice_pres" id="n_grotto_vice_pres">
<?php
          $selected = $vice_pres;
          echo groupOptions(getOptions($sql, $msg, $selected, $comparedCol, $countryCol, $textCol),$countryCol);
?>
          </select>
		    </td></tr><tr><td width="170" class="label">
          <label for="n_grotto_treasurer">
		      	<convert>#label=151<convert><!--Trésorier-->
          </label>
	      </td><td class="field">
      	  <select class="select2" name="n_grotto_treasurer" id="n_grotto_treasurer">
<?php
          $selected = $treasurer;
          echo groupOptions(getOptions($sql, $msg, $selected, $comparedCol, $countryCol, $textCol),$countryCol);
?>
          </select>
		    </td></tr><tr><td width="170" class="label">
          <label for="n_grotto_secretary">
		      	<convert>#label=152<convert><!--Secrétaire-->
          </label>
	      </td><td class="field">
      	  <select class="select2" name="n_grotto_secretary" id="n_grotto_secretary">
<?php
          $selected = $secretary;
          echo groupOptions(getOptions($sql, $msg, $selected, $comparedCol, $countryCol, $textCol),$countryCol);
?>
          </select>
		    </td></tr><tr><td width="170" class="label">
		      <label for="n_grotto_message">
		      	<convert>#label=541<convert><!--Message perso./liens--><span class="new_feature"><convert>#label=537<convert><!--Nouveau !--></span>
		      </label>
	      </td><td class="field">
      	  <textarea class="input1" id="n_grotto_message" name="n_grotto_message" style="width:100%" rows="3" cols="" wrap="soft"><?php echo $custom_message;?></textarea>
		    </td></tr><tr><td class="label" colspan="2" style="text-align:left;">
          <label for="e_myList">
            <b><convert>#label=153<convert><!--Les entrées découvertes par ce club--> :</b>
          </label>
	      </td></tr><tr><td class="field" colspan="2">
          <select style="width:270px;" name="e_myList" id="e_myList" size="10" multiple="multiple" onclick="JavaScript:selectOnClick(event, this);" ondblclick="JavaScript:entryRemove();">
<?php
if ($isNew == "False") {
          $sql = "SELECT ey.Id AS value, ey.Name AS text ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_entry` ey ";
          $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_grotto_entry` ge ON ge.Id_entry = ey.Id ";
          $sql .= "WHERE ge.Id_grotto = ".$id." ";
          $sql .= "ORDER BY text ";
          $msg = "";
          $comparedCol = "value";
          $countryCol = "country";
          $selected = "#";
          $textCol = "text";
          echo getOptions($sql, $msg, $selected, $comparedCol, $textCol);
} ?>
          </select>
		    </td></tr><tr><td class="field" colspan="2">
          <input type="button" class="button1" id="add" name="add" value="<convert>#label=74<convert>" onclick="JavaScript:entryAdd();" /><!--    Ajouter à ma liste  /\-->
		    </td></tr><tr><td class="field" colspan="2">
          <input type="button" class="button1" id="remove" name="remove" value="<convert>#label=73<convert>" onclick="JavaScript:entryRemove();" /><!--\/  Retirer de ma liste    -->
		    </td></tr><tr><td class="field" colspan="2">
          <input type="hidden" id="e_list" name="e_list" />
      		<input type="hidden" id="n_grotto_latitude" name="n_grotto_latitude" value="<?php echo $latitude; ?>" />
      		<input type="hidden" id="n_grotto_longitude" name="n_grotto_longitude" value="<?php echo $longitude; ?>" />
      		<input type="hidden" id="is_new" name="is_new" value="<?php echo $isNew; ?>" />
      		<input type="hidden" id="grotto_id" name="grotto_id" value="<?php echo $id; ?>" />
      	  <input class="button1" type="submit" id="save" name="save" value="<convert>#label=76<convert>" /><!--Valider-->
		    </td></tr><tr><td class="field" colspan="2">
      	  <input class="button1" onclick="JavaScript:newCancel();" type="button" id="cancel" name="cancel" value="<convert>#label=77<convert>" /><!--Annuler-->
        </td></tr><tr><td colspan="2">
          <div class="notes">
            <?php echo getTopBubble(); ?>
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=78<convert><!--Champs nécessaires.--><br />
            <sup>1</sup> <convert>#label=135<convert><!--Le nom du club--> <convert>#label=873<convert><!--doit être composé de 2 à 36 caractères sauf--> <b>|</b> <b>/</b> <b>\</b> <b>"</b> <b>#</b> <b>&amp;</b> <b>+</b> <convert>#label=46<convert><!--et--> <b>¨</b><br />
            <sup>2</sup> <convert>#label=126<convert><!--Faites glisser le marqueur sur la carte avec votre souris pour le positionner plus précisément !--><br />
            <sup>3</sup> <convert>#label=154<convert><!--L'e-mail du club doit être un e-mail valide (optionnel).--><br />
            <convert>#label=79<convert><!--Double-cliquez sur l'élément que vous souhaitez déplacer pour gagner du temps !-->
            <?php echo getBotBubble(); ?>
          </div>
        </td></tr>
      </table>
    </form>
<?php
				  }
				}
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
		<div class="frame_title"><?php echo setTitle("#", "filter", "<convert>#label=80<convert>", 2); ?></div>
		<div class="error"><?php echo getTopBubble(); ?><convert>#label=81<convert><!--Cas non traité !--><?php echo getBotBubble(); ?></div><br /><br />
<?php
    	break;
    }
?>
    <?php echo getBotFrame(); ?>
<?php
    $virtual_page = "grotto/".$type."/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>
