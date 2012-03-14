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
if (!allowAccess(url_view_all)){ 
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
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=47<convert></title>
    <link rel="stylesheet" type="text/css" href="../css/filter.css" />
    <link rel="stylesheet" type="text/css" href="../css/portlet.css" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
<?php
  	//Capture the action type :
    $type = (isset($_GET['type'])) ? $_GET['type'] : '';
    $parameters = "";
    $locked = false;
    $helpId = array("edit" => 16);
    
    if (isset($_GET['back'])) {
    	$backPage = (isset($_GET['back'])) ? $_GET['back'] : '';
    } else {
      $backPage = "url";
    }
    
    if (allowAccess(url_delete_all)) {
      //Delete the element
      if (isset($_POST['delete'])){
        $did = (isset($_POST['delete_id'])) ? $_POST['delete_id'] : '';
        if ($did != "") {
          trackAction("delete_url",$did,"T_url");
          $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`T_url` WHERE Id = ".$did;
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_entry_url` WHERE Id_url = ".$did;
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
        if (takeOver("url",$did) && $did != "") {
          $sql = "SELECT Name FROM T_url WHERE Id = ".$did;
          $name = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
          $name = $name[0]['Name'];
          $parameters = "&cancel=True&cid=".$did."&ccat=url";
        } else {
          $locked = true;
          $type = "menu";
        }
      }
    }
  
    if (allowAccess(url_edit_all)) {
      if (isset($_POST['save'])){
        $save_failed = true;
        $name = (isset($_POST['n_url_name'])) ? $_POST['n_url_name'] : '';
        $link = (isset($_POST['n_url_link'])) ? $_POST['n_url_link'] : '';
        $comments = (isset($_POST['n_url_comments'])) ? $_POST['n_url_comments'] : '';
        $isNew = (isset($_POST['is_new'])) ? $_POST['is_new'] : '';
        $id = (isset($_POST['url_id'])) ? $_POST['url_id'] : '';
        $list = (isset($_POST['e_list'])) ? $_POST['e_list'] : '';
        if ($isNew == "True") {
          $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`T_url` ";
          $sql .= "(`Id_author`, `Name`, `Url`, `Comments`, `Date_inscription`)";
          $sql .= " VALUES (";
          $sql .= $_SESSION['user_id'].", ";
          $sql .= returnDefault($name, 'text').", ";
          $sql .= returnDefault($link, 'url').", ";
          $sql .= returnDefault($comments, 'text').", ";
          $sql .= "Now()) ";
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $nid = $req['mysql_insert_id'];
          trackAction("insert_url",$nid,"T_url");
        } else {
          $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_url` ";
          $sql .= " SET ";
          $sql .= "Locked = 'NO', ";
          $sql .= "Id_reviewer = ".$_SESSION['user_id'].", ";
          $sql .= "Name = ".returnDefault($name, 'text').", ";
          $sql .= "Url = ".returnDefault($link, 'url').", ";
          $sql .= "Comments = ".returnDefault($comments, 'text').", ";
          $sql .= "Date_reviewed = Now() ";
          $sql .= "WHERE Id = ".$id;
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
        	$sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_entry_url` ";
        	$sql .= "WHERE `Id_url` = ".$id;
        	$req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          trackAction("edit_url",$id,"T_url");
        }
      	if ($isNew == "True") {
      		$onid = $nid;
      	} else {
      		$onid = $id;
      	}
        if ($list != "") {
          $arrList = split('[|]+', $list);
          
          //Establish the relationship between entries and this url
          $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`J_entry_url` (`Id_url`, `Id_entry`) VALUES ";
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
          if (takeOver("url",$id) && $id != "") {
            $sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`T_url` "; 
            $sql .= "WHERE Id = ".$id;
            $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
            if ($data['Count'] > 0) {
              $name = $data[0]['Name'];
              $link = $data[0]['Url'];
              $comments = $data[0]['Comments'];
              $isNew = "False";
            }
            $parameters = "&cancel=True&cid=".$id."&ccat=url";
          } else {
            $locked = true;
            $type = "menu";
          }
        } else {
          $isNew = "True";
        }
      }
    }
?>
    <script type="text/javascript" src="../scripts/classeGCTest.js"></script>
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
    var doCancel = true;
    var urlNamesArray = new Array();
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
    }
    
    function urlEdit(oForm) {
      var oRadio = oForm.radio_list;
      var urlId = getRadioValue(oRadio);
      if (urlId) {
        self.location.href = "url_<?php echo $_SESSION['language']; ?>.php?type=edit&id=" + urlId;
      }
    }
    
    function urlRefresh(oForm) {
      oForm.submit();
    }
    
    function urlDelete(oForm) {
      var oRadioArray = oForm.radio_list;
      var urlId = getRadioValue(oRadioArray);
      if (urlId) {
        deleteMarker("url", urlId, "<?php echo $_SESSION['language']; ?>");
      }
    }
<?php
	break;
	case "delete":
?>
    function deleteOnLoad() {
      var oForm = document.delete_url;
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
      } else {
        parent.setFilterSize(25);
      }
    }
  
    function newOnLoad(hasFailed) {
      var oForm = document.new_url;
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      if (hasFailed) {
        parent.setFilterSize(getMaxWidthInput(oForm),"px");
        var oHtml = document.getElementsByTagName('HTML')[0];
        parent.setFilterSizeTight(oHtml);
        urlNamesArray = loadNames("url");
        checkThisName(oForm.n_url_name, 'name_pic', 'url', urlNamesArray);
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
      var oForm = document.new_url;
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
      openWindow(url, windowName, 1025, 520);
  	}
	
  	function addEntry(oForm) {
      var uForm = document.new_url;
      addOptionsFromSelection(oForm, uForm.e_myList);
    }
    
    function newSubmit(event) {
      var oForm = document.new_url;
      var rightSource = toAbsURL(rightPic);
      var oField = xtdGetElementById('name_pic');
      var sMessage = "<convert>#label=674<convert> <convert>#label=876<convert> | / \\ ' \" & + <convert>#label=46<convert> ¨";//Le nom du site //doit être composé de 2 à 200 caractères sauf //et
      createTest(oField.name, oField.src, rightSource, "==", sMessage, true);
      if (!testForm()) {
        stopSubmit(event);
      } else {
      	doChallengeList(oForm.e_myList,oForm.e_list);
        doCancel = false;
      } 
    }
    
    function checkThisName(oObject, namePic, category, namesArray, sName) {
      if (category == "url") {
        sName = "<?php echo $name; ?>";
      } else {
        sName = "";
      }
      checkName(oObject, namePic, category, sName, namesArray, false); //put the last parameter to false
      displayCloseNames(oObject, getMatchingValues(oObject.value, namesArray, sName), '<convert>#label=844<convert>'); //Noms existants déjà en base :
    }
    
    function newOnBeforeUnload(event) {
      if (doCancel) {
        var msg = "<convert>#label=92<convert>";//Les modifications seront perdues !
        stopUnload(event, msg);
      }
    }
    
    function newOnUnload(hasFailed) {
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
    function urlNew() {
      self.location.href = "url_<?php echo $_SESSION['language']; ?>.php?type=edit";
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
		<div class="frame_title"><?php echo setTitle("url_".$_SESSION['language'].".php?type=menu", "filter", "<convert>#label=673<convert>", 2); ?></div><!--Menu des partenaires-->
<?php
      if ($locked) {
?>
  	<div class="error"><?php echo getTopBubble(); ?><convert>#label=675<convert> <convert>#label=43<convert><?php echo getBotBubble(); ?></div><!--Ce site partenaire est en cours de modification par un autre utilisateur, veuillez essayer plus tard !-->
<?php
      } else {
        if (isset($_POST['save']) && !$save_failed){
?>
    <div class="warning"><?php echo getTopBubble(); ?><convert>#label=676<convert> <convert>#label=139<convert><!--Le site partenaire a été enregistré avec succès !--><?php echo getBotBubble(); ?></div>
<?php
        }
        if (isset($_POST['delete'])) {
          if ($delete_failed){
?>
  	<div class="error"><?php echo getTopBubble(); ?><convert>#label=840<convert><!--Vous n'êtes pas autorisé à supprimer--> <convert>#label=675<convert><!--ce site partenaire--><?php echo getBotBubble(); ?></div>
<?php
          } else {
?>
    <div class="warning"><?php echo getTopBubble(); ?><convert>#label=676<convert> <convert>#label=609<convert><!--Le site partenaire a été supprimé avec succès !--><?php echo getBotBubble(); ?></div>
<?php
          }
        }
      }
?>

<?php
    $sql = "SELECT DISTINCT ";
    $sql .= "T_url.Id AS `0`, ";
    $sql .= "IF((T_url.Locked = 'YES' AND NOT T_url.Id_locker = '".$_SESSION['user_id']."'),1,0) AS `1`, ";
    $sql .= "CONCAT_WS('',CONCAT_WS(' /!\\\\ ',T_url.Name,IF(T_url.Locked = 'YES','<convert>#label=42<convert>',NULL)),'[|]',IF((T_url.Locked = 'YES' AND T_url.Id_locker = '".$_SESSION['user_id']."'),'".str_replace("'","''","<convert>#label=573<convert>")."',NULL),'[|]',T_url.Url) AS `2`, ";//Nom du site //Url
    $sql .= "T_url.Name AS `3`, "; //Nom du site
    $sql .= "T_url.Url AS `4`, "; //Url
    $sql .= "T_url.Comments AS `5`, "; //Commentaires
    $sql .= "IF((T_url.Locked = 'YES' AND T_url.Id_locker = '".$_SESSION['user_id']."'),1,0) AS `6` ";
    $sql .= "FROM `".$_SESSION['Application_host']."`.`T_url` ";
    $columns_params = array(
			0 => "[hidden]|[hidden]Id",
			1 => "[hidden]|[hidden]Locked",
			2 => "[hidden]|<convert>#label=671<convert><br /><convert>#label=672<convert>",
			3 => "T_url*Name|[hidden]<convert>#label=671<convert>",
			4 => "T_url*Url|[hidden]<convert>#label=672<convert>",
			5 => "T_url*Comments|[hidden]<convert>#label=638<convert>",
			6 => "[hidden]|[hidden]6"
		);
    $links = array (
            2 => array(
                'conditions' =>  array(
                                1 => '0'),
                'parameters' => array(
                                '<Url>' => 4),
                'link' => "<Url>",
                'target' => '_blank'));
    $input_type = array(
                'type' => 'radio',
                'conditions' => array(
                    1 => '0'));
    $style = array(
            2 => array(
                'tag' => 'div',
                'class' => 'plt_warning',
                'conditions' => array(
                    6 => '1')));
    $default_order = 3;
?>

<?php
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
    if ($total_count > 0) {
      $navigator = getPageNavigator($length_page, $current_page, $count_page, $filter_form);
    } else {
      $navigator = "";
    }
?>
    <div>
      <form id="<?php echo $filter_form; ?>" name="<?php echo $filter_form; ?>" method="post" action="url_<?php echo $_SESSION['language']; ?>.php?type=menu">
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
          <convert>#label=677<convert><!--Si le site partenaire que vous cherchez n'est pas dans--> <?php echo $_SESSION['Application_name']; ?>, <a href="JavaScript:urlNew();"><convert>#label=550<convert><!--créez le--></a> !
          <?php echo getBotBubble(); ?>
        </div>
<?php
    if (allowAccess(url_edit_all)) {
?>
        <input type="button" class="button1" id="edit_url" name="edit_url" value="<convert>#label=53<convert>" onclick="JavaScript:urlEdit(this.form);" /><!--Modifier--><br />
        <input type="button" class="button1" id="new_url" name="new_url" value="<convert>#label=54<convert>" onclick="JavaScript:urlNew();" /><!--Nouveau--><br />
<?php
    }
    if (allowAccess(url_delete_all)) {
?>
        <input type="button" class="button1" id="del_url" name="del_url" value="<convert>#label=55<convert>" onclick="JavaScript:urlDelete(this.form);" /><!--Supprimer--><br />
<?php
    }
?>
        <input type="button" class="button1" id="refresh_url" name="refresh_url" value="<convert>#label=56<convert>" onclick="JavaScript:urlRefresh(document.<?php echo $filter_form; ?>);" /><!--Rafraîchir-->
      </form>
    </div>
<?php
			break;
    	case "delete":
        if (!allowAccess(url_delete_all)) {
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
		<div class="frame_title"><?php echo setTitle("#", "filter", "<convert>#label=678<convert>", 3); ?></div><!--Suppression un lien-->
		<form id="delete_url" name="delete_url" method="post" action="">
			<table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
			  <tr><td>
			    <div class="warning"><?php echo getTopBubble(); ?>
			      <convert>#label=44<convert> <convert>#label=679<convert> <?php echo $name; ?> ?<!--Etes vous sûr de vouloir supprimer le lien-->
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
        if (!allowAccess(url_edit_all)) {
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
  	<form id="new_url" name="new_url" method="post" action="" onsubmit="JavaScript:newSubmit(event);">
      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><th colspan="2"><?php echo getHelpTopic($helpId[$type], "<convert>#label=23<convert>"); ?></th></tr>
        <tr><td width="170" class="label">
          <label for="n_url_name">
  			    <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=671<convert><!--Nom du site--><sup>1</sup>
          </label>
        </td><td class="field">
      		<input class="input1" type="text" id="n_url_name" name="n_url_name" value="<?php echo $name; ?>" size="15" maxlength="200" onkeyup="JavaScript:checkThisName(this, 'name_pic', 'url', urlNamesArray);" />
      		<img class="status1" name="name_pic" id="name_pic" src="../images/icons/wrong.png" alt="image" />
    		</td></tr><tr><td width="170" class="label">
          <label for="n_url_link">
            <convert>#label=672<convert><!--Lien (URL)-->
          </label>
        </td><td class="field">
      		<input class="input1" type="text" id="n_url_link" name="n_url_link" value="<?php echo $link; ?>" size="45" maxlength="200" />
    		</td></tr><tr><td width="170" class="label">
          <label for="n_url_comments">
  			    <convert>#label=638<convert><!--Commentaires-->
          </label>
        </td><td class="field">
      		<textarea class="input1" id="n_url_comments" name="n_url_comments" style="width:100%" rows="3" cols="" wrap="soft"><?php echo $comments; ?></textarea>
    		</td></tr><tr><td class="label" colspan="2" style="text-align:left;">
          <label for="e_myList">
            <b><convert>#label=680<convert><!--Les entrées concernées par ce lien--> :</b>
          </label>
        </td></tr><tr><td class="field" colspan="2">
          <select style="width:270px;" name="e_myList" id="e_myList" size="10" multiple="multiple" onclick="JavaScript:selectOnClick(event, this);" ondblclick="JavaScript:entryRemove();">
<?php
if ($isNew == "False") {
          $sql = "SELECT ey.Id AS value, ey.Name AS text ";
          $sql .= "FROM `".$_SESSION['Application_host']."`.`T_entry` ey ";
          $sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_entry_url` eu ON eu.Id_entry = ey.Id ";
          $sql .= "WHERE eu.Id_url = ".$id." ";
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
      		<input type="hidden" id="url_id" name="url_id" value="<?php echo $id; ?>" />
		      <input class="button1" type="submit" id="save" name="save" value="<convert>#label=76<convert>" /><!--Valider-->
        </td></tr><tr><td class="field" colspan="2">
		      <input class="button1" onclick="JavaScript:newCancel();" type="button" id="cancel" name="cancel" value="<convert>#label=77<convert>" /><!--Annuler-->
        </td></tr><tr><td colspan="2">
          <div class="notes">
            <?php echo getTopBubble(); ?>
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=78<convert><!--Champs nécessaires.--><br />
            <sup>1</sup> <convert>#label=674<convert> <convert>#label=876<convert><!--Le nom du site partenaire--> <!-- doit être composé de 2 à 200 caractères sauf--> <b>|</b> <b>/</b> <b>\</b> <b>"</b> <b>#</b> <b>&amp;</b> <b>+</b> <convert>#label=46<convert><!--et--> <b>¨</b><br />
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
    $virtual_page = "url/".$type."/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>