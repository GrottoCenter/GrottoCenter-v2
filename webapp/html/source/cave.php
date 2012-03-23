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
if (!allowAccess(cave_view_all)){ 
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
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=33<convert></title>
    <link rel="stylesheet" type="text/css" href="../css/filter.css" />
    <link rel="stylesheet" type="text/css" href="../css/portlet.css" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
<?php
  	//Variables init
    $type = (isset($_GET['type'])) ? $_GET['type'] : '';
    $parameters = "";
    $locked = false;
    $backPage = (isset($_GET['back'])) ? $_GET['back'] : "cave";
    $did = "-1";
    $save_failed = false;
    $name = '';
    //$min_depth = '';
    //$max_depth = '';
    $depth = '';
    $length = '';
    $temperature = '';
    $diving = '';
    $isNew = '';
    $id = '';
    $list = '';
    $isMassifed = '';
    $old_massif_id = '';
    $massif_id = '';
    $cave_name = '';
    $isNewMassif = '';
    $helpId = array("edit" => 4);
		$track_array = array('table' => array('T_cave', 'J_cave_entry', 'J_massif_cave'),
												'column' => array('Id', 'Id_cave', 'Id_cave'));
    
    
  if (allowAccess(cave_delete_all)) {
    //Delete the element
    if (isset($_POST['delete'])){
      $did = (isset($_POST['delete_id'])) ? $_POST['delete_id'] : '';
      if ($did != "") {
        trackAction("delete_cave", $did, $track_array);
        $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`T_cave` WHERE Id = ".$did;
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_cave_entry` WHERE Id_cave = ".$did;
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_massif_cave` WHERE Id_cave = ".$did;
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
      if (takeOver("cave",$did) && $did != "") {
        $sql = "SELECT Name FROM T_cave WHERE Id = ".$did;
        $name = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
        $name = $name[0]['Name'];
        $parameters = "&cancel=True&cid=".$did."&ccat=cave";
      } else {
        $locked = true;
        $type = "menu";
      }
    }
  }

  if (allowAccess(cave_edit_all)) {
    if (isset($_POST['save'])){
      $save_failed = true;
      $name = (isset($_POST['n_cave_name'])) ? $_POST['n_cave_name'] : '';
      //$min_depth = (isset($_POST['n_cave_min_depth'])) ? $_POST['n_cave_min_depth'] : '';
      //$max_depth = (isset($_POST['n_cave_max_depth'])) ? $_POST['n_cave_max_depth'] : '';
      $depth = (isset($_POST['n_cave_depth'])) ? $_POST['n_cave_depth'] : '';
      $length = (isset($_POST['n_cave_length'])) ? $_POST['n_cave_length'] : '';
      $temperature = (isset($_POST['n_cave_temperature'])) ? $_POST['n_cave_temperature'] : '';
      $diving = (isset($_POST['n_cave_diving'])) ? $_POST['n_cave_diving'] : '';
      $isNew = (isset($_POST['is_new'])) ? $_POST['is_new'] : '';
      $id = (isset($_POST['cave_id'])) ? $_POST['cave_id'] : '';
      $list = (isset($_POST['e_list'])) ? $_POST['e_list'] : '';
      $isMassifed = (isset($_POST['n_cave_mas'])) ? $_POST['n_cave_mas'] : '';
      $old_massif_id = (isset($_POST['n_old_massif'])) ? $_POST['n_old_massif'] : '';
      $massif_id = (isset($_POST['n_selected_massif'])) ? $_POST['n_selected_massif'] : '';
      $cave_name = (isset($_POST['n_massif_name'])) ? $_POST['n_massif_name'] : '';
      $isNewMassif = (isset($_POST['n_massif'])) ? $_POST['n_massif'] : '';
      if ($isNew == "True") {
        $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`T_cave` ";
        //$sql .= "(`Id_author`, `Name`, `Min_depth`, `Max_depth`, `Length`, `Is_diving`, `Temperature`, `Date_inscription`)";
        $sql .= "(`Id_author`, `Name`, `Depth`, `Length`, `Is_diving`, `Temperature`, `Date_inscription`)";
        $sql .= " VALUES (";
        $sql .= $_SESSION['user_id'].", ";
        $sql .= returnDefault($name, 'text').", ";
        //$sql .= returnDefault($min_depth, 'float').", ";
        //$sql .= returnDefault($max_depth, 'float').", ";
        $sql .= returnDefault($depth, 'float').", ";
        $sql .= returnDefault($length, 'float').", ";
        $sql .= returnDefault($diving, 'checkbox').", ";
        $sql .= returnDefault($temperature, 'float').", ";
        $sql .= "Now()) ";
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        $nid = $req['mysql_insert_id'];
        trackAction("insert_cave",$nid,"T_cave");
      } else {
        trackAction("edit_cave", $id, $track_array);
        $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_cave` ";
        $sql .= " SET ";
        $sql .= "Locked = 'NO', ";
        $sql .= "Id_reviewer = ".$_SESSION['user_id'].", ";
        $sql .= "Name = ".returnDefault($name, 'text').", ";
        //$sql .= "Min_depth = ".returnDefault($min_depth, 'float').", ";
				//$sql .= "Max_depth = ".returnDefault($max_depth, 'float').", ";
				$sql .= "Depth = ".returnDefault($depth, 'float').", ";
        $sql .= "Length = ".returnDefault($length, 'float').", ";
        $sql .= "Is_diving = ".returnDefault($diving, 'checkbox').", ";
        $sql .= "Temperature = ".returnDefault($temperature, 'float').", ";
        $sql .= "Date_reviewed = Now() ";
        $sql .= "WHERE Id = ".$id;
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
      	$sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_cave_entry` ";
      	$sql .= "WHERE `Id_cave` = ".$id;
      	$req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
      }
    	if ($isNew == "True") {
    		$onid = $nid;
    	} else {
    		$onid = $id;
    	}
      if ($list != "") {
        $arrList = split('[|]+', $list);
        
        //Establish the relationship between entries and this cave
        $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`J_cave_entry` (`Id_cave`, `Id_entry`) VALUES ";
        foreach($arrList as $value) {
          $sql .= "(".$onid.", ".$value."), ";
        }
        $sql = substr($sql,0,strlen($sql)-2);
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        
        //Reset possible relationship between entries and massifs
      	$sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_massif_cave` ";
      	$sql .= "WHERE `Id_entry` IN (";
        foreach($arrList as $value) {
          $sql .= $value.", ";
        }
        $sql = substr($sql,0,strlen($sql)-2);
        $sql .= ")";
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
      }
      $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_massif_cave` WHERE Id_massif = '".$old_massif_id."' AND Id_cave = ".$onid;
      $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
      if ($isMassifed == "YES") {
        if ($isNewMassif == "YES") {
          $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`T_massif` ";
          $sql .= "(`Id_author`, `Name`, `Date_inscription`)";
          $sql .= " VALUES (";
          $sql .= $_SESSION['user_id'].", ";
          $sql .= returnDefault($cave_name, 'text').", ";
          $sql .= "Now()) ";
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $massif_id = $req['mysql_insert_id'];
          trackAction("insert_massif",$massif_id,"T_massif");
        }
        $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`J_massif_cave` (`Id_massif`, `Id_cave`, `Id_entry`) VALUES (";
        $sql .= returnDefault($massif_id, 'text').", ";
        $sql .= returnDefault($onid, 'text').", ";
        $sql .= "0) ";
        $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
      }
      $save_failed = false;
      $type = "menu";
    } else {
      if (isset($_GET['id'])) {
        $id = (isset($_GET['id'])) ? $_GET['id'] : '';
        if (takeOver("cave",$id) && $id != "") {
          $sql = "SELECT ca.*, mc.Id_massif FROM `".$_SESSION['Application_host']."`.`T_cave` ca "; 
          $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_massif_cave` mc ON ca.Id = mc.Id_cave "; 
          $sql .= "WHERE ca.Id = ".$id;
          $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
          if ($data['Count'] > 0) {
            $name = $data[0]['Name'];
            //$min_depth = $data[0]['Min_depth'];
            //$max_depth = $data[0]['Max_depth'];
            $depth = $data[0]['Depth'];
            $length = $data[0]['Length'];
            $diving = $data[0]['Is_diving'];
            $temperature = $data[0]['Temperature'];
            $massif_id = $data[0]['Id_massif'];
            if ($data[0]['Id_massif'] == "") {
              $isMassifed = "NO";
            } else {
              $isMassifed = "YES";
            }
            $isNewMassif = "NO";
            $isNew = "False";
            $cave_id = $id;
          }
          $parameters = "&cancel=True&cid=".$id."&ccat=cave";
        } else {
          $locked = true;
          $type = "menu";
        }
      } else {
        $isNew = "True";
        $cave_id = "-1";
      }
    }
  }
?>
    <script type="text/javascript" src="../scripts/classeGCTest.js"></script>
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
    var doCancel = true;
    var caveNamesArray = new Array();
    var massifNamesArray = new Array();
    <?php include("../scripts/events.js"); ?>

<?php
switch ($type) {
	case "menu":
?>
    function menuBeforeLoad() {
      parent.setFilterSize(400, "px");
    }
    
    function menuOnLoad() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      var oHtml = document.getElementsByTagName('HTML')[0];
      parent.setFilterSizeTight(oHtml);
      <?php if (isset($_POST['did'])) { ?>
      reload(false);
      <?php } ?>
    }
    
    function caveEdit(oForm) {
      var oRadio = oForm.radio_list;
      var caveId = getRadioValue(oRadio);
      if (caveId) {
        self.location.href = "cave_<?php echo $_SESSION['language']; ?>.php?type=edit&id=" + caveId;
      }
    }
    
    function caveRefresh(oForm) {
      oForm.submit();
    }
    
    function caveDelete(oForm) {
      var oRadioArray = oForm.radio_list;
      var caveId = getRadioValue(oRadioArray);
      if (caveId) {
        deleteMarker("cave", caveId, "<?php echo $_SESSION['language']; ?>");
      }
    }
    
    function caveOnClick(e, id) {
      detailMarker(e, 'cave', id, '<?php echo $_SESSION['language']; ?>', false);
    }
<?php
	break;
	case "delete":
?>
    function deleteOnLoad() {
      var oForm = document.delete_cave;
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
      var oForm = document.new_cave;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      mySite.details.switchDetails(true);
      /*var myList = getMyList(listAllEntries, '<?php echo $cave_id; ?>');
    	var otherList = getOtherList(listAllEntries, myList, '<?php echo $cave_id; ?>');
    	fillSelect(myList,oForm.e_myList);
    	fillSelect(otherList,oForm.e_otherList);
    	sortSelect(oForm.e_myList);
    	sortSelect(oForm.e_otherList);
    	enableMove(oForm, oForm.e_myList, oForm.e_otherList);*/
      if (hasFailed) {
        var oRadio = oForm.n_massif;
        newMassifOnClick(getRadioValue(oRadio));
        oRadio = oForm.n_cave_mas;
        newMassifedOnClick(getRadioValue(oRadio));
        parent.setFilterSize(getMaxWidthInput(oForm),"px");
        var oHtml = document.getElementsByTagName('HTML')[0];
        parent.setFilterSizeTight(oHtml);
        parent.overview.hideId('reload');
        caveNamesArray = loadNames("cave");
        massifNamesArray = loadNames("massif");
        checkThisName(oForm.n_cave_name, 'name_pic', 'cave', caveNamesArray);
      } else {
        reload(false);
      }
    }
    
    function selectOnClick(e, oSelect) {
      var Id = oSelect.options[oSelect.selectedIndex].value;
      document.body.focus();
    	var Category = "entry";
    	openMe(Id, Category, false);
      detailMarker(e, Category, Id, '<?php echo $_SESSION['language']; ?>', false);
  	}

  	function entryRemove() {
      var oForm = document.new_cave;
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
      //var url = "<?php echo $_SESSION['Application_url']; ?>/html/pick_window_<?php echo $_SESSION['language']; ?>.php?type=entry&callback=addEntry";
      url = "pick_window_<?php echo $_SESSION['language']; ?>.php?type=entry&callback=addEntry";
      openWindow(url, windowName, 1025, 520);
  	}
	
  	function addEntry(oForm) {
      var uForm = document.new_cave;
      addOptionsFromSelection(oForm, uForm.e_myList);
    }
    
    function newSubmit(event) {
      var oForm = document.new_cave;
      var rightSource = toAbsURL(rightPic);
      var oField = xtdGetElementById('name_pic');
      var sMessage = "<convert>#label=88<convert> <convert>#label=873<convert> | / \\ ' \" & + <convert>#label=46<convert> ¨";//Le nom du réseau doit être composé de 2 à 36 caractères sauf //et
      createTest(oField.name, oField.src, rightSource, "==", sMessage, true);
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
      	doChallengeList(oForm.e_myList,oForm.e_list);
        doCancel = false;
      } 
    }
    
    function checkThisName(oObject, namePic, category, namesArray, sName) {
      if (category == "cave") {
        sName = "<?php echo $name; ?>";
      } else {
        sName = "";
      }
      checkName(oObject, namePic, category, sName, namesArray, false); //put the last parameter to false
      displayCloseNames(oObject, getMatchingValues(oObject.value, namesArray, sName), '<convert>#label=844<convert>'); //Noms existants déjà en base :
    }
    
    function newMassifOnClick(sValue) {
      var oForm = document.new_cave;
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
        var oForm = document.new_cave;
        var oRadio = oForm.n_massif;
        newMassifOnClick(getRadioValue(oRadio));
      } else {
        hideId('massif');
      }
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
    function caveNew() {
      self.location.href = "cave_<?php echo $_SESSION['language']; ?>.php?type=edit";
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
		<?php echo getCloseBtn("filter_".$_SESSION['language'].".php","<convert>#label=371<convert>"); ?><!-- Fermer -->
		<div class="frame_title"><?php echo setTitle("cave_".$_SESSION['language'].".php?type=menu", "filter", "<convert>#label=377<convert>", 2); ?></div><!--Menu des cavités (convert 48 remplacé)-->
<?php
      if ($locked) {
?>
  	<div class="error"><?php echo getTopBubble(); ?><convert>#label=84<convert> <convert>#label=49<convert><?php echo getBotBubble(); ?></div><!--Ce réseau est en cours de modification par un autre utilisateur, veuillez essayer plus tard !-->
<?php
      } else {
        if (isset($_POST['save']) && !$save_failed){
?>
    <div class="warning"><?php echo getTopBubble(); ?><convert>#label=35<convert> <convert>#label=50<convert><!--Le réseau a été enregistré avec succès !--><?php echo getBotBubble(); ?></div>
<?php
        }
        if (isset($_POST['delete'])) {
          if ($delete_failed){
?>
  	<div class="error"><?php echo getTopBubble(); ?><convert>#label=840<convert><!--Vous n'êtes pas autorisé à supprimer--> <convert>#label=84<convert><!--ce réseau--><?php echo getBotBubble(); ?></div>
<?php
          } else {
?>
    <div class="warning"><?php echo getTopBubble(); ?><convert>#label=35<convert> <convert>#label=609<convert><!--Le réseau a été supprimé avec succès !--><?php echo getBotBubble(); ?></div>
<?php
          }
        }
      }
?>

<?php
    $sql = "SELECT DISTINCT ";
    $sql .= "T_cave.Id AS `0`, ";
    $sql .= "IF((T_cave.Locked = 'YES' AND NOT T_cave.Id_locker = '".$_SESSION['user_id']."'),1,0) AS `1`, ";
    $sql .= "CONCAT_WS('',CONCAT_WS(' /!\\\\ ',T_cave.Name,IF(T_cave.Locked = 'YES','<convert>#label=42<convert>',NULL)),'[|]',IF((T_cave.Locked = 'YES' AND T_cave.Id_locker = '".$_SESSION['user_id']."'),'".str_replace("'","''","<convert>#label=573<convert>")."',NULL),'[|]',GROUP_CONCAT(DISTINCT T_country.".$_SESSION['language']."_name ORDER BY T_country.".$_SESSION['language']."_name SEPARATOR ', ')) AS `2`, ";//Nom du réseau //Pays
    $sql .= "T_cave.Name AS `3`, ";//Nom du réseau
    $sql .= "T_country.".$_SESSION['language']."_name AS `4`, ";//Pays
    $sql .= "IF((T_cave.Locked = 'YES' AND T_cave.Id_locker = '".$_SESSION['user_id']."'),1,0) AS `5` ";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_cave` ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_cave_entry` ON J_cave_entry.Id_cave = T_cave.Id ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_entry` ON T_entry.Id = J_cave_entry.Id_entry ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_country` ON T_country.Iso = T_entry.Country ";
    $sql .= "GROUP BY T_cave.Id ";
    $columns_params = array(
			0 => "[hidden]|[hidden]Id",
			1 => "[hidden]|[hidden]Locked",
			2 => "[hidden]|<convert>#label=63<convert><br /><convert>#label=98<convert>",
			3 => "T_cave*Name|[hidden]<convert>#label=63<convert>",
			4 => "T_entry*Country|[hidden]<convert>#label=98<convert>|SELECT Iso AS value,".$_SESSION['language']."_name AS text FROM T_country ORDER BY text",
			5 => "[hidden]|[hidden]5"
		);
    $param_link = "JavaScript:caveOnClick(event,'<Id>');";
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
                    5 => '1')));
    $default_order = 3;
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
      <form id="<?php echo $filter_form; ?>" name="<?php echo $filter_form; ?>" method="post" action="cave_<?php echo $_SESSION['language']; ?>.php?type=menu">
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
        <div class="notes">
          <?php echo getTopBubble(); ?>
          <convert>#label=59<convert><!--Si le réseau que vous cherchez n'est pas dans--> <?php echo $_SESSION['Application_name']; ?>, <a href="JavaScript:caveNew();"><convert>#label=60<convert><!--créez le--></a> !
          <?php echo getBotBubble(); ?>
        </div>
<?php
    if (allowAccess(cave_edit_all)) {
?>
        <input type="button" class="button1" id="edit_cave" name="edit_cave" value="<convert>#label=53<convert>" onclick="JavaScript:caveEdit(this.form);" /><!--Modifier--><br />
        <input type="button" class="button1" id="new_cave" name="new_cave" value="<convert>#label=54<convert>" onclick="JavaScript:caveNew();" /><!--Nouveau--><br />
<?php
    }
    if (allowAccess(cave_delete_all)) {
?>
        <input type="button" class="button1" id="del_cave" name="del_cave" value="<convert>#label=55<convert>" onclick="JavaScript:caveDelete(this.form);" /><!--Supprimer--><br />
<?php
    }
?>
        <input type="button" class="button1" id="refresh_cave" name="refresh_cave" value="<convert>#label=56<convert>" onclick="JavaScript:caveRefresh(document.<?php echo $filter_form; ?>);" /><!--Rafraîchir-->
      </form>
    </div>
<?php
			break;
    	case "delete":
        if (!allowAccess(cave_delete_all)) {
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
    <?php echo getCloseBtn("JavaScript:newCancel();","<convert>#label=371<convert>"); ?><!--Fermer-->
    <div class="frame_title"><?php echo setTitle("#", "filter", "<convert>#label=624<convert>", 3); ?></div><!--Suppression d'un réseau-->
    <form id="delete_cave" name="delete_cave" method="post" action="">
    	<table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
    	  <tr><td>
    	    <div class="warning"><?php echo getTopBubble(); ?>
    	      <convert>#label=44<convert> <convert>#label=86<convert> <?php echo $name; ?> ?<!--Etes vous sûr de vouloir supprimer le réseau-->
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
        if (!allowAccess(cave_edit_all)) {
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
  	<form id="new_cave" name="new_cave" method="post" action="" onsubmit="JavaScript:newSubmit(event);">
      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><th colspan="2"><?php echo getHelpTopic($helpId[$type], "<convert>#label=23<convert>"); ?></th></tr>
        <tr><td width="170" class="label">
          <label for="n_cave_name">
  			    <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=63<convert><!--Nom du réseau--><sup>1</sup>
          </label>
        </td><td class="field">
      		<input class="input1" type="text" id="n_cave_name" name="n_cave_name" value="<?php echo $name; ?>" size="15" maxlength="36" onkeyup="JavaScript:checkThisName(this, 'name_pic', 'cave', caveNamesArray);" />
      		<img class="status1" name="name_pic" id="name_pic" src="../images/icons/wrong.png" alt="image" />
    		</td></tr><tr><td width="170" class="label">
          <label for="n_cave_depth">
            <convert>#label=64<convert><!--Profondeur-->
          </label>
        </td><td class="field">
      		<input class="input1" type="text" id="n_cave_depth" name="n_cave_depth" value="<?php echo $depth; ?>" size="8" maxlength="36" />
      		<convert>#label=66<convert><!--mètres.-->
    		</td></tr><tr><td width="170" class="label">
          <label for="n_cave_length">
  			    <convert>#label=68<convert><!--Développement-->
          </label>
        </td><td class="field">
      		<input class="input1" type="text" id="n_cave_length" name="n_cave_length" value="<?php echo $length; ?>" size="8" maxlength="36" />
      		<convert>#label=66<convert><!--mètres.-->
    		</td></tr><tr><td width="170" class="label">
          <label for="n_cave_temperature">
		      	<convert>#label=69<convert><!--Température moy.-->
          </label>
        </td><td class="field">
  		    <input class="input1" type="text" id="n_cave_temperature" name="n_cave_temperature" value="<?php echo $temperature; ?>" size="8" maxlength="36" />
  		    <convert>#label=70<convert><!--degrés celsius.-->
    		</td></tr><tr><td width="170" class="label">
          <label for="n_cave_diving">
          	<convert>#label=71<convert><!--Spéléo. plongée-->
          </label>
        </td><td class="field">
      		<input class="input1" style="border: none;" type="checkbox" id="n_cave_diving" name="n_cave_diving" <?php if($diving=="YES" || $diving=="on"){echo "checked=\"checked\"";} ?> />
    		</td></tr><tr><td width="170" class="label">
          <label for="n_cave_mas0">
		      	<convert>#label=554<convert><!--Ce réseau fait partie d'un massif-->
          </label>
        </td><td class="field">
      	  <input class="input1" type="radio" id="n_cave_mas0" name="n_cave_mas" value="NO" onclick="JavaScript:newMassifedOnClick(this.value);" style="border: none;" <?php if ($isMassifed=='NO' || !isset($isMassifed)) { echo 'checked="checked"'; } ?> />
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
	      	  <input class="input1" type="radio" id="n_massif0" name="n_massif" value="NO" onclick="JavaScript:newMassifOnClick(this.value)" style="border: none;" <?php if ($isNewMassif=='NO' || !isset($isNewMassif)) { echo 'checked="checked"'; } ?> />
	      	  <convert>#label=120<convert><!--existant--> : 
            <select class="select1" name="n_selected_massif" id="n_selected_massif" onclick="JavaScript:newMassifOnClick(xtdGetElementById('n_massif0').value)">
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
      			    <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=551<convert><!--Nom du massif--><sup>1</sup>
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
        
        <tr><td class="label" colspan="2" style="text-align:left;">
          <label for="e_myList">
            <b><convert>#label=72<convert><!--Les entrées de ce réseau--> :</b>
          </label>
        </td></tr><tr><td class="field" colspan="2">
          <select style="width:270px;" name="e_myList" id="e_myList" size="10" multiple="multiple" onclick="JavaScript:selectOnClick(event, this);" ondblclick="JavaScript:entryRemove();">
<?php
if ($isNew == "False") {
          $sql = "SELECT ey.Id AS value, ey.Name AS text ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_entry` ey ";
          $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_cave_entry` ce ON ce.Id_entry = ey.Id ";
          $sql .= "WHERE ce.Id_cave = ".$id." ";
          $sql .= "ORDER BY text ";
          $msg = "";
          $comparedCol = "value";
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
      		<input type="hidden" id="is_new" name="is_new" value="<?php echo $isNew; ?>" />
      		<input type="hidden" id="cave_id" name="cave_id" value="<?php echo $id; ?>" />
      		<input type="hidden" id="n_old_massif" name="n_old_massif" value="<?php echo $massif_id; ?>" />
		      <input class="button1" type="submit" id="save" name="save" value="<convert>#label=76<convert>" /><!--Valider-->
        </td></tr><tr><td class="field" colspan="2">
		      <input class="button1" onclick="JavaScript:newCancel();" type="button" id="cancel" name="cancel" value="<convert>#label=77<convert>" /><!--Annuler-->
        </td></tr><tr><td colspan="2">
          <div class="notes">
            <?php echo getTopBubble(); ?>
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=78<convert><!--Champs nécessaires.--><br />
            <sup>1</sup> <convert>#label=88<convert> <convert>#label=873<convert><!--Le nom du réseau--> <!-- doit être composé de 2 à 36 caractères sauf--> <b>|</b> <b>/</b> <b>\</b> <b>"</b> <b>#</b> <b>&amp;</b> <b>+</b> <convert>#label=46<convert><!--et--> <b>¨</b><br />
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
  	<div class="frame_title"><?php echo setTitle("#", "filter", "<convert>#label=80<convert>", 2); ?></div><!--Erreur-->
  	<div class="error"><?php echo getTopBubble(); ?><convert>#label=81<convert><!--Cas non traité !--><?php echo getBotBubble(); ?></div>
<?php
    	break;
    }
?>
    <?php echo getBotFrame(); ?>
<?php
    $virtual_page = "cave/".$type."/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>