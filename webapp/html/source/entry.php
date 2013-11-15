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
if (!allowAccess(entry_view_all)) {
  exit();
}
$frame = "filter";
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
    <script type="text/javascript" charset="UTF-8" src="<?php echo getScriptJS(__FILE__); ?>"></script>
<?php
  	include("application_".$_SESSION['language'].".php");
  	include("mailfunctions_".$_SESSION['language'].".php");
?>
    <?php echo getMetaTags(); ?>
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=82<convert></title><!--Ajouter une entrée.-->
    <link rel="stylesheet" type="text/css" href="../css/filter.css" />
    <link rel="stylesheet" type="text/css" href="../css/portlet.css" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
<?php
  	//Init vars
    $type = (isset($_GET['type'])) ? $_GET['type'] : '';
    $parameters = "";
    $locked = false;
    $backPage = (isset($_GET['back'])) ? $_GET['back'] : 'entry';
    $did = "-1";
    $id = '';
    $save_failed = false;
    $name = '';
    $country = '';
    $region = '';
    $city = '';
    $birth = '';
    $latitude = '';
    $longitude = '';
    $altitude = '';
    $isNew = '';
    $isPublic = '';
    $isSensitive = '';
    $contact = '';
    $modalities_key = '';
    $modalities_stack = '';
    $modalities_applic = '';
    $modalities_escort = '';
    $cave_type = '';
    $list = '';
    $isNetworked = '';
    $isMassifed = '';
    $old_cave_id = '';
    $cave_id = '';
    $isNewNetwork = '';
    //$cave_min_depth = '';
    //$cave_max_depth = '';
    $cave_depth = '';
    $cave_length = '';
    $cave_diving = '';
    $cave_temperature = '';
    $cave_name = '';
    $old_massif_id = '';
    $massif_id = '';
    $isNewMassif = '';
    $massif_name = '';
    $helpId = array("edit" => 6);
		$track_array = array('table' => array('T_entry', 'J_entry_caver', 'J_grotto_entry', 'J_cave_entry', 'T_single_entry', 'J_massif_cave', 'J_entry_url'),
												'column' => array('Id', 'Id_entry', 'Id_entry', 'Id_entry', 'Id', 'Id_entry', 'Id_entry'));
    
    if (allowAccess(entry_delete_all)) {
      //Delete the element
      if (isset($_POST['delete'])){
        $did = (isset($_POST['delete_id'])) ? $_POST['delete_id'] : '';
        if ($did != "") {
          trackAction("delete_entry", $did, $track_array);
          $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`T_entry` WHERE Id = ".$did;
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_entry_caver` WHERE Id_entry = ".$did;
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_grotto_entry` WHERE Id_entry = ".$did;
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_cave_entry` WHERE Id_entry = ".$did;
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          //$sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_entry_rigging` WHERE Id_entry = ".$did;
          //$req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          //$sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_entry_description` WHERE Id_entry = ".$did;
          //$req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          //$sql = "DELETE FROM `".$_SESSION['Application_host']."`.`T_location` WHERE Id_entry = ".$did;
          //$req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`T_single_entry` WHERE `Id` = ".$did;
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_massif_cave` WHERE Id_entry = ".$did;
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_entry_url` WHERE Id_entry = ".$did;
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
        if (takeOver("entry",$did) && $did != "") {
          $sql = "SELECT CONCAT(Name, ' (',CONCAT_WS(', ',Country, Region),')') AS Name FROM T_entry WHERE Id = ".$did;
          $name = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
          $name = $name[0]['Name'];
          $parameters = "&cancel=True&cid=".$did."&ccat=entry";
        } else {
          $locked = true;
          $type = "menu";
        }
      }
    }
  
    if (allowAccess(entry_edit_all)) {
      //Save the entry
      if (isset($_POST['save'])){
        $save_failed = true;
        $name = (isset($_POST['n_entry_name'])) ? $_POST['n_entry_name'] : '';
        $country = (isset($_POST['n_entry_country'])) ? $_POST['n_entry_country'] : '';
        $region = (isset($_POST['n_entry_region'])) ? $_POST['n_entry_region'] : '';
        $city = (isset($_POST['n_entry_city'])) ? $_POST['n_entry_city'] : '';
        $birth = (isset($_POST['n_entry_birth'])) ? $_POST['n_entry_birth'] : '';
        $latitude = (isset($_POST['n_entry_latitude'])) ? $_POST['n_entry_latitude'] : '';
        $longitude = (isset($_POST['n_entry_longitude'])) ? $_POST['n_entry_longitude'] : '';
        $altitude = (isset($_POST['n_entry_altitude'])) ? $_POST['n_entry_altitude'] : '';
        $isNew = (isset($_POST['is_new'])) ? $_POST['is_new'] : '';
        $id = (isset($_POST['entry_id'])) ? $_POST['entry_id'] : '';
        $isPublic = (isset($_POST['n_entry_public'])) ? $_POST['n_entry_public'] : '';
        $isSensitive = (isset($_POST['n_entry_sensitive'])) ? $_POST['n_entry_sensitive'] : '';
        $isSensitive = ($isSensitive=='YES' && $isPublic=='NO')? 'YES' : 'NO';
        $contact = (isset($_POST['n_entry_contact'])) ? $_POST['n_entry_contact'] : '';
        $modalities_key = (isset($_POST['n_entry_key'])) ? $_POST['n_entry_key'] : '';
        $modalities_stack = (isset($_POST['n_entry_stack'])) ? $_POST['n_entry_stack'] : '';
        $modalities_applic = (isset($_POST['n_entry_applic'])) ? $_POST['n_entry_applic'] : '';
        $modalities_escort = (isset($_POST['n_entry_escort'])) ? $_POST['n_entry_escort'] : '';
        $cave_type = (isset($_POST['n_entry_type'])) ? $_POST['n_entry_type'] : '';
        $list = (isset($_POST['u_list'])) ? $_POST['u_list'] : '';
        $isNetworked = (isset($_POST['n_entry_netw'])) ? $_POST['n_entry_netw'] : '';
        $isMassifed = (isset($_POST['n_cave_mas'])) ? $_POST['n_cave_mas'] : '';
        $old_cave_id = (isset($_POST['n_old_cave'])) ? $_POST['n_old_cave'] : '';
        $cave_id = (isset($_POST['n_selected_cave'])) ? $_POST['n_selected_cave'] : '';
        $isNewNetwork = (isset($_POST['n_cave'])) ? $_POST['n_cave'] : '';
        //$cave_min_depth = (isset($_POST['n_cave_min_depth'])) ? $_POST['n_cave_min_depth'] : '';
        //$cave_max_depth = (isset($_POST['n_cave_max_depth'])) ? $_POST['n_cave_max_depth'] : '';
        $cave_depth = (isset($_POST['n_cave_depth'])) ? $_POST['n_cave_depth'] : '';
        $cave_length = (isset($_POST['n_cave_length'])) ? $_POST['n_cave_length'] : '';
        $cave_diving = (isset($_POST['n_cave_diving'])) ? $_POST['n_cave_diving'] : '';
        $cave_temperature = (isset($_POST['n_cave_temperature'])) ? $_POST['n_cave_temperature'] : '';
        $cave_name = (isset($_POST['n_cave_name'])) ? $_POST['n_cave_name'] : '';
        $old_massif_id = (isset($_POST['n_old_massif'])) ? $_POST['n_old_massif'] : '';
        $massif_id = (isset($_POST['n_selected_massif'])) ? $_POST['n_selected_massif'] : '';
        $isNewMassif = (isset($_POST['n_massif'])) ? $_POST['n_massif'] : '';
        $massif_name = (isset($_POST['n_massif_name'])) ? $_POST['n_massif_name'] : '';
        if ($isNew == "True") {
          $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`T_entry` ";
          $sql .= "(`Id_author`, `Name`, `Country`, `Region`, `City`, `Year_discovery`, `Id_type`, `Date_inscription`, `Is_public`, `Is_sensitive`, `Contact`, `Modalities`, `Latitude`, `Longitude`, `Altitude`)";
          $sql .= " VALUES (";
          $sql .= $_SESSION['user_id'].", ";
          $sql .= returnDefault($name, 'text').", ";
          $sql .= returnDefault($country, 'list').", ";
          $sql .= returnDefault($region, 'text').", ";
          $sql .= returnDefault($city, 'text').", ";
          $sql .= returnDefault($birth, 'text').", ";
          $sql .= returnDefault($cave_type, 'list').", ";
          $sql .= "Now(), ";
          $sql .= returnDefault($isPublic, 'text').", ";
          $sql .= returnDefault($isSensitive, 'text').", ";
          $sql .= returnDefault($contact, 'text').", ";
          $sql .= "'".returnDefault($modalities_key, 'checkboxarray').",".returnDefault($modalities_stack, 'checkboxarray').",".returnDefault($modalities_applic, 'checkboxarray').",".returnDefault($modalities_escort, 'checkboxarray')."', ";
          $sql .= returnDefault($latitude, 'latlng').", ";
          $sql .= returnDefault($longitude, 'latlng').", ";
          $sql .= returnDefault($altitude, 'float').") ";
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $nid = $req['mysql_insert_id'];
          trackAction("insert_entry",$nid,"T_entry");
        } else {
          trackAction("edit_entry", $id, $track_array);
          $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_entry` ";
          $sql .= " SET ";
          $sql .= "Locked = 'NO', ";
          $sql .= "Id_reviewer = ".$_SESSION['user_id'].", ";
          $sql .= "Name = ".returnDefault($name, 'text').", ";
          $sql .= "Country = ".returnDefault($country, 'list').", ";
          $sql .= "Region = ".returnDefault($region, 'text').", ";
          $sql .= "City = ".returnDefault($city, 'text').", ";
          $sql .= "Year_discovery = ".returnDefault($birth, 'text').", ";
          $sql .= "Id_type = ".returnDefault($cave_type, 'list').", ";
          $sql .= "Is_public = ".returnDefault($isPublic, 'text').", ";
          $sql .= "Is_sensitive = ".returnDefault($isSensitive, 'text').", ";
          $sql .= "Contact = ".returnDefault($contact, 'text').", ";
          $sql .= "Modalities = '".returnDefault($modalities_key, 'checkboxarray').",".returnDefault($modalities_stack, 'checkboxarray').",".returnDefault($modalities_applic, 'checkboxarray').",".returnDefault($modalities_escort, 'checkboxarray')."', ";
          $sql .= "Date_reviewed = Now(), ";
          $sql .= "Latitude = ".returnDefault($latitude, 'latlng').", ";
          $sql .= "Longitude = ".returnDefault($longitude, 'latlng').", ";
          $sql .= "Altitude = ".returnDefault($altitude, 'float')." ";
          $sql .= "WHERE Id = ".$id;
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        	$sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_entry_url` ";
        	$sql .= "WHERE `Id_entry` = ".$id;
        	$req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        }
        if ($isNew == "True") {
          $onid = $nid;
        } else {
          $onid = $id;
        }
        if ($list != "") {
          $arrList = split('[|]+', $list);
          //Establish the relationship between urls and this entry
          $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`J_entry_url` (`Id_entry`, `Id_url`) VALUES ";
          foreach($arrList as $value) {
            $sql .= "(".$onid.", ".$value."), ";
          }
          $sql = substr($sql,0,strlen($sql)-2);
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        }
        $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_cave_entry` WHERE Id_cave = '".$old_cave_id."' AND Id_entry = ".$onid;
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        if ($isNetworked == "YES") {
          if ($isNewNetwork == "YES") {
            $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`T_cave` ";
            //$sql .= "(`Id_author`, `Name`, `Min_depth`, `Max_depth`, `Length`, `Is_diving`, `Temperature`, `Date_inscription`)";
            $sql .= "(`Id_author`, `Name`, `Depth`, `Length`, `Is_diving`, `Temperature`, `Date_inscription`)";
            $sql .= " VALUES (";
            $sql .= $_SESSION['user_id'].", ";
            $sql .= returnDefault($cave_name, 'text').", ";
            //$sql .= returnDefault($cave_min_depth, 'float').", ";
            //$sql .= returnDefault($cave_max_depth, 'float').", ";
            $sql .= returnDefault($cave_depth, 'float').", ";
            $sql .= returnDefault($cave_length, 'float').", ";
            $sql .= returnDefault($cave_diving, 'checkbox').", ";
            $sql .= returnDefault($cave_temperature, 'float').", ";
            $sql .= "Now()) ";
            $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
            $cave_id = $req['mysql_insert_id'];
            trackAction("insert_cave",$cave_id,"T_cave");
          }
          $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`J_cave_entry` (`Id_cave`, `Id_entry`) VALUES (";
          $sql .= returnDefault($cave_id, 'text').", ";
          $sql .= returnDefault($onid, 'text').") ";
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          //Reset possible relationship between entries and massifs
          $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_massif_cave` WHERE Id_entry = ".$onid;
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        }
        $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_massif_cave` WHERE Id_massif = '".$old_massif_id."' AND Id_entry = ".$onid;
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        if ($isMassifed == "YES" && !($isNetworked == "YES" && $isNewNetwork != "YES")) {
          if ($isNewMassif == "YES") {
            $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`T_massif` ";
            $sql .= "(`Id_author`, `Name`, `Date_inscription`)";
            $sql .= " VALUES (";
            $sql .= $_SESSION['user_id'].", ";
            $sql .= returnDefault($massif_name, 'text').", ";
            $sql .= "Now()) ";
            $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
            $massif_id = $req['mysql_insert_id'];
            trackAction("insert_massif",$massif_id,"T_massif");
          }
          $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`J_massif_cave` (`Id_massif`, `Id_cave`, `Id_entry`) VALUES (";
          $sql .= returnDefault($massif_id, 'text').", ";
          if ($isNetworked == "YES" && $isNewNetwork == "YES") {
            $sql .= returnDefault($cave_id, 'text').", ";
            $sql .= "0) ";
          } else {
            $sql .= "0, ";
            $sql .= returnDefault($onid, 'text').") ";
          }
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        }
        if ($isNew == "True") {
          $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`T_single_entry` ";
          //$sql .= "(`Id`, `Min_depth`, `Max_depth`, `Length`, `Is_diving`, `Temperature`)";
          $sql .= "(`Id`, `Depth`, `Length`, `Is_diving`, `Temperature`)";
          $sql .= " VALUES (";
          $sql .= $onid.", ";
          //$sql .= returnDefault($cave_min_depth, 'float').", ";
          //$sql .= returnDefault($cave_max_depth, 'float').", ";
          $sql .= returnDefault($cave_depth, 'float').", ";
          $sql .= returnDefault($cave_length, 'float').", ";
          $sql .= returnDefault($cave_diving, 'checkbox').", ";
          $sql .= returnDefault($cave_temperature, 'float').") ";
        } else {
          $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_single_entry` ";
          $sql .= " SET ";
          //$sql .= "`Min_depth` = ".returnDefault($cave_min_depth, 'float').", ";
          //$sql .= "`Max_depth` = ".returnDefault($cave_max_depth, 'float').", ";
          $sql .= "`Depth` = ".returnDefault($cave_depth, 'float').", ";
          $sql .= "`Length` = ".returnDefault($cave_length, 'float').", ";
          $sql .= "`Temperature` = ".returnDefault($cave_temperature, 'float').", ";
          $sql .= "`Is_diving` = ".returnDefault($cave_diving, 'checkbox')." ";
          $sql .= "WHERE `Id` = ".$onid;
        }
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        $save_failed = false;
        $type = "menu";
      } else {
        if (isset($_GET['id'])) {
          $id = (isset($_GET['id'])) ? $_GET['id'] : '';
          if (takeOver("entry", $id) && $id != "") {
            $sql = "SELECT ey.*, ce.Id_cave, mc.Id_massif FROM `".$_SESSION['Application_host']."`.`T_entry` ey ";
            $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_cave_entry` ce ON ey.Id = ce.Id_entry "; 
            $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_massif_cave` mc ON ey.Id = mc.Id_entry "; 
            $sql .= "WHERE ey.Id = ".$id;
            $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
            if ($data['Count'] > 0) {
              $name = $data[0]['Name'];
              $country = $data[0]['Country'];
              $region = $data[0]['Region'];
              $city = $data[0]['City'];
              $birth = $data[0]['Year_discovery'];
              $cave_type = $data[0]['Id_type'];
              $isPublic = $data[0]['Is_public'];
              $isSensitive = $data[0]['Is_sensitive'];
              $contact = $data[0]['Contact'];
              $modalities = explode(",", $data[0]['Modalities']);
              $modalities_key = $modalities[0];
              $modalities_stack = $modalities[1];
              $modalities_applic = $modalities[2];
              $modalities_escort = $modalities[3];
              $latitude = $data[0]['Latitude'];
              $longitude = $data[0]['Longitude'];
              $altitude = $data[0]['Altitude'];
              $cave_id = $data[0]['Id_cave'];
              $massif_id = $data[0]['Id_massif'];
              if ($data[0]['Id_cave'] == "") {
                $isNetworked = "NO";
                $sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`T_single_entry` ";
                $sql .= "WHERE `Id` = ".$id;
                $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
                if ($data['Count'] > 0) {
                  //$cave_min_depth = $data[0]['Min_depth'];
                  //$cave_max_depth = $data[0]['Max_depth'];
                  $cave_depth = $data[0]['Depth'];
                  $cave_length = $data[0]['Length'];
                  $cave_temperature = $data[0]['Temperature'];
                  $cave_diving = $data[0]['Is_diving'];
                }
              } else {
                $isNetworked = "YES";
              }
              $isNewNetwork = "NO";
              if ($massif_id == "") {
                $isMassifed = "NO";
              } else {
                $isMassifed = "YES";
              }
              $isNewMassif = "NO";
              $isNew = "False";
            }
            $parameters = "&cancel=True&cid=".$id."&ccat=entry";
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
?>
    <script type="text/javascript" charset="UTF-8" src="../scripts/classeGCTest.js"></script>
    <script type="text/javascript" charset="UTF-8">
    <?php echo getCDataTag(true); ?>
    var doCancel = true;
    var checkIsRunning = false;
    var address_level;
    var entryNamesArray = new Array();
    var caveNamesArray = new Array();
    var massifNamesArray = new Array();
    <?php include("../scripts/events.js"); ?>
      
<?php
    switch ($type)
    {
    	case "menu":
?>
    function menuBeforeLoad() {
      parent.setFilterSize(418,'px');
    }
    
    function menuOnLoad() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      var oHtml = document.getElementsByTagName('HTML')[0];
      parent.setFilterSizeTight(oHtml);
      <?php if (isset($_POST['delete'])) { ?> reload(false); <?php } ?>
    }
    
    function entryEdit(oForm) {
      var oRadio = oForm.radio_list;
      var entryId = getRadioValue(oRadio);
      if (entryId) {
        openMe(entryId, "entry", false);
        self.location.href = "entry_<?php echo $_SESSION['language']; ?>.php?type=edit&id=" + entryId;
      }
    }
    
    function entryDelete(oForm) {
      var oRadioArray = oForm.radio_list;
      var entryId = getRadioValue(oRadioArray);
      if (entryId) {
        deleteMarker("entry", entryId, "<?php echo $_SESSION['language']; ?>");
      }
    }
    
    function entryRefresh(oForm) {
      //self.location.href = "entry_<?php echo $_SESSION['language']; ?>.php?type=menu";
      oForm.submit();
    }
    
    function entryOnClick(e, id) {
      var category = "entry";
      openMe(id, category, false);
      detailMarker(e, category, id, '<?php echo $_SESSION['language']; ?>', false);
    }
    
    function menuOnUnload() {
    }
<?php
      break;
    	case "delete":
?>
    function deleteOnLoad() {
      var oForm = document.delete_entry;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      parent.setFilterSize(getMaxWidthInput(oForm),"px");
      var oHtml = document.getElementsByTagName('HTML')[0];
      parent.setFilterSizeTight(oHtml);
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
      var oForm = document.new_entry;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      mySite.details.switchDetails(true);
      if (hasFailed) {
        var oRadio = oForm.n_entry_public;
        publicOnClick(getRadioValue(oRadio));
        var oRadio = oForm.n_entry_sensitive;
        sensitiveOnClick(getRadioValue(oRadio));
        var oRadio = oForm.n_cave;
        newCaveOnClick(getRadioValue(oRadio));
        oRadio = oForm.n_entry_netw;
        newNetwOnClick(getRadioValue(oRadio));
        oRadio = oForm.n_massif;
        newMassifOnClick(getRadioValue(oRadio));
        oRadio = oForm.n_cave_mas;
        newMassifedOnClick(getRadioValue(oRadio));
        parent.setFilterSize(getMaxWidthInput(oForm),"px");
        var oHtml = document.getElementsByTagName('HTML')[0];
        parent.setFilterSizeTight(oHtml);
        parent.overview.hideId('n_reload');
        <?php if ($isNew == "False") { ?>
        freeMe(<?php echo $id; ?>, "entry");
        <?php } else { if ($showMe == "True") { ?>
        newShowMe();
        <?php } } ?>
        entryNamesArray = loadNames("entry");
        caveNamesArray = loadNames("cave");
        massifNamesArray = loadNames("massif");
        checkThisName(oForm.n_entry_name, "name_pic", "entry", entryNamesArray);
      } else {
        reload(false);
      }
    }
    
    function newShowMe() {
      address_level = 4; //Address = 4, City = 3, Region = 2, Country = 1
      var oForm = document.new_entry;
      <?php if ($isNew == "True") { ?>
      if (oForm.n_entry_latitude.value != "" && oForm.n_entry_longitude.value != "") {
        showMarker(new mySite.overview.google.maps.LatLng(strToFloat(oForm.n_entry_latitude.value), strToFloat(oForm.n_entry_longitude.value)));
      } else {
        getCoordsByDirection(getAddress('n_', 'entry', address_level), showMarker2);
      }
      <?php } else { ?>        
      if (oForm.n_entry_latitude.value != "" && oForm.n_entry_longitude.value != "") {
        moveMarker(new mySite.overview.google.maps.LatLng(strToFloat(oForm.n_entry_latitude.value), strToFloat(oForm.n_entry_longitude.value)));
      } else {
        getCoordsByDirection(getAddress('n_', 'entry', address_level), moveMarker2);
      }
      <?php } ?>
    }
    
    function showMarker(gLatLng) {
      if (gLatLng) {
        showMe(gLatLng, "entry", false);
        setLocations(gLatLng.lat(), gLatLng.lng());
      } else {
        if (address_level > 0) {
          address_level --;
          getCoordsByDirection(getAddress('n_', 'entry', address_level), showMarker2);
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
        moveMarkerTo('<?php echo $id; ?>', 'entry', gLatLng.lat(), gLatLng.lng());
        openMe('<?php echo $id; ?>', "entry", false); //, gLatLng);
        setLocations(gLatLng.lat(), gLatLng.lng());
      } else {
        if (address_level > 0) {
          address_level --;
          getCoordsByDirection(getAddress('n_', 'entry', address_level), moveMarker2);
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
      newShowMe();
    }
    
    function setLocations(lat, lng) {
      var oForm = document.new_entry;
      oForm.n_entry_latitude.value = lat;
      oForm.n_entry_longitude.value = lng;
    }
    
    function openConverter(oForm) { //systemOnChange(oForm) {
      var lat, lng, iso, windowName, url, oField, oOption; //, system;
      //if (oForm.n_entry_system.selectedIndex > 0) {
        //system = oForm.n_entry_system.options[oForm.n_entry_system.selectedIndex].value;
        lat = oForm.n_entry_latitude.value;
        lng = oForm.n_entry_longitude.value;
				oField = oForm.n_entry_country;
				oOption = oField.options[oField.selectedIndex];
				iso = oOption.value;
        windowName = "";
        //url = "converter_<?php echo $_SESSION['language']; ?>.php?c=coords&g=" + encodeURI(encodeURIComponent(system)) + "&lat=" + lat + "&lng=" + lng;
				url = "converter_<?php echo $_SESSION['language']; ?>.php?c=coords&lat=" + lat + "&lng=" + lng + "&i=" + iso;
        //oForm.n_entry_system.selectedIndex = 0;
        openWindow(url, windowName, 434, 260);
      //}
    }

  	function urlRemove() {
      var oForm = document.new_entry;
      var oOptions = oForm.u_myList.options;
      for (var i=0; i<oOptions.length; i++) {
        if (oOptions[i].selected) {
          oOptions[i] = null;
          i--;
        }
      }
  	}
  
  	function urlAdd() {
      var windowName, url;
      windowName = "<convert>#label=612<convert>";//Choisissez une ou plusieurs entrées à ajouter
      url = "pick_window_<?php echo $_SESSION['language']; ?>.php?type=url&callback=addUrl";
      openWindow(url, windowName, 600, 520);
  	}
	
  	function addUrl(oForm) {
      var uForm = document.new_entry;
      addOptionsFromSelection(oForm, uForm.u_myList);
    }
    
    function publicOnClick(sValue) {
      if (sValue == "NO") {
        showId('sensitive');
      } else {
        hideId('sensitive');
      }
    }
    
    function sensitiveOnClick(sValue) {
      if (sValue == "YES") {
        showId('sensitiveinfo');
      } else {
        hideId('sensitiveinfo');
      }
    }
    
    function newNetwOnClick(sValue) {
      if (sValue == "YES") {
        showId('network');
        var oForm = document.new_entry;
        var oRadio = oForm.n_cave;
        newCaveOnClick(getRadioValue(oRadio));
      } else {
        hideId('network');
        showId('cave_properties');
      }
    }
    
    function newCaveOnClick(sValue) {
      var oForm = document.new_entry;
      if (sValue == "NO") {
        oForm.n_selected_cave.disabled = false;
        hideId('new_network');
        hideId('cave_properties');
      } else {
        this.disabled = true;
        showId('new_network');
        showId('cave_properties');
      }
      setRadio(oForm.n_cave,sValue);
    }
    
    function newMassifOnClick(sValue) {
      var oForm = document.new_entry;
      if (sValue == "NO") {
        oForm.n_selected_massif.disabled = false;
        hideId('new_massif');
      } else {
        this.disabled = true;
        showId('new_massif');
      }
      setRadio(oForm.n_massif,sValue);
    }
    
    function newMassifedOnClick(sValue) {
      if (sValue == "YES") {
        showId('massif');
        var oForm = document.new_entry;
        var oRadio = oForm.n_massif;
        newMassifOnClick(getRadioValue(oRadio));
      } else {
        hideId('massif');
      }
    }
    
    function checkThisName(oObject, sTargetId, sCategory, namesArray) {
      if (sCategory == "entry") {
        sName = "<?php echo $name; ?>";
      } else {
        sName = "";
      }
      checkName(oObject, sTargetId, sCategory, sName, namesArray, false); //put the last parameter to false
      displayCloseNames(oObject, getMatchingValues(oObject.value, namesArray, sName), '<convert>#label=844<convert>'); //Noms existants déjà en base :
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
      blockMe(<?php echo $id; ?>, "entry");
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
    
    function newSubmit(event) {
      var oForm, rightSource, oField, sMessage, oOption, wrongValue, testRegExp;
      oForm = document.new_entry;
      rightSource = toAbsURL(rightPic);
      oField = xtdGetElementById('name_pic');
      sMessage = "<convert>#label=89<convert> <convert>#label=874<convert> | / \\ ' \" & + <convert>#label=46<convert> ¨";//Le nom de l'entrée //doit être composé de 2 à 100 caractères sauf //et
      createTest(oField.name, oField.src, rightSource, "==", sMessage, true);
      oField = oForm.n_entry_country;
      oOption = oField.options[oField.selectedIndex];
      sMessage = "<convert>#label=90<convert> ";//Le champ Pays est obligatoire.
      wrongValue = "<?php echo Select_default; ?>";
      createTest(oField.name, oOption.value, wrongValue, "!=", sMessage, true);
      testRegExp = "^(\\-|\\+){0,1}[0-9]+[.,]*[0-9]*$";
      oField = oForm.n_entry_latitude;
      sMessage = "<convert>#label=808<convert> ";//Format de Latitude incorrect. Utiliser le convertisseur si besoin.
      createTest(oField.name, oField.value, testRegExp, "testRegExp", sMessage, true);
      oField = oForm.n_entry_longitude;
      sMessage = "<convert>#label=809<convert> ";//Format de Longitude incorrect. Utiliser le convertisseur si besoin.
      createTest(oField.name, oField.value, testRegExp, "testRegExp", sMessage, true);
      if (getRadioValue(oForm.n_entry_sensitive) == "YES" && getRadioValue(oForm.n_entry_public) == "NO") {
        oField = oForm.n_entry_contact;
        sMessage = "<convert>#label=747<convert> \"<convert>#label=741<convert>\"."; //Veuillez renseigner le champ //"Entité/personne à contacter".
        createTest(oField.name, oField.value, "", "!=", sMessage, true);
      }
      if (getRadioValue(oForm.n_entry_netw) == "YES") {
        if (getRadioValue(oForm.n_cave) == "YES") {
          oField = xtdGetElementById('name_ntw_pic');
          sMessage = "<convert>#label=88<convert> <convert>#label=873<convert>  | / \\ ' \" & + <convert>#label=46<convert> ¨.";//Le nom du réseau //doit être composé de 2 à 36 caractères sauf //et
          createTest(oField.name, oField.src, rightSource, "==", sMessage, true);
        } else {
          oField = oForm.n_selected_cave;
          oOption = oField.options[oField.selectedIndex];
          sMessage = "<convert>#label=91<convert>";//Le champ Réseau existant est obligatoire.
          wrongValue = "<?php echo Select_default; ?>";
          createTest(oField.name, oOption.value, wrongValue, "!=", sMessage, true);
        }
      }
      if (getRadioValue(oForm.n_cave_mas) == "YES") {
        if (getRadioValue(oForm.n_massif) == "YES") {
          oField = xtdGetElementById('name_mas_pic');
          sMessage = "<convert>#label=546<convert> <convert>#label=873<convert>  | / \\ ' \" & + <convert>#label=46<convert> ¨.";//Le nom du massif //doit être composé de 2 à 36 caractères sauf //et
          createTest(oField.name, oField.src, rightSource, "==", sMessage, true);
        } else {
          oField = oForm.n_selected_massif;
          oOption = oField.options[oField.selectedIndex];
          sMessage = "<convert>#label=557<convert>";//Le champ Massif existant est obligatoire.
          wrongValue = "<?php echo Select_default; ?>";
          createTest(oField.name, oOption.value, wrongValue, "!=", sMessage, true);
        }
      }
      if (!testForm()) {
        stopSubmit(event);
      } else {
        /*
        <?php if ($isNew == "False") { ?>
        var marker = parent.overview.getMarker(<?php echo $id; ?>, "entry");
        <?php } else { ?>
        var marker = parent.overview.marker_user;
        <?php } ?>
        doChallengeCoordinates(oForm.n_entry_latitude, oForm.n_entry_longitude, marker);
        */
        doChallengeList(oForm.u_myList,oForm.u_list);
        doCancel = false;
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
    function entryNew() {
      self.location.href = "entry_<?php echo $_SESSION['language']; ?>.php?type=edit";
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
  <body onload="JavaScript:menuOnLoad();" onunload="JavaScript:menuOnUnload();">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
		<?php echo getCloseBtn("filter_".$_SESSION['language'].".php","<convert>#label=371<convert>"); ?>
		<div class="frame_title"><?php echo setTitle("entry_".$_SESSION['language'].".php?type=menu", "filter", "<convert>#label=93<convert>", 2); ?></div><!--Menu des entrées-->
<?php
      if ($locked) {
?>
  	<div class="error"><?php echo getTopBubble(); ?><convert>#label=85<convert> <convert>#label=49<convert><!--Cette entrée est en cours de modification par un autre utilisateur, veuillez essayer plus tard !--><?php echo getBotBubble(); ?></div>
<?php
      } else {
        if (isset($_POST['save']) && !$save_failed){
?>
    <div class="warning"><?php echo getTopBubble(); ?><convert>#label=83<convert> <convert>#label=140<convert><!--L'entrée a été enregistrée avec succès !--><?php echo getBotBubble(); ?></div>
<?php
        }
        if (isset($_POST['delete'])) {
          if ($delete_failed){
?>
  	<div class="error"><?php echo getTopBubble(); ?><convert>#label=840<convert><!--Vous n'êtes pas autorisé à supprimer--> <convert>#label=85<convert><!--Cette entrée--><?php echo getBotBubble(); ?></div>
<?php
          } else {
?>
    <div class="warning"><?php echo getTopBubble(); ?><convert>#label=83<convert> <convert>#label=610<convert><!--L'entrée a été supprimée avec succès !--><?php echo getBotBubble(); ?></div>
<?php
          }
        }
      }
?>

<?php
    $sql = "SELECT DISTINCT ";
    $sql .= "T_entry.Id AS `0`, ";
    $sql .= "IF((T_entry.Locked = 'YES' AND NOT T_entry.Id_locker = '".$_SESSION['user_id']."'),1,0) AS `1`, ";
//CRO 2011-10-12
//    $sql .= "CONCAT_WS('',CONCAT_WS(' /!\\\\ ',T_entry.Name,IF(T_entry.Locked = 'YES','<convert>#label=42<convert>',NULL)),'[|]',IF((T_entry.Locked = 'YES' AND T_entry.Id_locker = '".$_SESSION['user_id']."'),'".str_replace("'","''","<convert>#label=574<convert>")."',NULL),'[|]',CONCAT_WS(', ',T_entry.City,T_entry.Region,T_country.".$_SESSION['language']."_name),'[|]',CONCAT_WS(' > ',T_massif.Name,T_cave.Name)) AS `2`, ";//Nom de la cavité //Commune //Etat/Région, //Pays //Massif //Réseau
    $sql .= "CONCAT_WS('',CONCAT_WS(' /!\\\\ ',T_entry.Name,IF(T_entry.Locked = 'YES','<convert>#label=42<convert>',NULL)),'[|]',IF((T_entry.Locked = 'YES' AND T_entry.Id_locker = '".$_SESSION['user_id']."'),'".str_replace("'","''","<convert>#label=574<convert>")."',NULL),'[|]',CONCAT_WS(', ',T_entry.City,T_entry.Region,T_country.".$_SESSION['language']."_name),'[|]',T_cave.Name) AS `2`, ";//Nom de la cavité //Commune //Etat/Région, //Pays //Réseau
    $sql .= "T_entry.Name AS `3`, ";//Nom de la cavité
    $sql .= "T_country.".$_SESSION['language']."_name AS `4`, ";//Pays
    $sql .= "T_entry.Region AS `5`, ";//Etat/Région
    $sql .= "T_entry.City AS `6`, ";//Commune
//CRO 2011-10-12
//    $sql .= "T_massif.Name AS `7`, ";//Massif
    $sql .= "T_cave.Name AS `8`, ";//Réseau
    $sql .= "IF((T_entry.Locked = 'YES' AND T_entry.Id_locker = '".$_SESSION['user_id']."'),1,0) AS `9` ";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_entry` ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_cave_entry` ON J_cave_entry.Id_entry = T_entry.Id ";
//CRO 2011-10-12
//    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_massif_cave` ON J_massif_cave.Id_cave = J_cave_entry.Id_cave ";
//    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_massif` ON T_massif.Id = J_massif_cave.Id_massif ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_cave` ON T_cave.Id = J_cave_entry.Id_cave ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_country` ON T_country.Iso = T_entry.Country ";
    $columns_params = array(
			0 => "[hidden]|[hidden]Id",
			1 => "[hidden]|[hidden]Locked",
			2 => "[hidden]|<convert>#label=97<convert><br /><convert>#label=101<convert>, <convert>#label=100<convert>, <convert>#label=98<convert><br /><convert>#label=555<convert> &gt; <convert>#label=119<convert>",
			3 => "T_entry*Name|[hidden]<convert>#label=97<convert>",
			4 => "T_entry*Country|[hidden]<convert>#label=98<convert>|SELECT Iso AS value,".$_SESSION['language']."_name AS text FROM T_country ORDER BY text",
			5 => "T_entry*Region|[hidden]<convert>#label=100<convert>",
			6 => "T_entry*City|[hidden]<convert>#label=101<convert>",
//CRO 2011-10-12
//			7 => "T_massif*Name|[hidden]<convert>#label=555<convert>",
			7 => "[hidden]|[hidden]Massif_name",
			8 => "T_cave*Name|[hidden]<convert>#label=119<convert>",
			9 => "[hidden]|[hidden]9"
		);
    //$param_link = "entry_".$_SESSION['language'].".php?type=edit&id=<Id>";
    $param_link = "JavaScript:entryOnClick(event,'<Id>');";
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
                    9 => '1')));
    $default_order = 3;
?>

<?php
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
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
      $navigator = getPageNavigator($length_page, $current_page, $count_page, $filter_form);//$base_url);
    } else {
      $navigator = "";
    }
?>
    <div>
      <form id="<?php echo $filter_form; ?>" name="<?php echo $filter_form; ?>" method="post" action="entry_<?php echo $_SESSION['language']; ?>.php?type=menu">
        <table border="0" cellspacing="1" cellpadding="0" id="filter_set">
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
        <!--<div class="info" id="info" name="info">
          <convert>#label=52<convert>--><!--Selectionnez un élément dans la liste pour afficher ici des informations supplémentaires.-->
        <!--</div>-->
        <div class="notes">
          <?php echo getTopBubble(); ?>
          <convert>#label=95<convert><!--Si l'entrée que vous cherchez n'est pas dans--> <?php echo $_SESSION['Application_name']; ?>, <a href="JavaScript:entryNew();"><convert>#label=96<convert><!--créez la--></a> !
          <?php echo getBotBubble(); ?>
        </div>
<?php
    if (allowAccess(entry_edit_all)) {
?>
        <input type="button" class="button1" id="edit_entry" name="edit_entry" value="<convert>#label=53<convert>" onclick="JavaScript:entryEdit(this.form);" /><!--Modifier--><br />
        <input type="button" class="button1" id="new_entry" name="new_entry" value="<convert>#label=54<convert>" onclick="JavaScript:entryNew();" /><!--Nouveau--><br />
<?php
    }
    if (allowAccess(entry_delete_all)) {
?>
        <input type="button" class="button1" id="del_entry" name="del_entry" value="<convert>#label=55<convert>" onclick="JavaScript:entryDelete(this.form);" /><!--Supprimer--><br />
<?php
    }
?>
        <input type="button" class="button1" id="refresh_entry" name="refresh_entry" value="<convert>#label=56<convert>" onclick="JavaScript:entryRefresh(document.<?php echo $filter_form; ?>);" /><!--Rafraîchir-->
      </form>
    </div>
<?php
			break;
    	case "delete":
        if (!allowAccess(entry_delete_all)) {
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
  	<div class="frame_title"><?php echo setTitle("#", "filter", "<convert>#label=611<convert>", 3); ?></div><!--Suppression d'une entrée-->
  	<form id="delete_entry" name="delete_entry" method="post" action="">
  		<table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
  		  <tr><td>
  		    <div class="warning"><?php echo getTopBubble(); ?>
  		      <convert>#label=44<convert> <convert>#label=87<convert> <?php echo $name; ?> ?<!--Etes vous sûr de vouloir supprimer l'entrée-->
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
    	case "edit":
        if (!allowAccess(entry_edit_all)) {
          exit();
        }
        if ($locked) {
        } else {
          if (!isset($_POST['save']) || $save_failed){
            include("properties_".$_SESSION['language'].".php");
?>
    newBeforeLoad(true);
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onbeforeunload="JavaScript:newOnBeforeUnload(event);"  onunload="JavaScript:newOnUnload(true);" onload="JavaScript:newOnLoad(true);">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
		<?php echo getCloseBtn("JavaScript:newCancel();","<convert>#label=371<convert>"); ?>
		<div class="frame_title"><?php echo setTitle("#", "filter", "<convert>#label=61<convert>", 3); ?></div><!--Création / Modification-->
<?php
          if ($save_failed) {
?>
		<div class="error"><?php echo getTopBubble(); ?><convert>#label=62<convert><!--Erreur, veuillez recommencer !--><?php echo getBotBubble(); ?></div>
<?php
          }
?>
  	<form id="new_entry" name="new_entry" method="post" action="" onsubmit="JavaScript:newSubmit(event);">
      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><th colspan="2"><?php echo getHelpTopic($helpId[$type], "<convert>#label=23<convert>"); ?></th></tr>
        <tr><td width="170" class="label">
	      	<label for="n_entry_name">
	      		<img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=97<convert><!--Nom de l'entrée--><sup>1</sup>
	      	</label>
        </td><td class="field">
    		  <input class="input1" type="text" id="n_entry_name" name="n_entry_name" value="<?php echo $name; ?>" maxlength="100" onkeyup="JavaScript:checkThisName(this, 'name_pic', 'entry', entryNamesArray);" style="width:100%;" /><br/>
    		  <img class="status1" name="name_pic" id="name_pic" src="../images/icons/wrong.png" alt="image" />
    		  <i><convert>#label=654<convert><!--Ex : Lascaux (Grotte de)--></i>
    		</td></tr><tr><td width="170" class="label">
		      <label for="n_entry_country">
		      	<img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=98<convert><!--Pays-->
	      	</label>
        </td><td class="field">
					<select class="select2" name="n_entry_country" id="n_entry_country">
<?php
          echo getOptionCountry($_SESSION['language'], $country, "<convert>#label=99<convert>");//Sélectionnez un pays ...
?>
					</select>
				</td></tr><tr><td width="170" class="label">
					<label for="n_entry_region">
		      	<convert>#label=100<convert><!--Région-->
	      	</label>
        </td><td class="field">
		      <input class="input1" type="text" id="n_entry_region" name="n_entry_region" value="<?php echo $region; ?>"  size="15" maxlength="32" />
		      <i><convert>#label=665<convert><!--Ex : Rhône (69)--></i>
		    </td></tr><tr><td width="170" class="label">
		      <label for="n_entry_city">
		      	<convert>#label=101<convert><!--Ville-->
	      	</label>
        </td><td class="field">
		      <input class="input1" type="text" id="n_entry_city" name="n_entry_city" value="<?php echo $city; ?>"  size="15" maxlength="32" />
		    </td></tr><!--<tr><td width="170" class="label">
					<label for="n_entry_address">
		      	<convert>#label=102<convert>--><!--Adresse-->
	      	<!--</label>
        </td><td class="field">
		      <input class="input1" type="text" id="n_entry_address" name="n_entry_address" value="<?php echo $address; ?>" size="20" maxlength="128" />
		    </td></tr>--><tr><td width="170" class="label">
					<label for="n_entry_latitude">
		      	<convert>#label=103<convert><!--Latitude--><sup>2</sup>
	      	</label>
        </td><td class="field">
          <b><convert>#label=286<convert></b><br /><!--(GPS) - WGS84 Décimal-->
      	  <input class="input1" type="text" id="n_entry_latitude" name="n_entry_latitude" value="<?php echo $latitude; ?>" size="20" maxlength="128" />
      	  <convert>#label=290<convert><!--°--> <convert>#label=293<convert><!--N-->
      	</td></tr><tr><td width="170" class="label">
					<label for="n_entry_longitude">
		      	<convert>#label=105<convert><!--Longitude--><sup>2</sup>
	      	</label>
        </td><td class="field">
      	  <input class="input1" type="text" id="n_entry_longitude" name="n_entry_longitude" value="<?php echo $longitude; ?>" size="20" maxlength="128" />
      	  <convert>#label=290<convert><!--°--> <convert>#label=294<convert><!--E-->
      	  <!--select class="select2" name="n_entry_system" id="n_entry_system" onchange="JavaScript:systemOnChange(this.form);">
<?php
//            echo getGeodesicOptions();
?>
          </select-->
					&nbsp;&nbsp;<input type="button" name="n_entry_convert" class="button1" value="<convert>#label=31<convert>..." onclick="JavaScript:openConverter(this.form);" /><!--Convertisseur...-->
      	</td></tr><tr><td width="170" class="label">
          <label for="n_entry_altitude">
		      	<convert>#label=106<convert><!--Altitude-->
	      	</label>
        </td><td class="field">
      	  <input class="input1" type="text" id="n_entry_altitude" name="n_entry_altitude" value="<?php echo $altitude; ?>" size="20" maxlength="128" />
      	  <convert>#label=66<convert><!--mètres.-->
      	</td></tr><tr><td width="170" class="label">
          <label for="n_entry_locate">
		      	<convert>#label=107<convert><!--Indiquer sa position sur la carte--><sup>3</sup>
	      	</label>
        </td><td class="field">
		      <input class="button1" type="button" id="n_entry_locate" name="n_entry_locate" onclick="JavaScript:newShowMe();" value="<convert>#label=108<convert>" /><!--Cliquez ici-->
		    </td></tr><tr><td width="170" class="label">
		      <label for="n_entry_birth">
		      	<convert>#label=109<convert><!--Année de découverte-->
	      	</label>
        </td><td class="field">
      	  <input class="input1" type="text" id="n_entry_birth" name="n_entry_birth" value="<?php echo $birth; ?>" size="4" maxlength="4" />
      	  <i><convert>#label=110<convert><!--AAAA--></i>
      	</td></tr><tr><td width="170" class="label">
					<label for="n_entry_public2">			      	
		      	<convert>#label=111<convert><!--Accès aux informations sur cette entrée-->
	      	</label>
        </td><td class="field">
      	  <input class="input1" type="radio" id="n_entry_public2" name="n_entry_public" value="YES" onclick="JavaScript:publicOnClick(this.value);" style="border: none;" <?php if ($isPublic=='YES' || $isPublic=='') { echo 'checked="checked"'; } ?> />
      	  <convert>#label=113<convert><!--Tout le monde (publique).-->
      	</td></tr><tr><td width="170" class="label">
		      <label for="n_entry_public1">
		      </label>
        </td><td class="field">
      	  <input class="input1" type="radio" id="n_entry_public1" name="n_entry_public" value="NO" onclick="JavaScript:publicOnClick(this.value);" style="border: none;" <?php if ($isPublic=='NO') { echo 'checked="checked"'; } ?> />
      	  <convert>#label=112<convert><!--Inscrits (cavité sensible et/ou à accès réglementé)-->
      	</td></tr>
      	
        <tr><td colspan="2">
      	<div id="sensitive" style="margin:-2px 0px -2px -5px">
      	<table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
          <tr><td width="170" class="label">
  					<label for="n_entry_sensitive1">
  	      	</label>
          </td><td class="field">
        	  <input class="input1" type="radio" id="n_entry_sensitive1" name="n_entry_sensitive" value="NO" onclick="JavaScript:sensitiveOnClick(this.value);" style="border: none;" <?php if ($isSensitive=='NO' || $isSensitive=='') { echo 'checked="checked"'; } ?> />
        	  <convert>#label=739<convert><!--Accès libre-->
        	</td></tr><tr><td width="170" class="label">
  					<label for="n_entry_sensitive2">
  	      	</label>
          </td><td class="field">
        	  <input class="input1" type="radio" id="n_entry_sensitive2" name="n_entry_sensitive" value="YES" onclick="JavaScript:sensitiveOnClick(this.value);" style="border: none;" <?php if ($isSensitive=='YES') { echo 'checked="checked"'; } ?> />
        	  <convert>#label=740<convert><!--Cavité sensible et/ou à accès réglementé-->
        	</td></tr>
        	
          <tr><td colspan="2">
        	<div id="sensitiveinfo" style="margin:-2px 0px -2px -5px">
        	<table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
            <tr><td width="170" class="label">
    					<label for="n_entry_contact">
                <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=741<convert><!--Entité/personne à contacter-->
    	      	</label>
            </td><td class="field">
          	  <input class="input1" type="text" id="n_entry_contact" name="n_entry_contact" value="<?php echo $contact; ?>" />
          	</td></tr><tr><td width="170" class="label">
    					<label for="n_entry_modalities">
                <convert>#label=742<convert><!--Accès à la cavité selon les modalités suivantes-->
    	      	</label>
            </td><td class="field">
          	  <input class="input1" style="border:0px none;" type="checkbox" id="n_entry_key" name="n_entry_key" <?php if($modalities_key=="YES" || $modalities_key=="on"){echo "checked=\"checked\"";} ?> /> <convert>#label=743<convert><!--Clé.--><br />
          	  <input class="input1" style="border:0px none;" type="checkbox" id="n_entry_stack" name="n_entry_stack" <?php if($modalities_stack=="YES" || $modalities_stack=="on"){echo "checked=\"checked\"";} ?> /> <convert>#label=744<convert><!--Liste d'attente.--><br />
          	  <input class="input1" style="border:0px none;" type="checkbox" id="n_entry_applic" name="n_entry_applic" <?php if($modalities_applic=="YES" || $modalities_applic=="on"){echo "checked=\"checked\"";} ?> /> <convert>#label=745<convert><!--Demande écrite.--><br />
          	  <input class="input1" style="border:0px none;" type="checkbox" id="n_entry_escort" name="n_entry_escort" <?php if($modalities_escort=="YES" || $modalities_escort=="on"){echo "checked=\"checked\"";} ?> /> <convert>#label=746<convert><!--Accompagnement.-->
          	</td></tr>
          </table>
          </div>
          </td></tr>
        	
        </table>
        </div>
        </td></tr>
        
        <tr><td width="170" class="label">
          <label for="n_entry_type">
            <convert>#label=114<convert><!--Type de sous-sol-->
	      	</label>
        </td><td class="field">
          <select class="select2" name="n_entry_type" id="n_entry_type">
<?php
          $sql = "SELECT Id AS value, ".$_SESSION['language']."_type AS type FROM `".$_SESSION['Application_host']."`.`T_type` ORDER BY type";
          $msg = "<convert>#label=115<convert>";//Sélectionnez un type ...
          $comparedCol = "value";
          $textCol = "type";
          $selected = $cave_type;
          echo getOptions($sql, $msg, $selected, $comparedCol, $textCol);
?>
          </select>
        </td></tr><tr><td width="170" class="label">
		      <label for="u_myList">
		      	<convert>#label=670<convert><!--Sites partenaires-->
	      	</label>
        </td><td class="field">
          <select style="width:100%;" name="u_myList" id="u_myList" size="3" multiple="multiple" ondblclick="JavaScript:urlRemove();">
<?php
if ($isNew == "False") {
          $sql = "SELECT u.Id AS value, u.Name AS text ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_url` u ";
          $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_entry_url` eu ON eu.Id_url = u.Id ";
          $sql .= "WHERE eu.Id_entry = ".$id." ";
          $sql .= "ORDER BY text ";
          $msg = "";
          $comparedCol = "value";
          $selected = "#";
          $textCol = "text";
          echo getOptions($sql, $msg, $selected, $comparedCol, $textCol);
} ?>
          </select>
		    </td></tr><tr><td width="170" class="label">
        </td><td class="field">
          <input type="button" class="button1" id="add" name="add" value="<convert>#label=74<convert>" onclick="JavaScript:urlAdd();" /><!--    Ajouter à ma liste  /\-->
          <input type="button" class="button1" id="remove" name="remove" value="<convert>#label=73<convert>" onclick="JavaScript:urlRemove();" /><!--\/  Retirer de ma liste    -->
        </td></tr><tr><td width="170" class="label">
          <label for="n_entry_netw0">
		      	<convert>#label=116<convert><!--Cette entrée fait partie d'un réseau-->
	      	</label>
        </td><td class="field">
      	  <input class="input1" type="radio" id="n_entry_netw0" name="n_entry_netw" value="NO" onclick="JavaScript:newNetwOnClick(this.value);" style="border: none;" <?php if ($isNetworked=='NO' || $isNetworked=='') { echo 'checked="checked"'; } ?> />
      	  <convert>#label=117<convert><!--non.-->
      	</td></tr><tr><td width="170" class="label">
          <label for="n_entry_netw1">
          </label>
        </td><td class="field">
      	  <input class="input1" type="radio" id="n_entry_netw1" name="n_entry_netw" value="YES" onclick="JavaScript:newNetwOnClick(this.value);" style="border: none;" <?php if ($isNetworked=='YES') { echo 'checked="checked"'; } ?> />
      	  <convert>#label=118<convert><!--oui.-->
      	</td></tr>
      	
      	<tr><td colspan="2">
      	<div id="network" style="margin:-2px 0px -2px -5px">
      	<table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        
          <tr><td width="170" class="label">
            <label for="n_selected_cave">
			      	<img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=119<convert><!--Réseau-->
            </label>
          </td><td class="field">
	      	  <input class="input1" type="radio" id="n_cave0" name="n_cave" value="NO" onclick="JavaScript:newCaveOnClick(this.value)" style="border: none;" <?php if ($isNewNetwork=='NO' || $isNewNetwork=='') { echo 'checked="checked"'; } ?> />
	      	  <convert>#label=120<convert><!--existant--> :
            <select class="select2" name="n_selected_cave" id="n_selected_cave" onclick="JavaScript:newCaveOnClick(xtdGetElementById('n_cave0').value)">
<?php
            $sql = "SELECT Id AS value, Name FROM `".$_SESSION['Application_host']."`.`T_cave` ORDER BY Name";
            $msg = "<convert>#label=121<convert>";//Sélectionnez un réseau ...
            $comparedCol = "value";
            $textCol = "Name";
            $selected = $cave_id;
            echo getOptions($sql, $msg, $selected, $comparedCol, $textCol);
?>
            </select>
          </td></tr><tr><td width="170" class="label">
            <label for="n_cave1">
            </label>
          </td><td class="field">
	      	  <input class="input1" type="radio" id="n_cave1" name="n_cave" value="YES" onclick="JavaScript:newCaveOnClick(this.value)" style="border: none;" <?php if ($isNewNetwork=='YES') { echo 'checked="checked"'; } ?> />
	      	  <convert>#label=122<convert><!--nouveau.-->
	      	</td></tr>
	      	
	      	<tr><td colspan="2">
	      	<div id="new_network" style="margin:-2px 0px -2px -5px">
          <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
          
            <tr><td width="170" class="label">
              <label for="n_cave_name">
                <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=63<convert><!--Nom du réseau--><sup>4</sup>
              </label>
            </td><td class="field">
      		    <input class="input1" type="text" id="n_cave_name" name="n_cave_name" value="<?php echo $cave_name; ?>" size="15" maxlength="36" onkeyup="JavaScript:checkThisName(this, 'name_ntw_pic', 'cave', caveNamesArray);" />
      		    <img class="status1" name="name_ntw_pic" id="name_ntw_pic" src="../images/icons/wrong.png" alt="image" />
      		  </td></tr>
      		  
          </table>
          </div>
          </td></tr>
            
        </table>
        </div>
        </td></tr>
        
        <tr><td colspan="2">
        <div id="cave_properties" style="margin:-2px 0px -2px -5px">
        <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
          
          <tr><td width="170" class="label">
            <label for="n_cave_depth">
    			    <convert>#label=64<convert><!--Profondeur-->
	      	  </label>
          </td><td class="field">
	      		<input class="input1" type="text" id="n_cave_depth" name="n_cave_depth" value="<?php echo $cave_depth; ?>" size="8" maxlength="36" />
	      		<convert>#label=66<convert><!--mètres.-->
	      	</td></tr><tr><td width="170" class="label">
            <label for="n_cave_length">
			      	<convert>#label=68<convert><!--Développement-->
	      	  </label>
          </td><td class="field">
    		    <input class="input1" type="text" id="n_cave_length" name="n_cave_length" value="<?php echo $cave_length; ?>" size="8" maxlength="36" />
    		    <convert>#label=66<convert><!--mètres.-->
    		  </td></tr><tr><td width="170" class="label">
            <label for="n_cave_temperature">
			      	<convert>#label=69<convert><!--Température moyenne-->
	      	  </label>
          </td><td class="field">
    		    <input class="input1" type="text" id="n_cave_temperature" name="n_cave_temperature" value="<?php echo $cave_temperature; ?>" size="8" maxlength="36" />
    		    <convert>#label=70<convert><!--degrés celsius.-->
    		  </td></tr><tr><td width="170" class="label">
            <label for="n_cave_diving">
            	<convert>#label=71<convert><!--Spéléo. plongée-->
            </label>
          </td><td class="field">
      		  <input class="input1" style="border:0px none;" type="checkbox" id="n_cave_diving" name="n_cave_diving" <?php if($cave_diving=="YES" || $cave_diving=="on"){echo "checked=\"checked\"";} ?> />
      		</td></tr><tr><td width="170" class="label">
            <label for="n_cave_mas0">
			      	<convert>#label=558<convert><!--Cette entrée fait partie d'un massif-->
	      	  </label>
          </td><td class="field">
	      	  <input class="input1" type="radio" id="n_cave_mas0" name="n_cave_mas" value="NO" onclick="JavaScript:newMassifedOnClick(this.value);" style="border: none;" <?php if ($isMassifed=='NO' || $isMassifed=='') { echo 'checked="checked"'; } ?> />
	      	  <convert>#label=117<convert><!--non.-->
	      	</td></tr><tr><td width="170" class="label">
            <label for="n_cave_mas1">
            </label>
          </td><td class="field">
	      	  <input class="input1" type="radio" id="n_cave_mas1" name="n_cave_mas" value="YES" onclick="JavaScript:newMassifedOnClick(this.value);" style="border: none;" <?php if ($isMassifed=='YES') { echo 'checked="checked"'; } ?> />
	      	  <convert>#label=118<convert><!--oui.-->
	      	</td></tr>
	      	
	      	<tr><td colspan="2">
	      	<div id="massif" style="margin:-2px 0px -2px -5px">
          <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
          
            <tr><td width="170" class="label">
              <label for="n_selected_massif">
  			      	<img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=555<convert><!--Massif-->
	      	    </label>
            </td><td class="field">
		      	  <input class="input1" type="radio" id="n_massif0" name="n_massif" value="NO" onclick="JavaScript:newMassifOnClick(this.value)" style="border: none;" <?php if ($isNewMassif=='NO' || $isNewMassif=='') { echo 'checked="checked"'; } ?> />
		      	  <convert>#label=120<convert><!--existant--> : 
              <select class="select2" name="n_selected_massif" id="n_selected_massif" onclick="JavaScript:newMassifOnClick(xtdGetElementById('n_massif0').value)">
<?php
              $sql = "SELECT Id AS value, Name FROM `".$_SESSION['Application_host']."`.`T_massif` ORDER BY Name";
              $msg = "<convert>#label=556<convert>";//Sélectionnez un massif ...
              $comparedCol = "value";
              $textCol = "Name";
              $selected = $massif_id;
              echo getOptions($sql, $msg, $selected, $comparedCol, $textCol);
?>
              </select>
            </td></tr><tr><td width="170" class="label">
              <label for="n_massif1">
              </label>
            </td><td class="field">
		      	  <input class="input1" type="radio" id="n_massif1" name="n_massif" value="YES" onclick="JavaScript:newMassifOnClick(this.value)" style="border: none;" <?php if ($isNewMassif=='YES') { echo 'checked="checked"'; } ?> />
		      	  <convert>#label=122<convert><!--nouveau.-->
		      	</td></tr>
		      	
		      	<tr><td colspan="2">
		      	<div id="new_massif" style="margin:-2px 0px -2px -5px">
            <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
            
              <tr><td width="170" class="label">
                <label for="n_massif_name">
                  <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=551<convert><!--Nom du massif--> <sup>4</sup>
                </label>
              </td><td class="field">
  		      		<input class="input1" type="text" id="n_massif_name" name="n_massif_name" value="<?php echo $massif_name; ?>" size="15" maxlength="36" onkeyup="JavaScript:checkThisName(this, 'name_mas_pic', 'massif', massifNamesArray);" />
  		      		<img class="status1" name="name_mas_pic" id="name_mas_pic" src="../images/icons/wrong.png" alt="image" />
  		      	</td></tr>
            </table>
            </div>
            </td></tr>
            
          </table>
          </div>
          </td></tr>
            
        </table>
        </div>
        </td></tr>
        
    		<tr><td width="170" class="label">
          <label for="save">
	      	</label>
        </td><td class="field">
          <input type="hidden" id="u_list" name="u_list" />
          <input type="hidden" id="is_new" name="is_new" value="<?php echo $isNew; ?>" />
      		<input type="hidden" id="entry_id" name="entry_id" value="<?php echo $id; ?>" />
      		<input type="hidden" id="n_old_cave" name="n_old_cave" value="<?php echo $cave_id; ?>" />
      		<input type="hidden" id="n_old_massif" name="n_old_massif" value="<?php echo $massif_id; ?>" />
	        <input class="button1" type="submit" id="save" name="save" value="<convert>#label=76<convert>" /><!--Valider-->
	      </td></tr><tr><td width="170" class="label">
          <label for="cancel">
	      	</label>
        </td><td class="field">
	        <input class="button1" onclick="JavaScript:newCancel();" type="button" id="cancel" name="cancel" value="<convert>#label=77<convert>" /><!--Annuler-->
        </td></tr><tr><td colspan="2">
          <div class="notes">
            <?php echo getTopBubble(); ?>
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=78<convert><!--Champs nécessaires.--><br />
            <sup>1</sup> <convert>#label=123<convert><!--Le nom--> <convert>#label=874<convert><!--doit être composé de 2 à 100 caractères sauf--> <b>|</b> <b>/</b> <b>\</b> <b>"</b> <b>#</b> <b>&amp;</b> <b>+</b> <convert>#label=46<convert><!--et--> <b>¨</b><br />
            <sup>2</sup> <convert>#label=124<convert><!--Les coordonnées seront prioritaires sur l'adresse pour le positionnement de l'entrée, effacez les si vous désirez la positionner selon l'adresse.--><br />
            <convert>#label=125<convert><!--Clickez droit à l'endroit désiré sur la carte, puis cliquez sur "Utiliser ces coordonnées" pour alimenter automatiquemen ces champs.--><br />
            <sup>3</sup> <convert>#label=126<convert><!--Faites glisser le marqueur sur la carte avec votre souris pour le positionner plus précisément !--><br />
						<sup>4</sup> <convert>#label=123<convert><!--Le nom--> <convert>#label=873<convert><!--doit être composé de 2 à 36 caractères sauf--> <b>|</b> <b>/</b> <b>\</b> <b>"</b> <b>#</b> <b>&amp;</b> <b>+</b> <convert>#label=46<convert><!--et--> <b>¨</b>
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
  	<div class="error"><?php echo getTopBubble(); ?><convert>#label=81<convert><!--Cas non traité !--><?php echo getBotBubble(); ?></div>
<?php
    	break;
    }
?>
    <?php echo getBotFrame(); ?>
<?php
    $virtual_page = "entry/".$type."/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>