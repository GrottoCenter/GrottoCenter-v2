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
if (!allowAccess(massif_view_all)){ 
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
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=547<convert></title>
    <link rel="stylesheet" type="text/css" href="../css/filter.css" />
    <link rel="stylesheet" type="text/css" href="../css/portlet.css" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
<?php
  	//Capture the action type :
    $type = (isset($_GET['type'])) ? $_GET['type'] : '';
    $parameters = "";
    $locked = false;
    $regForCat = "*";
    $helpId = array("edit" => 9);
    
    if (isset($_GET['back'])) {
    	$backPage = (isset($_GET['back'])) ? $_GET['back'] : '';
    } else {
      $backPage = "massif";
    }
    
    if (allowAccess(massif_delete_all)) {
      //Delete the element
      if (isset($_POST['delete'])){
        $did = (isset($_POST['delete_id'])) ? $_POST['delete_id'] : '';
        if ($did != "") {
          trackAction("delete_massif",$did,"T_massif");
          $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`T_massif` WHERE Id = ".$did;
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_massif_cave` WHERE Id_massif = ".$did;
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
        if (takeOver("massif",$did) && $did != "") {
          $sql = "SELECT Name FROM T_massif WHERE Id = ".$did;
          $name = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
          $name = $name[0]['Name'];
          $parameters = "&cancel=True&cid=".$did."&ccat=massif";
        } else {
          $locked = true;
          $type = "menu";
        }
      }
    }
  
    if (allowAccess(massif_edit_all)) {
      // Save the massif
      if (isset($_POST['save'])){
        $save_failed = true;
        $name = (isset($_POST['n_massif_name'])) ? $_POST['n_massif_name'] : '';
        $isNew = (isset($_POST['is_new'])) ? $_POST['is_new'] : '';
        $id = (isset($_POST['massif_id'])) ? $_POST['massif_id'] : '';
        $list = (isset($_POST['e_list'])) ? $_POST['e_list'] : '';
        if ($isNew == "True") {
          $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`T_massif` ";
          $sql .= "(`Id_author`, `Name`, `Date_inscription`)";
          $sql .= " VALUES (";
          $sql .= $_SESSION['user_id'].", ";
          $sql .= returnDefault($name, 'text').", ";
          $sql .= "Now()) ";
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $nid = $req['mysql_insert_id'];
          trackAction("insert_massif",$nid,"T_massif");
        } else {
          $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_massif` ";
          $sql .= " SET ";
          $sql .= "Locked = 'NO', ";
          $sql .= "Id_reviewer = ".$_SESSION['user_id'].", ";
          $sql .= "Name = ".returnDefault($name, 'text').", ";
          $sql .= "Date_reviewed = Now() ";
          $sql .= "WHERE Id = ".$id;
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        	$sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_massif_cave` ";
        	$sql .= "WHERE `Id_massif` = ".$id;
        	$req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          trackAction("edit_massif",$id,"T_massif");
        }
        if ($list != "") {
        	if ($isNew == "True") {
        		$onid = $nid;
        	} else {
        		$onid = $id;
        	}
          $arrList = explode("|", $list);
          $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`J_massif_cave` (`Id_massif`, `Id_cave`, `Id_entry`) VALUES ";
          foreach($arrList as $value) {
            $linked_id = explode($regForCat, $value);
            $sql .= "(".$onid.", ".$linked_id[0].", ".$linked_id[1]."), ";
          }
          $sql = substr($sql,0,strlen($sql)-2);
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        }
        $save_failed = false;
        $type = "menu";
      } else {
        if (isset($_GET['id'])) {
          $id = (isset($_GET['id'])) ? $_GET['id'] : '';
          if (takeOver("massif",$id) && $id != "") {
            $sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`T_massif` "; 
            $sql .= "WHERE Id = ".$id;
            $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
            if ($data['Count'] > 0) {
              $name = $data[0]['Name'];
              $isNew = "False";
//              $massif_id = $id;
            }
            $parameters = "&cancel=True&cid=".$id."&ccat=massif";
          } else {
            $locked = true;
            $type = "menu";
          }
        } else {
          $isNew = "True";
//          $massif_id = "-1";
        }
      }
    }
?>
    <script type="text/javascript" src="../scripts/classeGCTest.js"></script>
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
    var doCancel = true;
    <?php include("../scripts/events.js"); ?>
    var namesArray = new Array();

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
      <?php if (isset($_POST['delete'])) { ?>
      reload(false);
      <?php } ?>
    }
    
    function massifEdit(oForm) {
      var oRadio = oForm.radio_list;
      var massifId = getRadioValue(oRadio);
      if (massifId) {
        self.location.href = "massif_<?php echo $_SESSION['language']; ?>.php?type=edit&id=" + massifId;
      }
    }
    
    function massifRefresh(oForm) {
      oForm.submit();
    }
    
    function massifDelete(oForm) {
      var oRadioArray = oForm.radio_list;
      var massifId = getRadioValue(oRadioArray);
      if (massifId) {
        deleteMarker("massif", massifId, "<?php echo $_SESSION['language']; ?>");
      }
    }
    
    function massifOnClick(id) {
      detailMarker('massif', id, '<?php echo $_SESSION['language']; ?>', false);
    }
<?php
	break;
	case "delete":
?>
    function deleteOnLoad() {
      var oForm = document.delete_massif;
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
      var oForm = document.new_massif;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      mySite.details.switchDetails(true);
      if (hasFailed) {
        parent.setFilterSize(getMaxWidthInput(oForm),"px");
        var oHtml = document.getElementsByTagName('HTML')[0];
        parent.setFilterSizeTight(oHtml);
        parent.overview.hideId('n_reload');
        namesArray = loadNames("massif");
        checkThisName(oForm.n_massif_name);
      } else {
        reload(false);
      }
    }
    
    function selectOnClick(oSelect) {
      var Id = oSelect.options[oSelect.selectedIndex].value;
      document.body.focus();
      var reg = new RegExp("[<?php echo $regForCat; ?>]+", "gi");
      var arrayId = Id.split(reg);
      if (arrayId[0] == "0") {
        Id = arrayId[1];
        Category = "entry";
        openMe(Id, Category, false);
        detailMarker(Category, Id, '<?php echo $_SESSION['language']; ?>', false);
      }
  	}
  	
  	function entryRemove() {
      var oForm = document.new_massif;
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
      //url = "<?php echo $_SESSION['Application_url']; ?>/html/pick_window_<?php echo $_SESSION['language']; ?>.php?type=entryncave&callback=addEntry";
      url = "pick_window_<?php echo $_SESSION['language']; ?>.php?type=entryncave&callback=addEntry";
      openWindow(url, windowName, 690, 520);
  	}
  	
  	function addEntry(oForm) {
      var uForm = document.new_massif;
      addOptionsFromSelection(oForm, uForm.e_myList);
    }
    
    function newSubmit(event) {
      var oForm = document.new_massif;
      var rightSource = toAbsURL(rightPic);
      var oField = xtdGetElementById('name_pic');
      var sMessage = "<convert>#label=546<convert> <convert>#label=873<convert> | / \\ ' \" & + <convert>#label=46<convert> ¨";//Le nom du massif //doit être composé de 2 à 36 caractères sauf //et
      createTest(oField.name, oField.src, rightSource, "==", sMessage, true);
      if (!testForm()) {
        stopSubmit(event);
      } else {
      	doChallengeList(oForm.e_myList,oForm.e_list);
        doCancel = false;
      } 
    }
    
    function checkThisName(oObject) {
      checkName(oObject, "name_pic", "massif", "<?php echo $name; ?>", namesArray, false); //put the last parameter to false
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
    function massifNew() {
      self.location.href = "massif_<?php echo $_SESSION['language']; ?>.php?type=edit";
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
		<div class="frame_title"><?php echo setTitle("massif_".$_SESSION['language'].".php?type=menu", "filter", "<convert>#label=547<convert>", 2); ?></div><!--Menu des massifs -->
<?php
      if ($locked) {
?>
  	<div class="error"><?php echo getTopBubble(); ?><convert>#label=544<convert> <convert>#label=49<convert><?php echo getBotBubble(); ?></div><!--Ce massif est en cours de modification par un autre utilisateur, veuillez essayer plus tard !-->
<?php
      } else {
        if (isset($_POST['save']) && !$save_failed){
?>
    <div class="warning"><?php echo getTopBubble(); ?><convert>#label=543<convert> <convert>#label=50<convert><!--Le massif a été enregistré avec succès !--><?php echo getBotBubble(); ?></div>
<?php
        }
        if (isset($_POST['delete'])) {
          if ($delete_failed){
?>
  	<div class="error"><?php echo getTopBubble(); ?><convert>#label=840<convert><!--Vous n'êtes pas autorisé à supprimer--> <convert>#label=544<convert><!--ce massif--><?php echo getBotBubble(); ?></div>
<?php
          } else {
?>
    <div class="warning"><?php echo getTopBubble(); ?><convert>#label=543<convert> <convert>#label=609<convert><!--Le massif a été supprimé avec succès !--><?php echo getBotBubble(); ?></div>
<?php
          }
        }
      }
?>

<?php
    $sql = "SELECT DISTINCT ";
    $sql .= "T_massif.Id AS `[hidden]|[hidden]Id`, ";
    $sql .= "IF((T_massif.Locked = 'YES' AND NOT T_massif.Id_locker = '".$_SESSION['user_id']."'),1,0) AS `[hidden]|[hidden]Locked`, ";
    $sql .= "CONCAT_WS('',CONCAT_WS(' /!\\\\ ',T_massif.Name,IF(T_massif.Locked = 'YES','<convert>#label=42<convert>',NULL)),'[|]',IF((T_massif.Locked = 'YES' AND T_massif.Id_locker = '".$_SESSION['user_id']."'),'".str_replace("'","''","<convert>#label=573<convert>")."',NULL),'[|]',GROUP_CONCAT(DISTINCT T_country.".$_SESSION['language']."_name ORDER BY T_country.".$_SESSION['language']."_name SEPARATOR ', ')) AS `[hidden]|<convert>#label=551<convert><br /><convert>#label=98<convert>`, ";//Nom du massif //Pays
    $sql .= "T_massif.Name AS `T_massif*Name|[hidden]<convert>#label=551<convert>`, ";//Nom du massif
    $sql .= "T_country.".$_SESSION['language']."_name AS `T_entry*Country|[hidden]<convert>#label=98<convert>|SELECT Iso AS value,".$_SESSION['language']."_name AS text FROM T_country ORDER BY text`, ";//Pays
    $sql .= "IF((T_massif.Locked = 'YES' AND T_massif.Id_locker = '".$_SESSION['user_id']."'),1,0) AS `[hidden]|[hidden]5` ";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_massif` ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_massif_cave` ON J_massif_cave.Id_massif = T_massif.Id ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_cave_entry` ON J_cave_entry.Id_cave = J_massif_cave.Id_cave ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_entry` ON T_entry.Id = J_cave_entry.Id_entry ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_country` ON T_country.Iso = T_entry.Country ";
    $sql .= "GROUP BY T_massif.Id ";
    
    $param_link = "JavaScript:massifOnClick('<Id>');";
    $links = array (
            2 => array(
                'conditions' =>  array(
                                1 => '0'),
                'parameters' => array(
                                '<Id>' => 0),
                'link' => $param_link,
                'target' => ''));
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
?>
<?php
    $length_page = 10;
    $records_by_page_array = array(5, 10, 15, 20, 25, 30, 40, 50);
    $records_by_page = (isset($_POST['records_by_page'])) ? $_POST['records_by_page'] : 15;
    $filter_form = "automatic_form";
    $list_form = "result_form";
    $result = getRowsFromSQL($sql, $links, $records_by_page, $filter_form, $list_form, $_POST, $input_type, $style, $default_order, true, true, "");
    $resource_id = $result['resource_id'];
    $filter_fields = getFilterFields($sql,$_POST,$filter_form,"<convert>#label=542<convert>",true, $resource_id);//Tous
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
      <form id="<?php echo $filter_form; ?>" name="<?php echo $filter_form; ?>" method="post" action="massif_<?php echo $_SESSION['language']; ?>.php?type=menu">
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
        <div class="v3info">
          <convert>#label=951<convert>
        </div>
<?php
    if (allowAccess(massif_edit_all)) {
?>
        <input type="button" disabled class="buttonDisabled" id="edit_massif" name="edit_massif" value="<convert>#label=53<convert>" onclick="JavaScript:massifEdit(this.form);" /><!--Modifier--><br />
        <input type="button" disabled class="buttonDisabled" id="new_massif" name="new_massif" value="<convert>#label=54<convert>" onclick="JavaScript:massifNew();" /><!--Nouveau--><br />
<?php
    }
    if (allowAccess(massif_delete_all)) {
?>
        <input type="button" disabled class="buttonDisabled" id="del_massif" name="del_massif" value="<convert>#label=55<convert>" onclick="JavaScript:massifDelete(this.form);" /><!--Supprimer--><br />
<?php
    }
?>
        <input type="button" class="button1" id="refresh_massif" name="refresh_massif" value="<convert>#label=56<convert>" onclick="JavaScript:massifRefresh(document.<?php echo $filter_form; ?>);" /><!--Rafraîchir-->
      </form>
    </div>
<?php
			break;
    	case "delete":
        if (!allowAccess(massif_delete_all)) {
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
		<div class="frame_title"><?php echo setTitle("#", "filter", "<convert>#label=623<convert>", 3); ?></div><!--Suppression d'un massif-->
		<form id="delete_massif" name="delete_massif" method="post" action="">
			<table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
			  <tr><td>
			    <div class="warning"><?php echo getTopBubble(); ?>
			      <convert>#label=44<convert> <convert>#label=545<convert> <?php echo $name; ?> ?<!--Etes vous sûr de vouloir supprimer le massif-->
			    <?php echo getBotBubble(); ?></div>
			  </td></tr><tr><td class="field">
			    <input type="hidden" id="delete_id" name="delete_id" value="<?php echo $did; ?>" />
          <input type="submit" disabled class="buttonDisabled" id="delete" name="delete" value="<convert>#label=55<convert>" /><!--Supprimer-->
        </td></tr><tr><td class="field">
          <input class="button1" type="button" id="cancel" name="cancel" value="<convert>#label=77<convert>" onclick="JavaScript:newCancel();" /><!--Annuler-->
        </td></tr>
      </table>
    </form>
<?php
			break;
    	case "edit":
        if (!allowAccess(massif_edit_all)) {
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
  	<form id="new_massif" name="new_massif" method="post" action="" onsubmit="JavaScript:newSubmit(event);">
      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><th colspan="2"><?php echo getHelpTopic($helpId[$type], "<convert>#label=23<convert>"); ?></th></tr>
        <tr><td width="170" class="label">
          <label for="n_massif_name">
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=551<convert><!--Nom du massif--><sup>1</sup>
          </label>
        </td><td class="field">
          <input class="input1" type="text" id="n_massif_name" name="n_massif_name" value="<?php echo $name; ?>" size="15" maxlength="36" onkeyup="JavaScript:checkThisName(this);" />
          <img class="status1" name="name_pic" id="name_pic" src="../images/icons/wrong.png" alt="image" />
        </td></tr><tr><td class="label" colspan="2" style="text-align:left;">
          <label for="e_myList">
            <b><convert>#label=552<convert><!--Les entrées et réseaux de ce massif--> :</b>
          </label>
        </td></tr><tr><td class="field" colspan="2">
          <select style="width:270px;" name="e_myList" id="e_myList" size="10" multiple="multiple" onclick="JavaScript:selectOnClick(this);" ondblclick="JavaScript:entryRemove();">
<?php
if ($isNew == "False") {
          $sql = "SELECT CONCAT(ca.Id,'".$regForCat."','0') AS value, CONCAT(ca.Name,' [<convert>#label=119<convert>]') AS text ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_cave` ca ";
          $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_massif_cave` mc ON mc.Id_cave = ca.Id ";
          $sql .= "WHERE mc.Id_massif = ".$id." ";
          $sql .= "UNION ";
          $sql .= "SELECT CONCAT('0','".$regForCat."',ey.Id) AS value, CONCAT(ey.Name,' [<convert>#label=625<convert>]') AS text ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_entry` ey ";
          $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_massif_cave` mc ON mc.Id_entry = ey.Id ";
          $sql .= "WHERE mc.Id_massif = ".$id." ";
          $sql .= "ORDER BY text ";
          $msg = "";
          $comparedCol = "value";
          $selected = "#";
          $textCol = "text";
          echo getOptions($sql, $msg, $selected, $comparedCol, $textCol);
} ?>
          </select>
        </td></tr><tr><td class="field" colspan="2">
          <input type="button" disabled class="buttonDisabled" id="add" name="add" value="<convert>#label=74<convert>" onclick="JavaScript:entryAdd();" /><!--Ajouter à ma liste-->
        </td></tr><tr><td class="field" colspan="2">
          <input type="button" disabled class="buttonDisabled" id="remove" name="remove" value="<convert>#label=73<convert>" onclick="JavaScript:entryRemove();" /><!--Retirer de ma liste-->
        </td></tr><tr><td class="field" colspan="2">
          <input type="hidden" id="e_list" name="e_list" />
          <input type="hidden" id="is_new" name="is_new" value="<?php echo $isNew; ?>" />
          <input type="hidden" id="massif_id" name="massif_id" value="<?php echo $id; ?>" />
          <input disabled class="buttonDisabled" type="submit" id="save" name="save" value="<convert>#label=76<convert>" /><!--Valider-->
        </td></tr><tr><td class="field" colspan="2">
          <input class="button1" onclick="JavaScript:newCancel();" type="button" id="cancel" name="cancel" value="<convert>#label=77<convert>" /><!--Annuler-->
        </td></tr><tr><td colspan="2">
          <div class="notes">
            <?php echo getTopBubble(); ?>
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=78<convert><!--Champs nécessaires.--><br />
            <sup>1</sup> <convert>#label=546<convert> <convert>#label=873<convert><!--Le nom du massif--> <!--doit être composé de 2 à 36 caractères sauf--> <b>|</b> <b>/</b> <b>\</b> <b>"</b> <b>#</b> <b>&amp;</b> <b>+</b> <convert>#label=46<convert><!--et--> <b>¨</b><br />
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
    $virtual_page = "massif/".$type."/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>
