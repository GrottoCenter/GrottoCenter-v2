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
//'([^']*)<convert>#label=([0-9]+)<convert>([^']*)'
//"$1<convert>#label=$2<convert>$3"

include("../conf/config.php");
include("../func/function.php");
include("declaration.php");
$frame = "file";
$allowed_to_lock = (allowAccess(location_lock_all) && allowAccess(description_lock_all) && allowAccess(rigging_lock_all) && allowAccess(history_lock_all) && allowAccess(biblio_lock_all) && allowAccess(comment_lock_all));
include("application_".$_SESSION['language'].".php");
include("mailfunctions_".$_SESSION['language'].".php");
include("../func/file_func.php");
$category = (isset($_GET['category'])) ? $_GET['category'] : 'entry';
$category = ($category!="") ? $category : 'entry';
$id = (isset($_GET['id'])) ? (int)$_GET['id'] : 1;
$id = ($id!="") ? $id : 1;
$type = (isset($_GET['type'])) ? $_GET['type'] : '';
$entry_specific_data = getDataFromSQL("SELECT * FROM T_entry WHERE Id=".$id, __FILE__, $frame, __FUNCTION__);
$isPublic = ($entry_specific_data[0]['Is_public']=='YES');
$isSensitive = ($entry_specific_data[0]['Is_sensitive']=='YES');
$sensContact = $entry_specific_data[0]['Contact'];
$sensModalitiesArray = explode(",", $entry_specific_data[0]['Modalities']);
$caverRelevance = "";
$scoreMessage = "";
$helpId = array("file" => 20);
if (!$isPublic && !USER_IS_CONNECTED) {
  header("location:".$_SESSION['Application_url']);
  exit();
}
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
    <script type="text/javascript" src="<?php echo getScriptJS(__FILE__); ?>"></script>
<?php
    $rig_separator = "|;|";
    $countEntries = countEntries($id);
    $labelsBlank = array("","","","","","","");
    $labelsSinceDate = array("<convert>#label=8<convert>","<convert>#label=9<convert>","<convert>#label=10<convert>","<convert>#label=11<convert>","<convert>#label=12<convert>","<convert>#label=13<convert>","<convert>#label=14<convert>");
    include("../func/loader_func.php");
    include("properties_".$_SESSION['language'].".php");
    $entryName = getEntryName($id);
    $noChangePossibleError = '<div class="error">'.getTopBubble()."<convert>#label=339<convert><!--Cette fiche--> <convert>#label=49<convert><!--est en cours de modification par un autre utilisateur, veuillez essayer plus tard !-->".getBotBubble().'</div>';
    $deletionWarning = '<div class="warning">'.getTopBubble()."<convert>#label=732<convert><!--Votre contribution--> <convert>#label=610<convert><!--a été supprimée avec succès !-->".getBotBubble().'</div>';
    $saveWarning = '<div class="warning">'.getTopBubble()."<convert>#label=732<convert><!--Votre contribution--> <convert>#label=733<convert><!--a été enregistrée avec succès !-->".getBotBubble().'</div>';
?>
    <?php echo getMetaTags(); ?>
    <!-- version IE //-->
	  <link rel="shortcut icon" type="image/x-icon" href="<?php echo $_SESSION['Application_url']; ?>/favicon.ico" />
	  <!-- version standart //-->
	  <link rel="shortcut icon" type="image/png" href="<?php echo $_SESSION['Application_url']; ?>/favicon.png" />
    <title><?php echo $_SESSION['Application_title']; ?> <?php echo $entryName; ?></title>
<?php
    $is_change = false;
    $save_failed = false;
    $contributionSaved = false;
    $contributionDeleted = false;
    if (!isset($_POST['print'])) {
      // Modification of content
      if ($type == "change" && !isset($_POST['new'])) {
      	// Get the parameters
        $chid = (isset($_GET['chid'])) ? $_GET['chid'] : '';
        $chcat = (isset($_GET['chcat'])) ? $_GET['chcat'] : '';
        if (($chcat == "location" && allowAccess(location_edit_all)) ||
        ($chcat == "description" && allowAccess(description_edit_all)) ||
        ($chcat == "rigging" && allowAccess(rigging_edit_all)) ||
        ($chcat == "history" && allowAccess(history_edit_all)) ||
        ($chcat == "bibliography" && allowAccess(biblio_edit_all)) ||
        ($chcat == "comment" && allowAccess(comment_edit_all))){
          // Save the content
          if (isset($_POST['save'])){
            $save_failed = true;
            $title = (isset($_POST['title'])) ? $_POST['title'] : '';
            $oldTitle = (isset($_POST['oldtitle'])) ? $_POST['oldtitle'] : '';
            $body = (isset($_POST['body']) ? $_POST['body'] : '');
            $oldBody = (isset($_POST['oldbody']) ? $_POST['oldbody'] : '');
            $id_exit = (isset($_POST['id_exit']) ? $_POST['id_exit'] : '');
            $obstacles = (isset($_POST['obstacles']) ? $_POST['obstacles'] : '');
            $ropes = (isset($_POST['ropes']) ? $_POST['ropes'] : '');
            $anchors = (isset($_POST['anchors']) ? $_POST['anchors'] : '');
            $observations = (isset($_POST['observations']) ? $_POST['observations'] : '');
            $e_t_underground = (isset($_POST['e_t_underground']) ? $_POST['e_t_underground'] : '');
            $e_t_trail = (isset($_POST['e_t_trail']) ? $_POST['e_t_trail'] : '');
            $aestheticism = (isset($_POST['aestheticism']) ? $_POST['aestheticism'] : '');
            $caving = (isset($_POST['caving']) ? $_POST['caving'] : '');
            $approach = (isset($_POST['approach']) ? $_POST['approach'] : '');
            $alert_me = (isset($_POST['alert_me']) ? $_POST['alert_me'] : '');
            $aestheticism = limitValue($aestheticism, 0, 10);
            $caving = limitValue($caving, 0, 10);
            $approach = limitValue($approach, 0, 10);

            //Relevance of the contribution
            $smallStr = false;
            if($chcat == "rigging" || $chcat == "bibliography") {
              $smallStr = true;
            }
            $caverRelevance = getScore($body." ".$title,$oldBody." ".$oldTitle,true,$smallStr);
            $contribRelevance = getScore($body." ".$title,"",false,$smallStr);
            updateCaverRelevance($caverRelevance, $_SESSION['user_id']);

            //Querry
            $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_".$chcat."` SET ";
            $sql .= "Locked = 'NO', ";
            $sql .= "Relevance = ".returnDefault($contribRelevance, 'float').", ";
            if ($chcat == "comment" || $chcat == "description" || $chcat == "rigging") {
              $sql .= "Title = ".returnDefault($title, 'text').", ";
            }
            if ($chcat == "rigging" || $chcat == "description" || $chcat == "comment") {
              $sql .= "Id_exit = ".returnDefault($id_exit, 'id').", ";
            }
            if ($chcat == "comment") {
              $sql .= "Id_entry = ".returnDefault($id, 'id').", ";
            }
            if ($chcat == "rigging") {
            	$sql .= "Obstacles = ".returnDefault($obstacles, 'text').", ";
              $sql .= "Ropes = ".returnDefault($ropes, 'text').", ";
            	$sql .= "Anchors = ".returnDefault($anchors, 'text').", ";
            	$sql .= "Observations = ".returnDefault($observations, 'text').", ";
            } else {
              $sql .= "Body = ".returnDefault($body, 'text').", ";
            }
            if ($chcat == "comment") {
              $sql .= "e_t_underground = ".returnDefault($e_t_underground, 'time').", ";
              $sql .= "e_t_trail = ".returnDefault($e_t_trail, 'time').", ";
              $sql .= "Aestheticism = ".returnDefault($aestheticism, 'float').", ";
              $sql .= "Caving = ".returnDefault($caving, 'float').", ";
              $sql .= "Approach = ".returnDefault($approach, 'float').", ";
              $sql .= "Alert = ".returnDefault($alert_me, 'checkbox').", ";
            }
            $sql .= "Id_reviewer = ".$_SESSION['user_id'].", ";
            $sql .= "Date_reviewed = Now() ";
            $sql .= "WHERE `Id` = ".$chid;
            $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);

            //Tracker
            trackAction("edit_".$chcat, $chid, "T_".$chcat);

            if ($chcat == "rigging" || $chcat == "description") {
              $sql = "UPDATE `".$_SESSION['Application_host']."`.`J_entry_".$chcat."` SET ";
              $sql .= "Id_entry = ".returnDefault($id, 'id')." ";
              $sql .= "WHERE Id_".$chcat." = ".$chid;
              $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
            }
            $save_failed = false;
          }

          // Cancel the modifications
          if (isset($_POST['cancel'])) {
            backOver($chcat, $chid);
          }

          // Take Over
          $is_change = (!isset($_POST['cancel']) && !isset($_POST['save']) && ($chid != "") && ($chcat != "")); // isset($_GET['chcat']);
          if ($is_change) {
            $is_change = takeOver($chcat,$chid);
            $no_change_possible = !$is_change;
          }
          $type = "";
        }
      }

      if ($type == "lock") {
        // Get the parameters
        $status = (isset($_GET['status'])) ? $_GET['status'] : '';
        $lid = (isset($_GET['lid'])) ? $_GET['lid'] : '';
        $lcat = (isset($_GET['lcat'])) ? $_GET['lcat'] : '';
        if (($lcat == "location" && allowAccess(location_lock_all)) ||
        ($lcat == "description" && allowAccess(description_lock_all)) ||
        ($lcat == "rigging" && allowAccess(rigging_lock_all)) ||
        ($lcat == "history" && allowAccess(history_lock_all)) ||
        ($lcat == "bibliography" && allowAccess(biblio_lock_all)) ||
        ($lcat == "comment" && allowAccess(comment_lock_all))){
          // Lock / Unlock the content
          if ($status == "true") {
            takeOver($lcat,$lid);
          } else {
            backOver($lcat,$lid);
          }
          $type = "";
        }
      }

      if ($type == "delete") {
        $contributionDeleted = false;
        // Get the parameters
        $did = (isset($_GET['did'])) ? $_GET['did'] : '';
        $dcat = (isset($_GET['dcat'])) ? $_GET['dcat'] : '';
        if (($dcat == "location" && allowAccess(location_delete_all)) ||
        ($dcat == "description" && allowAccess(description_delete_all)) ||
        ($dcat == "rigging" && allowAccess(rigging_delete_all)) ||
        ($dcat == "history" && allowAccess(history_delete_all)) ||
        ($dcat == "bibliography" && allowAccess(biblio_delete_all)) ||
        ($dcat == "comment" && allowAccess(comment_delete_all))){
          if (takeOver($dcat,$did) && $did != "") {
            //Tracker
            trackAction("delete_".$dcat, $did, "T_".$dcat);

            // Delete the content
            $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`T_".$dcat."` ";
            $sql .= "WHERE `Id` = ".$did;
            $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
            if ($dcat == "description" || $dcat == "rigging") {
              $sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_entry_".$dcat."` ";
              $sql .= "WHERE `Id_".$dcat."` = ".$did;
              $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
            }
            //Update the entry contribution flag
            $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_entry` ";
            $sql .= "SET Has_contributions = IF((SELECT COUNT(*) FROM `".$_SESSION['Application_host']."`.`V_contributions` WHERE Id_entry = ".$id.") = 0, 'NO', 'YES') ";
            $sql .= "WHERE Id = ".$id;
            $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
            $contributionDeleted = true;
          } else {
            $no_change_possible = true;
          }
          $type = "";
        }
      }

      // Save the new content
      if (isset($_POST['new'])){
        $ncat = (isset($_POST['new_category']) ? $_POST['new_category'] : '');
        if (($ncat == "location" && allowAccess(location_edit_all)) ||
        ($ncat == "description" && allowAccess(description_edit_all)) ||
        ($ncat == "rigging" && allowAccess(rigging_edit_all)) ||
        ($ncat == "history" && allowAccess(history_edit_all)) ||
        ($ncat == "bibliography" && allowAccess(biblio_edit_all)) ||
        ($ncat == "comment" && allowAccess(comment_edit_all))){
          $save_failed = true;
          $title = (isset($_POST['title'])) ? $_POST['title'] : '';
          $body = (isset($_POST['body']) ? $_POST['body'] : '');
          $obstacles = (isset($_POST['obstacles']) ? $_POST['obstacles'] : '');
          $ropes = (isset($_POST['ropes']) ? $_POST['ropes'] : '');
          $anchors = (isset($_POST['anchors']) ? $_POST['anchors'] : '');
          $observations = (isset($_POST['observations']) ? $_POST['observations'] : '');
          $date = (isset($_POST['date']) ? $_POST['date'] : '');
          $e_t_underground = (isset($_POST['e_t_underground']) ? $_POST['e_t_underground'] : '');
          $e_t_trail = (isset($_POST['e_t_trail']) ? $_POST['e_t_trail'] : '');
          $aestheticism = (isset($_POST['aestheticism']) ? $_POST['aestheticism'] : '');
          $caving = (isset($_POST['caving']) ? $_POST['caving'] : '');
          $approach = (isset($_POST['approach']) ? $_POST['approach'] : '');
          $alert_me = (isset($_POST['alert_me']) ? $_POST['alert_me'] : '');
          $id_answered = (isset($_POST['id_answered']) ? $_POST['id_answered'] : '');
          $aestheticism = limitValue($aestheticism, 0, 10);
          $caving = limitValue($caving, 0, 10);
          $approach = limitValue($approach, 0, 10);

          //Relevance of the contribution
          $smallStr = false;
          if($ncat == "rigging" || $ncat == "bibliography") {
            $smallStr = true;
          }
          $contribRelevance = getScore($body." ".$title,"",false,$smallStr);
          $caverRelevance = $contribRelevance;
          updateCaverRelevance($caverRelevance, $_SESSION['user_id']);

          //Querry
          $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`T_".$ncat."` ( ";
          $sql .= "Relevance, ";
          if ($ncat == "comment" || $ncat == "description" || $ncat == "rigging") {
            $sql .= "Title, ";
          }
          if ($ncat == "rigging" || $ncat == "description" || $ncat == "comment") {
            $sql .= "Id_exit, ";
          }
          if ($ncat == "rigging") {
          	$sql .= "Obstacles, ";
            $sql .= "Ropes, ";
          	$sql .= "Anchors, ";
          	$sql .= "Observations, ";
          } else {
            $sql .= "Body, ";
          }
          if ($ncat == "comment") {
            $sql .= "e_t_underground, ";
            $sql .= "e_t_trail, ";
            $sql .= "Aestheticism, ";
            $sql .= "Caving, ";
            $sql .= "Approach, ";
            $sql .= "Id_answered, ";
            $sql .= "Alert, ";
          }
          if ($ncat == "comment" || $ncat == "location" || $ncat == "bibliography" || $ncat == "history") {
            $sql .= "Id_entry, ";
          }
          $sql .= "Locked, ";
          $sql .= "Id_author, ";
          $sql .= "Date_inscription ";
          $sql .= ") VALUES ( ";
          $sql .= returnDefault($contribRelevance, 'float').", ";
          if ($ncat == "comment" || $ncat == "description" || $ncat == "rigging") {
            $sql .= returnDefault($title, 'text').", ";
          }
          if ($ncat == "rigging" || $ncat == "description" || $ncat == "comment") {
            $sql .= returnDefault($id_exit, 'id').", ";
          }
          if ($ncat == "rigging") {
          	$sql .= returnDefault($obstacles, 'text').", ";
            $sql .= returnDefault($ropes, 'text').", ";
          	$sql .= returnDefault($anchors, 'text').", ";
          	$sql .= returnDefault($observations, 'text').", ";
          } else {
            $sql .= returnDefault($body, 'text').", ";
          }
          if ($ncat == "comment") {
            $sql .= returnDefault($e_t_underground, 'time').", ";
            $sql .= returnDefault($e_t_trail, 'time').", ";
            $sql .= returnDefault($aestheticism, 'float').", ";
            $sql .= returnDefault($caving, 'float').", ";
            $sql .= returnDefault($approach, 'float').", ";
            $sql .= returnDefault($id_answered, 'id').", ";
            $sql .= returnDefault($alert_me, 'checkbox').", ";
          }
          if ($ncat == "comment" || $ncat == "location" || $ncat == "bibliography" || $ncat == "history") {
            $sql .= returnDefault($id, 'id').", ";
          }
          $sql .= "'NO', ";
          $sql .= $_SESSION['user_id'].", ";
          $sql .= "Now() ";
          $sql .= ") ";
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          $nid = $req['mysql_insert_id'];

          //Tracker
          trackAction("insert_".$ncat, $nid, "T_".$ncat);

          //Update the entry contribution flag
          $sql = "UPDATE `".$_SESSION['Application_host']."`.`T_entry` ";
          $sql .= "SET Has_contributions = 'YES' ";
          $sql .= "WHERE Id = ".$id;
          $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);

          //Send an e-mail to the answered person
          if ($id_answered != "" && $ncat == "comment") {
            $get_answered_sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`T_comment` WHERE Id = ".$id_answered;
            $answered_array = getDataFromSQL($get_answered_sql, __FILE__, $frame, __FUNCTION__);
            if ($answered_array[0]["Alert"] == "YES") {
              alertForCommentReply($id_answered, $nid, $category, $id);
            }
          }

          if ($ncat == "rigging" || $ncat == "description") {
            $sql = "INSERT INTO `".$_SESSION['Application_host']."`.`J_entry_".$ncat."` (Id_entry, Id_".$ncat.") VALUES ( ";
            $sql .= returnDefault($id, 'id').", ";
            $sql .= $nid." ";
            $sql .= ") ";
            $req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
          }
          $save_failed = false;
        }
      }
      $contributionSaved = (!$save_failed && (isset($_POST['save']) || isset($_POST['new'])));
    }

    if ($contributionSaved) {
      if ($caverRelevance < 0) {
        $scoreMessage = '<div class="error">';
      } else {
        $scoreMessage = '<div class="warning">';
      }
      $scoreMessage .= getTopBubble()."<convert>#label=753<convert> ";
      if ($caverRelevance < 0) {
        $scoreMessage .= "<convert>#label=754<convert> ";
      } else {
        $scoreMessage .= "<convert>#label=755<convert> ";
      }
      $scoreMessage .= abs($caverRelevance)." <convert>#label=756<convert>".getBotBubble().'</div>';
    }

    function getLicenseAlert()
    {
      $alert = "<div class=\"notes\" style=\"margin:5px;\">";
      $alert .= " <convert>#label=486<convert>";
      $alert .= "</div>";
      $alert .= "<div class=\"new_content_license\">";
      $alert .= " <p><convert>#label=482<convert></p>";
      $alert .= " <p><convert>#label=483<convert></p>";
      $alert .= " <p><convert>#label=484<convert></p>";
      $alert .= " <p><convert>#label=509<convert> : ".getLicensePicture(1)."</p>";
      $alert .= "</div>";
      return $alert;
    }

    //Get the values to display :
    $locations = getLocation($category, $id);
    $topographies = getTopography($category, $id);
    $descriptions = getDescription($category, $id);
    $riggings = getRigging($category, $id);
    $histories = getHistory($category, $id);
    $bibliographies = getBibliography($category, $id);
    $comments = getComment($category, $id);
  	//Printing options setup :
  	$disabledOnlyAttribute = "disabled=\\\"disabled\\\" ";
  	$checkedAttribute = "checked=\\\"checked\\\" ";
  	$printLocations = ($locations['Count']>0) ? "" : $disabledOnlyAttribute;
  	$printDescriptions = ($descriptions['Count']>0) ? "" : $disabledOnlyAttribute;
  	$printRiggings = ($riggings['Count']>0) ? "" : $disabledOnlyAttribute;
  	$printTopographies = ($topographies['Count']>0) ? "" : $disabledOnlyAttribute;
  	$printHistories = ($histories['Count']>0) ? "" : $disabledOnlyAttribute;
  	$printBibliographies = ($bibliographies['Count']>0) ? "" : $disabledOnlyAttribute;
  	$printComments = ($comments['Count']>0) ? "" : $disabledOnlyAttribute;
  	$defaultProperties = "";
  	$defaultInput_area = "";
  	$defaultMap = "checked=\\\"checked\\\" ";
  	$defaultTable_of_content = "";
  	$defaultLocation = ($locations['Count']>0) ? $checkedAttribute : "";
  	$defaultDescription = ($descriptions['Count']>0) ? $checkedAttribute : "";
  	$defaultRigging = ($riggings['Count']>0) ? $checkedAttribute : "";
  	$defaultTopography = ($topographies['Count']>0) ? $checkedAttribute : "";
  	$defaultHistory = ($histories['Count']>0) ? $checkedAttribute : "";
  	$defaultBibliography = ($bibliographies['Count']>0) ? $checkedAttribute : "";
  	$defaultComment = "";
?>
    <script type="text/javascript" src="http://www.google.com/jsapi?key=<?php echo Google_key; ?>"></script>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?v=3.exp&sensor=false&libraries=places&language=<?php echo $_SESSION['language']; ?>"></script>
    <!--script type="text/javascript" src="../scripts/classeGCTest.js"></script-->
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
    //functions may be needed : detailMarker

    var thisLocation, toolsHandler, showOffset, hideOffset;
    showOffset = 300;//ms
    hideOffset = 0;//ms
    document.onclick = triggerAction;
<?php if (!$is_change && false) { ?>
    document.onmouseover=containerOnOver;
    document.onmouseout=containerOnOut;
<?php } ?>

    function load() {
      thisLocation = document.location.href;
<?php
if (!$is_change) {
?>
      loadMap();
      /*var toc = getTableOfContent(self.document,2,6);
      xtdGetElementById('table_of_content').innerHTML = xtdGetElementById('table_of_content').innerHTML + toc;*/
<?php
} else {
?>
      var oForm = document.modif;
      if (oForm.body) {
        displayLength(oForm.body, 'length_display_<?php echo $chcat.$chid; ?>');
      }
      /*if (oForm.ropes) {
        displayLength(oForm.ropes, 'r_length_display_<?php echo $chcat.$chid; ?>');
      }
      if (oForm.anchors) {
        displayLength(oForm.anchors, 'a_length_display_<?php echo $chcat.$chid; ?>');
      }*/
<?php
}
if (isset($chcat) && isset($chid)) {
?>
      document.location.href = "#<?php echo $chcat.$chid; ?>";
<?php
}
if (isset($lcat) && isset($lid)) {
?>
      document.location.href = "#<?php echo $lcat.$lid; ?>";
<?php
}
if (isset($_POST['print'])) {
?>
      setTimeout('window.print()',5*1000);
<?php
}
?>
      if (mySite) {
        mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
      }
    }

    function infoHistoAe() {
      /*
      var sMessage = "<title>- Intérêt -</title>\n";
      sMessage = sMessage + "<h1>Intérêt de la cavité</h1>\n";
      sMessage = sMessage + "<p>Cet histogramme représente l'interêt que portent les utilisateurs sur cette cavité au sens de l'esthétisme.</p>\n";
      sMessage = sMessage + "<p>En abscisse est représenté l'interêt (0=aucun intérêt; 10=très esthétique) et en ordonnée le nombre d'utilisateurs ayant attribué cette note.</p>\n";
      sMessage = sMessage + "<p>Cet affichage permet d'avoir un oeil un peu plus critique sur la note moyenne présentée au dessus.</p>\n";
      */
      var sMessage = "<convert>#label=323<convert>";
      popUpMsg(sMessage);
    }

    function infoHistoCa() {
      /*
      var sMessage = "<title>- Progression -</title>\n";
      sMessage = sMessage + "<h1>Progression dans la cavité</h1>\n";
      sMessage = sMessage + "<p>Cet histogramme représente la facilité de progression dans cette cavité selon les utilisateurs.</p>\n";
      sMessage = sMessage + "<p>En abscisse est représentée la facilité (0=très difficile; 10=très facile) et en ordonnée le nombre d'utilisateurs ayant attribué cette note.</p>\n";
      sMessage = sMessage + "<p>Cet affichage permet d'avoir un oeil un peu plus critique sur la note moyenne présentée au dessus.</p>\n";
      */
      var sMessage = "<convert>#label=324<convert>";
      popUpMsg(sMessage);
    }

    function infoHistoAp() {
      /*
      var sMessage = "<title>- Accès -</title>\n";
      sMessage = sMessage + "<h1>Accès à la cavité</h1>\n";
      sMessage = sMessage + "<p>Cet histogramme représente la facilité d'accès à cette cavité selon les utilisateurs.</p>\n";
      sMessage = sMessage + "<p>En abscisse est représentée la facilité (0=très difficile d'accès; 10=très facile d'accès) et en ordonnée le nombre d'utilisateurs ayant attribué cette note.</p>\n";
      sMessage = sMessage + "<p>Cet affichage permet d'avoir un oeil un peu plus critique sur la note moyenne présentée au dessus.</p>\n";
      */
      var sMessage = "<convert>#label=325<convert>";
      popUpMsg(sMessage);
    }

    function infoRadar() {
      /*
      var sMessage = "<title>- Synthèse -</title>\n";
      sMessage = sMessage + "<h1>Graphique radar, synthèse</h1>\n";
      sMessage = sMessage + "<p>Ce graphique radar permet d'avoir une vue d'ensemble de l'intérêt et de la facilité de la cavité.</p>\n";
      sMessage = sMessage + "<p>Le centre du radar est son origine : le zéro.</p>\n";
      sMessage = sMessage + "<p><b>I</b> est la moyenne des notes d'<b>i</b>ntérêt de la cavité, plus I est élevé plus la cavité est intéressante esthétiquement parlant (note sur 10).</p>\n";
      sMessage = sMessage + "<p><b>A</b> est la moyenne des notes de facilité d'<b>a</b>ccès à la cavité, plus A est élevé plus la cavité est facile d'accès (note sur 10).</p>\n";
      sMessage = sMessage + "<p><b>P</b> est la moyenne des notes de facilité de <b>p</b>rogression dans la cavité, plus P est élevé plus la cavité est facile à parcourir (note sur 10).</p>\n";
      sMessage = sMessage + "<p>Conclusion : plus la zone orange est grande, meilleur est la sortie !;)</p>\n";
      */
      var sMessage = "<convert>#label=326<convert>";
      popUpMsg(sMessage);
    }

    function infoRadarUser() {
      /*
      var sMessage = "<title>- Notes -</title>\n";
      sMessage = sMessage + "<h1>Graphique radar</h1>\n";
      sMessage = sMessage + "<p>Ce graphique radar permet d'avoir une vue de l'intérêt et de la facilité de la cavité selon ce spéléologue.</p>\n";
      sMessage = sMessage + "<p>Le centre du radar est son origine : le zéro.</p>\n";
      sMessage = sMessage + "<p><b>I</b> est la note d'<b>i</b>ntérêt de la cavité, plus I est élevé plus la cavité à intéressé le spéléologue, esthétiquement parlant (note sur 10).</p>\n";
      sMessage = sMessage + "<p><b>A</b> est la note de facilité d'<b>a</b>ccès à la cavité, plus A est élevé plus le spéléologue a eu de la facilité à accéder à la cavité (note sur 10).</p>\n";
      sMessage = sMessage + "<p><b>P</b> est la note de facilité de <b>p</b>rogression dans la cavité, plus P est élevé plus le spéléologue a eu de la facilité à parcourir la cavité (note sur 10).</p>\n";
      */
      var sMessage = "<convert>#label=327<convert>";
      popUpMsg(sMessage);
    }

    function editElement(sCategory, iId) {
      switch(sCategory) {
        case "description":
        case "location":
        case "bibliography":
        case "history":
        case "rigging":
        case "comment":
          var loadLocation = "file_<?php echo $_SESSION['language']; ?>.php?type=change&category=<?php echo $category; ?>&id=<?php echo $id; ?>&chcat=" + sCategory + "&chid=" + iId;
          self.location = loadLocation;
        break;
        default:
          if (mySite) {
            mySite.editMarker(sCategory, iId, "<?php echo $_SESSION['language']; ?>");
            window.opener.focus();
          }
        break;
      }
    }

    var openedForm;

    function replyElement(sCategory, iId) {
      var oForm = document.forms["new_" + sCategory + "_form"];
      if (sCategory == "comment") {
        oForm.id_answered.value = iId;
        oForm.title.value = "Re : " + xtdGetElementById(sCategory + iId + "_title").innerHTML;
        //oForm.title.disabled = true;
      }
      addElement(sCategory);
    }

    function addElement(sCategory) {
      if (openedForm) {
        hideId(openedForm);
      }
      openedForm = "new_" + sCategory;
      showId(openedForm);
      document.location.href = "#new_" + sCategory + "_anchor";
    }

    function cancelElement(sCategory) {
      var oForm = document.forms["new_" + sCategory + "_form"];
      if (sCategory == "comment") {
        oForm.id_answered.value = "";
        oForm.title.value = "";
        //oForm.title.disabled = false;
      }
      hideId("new_" + sCategory);
    }

    function deleteElement(sCategory, iId) {
      switch(sCategory) {
        case "description":
        case "location":
        case "bibliography":
        case "history":
        case "rigging":
        case "comment":
          if (confirm("<convert>#label=44<convert> <convert>#label=321<convert> ?")) {//Etes vous sûr de vouloir supprimer //cet élément
            var loadLocation = "file_<?php echo $_SESSION['language']; ?>.php?type=delete&category=<?php echo $category; ?>&id=<?php echo $id; ?>&dcat=" + sCategory + "&did=" + iId;
            self.location = loadLocation;
          }
        break;
        default:
        break;
      }
    }


<?php
if ($allowed_to_lock) {
?>
    function lockElement(sCategory, iId, sIsLocked) {
      if (sIsLocked == "NO") {
        var status = "true";
      } else {
        var status = "false";
      }
      self.location.href = "file_<?php echo $_SESSION['language']; ?>.php?type=lock&category=<?php echo $category; ?>&id=<?php echo $id; ?>&status=" + status + "&lid=" + iId + "&lcat=" + sCategory;
    }
<?php
}
?>

    function badContent(sCategory, sName) {
      var url;
      /*if (mySite) {
        mySite.filter.location.href = "contact_<?php echo $_SESSION['language']; ?>.php?type=message&subject=bad_content&category=" + sCategory + "&name=" + sName;
        window.opener.focus();
      }*/
      url = "contact_<?php echo $_SESSION['language']; ?>.php?type=message&subject=bad_content&category=" + encodeURI(encodeURIComponent(sCategory)) + "&name=" + encodeURI(encodeURIComponent(sName));
      openWindow(url, '', 700, 525);
    }

    function bodyOnKeyUp(oObject, sDest) {
      if (sDest == undefined) {
        sDest = "";
      }
      limitLength(oObject, 20000);
      displayLength(oObject, sDest);
    }

    function refresh() {
      self.location.href = "file_<?php echo $_SESSION['language']; ?>.php?category=<?php echo $category; ?>&id=<?php echo $id; ?>";
    }

    function saveOnClick(subCat) {
      alert("<convert>#label=484<convert>");
      if (subCat == "rigging") {
        var sSeparator = "<?php echo $rig_separator; ?>";
        var obstacles = concatFieldsByName("Obstacle_cell",sSeparator);
        var ropes = concatFieldsByName("Ropes_cell",sSeparator);
        var anchors = concatFieldsByName("Anchors_cell",sSeparator);
        var observations = concatFieldsByName("Observations_cell",sSeparator);
        if (document.modif) {
          document.modif.obstacles.value = obstacles;
          document.modif.ropes.value = ropes;
          document.modif.anchors.value = anchors;
          document.modif.observations.value = observations;
        }
        if (document.new_rigging_form) {
          document.new_rigging_form.obstacles.value = obstacles;
          document.new_rigging_form.ropes.value = ropes;
          document.new_rigging_form.anchors.value = anchors;
          document.new_rigging_form.observations.value = observations;
        }
      }
    }

    function switchDiv(sId, bShow) {
      if (bShow) {
        hideId(sId + "_s");
        showId(sId + "_c");
      } else {
        hideId(sId + "_c");
        showId(sId + "_s");
      }
    }

    function sendToAFriend(sLocation) {
      var eMail = "mailto:";
      eMail = eMail + "?subject=<?php echo $_SESSION['Application_name']; ?> <convert>#label=322<convert>." ;//Un ami vous recommande cette fiche descriptive
      eMail = eMail + "&body=" + encodeURIComponent(sLocation) ;
      document.location.href = eMail;
    }

    function printThis(sLocation) {
      openPrintForm(sLocation);
    }

    function openUriDiv(sLocation) {
      xtdGetElementById("uriForGuest").value = sLocation;
      showId("uriDiv");
    }

    function openPrintForm(sLocation) {
      var sMessage = "<h1><convert>#label=520<convert> \"<?php echo $entryName; ?>\"</h1>"; //Imprimer la fiche ""
      sMessage = sMessage + "<h2><convert>#label=521<convert><!--Veuillez choisir les sections à imprimer--> :</h2>\n";
      sMessage = sMessage + "<form id=\"print_document\" name=\"print_document\" method=\"post\" action=\"" + sLocation.replace(new RegExp("&", "g"), '&amp;') + "\">\n";
      sMessage = sMessage + " <label for=\"properties\"><input type=\"checkbox\" id=\"properties\" name=\"properties\" class=\"input1\" style=\"border:none;\" <?php echo $defaultProperties;?>/> <convert>#label=333<convert><!--Propriétés--></label><br />\n";
      sMessage = sMessage + " <label for=\"input_area\"><input type=\"checkbox\" id=\"input_area\" name=\"input_area\" class=\"input1\" style=\"border:none;\" <?php echo $defaultInput_area;?>/> <convert>#label=524<convert><!--Zone de saisie libre--></label><br />\n";
      sMessage = sMessage + " <label for=\"map\"><input type=\"checkbox\" id=\"map\" name=\"map\" class=\"input1\" style=\"border:none;\" <?php echo $defaultMap;?>/> <convert>#label=523<convert><!--Carte de localisation--></label><br />\n";
      //sMessage = sMessage + " <label for=\"table_of_content\"><input type=\"checkbox\" id=\"table_of_content\" name=\"table_of_content\" class=\"input1\" style=\"border:none;\" <?php echo $defaultTable_of_content;?>/> <convert>#label=335<convert><!--Sommaire--></label><br />\n";
      sMessage = sMessage + " <label for=\"location\"><input type=\"checkbox\" id=\"location\" name=\"location\" class=\"input1\" style=\"border:none;\" <?php echo $defaultLocation;?><?php echo $printLocations; ?>/> <convert>#label=336<convert><!--Localisation de l'entrée--></label><br />\n";
      sMessage = sMessage + " <label for=\"description\"><input type=\"checkbox\" id=\"description\" name=\"description\" class=\"input1\" style=\"border:none;\" <?php echo $defaultDescription;?><?php echo $printDescriptions; ?>/> <convert>#label=346<convert><!--Descriptions de la cavité--></label><br />\n";
      sMessage = sMessage + " <label for=\"rigging\"><input type=\"checkbox\" id=\"rigging\" name=\"rigging\" class=\"input1\" style=\"border:none;\" <?php echo $defaultRigging;?><?php echo $printRiggings; ?>/> <convert>#label=353<convert><!--Fiches d'équipement--></label><br />\n";
      sMessage = sMessage + " <label for=\"topography\"><input type=\"checkbox\" id=\"topography\" name=\"topography\" class=\"input1\" style=\"border:none;\" <?php echo $defaultTopography;?><?php echo $printTopographies; ?>/> <convert>#label=815<convert><!--Topographies--></label><br />\n";
      sMessage = sMessage + " <label for=\"history\"><input type=\"checkbox\" id=\"history\" name=\"history\" class=\"input1\" style=\"border:none;\" <?php echo $defaultHistory;?><?php echo $printHistories; ?>/> <convert>#label=593<convert><!--Historique--></label><br />\n";
      sMessage = sMessage + " <label for=\"bibliography\"><input type=\"checkbox\" id=\"bibliography\" name=\"bibliography\" class=\"input1\" style=\"border:none;\" <?php echo $defaultBibliography;?><?php echo $printBibliographies; ?>/> <convert>#label=590<convert><!--Bibliographie--></label><br />\n";
      sMessage = sMessage + " <label for=\"comment\"><input type=\"checkbox\" id=\"comment\" name=\"comment\" class=\"input1\" style=\"border:none;\" <?php echo $defaultComment;?><?php echo $printComments; ?>/> <convert>#label=361<convert><!--Commentaires--></label><br />\n";
      sMessage = sMessage + " <label for=\"print\"><input type=\"submit\" id=\"print\" name=\"print\" value=\"<convert>#label=522<convert>\" class=\"button1\" /></label>\n";//Suivant
      sMessage = sMessage + "</form>\n";
      popUpMsg(sMessage,"<?php echo $_SESSION['Application_title']; ?> <convert>#label=520<convert> <?php echo $entryName; ?>","toolbar=no,menubar=yes,scrollbars=yes"); //,"<convert>#label=520<convert>","toolbar=no,menubar=yes,scrollbars=yes"); //Imprimer la page
    }

    function loadMap() {
        var point = new google.maps.LatLng(<?php echo getMapParams($id); ?>);

        var map = new google.maps.Map(xtdGetElementById("map"),{
            center: point,
            zoom: 10,
            scaleControl: true,
            overviewMapControl: true,
            mapTypeId: google.maps.MapTypeId.TERRAIN,
        });
        var maxZoomService = new google.maps.MaxZoomService();
        maxZoomService.getMaxZoomAtLatLng(
      		  point,
      		  function(response) {
      		    if (response.status == google.maps.MaxZoomStatus.OK) {
      		      map.setZoom(response.zoom-2);
      		    } else {
      		      console.log("Error in Max Zoom Service.");
      		    }
      		    map.panTo(point);
        });
        var marker = new google.maps.Marker({
            position: point,
            map: map,
            draggable: false,
            bouncy: false,
        });


        /*var map = new GMap2(xtdGetElementById("map"));
        map.addMapType(G_PHYSICAL_MAP);
        var point = new GLatLng(<?php echo getMapParams($id); ?>);
        map.setCenter(point);
        map.setMapType(G_PHYSICAL_MAP);
        map.setZoom(map.getCurrentMapType().getMaximumResolution()-2);
        var marker = new GMarker(point);
        map.addOverlay(marker);*/
    }

    function concatFieldsByName(sName,sSeparator) {
      var fieldArray = document.getElementsByName(sName);
      var concatText = "";
      for(i = 0; i < fieldArray.length - 1; i++) {
        if(fieldArray[i].value != undefined) {
          if(fieldArray[i].nodeName == "TEXTAREA") {
            concatText = concatText + fieldArray[i].value + sSeparator;
          }
        }
      }
      return concatText.substring(0, concatText.length - sSeparator.length);
    }

    function addLine(oContainer, oSource) {
      var sourceCopy = oSource.cloneNode(true);
      oContainer.appendChild(sourceCopy);
    }

    function insertLine(oSource, oLine) {
      var oContainer = oLine.parentNode;
      var sourceCopy = oSource.cloneNode(true);
      oContainer.insertBefore(sourceCopy, oLine);
    }

    function removeLine(oLine) {
      var oContainer = oLine.parentNode;
      oContainer.removeChild(oLine);
    }

    function moveLineUp(oLine) {
      if (oLine.previousSibling.previousSibling) {
          var oContainer = oLine.parentNode;
          //var lineClone = oLine.cloneNode(true);
          //oContainer.insertBefore(lineClone, oLine.previousSibling);
          oContainer.insertBefore(oLine, oLine.previousSibling);
          //oContainer.removeChild(oLine);
      }
    }

    function moveLineDown(oLine) {
      if (oLine.nextSibling) {
          var oContainer = oLine.parentNode;
          //var lineClone = oLine.nextSibling.cloneNode(true);
          //oContainer.insertBefore(lineClone, oLine);
          oContainer.insertBefore(oLine.nextSibling, oLine);
          //oContainer.removeChild(oLine.nextSibling);
      }
    }

    //Implementation
    function addOnClick(sRigContId) {
      var oContainer = xtdGetElementById(sRigContId);//.firstChild.nextSibling;
      var oSource = xtdGetElementById("rigging_source");//.firstChild.nextSibling.firstChild;
      addLine(oContainer, oSource);
    }

    function insertOnClick(oLine) {
      var oSource = xtdGetElementById("rigging_source");//.firstChild.nextSibling.firstChild;
      insertLine(oSource, oLine);
    }

    function triggerAction(e) {
      var firingobj = getTargetNode(e);
      if (firingobj) {
        switch(firingobj.name) {
          case "remove_line":
            removeLine(firingobj.parentNode.parentNode.parentNode);
            break;
          case "move_up_line":
            moveLineUp(firingobj.parentNode.parentNode.parentNode);
            break;
          case "move_down_line":
            moveLineDown(firingobj.parentNode.parentNode.parentNode);
            break;
          case "insert_line":
            insertOnClick(firingobj.parentNode.parentNode.parentNode);
            break;
        }
      }
    }

    //Show Hide toolbox on hover:
    /*function getContainerTable(oNode) {
      while (oNode.parentNode) {
        oNode = oNode.parentNode;
        if (oNode.tagName == "TABLE" && oNode.className == "container") {
          return oNode;
        }
      }
      return false;
    }

    function showTools(id, x, y) {
      //setNodePosition(id, x, y);
      showId(id);
    }

    function containerOnOver(e) {
      var id, firingObj, mousePosition;
      if (firingObj = getContainerTable(getTargetNode(e))) {
        id = 'tools' + firingObj.id;
        //if (xtdGetDisplay(id).indexOf("none") !== -1) {
          mousePosition = getMousePosition(e);
          if (toolsHandler != undefined) {
      		  clearTimeout(toolsHandler);
          }
          toolsHandler = setTimeout('showTools("' + id + '", "' + mousePosition.x + '", "' + mousePosition.y + '");', showOffset);
        //}
      }
    }

    function containerOnOut(e) {
      var id, firingObj;
      if (firingObj = getContainerTable(getTargetNode(e))) {
        id = 'tools' + firingObj.id;
        if (toolsHandler != undefined) {
    		  clearTimeout(toolsHandler);
        }
        toolsHandler = setTimeout('hideId("' + id + '");', hideOffset);
      }
    }*/

    //onmouseover="JavaScript:containerOnOver();" onmouseout="JavaScript:containerOnOut();"

    /*var elementFocused = undefined;
    function setFocus(){
      elementFocused = this;
    } // Methodes permettant de savoir a tout instant qui a le focus
    function unsetFocus(){
      elementFocused = undefined;
    }*/

    <?php echo getCDataTag(false); ?>
    </script>
<?php
//ob_start();
?>
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" <?php if (!isset($_POST['print'])) { echo 'media="print"'; } ?> />
    <link rel="stylesheet" type="text/css" href="../css/file.css" />
    <link rel="stylesheet" type="text/css" href="../css/file_p.css" <?php if (!isset($_POST['print'])) { echo 'media="print"'; } ?> />
    <link rel="stylesheet" type="text/css" href="../css/portlet.css" />
<?php
    if (isset($_POST['print'])) {
      echo "<style type=\"text/css\">\n";
      $sections_array = array('properties','table_of_content','location','description','rigging','history','bibliography','comment','map','input_area');
      foreach ($sections_array as $key) {
        $value = (isset($_POST[$key])) ? $_POST[$key] : '';
        if ($value != "on") {
          echo "#".$key."\n";
          echo "{\n";
          echo "  display:none;\n";
          echo "}\n";
        } else {
          echo "#".$key."\n";
          echo "{\n";
          echo "  display:block;\n";
          echo "}\n";
        }
      }
      echo "</style>\n";
    }
?>
<?php
		switch ($_SESSION['Application_availability']) {
		  case 1:
?>
  </head>
  <body onload="JavaScript:load();">
    <?php echo getTopFrame(false); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
    <div id="content">
      <div style="margin-bottom:30px;"><!-- id="title"-->
        <div id="banner_options" style="position:absolute;"> <!--float:left;-->
<?php if (!isset($_POST['print'])) { ?>
          <select class="select1" name="banner_language" id="banner_language" onchange="JavaScript:changeLanguage(this, true);">
<?php
            echo getOptionLanguage($_SESSION['language']);
?>
          </select><br /><br />
<?php } ?>
          <?php echo getLicensePicture(3); ?><br />
          <a href="JavaScript:openLegalNPrivacy('<?php echo $_SESSION['language']; ?>');"><convert>#label=3<convert><!--Mentions légales &amp; Charte de confidentialité--></a>
          </div>
        <h1><?php echo $entryName; ?></h1>
<?php if (!isset($_POST['print'])) { ?>
        <div onclick="JavaScript:infoRadar();" class="radar"><img src="../images/gen/getChart.php?type=radar&amp;data=<?php echo getAvgAestheticism($id); ?>|<?php echo getAvgCaving($id); ?>|<?php echo getAvgApproach($id); ?>&amp;label=<convert>#label=328<convert>|<convert>#label=329<convert>|<convert>#label=330<convert>" alt="image" /></div>
        <div id="mailto">
          <a href="JavaScript:sendToAFriend(thisLocation);" title="<convert>#label=331<convert>">
            <img src="../images/icons/mail.png" alt="" /> <convert>#label=332<convert><!--Envoyer l'adresse de cette page à un ami.--><!--Envoyer à un ami--></a>
         | <a href="JavaScript:printThis(thisLocation);" title="<convert>#label=525<convert>">
            <img src="../images/icons/printer.png" alt="" /> <convert>#label=525<convert><!--Imprimer--></a>
         | <a href="JavaScript:openUriDiv(thisLocation);" title="<convert>#label=672<convert>">
            <img src="../images/icons/uri.png" alt="" /> <convert>#label=672<convert><!--Lien (URL)--></a>
         | <a href="http://tools.wmflabs.org/geohack/geohack.php?language=<?php echo $_SESSION['language']; ?>&amp;params=<?php echo $entry_specific_data[0]['Latitude'] ?>;<?php echo $entry_specific_data[0]['Longitude'] ?>" title="Geohack" target="_blank">
            <img src="../images/icons/geohack.png" alt="GeoHack" /> GeoHack</a>
         | <a href="http://www.openstreetmap.org/?lat=<?php echo $entry_specific_data[0]['Latitude'] ?>&amp;lon=<?php echo $entry_specific_data[0]['Longitude'] ?>&amp;zoom=17&amp;layers=M" title="Open Street Map" target="_blank">
            <img src="../images/icons/openstreetmap.png" alt="OpenStreetMap" /> OpenStreetMap</a>
        </div>
<?php } ?>
        <div id="uriDiv" style="position:absolute;top:105px;left:50%;display:none;visibility:hidden;width:300px;white-space:nowrap;"><input type="text" class="input1" name="uriForGuest" id="uriForGuest" value="" /> <b><a href="JavaScript:hideId('uriDiv');" title="<convert>#label=371<convert>">X</a><!-- Fermer --></b></div>
        </div>
      <div id="body">
<!--#################### MAP ################################################-->
        <div id="map" class="div_2_l" style="height:250px;">
        </div>
<!--#################### PROPERTIES #########################################-->
        <div id="properties" class="div_2_l">
          <h2><convert>#label=333<convert><!--Propriétés--></h2>
<?php
        $geodesic = (isset($_GET['geodesic'])) ? $_GET['geodesic'] : '';
        $length = (isset($_GET['length'])) ? $_GET['length'] : '';
        $temperature = (isset($_GET['temperature'])) ? $_GET['temperature'] : '';
        $systemArray = array("geodesic" => urldecode(stripslashes($geodesic)),"length" => urldecode(stripslashes($length)),"temperature" => urldecode(stripslashes($temperature)));
        echo getProperties($category,$id,USER_IS_CONNECTED,$labelsBlank,$labelsSinceDate,false,$systemArray,isset($_POST['print']),"file_".$_SESSION['language'].".php?category=".$category."&id=".$id);
?>
<span id="hidden_name" style="display:none;visibility:hidden;"><?php echo $entryName; ?></span>
        </div>
<!--#################### INPUT AREA #########################################-->
        <div id="input_area" class="div_2_r">
          <textarea class="input1" rows="5" cols=""></textarea>
        </div>
<!--#################### LICENSE ############################################-->
        <div id="license" class="credit div_2_l" style="text-align:center;">
          <?php echo getLicense(1); ?>
        </div>
<!--#################### TOC ################################################
        <div id="table_of_content" class="div_2_r">
          <h2 class="toc_title"><convert>#label=335<convert></h2>
        </div>-->
<!--#################### SENSITIVENESS ######################################-->
<?php if (!$is_change && $isSensitive) { ?>
        <div id="sensitiveness" class="div_2_r" style="border:2px solid red;">
          <h2 style="border-bottom:2px solid red;"><convert>#label=740<convert><!--Cavité sensible et/ou à accès réglementé--></h2>
<?php     if ($sensContact != "") { ?>
          <convert>#label=741<convert><!--Entité/personne à contacter--> : <b><?php echo $sensContact; ?></b><br />
<?php     }
          if (in_array('YES', $sensModalitiesArray)) { ?>
          <convert>#label=742<convert><!--Accès à la cavité selon les modalités suivantes--> :<br />
          <ul>
            <?php if($sensModalitiesArray[0]=="YES"){ ?><li><convert>#label=743<convert><!--Clé.--></li><?php } ?>
            <?php if($sensModalitiesArray[1]=="YES"){ ?><li><convert>#label=744<convert><!--Liste d'attente.--></li><?php } ?>
            <?php if($sensModalitiesArray[2]=="YES"){ ?><li><convert>#label=745<convert><!--Demande écrite.--></li><?php } ?>
            <?php if($sensModalitiesArray[3]=="YES"){ ?><li><convert>#label=746<convert><!--Accompagnement.--></li><?php } ?>
          </ul>
<?php     } ?>
        </div>
<?php } ?>
<!--###################### HELP #############################################-->
        <div id="help" class="div_2_r" style="text-align:center;">
          <?php echo getHelpTopic($helpId['file'], "<convert>#label=23<convert>"); ?>
        </div>
<!--#################### LOCATION ###########################################-->
<?php if ($locations['Count']>0 || allowAccess(location_edit_all)) {
  $local_cat = "location";
  $array = $locations;
?>
        <div id="location" class="div_2_r">
<?php if (!$is_change) { ?>
          <span class="generic_tools">
<?php if (allowAccess(location_edit_all)) { ?>
            <a href="JavaScript:addElement('<?php echo $local_cat; ?>');" title="<convert>#label=54<convert>" class="nothing"><img src="../images/icons/add.png" class="icon" alt="<convert>#label=54<convert>" /></a><!--Ajouter une fiche de localisation-->
<?php } ?>
            <a href="JavaScript:refresh();" title="<convert>#label=334<convert>" class="nothing"><img src="../images/icons/refresh.png" class="icon" alt="<convert>#label=334<convert>" /></a><!--Actualiser la page-->
<?php if ($array['Count']>0) { ?>
            <a href="JavaScript:badContent('<?php echo $local_cat; ?>',xtdGetElementById('hidden_name').innerHTML);" title="<convert>#label=313<convert>" class="nothing"><img src="../images/icons/warning.png" class="icon" alt="<convert>#label=313<convert>" /></a>
<?php } ?>
          </span>
<?php } ?>
          <h2><convert>#label=336<convert><!--Localisation de l'entrée--></h2>
<?php
        $thisCategoryDeleted = ($dcat == $local_cat);
        if ($thisCategoryDeleted && $contributionDeleted) {
          echo $deletionWarning;
        }
        for($i=0;$i<$array['Count'];$i++) {
          $edition = ($is_change && ($chcat == $local_cat) && ($chid == $array[$i]["Id"]) && allowAccess(location_edit_all));
          $thisContribChanged = (($chcat == $local_cat) && ($chid == $array[$i]["Id"]));
          $thisContribNew = (($ncat == $local_cat) && ($nid == $array[$i]["Id"]));
          $thisContribEvol = ($thisContribChanged || ($thisCategoryDeleted && ($did == $array[$i]["Id"])));
          $no_change = ($no_change_possible && $thisContribEvol && allowAccess(location_edit_all));
          $thisContributionSaved = $contributionSaved && ($thisContribChanged || $thisContribNew);
          if($no_change) {
            echo $noChangePossibleError;
          }
          if($thisContributionSaved) {
            echo $saveWarning;
            echo $scoreMessage;
          }
          if ($edition) {
?>
            <form id="modif" name="modif" method="post" action="#<?php echo $local_cat.$array[$i]["Id"]; ?>">
<?php
          }
?>
            <a name="<?php echo $local_cat.$array[$i]["Id"]; ?>"></a>
            <table class="container" id="locationcontainertable<?php echo $i; ?>">
              <tr class="container">
                <td class="sub_prop">
<?php
            if (!$is_change) {
              if (allowAccess(location_edit_all)) {
?>
                  <div class="tools" id="toolslocationcontainertable<?php echo $i; ?>">
                    <?php if($allowed_to_lock){ ?><a href="JavaScript:lockElement('<?php echo $local_cat; ?>',<?php echo $array[$i]["Id"]; ?>,'<?php echo $array[$i]["Locked"];?>');" title="<convert>#label=341<convert>" class="nothing"><?php } ?><img src="../images/icons/locker_<?php echo $array[$i]["Locked"];?>.png" class="icon" alt="<convert>#label=340<convert> : <?php echo $array[$i]["Locked"]; ?>" /><?php if($allowed_to_lock){ ?></a><?php } ?><!--Verrouillé-->
<?php
                if ($array[$i]["Locked"] == "NO") {
?>
                    <a href="JavaScript:editElement('<?php echo $local_cat; ?>',<?php echo $array[$i]["Id"]; ?>);" title="<convert>#label=53<convert>" class="nothing"><img src="../images/icons/edit.png" class="icon" alt="<convert>#label=53<convert>" /></a>
                    <a href="JavaScript:addElement('<?php echo $local_cat; ?>');" title="<convert>#label=54<convert>" class="nothing"><img src="../images/icons/add.png" class="icon" alt="<convert>#label=54<convert>" /></a>
<?php
                  if (allowAccess(location_delete_all)) {
?>
                    <a href="JavaScript:deleteElement('<?php echo $local_cat; ?>',<?php echo $array[$i]["Id"]; ?>);" title="<convert>#label=55<convert>" class="nothing"><img src="../images/icons/delete.png" class="icon" alt="<convert>#label=55<convert>" /></a>
<?php
                  }
                }
?>
                  </div>
<?php
              }
            }
?>
                  <div class="who_when">
                    <convert>#label=337<convert><!--Posté par--> <b><?php echo $array[$i]["Author"];?></b><br />
                    <convert>#label=338<convert><!--Le--> <b><?php echo timeToStr($array[$i]["Date_inscription"]);?></b><br /><br />
<?php
          if (isset($array[$i]["Date_reviewed"]) && !$edition) {
?>
                    <convert>#label=344<convert><!--Modifié par--> <b><?php echo $array[$i]["Reviewer"];?></b><br />
                    <convert>#label=338<convert><!--Le--> <b><?php echo timeToStr($array[$i]["Date_reviewed"]); ?></b>
<?php
          }
?>
                  </div>
                </td>
                <td class="sub_container">
                  <?php if ($edition) { ?><div class="info">
                    <?php echo getTopBubble(); ?>
                    <convert>#label=530<convert><!--Indiquez ici comment accèder à cette entrée depuis l'extérieur.<br />
                    Ex: Entrer dans le hameau de Chougeat puis prendre le premier chemin à droite, l'entrée se situe 200m après un grand virage, dans les bois en direction du Nord ...-->
                    <?php echo getBotBubble(); ?>
                  </div><?php } ?>
                  <div class="sub_content">
                    <?php if ($edition) { ?>
                      <label for="body<?php echo $local_cat.$array[$i]["Id"]; ?>"><convert>#label=345<convert><!--Décrivez l'accès à cette entrée--> :</label><br />
                      <textarea class="input1" id="body<?php echo $local_cat.$array[$i]["Id"]; ?>" name="body" rows="6" cols="" onkeyup="JavaScript:bodyOnKeyUp(this,'length_display_<?php echo $local_cat.$array[$i]["Id"]; ?>');"><?php echo $array[$i]["Body"];?></textarea><br />
                      <span id="length_display_<?php echo $local_cat.$array[$i]["Id"]; ?>">0</span> <convert>#label=238<convert><!--caractères sur--> 20 000.
                      <input type="hidden" id="oldbody" name="oldbody" value="<?php echo $array[$i]["Body"];?>" /><!--htmlentities(-->
                    <?php } else {
                      echo getRef(replaceLinks(nl2br($array[$i]["Body"]))); //htmlentities(
                    } ?>
                  </div>
<?php
            if ($edition) {
?>
                  <?php echo getLicenseAlert(); ?>
                  <br /><input type="submit" id="save" name="save" onclick="JavaScript:saveOnClick()" value="<convert>#label=76<convert>" class="button1" /><!--Valider--><input type="submit" id="cancel<?php echo $local_cat.$array[$i]["Id"]; ?>" name="cancel" onclick="" value="<convert>#label=77<convert>" class="button1" /><!--Annuler-->
<?php
            }
?>
                </td>
              </tr>
            </table>
<?php
          if ($edition) {
?>
            </form>
<?php
          }
        }
        if (allowAccess(location_edit_all) && !$is_change) { //$edition) { // && ($array['Count'] < 1)
?>
<!--#################### NEW LOCATION #######################################-->
          <a name="new_<?php echo $local_cat; ?>_anchor"></a>
          <div id="new_<?php echo $local_cat; ?>" style="display:none;background-color:white;">
            <form id="new_<?php echo $local_cat; ?>_form" name="new_<?php echo $local_cat; ?>_form" method="post" action="">
              <table class="container">
                <tr class="container">
                  <td class="sub_prop">
                  </td>
                  <td class="sub_container">
                    <div class="info">
                      <?php echo getTopBubble(); ?>
                      <convert>#label=530<convert><!--Indiquez ici comment accèder à cette entrée depuis l'extérieur.<br />
                      Ex: Entrer dans le hameau de Chougeat puis prendre le premier chemin à droite, l'entrée se situe 200m après un grand virage, dans les bois en direction du Nord ...-->
                      <?php echo getBotBubble(); ?>
                    </div>
                    <div class="sub_content">
                      <label for="body<?php echo $local_cat; ?>"><convert>#label=345<convert><!--Décrivez l'accès à cette entrée--> :</label><br />
                      <textarea class="input1" id="body<?php echo $local_cat; ?>" name="body" rows="6" cols="" onkeyup="JavaScript:bodyOnKeyUp(this,'length_display_<?php echo $local_cat; ?>');"></textarea><br />
                      <span id="length_display_<?php echo $local_cat; ?>">0</span> <convert>#label=238<convert><!--caractères sur--> 20 000.
                    </div>
                    <input type="hidden" id="new_category<?php echo $local_cat; ?>" name="new_category" value="<?php echo $local_cat; ?>" />
                    <?php echo getLicenseAlert(); ?>
                    <br /><input type="submit" id="new<?php echo $local_cat; ?>" name="new" onclick="JavaScript:saveOnClick()" value="<convert>#label=360<convert>" class="button1" /><!--Ajouter--><input type="button" id="cancel<?php echo $local_cat; ?>" name="cancel" value="<convert>#label=77<convert>" class="button1" onclick="JavaScript:cancelElement('<?php echo $local_cat; ?>');" />
                  </td>
                </tr>
              </table>
            </form>
          </div>
<?php
        }
?>
        </div>
<?php } ?>
<!--#################### DESCRIPTION ########################################-->
<?php if ($descriptions['Count']>0 || allowAccess(description_edit_all)) {
  $local_cat = "description";
  $array = $descriptions;
?>
        <div id="description" class="div_2_r">
<?php if (!$is_change) { ?>
          <span class="generic_tools">
<?php if (allowAccess(description_edit_all)) { ?>
            <a href="JavaScript:addElement('<?php echo $local_cat; ?>');" title="<convert>#label=54<convert>" class="nothing"><img src="../images/icons/add.png" class="icon" alt="<convert>#label=54<convert>" /></a>
<?php } ?>
            <a href="JavaScript:refresh();" title="<convert>#label=334<convert>" class="nothing"><img src="../images/icons/refresh.png" class="icon" alt="<convert>#label=334<convert>" /></a><!--Actualiser la page-->
<?php if ($array['Count']>0) { ?>
            <a href="JavaScript:badContent('<?php echo $local_cat; ?>',xtdGetElementById('hidden_name').innerHTML);" title="<convert>#label=313<convert>" class="nothing"><img src="../images/icons/warning.png" class="icon" alt="<convert>#label=313<convert>" /></a>
<?php } ?>
          </span>
<?php } ?>
          <h2><convert>#label=346<convert><!--Descriptions de la cavité--></h2>
<?php
        $thisCategoryDeleted = ($dcat == $local_cat);
        if ($thisCategoryDeleted && $contributionDeleted) {
          echo $deletionWarning;
        }
        for($i=0;$i<$array['Count'];$i++) {
          $edition = ($is_change && ($chcat == $local_cat) && ($chid == $array[$i]["Id"]) && allowAccess(description_edit_all));
          $thisContribChanged = (($chcat == $local_cat) && ($chid == $array[$i]["Id"]));
          $thisContribNew = (($ncat == $local_cat) && ($nid == $array[$i]["Id"]));
          $thisContribEvol = ($thisContribChanged || ($thisCategoryDeleted && ($did == $array[$i]["Id"])));
          $no_change = ($no_change_possible && $thisContribEvol && allowAccess(description_edit_all));
          $thisContributionSaved = $contributionSaved && ($thisContribChanged || $thisContribNew);
          if($no_change) {
            echo $noChangePossibleError;
          }
          if($thisContributionSaved) {
            echo $saveWarning;
            echo $scoreMessage;
          }
          if ($edition) {
?>
            <form id="modif" name="modif" method="post" action="#<?php echo $local_cat.$array[$i]["Id"]; ?>">
<?php
          }
?>
            <a name="<?php echo $local_cat.$array[$i]["Id"]; ?>"></a>
            <table class="container" id="descriptioncontainertable<?php echo $i; ?>">
              <tr class="container">
                <td class="sub_prop">
<?php
            if (!$is_change) {
              if (allowAccess(description_edit_all)) {
?>
                  <div class="tools" id="toolsdescriptioncontainertable<?php echo $i; ?>">
                    <?php if($allowed_to_lock){ ?><a href="JavaScript:lockElement('<?php echo $local_cat; ?>',<?php echo $array[$i]["Id"]; ?>,'<?php echo $array[$i]["Locked"];?>');" title="<convert>#label=341<convert>" class="nothing"><?php } ?><img src="../images/icons/locker_<?php echo $array[$i]["Locked"];?>.png" class="icon" alt="<convert>#label=340<convert> : <?php echo $array[$i]["Locked"]; ?>" /><?php if($allowed_to_lock){ ?></a><?php } ?>
<?php
                if ($array[$i]["Locked"] == "NO") {
?>
                    <a href="JavaScript:editElement('<?php echo $local_cat; ?>',<?php echo $array[$i]["Id"]; ?>);" title="<convert>#label=53<convert>" class="nothing"><img src="../images/icons/edit.png" class="icon" alt="<convert>#label=53<convert>" /></a>
                    <a href="JavaScript:addElement('<?php echo $local_cat; ?>');" title="<convert>#label=54<convert>" class="nothing"><img src="../images/icons/add.png" class="icon" alt="<convert>#label=54<convert>" /></a>
<?php
                  if (allowAccess(description_delete_all)) {
?>
                    <a href="JavaScript:deleteElement('<?php echo $local_cat; ?>',<?php echo $array[$i]["Id"]; ?>);" title="<convert>#label=55<convert>" class="nothing"><img src="../images/icons/delete.png" class="icon" alt="<convert>#label=55<convert>" /></a>
<?php
                  }
                }
                //if ($array['Count'] < $countEntries) {
?>
                    <!--<a href="JavaScript:addElement('<?php echo $local_cat; ?>');" title="<convert>#label=54<convert>" class="nothing"><img src="../images/icons/add.png" class="icon" alt="<convert>#label=54<convert>" /></a>-->
<?php
                //}
?>
                  </div>
<?php
              }
            }
?>
                  <div class="who_when">
                    <convert>#label=337<convert><!--Posté par--> <b><?php echo $array[$i]["Author"];?></b><br />
                    <convert>#label=338<convert><!--Le--> <b><?php echo timeToStr($array[$i]["Date_inscription"]); ?></b><br /><br />
<?php
          if (isset($array[$i]["Date_reviewed"]) && !$edition) {
?>
                    <convert>#label=344<convert><!--Modifié par--> <b><?php echo $array[$i]["Reviewer"];?></b><br />
                    <convert>#label=338<convert><!--Le--> <b><?php echo timeToStr($array[$i]["Date_reviewed"]); ?></b>
<?php
          }
?>
                  </div>
                </td>
                <td class="sub_container">
                  <?php if ($edition) { ?><div class="info">
                    <?php echo getTopBubble(); ?>
                    <convert>#label=531<convert><!--Décrivez dans cette section le(s) chemin(s) que vous avez emprunté à l'intérieur de la cavité en entrant, ou en sortant, par cette entrée.<br />
                    Ex : P20 puis patte d'oie à gauche, escalade 1m50 puis descendre dans les blocs sur la droite de la salle ...-->
                    <?php echo getBotBubble(); ?>
                  </div><?php } ?>
                  <div class="sub_title">
<?php
										if ($array[$i]["Id_exit"] == $id) {
                      $exit_id = getEntryId($local_cat, $array[$i]["Id"]);
                      $exit_name = getEntryName($exit_id);
                    } else {
                      $exit_name = $array[$i]["Exit_name"];
                      $exit_id = $array[$i]["Id_exit"];
                    }
                    if (isset($exit_id) && $exit_id != Select_default && $exit_id != "" && !$edition && $countEntries > 1) {
?>
                      <div style="text-align:left;font-weight:normal">
                        <b><convert>#label=347<convert><!--Traversée--> : </b><?php echo $entryName; ?> - <?php echo $exit_name; ?>
                      </div>
<?php
                    }
?>
                    <?php if ($edition) { ?><label for="title<?php echo $local_cat.$array[$i]["Id"]; ?>"><convert>#label=348<convert><!--Donnez un titre à votre description (-->300 <convert>#label=349<convert><!--carac. max.)--> :</label><br /><?php } ?>
                    <h3>
                      <?php if ($edition) { ?><input type="text" class="input1" name="title" id="title<?php echo $local_cat.$array[$i]["Id"]; ?>" value="<?php } ?><?php echo $array[$i]["Title"];?><?php if ($edition) { ?>" /><?php } ?>
                    </h3>
                  </div>
                  <div class="sub_content">
                    <?php if ($edition) {
                            if ($countEntries > 1) {
                              $sql_exits = getSqlExits($id, $local_cat, $exit_id);
                    ?>
                      <label for="id_exit"><convert>#label=350<convert><!--Autre entrée si traversée--> : </label>
                      <select class="select1" name="id_exit" id="id_exit">
<?php
                        $msg = "- <convert>#label=351<convert> -";//N'est pas une traversée
                        $comparedCol = "value";
                        $textCol = "text";
                        $selected = $exit_id;
                        echo getOptions($sql_exits, $msg, $selected, $comparedCol, $textCol);
?>
                      </select><br /><br />
                    <?php   } ?>
                      <label for="body<?php echo $local_cat.$array[$i]["Id"]; ?>"><convert>#label=352<convert><!--Décrivez votre parcours dans la cavité--> :</label><br />
                      <textarea class="input1" id="body<?php echo $local_cat.$array[$i]["Id"]; ?>" name="body" rows="6" cols="" onkeyup="JavaScript:bodyOnKeyUp(this,'length_display_<?php echo $local_cat.$array[$i]["Id"]; ?>');"><?php echo $array[$i]["Body"];?></textarea><br />
                      <span id="length_display_<?php echo $local_cat.$array[$i]["Id"]; ?>">0</span> <convert>#label=238<convert><!--caractères sur--> 20 000.
                      <input type="hidden" id="oldbody" name="oldbody" value="<?php echo $array[$i]["Body"];?>" /> <!-- //htmlentities(-->
                      <input type="hidden" id="oldtitle" name="oldtitle" value="<?php echo $array[$i]["Title"];?>" />
                    <?php } else {
                      echo getRef(replaceLinks(nl2br($array[$i]["Body"]))); //htmlentities(
                          } ?>
                  </div>
<?php
            if ($edition) {
?>
                  <?php echo getLicenseAlert(); ?>
                  <br /><input type="submit" id="save" name="save" onclick="JavaScript:saveOnClick()" value="<convert>#label=76<convert>" class="button1" /><!--Valider--><input type="submit" id="cancel<?php echo $local_cat.$array[$i]["Id"]; ?>" name="cancel" onclick="" value="<convert>#label=77<convert>" class="button1" /><!--Annuler-->
<?php
            }
?>
                </td>
              </tr>
            </table>
<?php
          if ($edition) {
?>
            </form>
<?php
          }
        }
        if (allowAccess(description_edit_all) && !$is_change) { //$edition) { // && $array['Count'] == 0 (($array['Count'] < $countEntries) || ($array['Count'] == 0))
          $sql_exits = getSqlExits($id, $local_cat);
?>
<!--#################### NEW DESCRIPTION ####################################-->
          <a name="new_<?php echo $local_cat; ?>_anchor"></a>
          <div id="new_<?php echo $local_cat; ?>" style="display:none;background-color:white;">
            <form id="new_<?php echo $local_cat; ?>_form" name="new_<?php echo $local_cat; ?>_form" method="post" action="">
              <table class="container">
                <tr class="container">
                  <td class="sub_prop">
                  </td>
                  <td class="sub_container">
                    <div class="info">
                      <?php echo getTopBubble(); ?>
                      <convert>#label=531<convert><!--Décrivez dans cette section le(s) chemin(s) que vous avez emprunté à l'intérieur de la cavité en entrant, ou en sortant, par cette entrée.<br />
                      Ex : P20 puis patte d'oie à gauche, escalade 1m50 puis descendre dans les blocs sur la droite de la salle ...-->
                      <?php echo getBotBubble(); ?>
                    </div>
                    <div class="sub_title">
                      <label for="title<?php echo $local_cat; ?>"><convert>#label=348<convert><!--Donnez un titre à votre description (-->300 <convert>#label=349<convert><!--carac. max.)--> :</label><br />
                      <h3>
                        <input type="text" class="input1" name="title" id="title<?php echo $local_cat; ?>" value="" />
                      </h3>
                    </div>
                    <div class="sub_content">
                    <?php if ($countEntries > 1) { ?>
                      <label for="id_exit"><convert>#label=350<convert><!--Autre entrée si traversée--> : </label>
                      <select class="select1" name="id_exit" id="id_exit<?php echo $local_cat; ?>">
<?php
                        $msg = "- <convert>#label=351<convert> -";//N'est pas une traversée
                        $comparedCol = "value";
                        $textCol = "text";
                        $selected = "";
                        echo getOptions($sql_exits, $msg, $selected, $comparedCol, $textCol);
?>
                      </select><br /><br />
                    <?php } ?>
                      <label for="body<?php echo $local_cat; ?>"><convert>#label=352<convert><!--Décrivez votre parcours dans la cavité--> :</label><br />
                      <textarea class="input1" id="body<?php echo $local_cat; ?>" name="body" rows="6" cols="" onkeyup="JavaScript:bodyOnKeyUp(this,'length_display_<?php echo $local_cat; ?>');"></textarea><br />
                      <span id="length_display_<?php echo $local_cat; ?>">0</span> <convert>#label=238<convert><!--caractères sur--> 20 000.
                    </div>
                    <input type="hidden" id="new_category<?php echo $local_cat; ?>" name="new_category" value="<?php echo $local_cat; ?>" />
                    <?php echo getLicenseAlert(); ?>
                    <br /><input type="submit" id="new<?php echo $local_cat; ?>" name="new" onclick="JavaScript:saveOnClick()" value="<convert>#label=360<convert>" class="button1" /><!--Ajouter--><input type="button" id="cancel<?php echo $local_cat; ?>" name="cancel" value="<convert>#label=77<convert>" class="button1" onclick="JavaScript:cancelElement('<?php echo $local_cat; ?>');" />
                  </td>
                </tr>
              </table>
            </form>
          </div>
<?php
        }
?>
        </div>
<?php } ?>
<!--#################### RIGGING ############################################-->
<?php if ($riggings['Count']>0 || allowAccess(rigging_edit_all)) {
  $local_cat = "rigging";
  $array = $riggings;
?>
        <div id="rigging" class="div_2_r">
<?php if (!$is_change) { ?>
          <span class="generic_tools">
<?php if (allowAccess(rigging_edit_all)) { ?>
            <a href="JavaScript:addElement('<?php echo $local_cat; ?>');" title="<convert>#label=54<convert>" class="nothing"><img src="../images/icons/add.png" class="icon" alt="<convert>#label=54<convert>" /></a>
<?php } ?>
            <a href="JavaScript:refresh();" title="<convert>#label=334<convert>" class="nothing"><img src="../images/icons/refresh.png" class="icon" alt="<convert>#label=334<convert>" /></a><!--Actualiser la page-->
<?php if ($array['Count']>0) { ?>
            <a href="JavaScript:badContent('<?php echo $local_cat; ?>',xtdGetElementById('hidden_name').innerHTML);" title="<convert>#label=313<convert>" class="nothing"><img src="../images/icons/warning.png" class="icon" alt="<convert>#label=313<convert>" /></a>
<?php } ?>
          </span>
<?php } ?>
          <h2><convert>#label=353<convert><!--Fiches d'équipement--></h2>
<?php
        $thisCategoryDeleted = ($dcat == $local_cat);
        if ($thisCategoryDeleted && $contributionDeleted) {
          echo $deletionWarning;
        }
        for($i=0;$i<$array['Count'];$i++) {
          $edition = ($is_change && ($chcat == $local_cat) && ($chid == $array[$i]["Id"]) && allowAccess(rigging_edit_all));
          $thisContribChanged = (($chcat == $local_cat) && ($chid == $array[$i]["Id"]));
          $thisContribNew = (($ncat == $local_cat) && ($nid == $array[$i]["Id"]));
          $thisContribEvol = ($thisContribChanged || ($thisCategoryDeleted && ($did == $array[$i]["Id"])));
          $no_change = ($no_change_possible && $thisContribEvol && allowAccess(rigging_edit_all));
          $thisContributionSaved = $contributionSaved && ($thisContribChanged || $thisContribNew);
          $localRigContId = rand(0,999);
          if($no_change) {
            echo $noChangePossibleError;
          }
          if($thisContributionSaved) {
            echo $saveWarning;
            echo $scoreMessage;
          }
          if ($edition) {
?>
            <form id="modif" name="modif" method="post" action="#<?php echo $local_cat.$array[$i]["Id"]; ?>">
<?php
          }
?>
            <a name="<?php echo $local_cat.$array[$i]["Id"]; ?>"></a>
            <table class="container" id="riggingcontainertable<?php echo $i; ?>">
              <tr class="container">
                <td class="sub_prop">
<?php
            if (!$is_change) {
              if (allowAccess(rigging_edit_all)) {
?>
                  <div class="tools" id="toolsriggingcontainertable<?php echo $i; ?>">
                    <?php if($allowed_to_lock){ ?><a href="JavaScript:lockElement('<?php echo $local_cat; ?>',<?php echo $array[$i]["Id"]; ?>,'<?php echo $array[$i]["Locked"];?>');" title="<convert>#label=341<convert>" class="nothing"><?php } ?><img src="../images/icons/locker_<?php echo $array[$i]["Locked"];?>.png" class="icon" alt="<convert>#label=340<convert> : <?php echo $array[$i]["Locked"]; ?>" /><?php if($allowed_to_lock){ ?></a><?php } ?>
<?php
                if ($array[$i]["Locked"] == "NO") {
?>
                    <a href="JavaScript:editElement('<?php echo $local_cat; ?>',<?php echo $array[$i]["Id"]; ?>);" title="<convert>#label=53<convert>" class="nothing"><img src="../images/icons/edit.png" class="icon" alt="<convert>#label=53<convert>" /></a>
                    <a href="JavaScript:addElement('<?php echo $local_cat; ?>');" title="<convert>#label=54<convert>" class="nothing"><img src="../images/icons/add.png" class="icon" alt="<convert>#label=54<convert>" /></a>
<?php
                  if (allowAccess(rigging_delete_all)) {
?>
                    <a href="JavaScript:deleteElement('<?php echo $local_cat; ?>',<?php echo $array[$i]["Id"]; ?>);" title="<convert>#label=55<convert>" class="nothing"><img src="../images/icons/delete.png" class="icon" alt="<convert>#label=55<convert>" /></a>
<?php
                  }
                }
                //if ($array['Count'] < $countEntries) {
?>
                    <!--<a href="JavaScript:addElement('<?php echo $local_cat; ?>');" title="<convert>#label=54<convert>" class="nothing"><img src="../images/icons/add.png" class="icon" alt="<convert>#label=54<convert>" /></a>-->
<?php
                //}
?>
                  </div>
<?php
              }
            }
?>
                  <div class="who_when">
                    <convert>#label=337<convert><!--Posté par--> <b><?php echo $array[$i]["Author"];?></b><br />
                    <convert>#label=338<convert><!--Le--> <b><?php echo timeToStr($array[$i]["Date_inscription"]); ?></b><br /><br />
<?php
            if (isset($array[$i]["Date_reviewed"]) && !$edition) {
?>
                    <convert>#label=344<convert><!--Modifié par--> <b><?php echo $array[$i]["Reviewer"];?></b><br />
                    <convert>#label=338<convert><!--Le--> <b><?php echo timeToStr($array[$i]["Date_reviewed"]); ?></b>
<?php
            }
?>
                  </div>
                </td>
                <td class="sub_container">
                  <?php if ($edition) { ?><div class="info">
                    <?php echo getTopBubble(); ?>
                    <convert>#label=532<convert><!--Listez ici les obstacles nécessitant l'installation de dispositifs de sécurité, ainsi que la quantité de ces dispositifs.<br />
                    Ex : --><br />
                    <table border="1px" cellspacing="0px">
                      <tr>
                        <th><convert>#label=356<convert><!--Obstacles--></th>
                        <th><convert>#label=358<convert><!--Cordes--></th>
                        <th><convert>#label=359<convert><!--Amarrages--></th>
                        <th><convert>#label=357<convert><!--Observations--></th>
                      </tr>
                      <tr>
                        <convert>#label=533<convert>
                        <!--td>P33 de l'entrée</td>
                        <td>50m</td>
                        <td>2S en Y, descendre de 10m, 2S en Y décalés sur la gauche,descente de 10m, 1S frac, decsendre de de 10m, 1 Dév (S ou AN), descendre de 20m</td>
                        <td>Bien rester sur la gauche du puit, à l'écart des embruns.</td-->
                      </tr>
                    </table>
                    <?php echo getBotBubble(); ?>
                  </div><?php } ?>
                  <div class="sub_title">
<?php
            if ($array[$i]["Id_exit"] == $id) {
              $exit_id = getEntryId($local_cat, $array[$i]["Id"]);
              $exit_name = getEntryName($exit_id);
            } else {
              $exit_name = $array[$i]["Exit_name"];
              $exit_id = $array[$i]["Id_exit"];
            }
            if (isset($exit_id) && $exit_id != Select_default && !$edition && $exit_id != "" && $countEntries > 1) {
?>
                      <div style="text-align:left;font-weight:normal">
                        <b><convert>#label=347<convert><!--Traversée--> : </b><?php echo $entryName; ?> - <?php echo $exit_name; ?>
                      </div>
<?php
            }
?>
                    <?php if ($edition) { ?><label for="title<?php echo $local_cat.$array[$i]["Id"]; ?>"><convert>#label=354<convert><!--Donnez un titre à votre fiche d'équipement (-->300 <convert>#label=355<convert><!--carac. max.)--> :</label><br /><?php } ?>
                    <h3>
                      <?php if ($edition) { ?><input type="text" class="input1" name="title" id="title<?php echo $local_cat.$array[$i]["Id"]; ?>" value="<?php } ?><?php echo $array[$i]["Title"];?><?php if ($edition) { ?>" /><?php } ?>
                    </h3>
                  </div>
                  <div class="sub_content">
<?php       if ($edition) {
              if ($countEntries > 1) {
                $sql_exits = getSqlExits($id, $local_cat, $exit_id);
?>
                      <label for="id_exit"><convert>#label=350<convert><!--Autre entrée si traversée--> : </label>
                      <select class="select1" name="id_exit" id="id_exit">
<?php
                        $msg = "- <convert>#label=351<convert> -";//N'est pas une traversée
                        $comparedCol = "value";
                        $textCol = "text";
                        $selected = $exit_id;
                        echo getOptions($sql_exits, $msg, $selected, $comparedCol, $textCol);
?>
                      </select><br /><br />
<?php         }
            }
                          $obstacles = explode($rig_separator,$array[$i]["Obstacles"]);
                          $ropes = explode($rig_separator,$array[$i]["Ropes"]);
                          $anchors = explode($rig_separator,$array[$i]["Anchors"]);
                          $observations = explode($rig_separator,$array[$i]["Observations"]);
                          $rig_length = max(count($obstacles),count($ropes),count($anchors),count($observations));
?>
                      <?php if ($edition) { ?><span style="font-style:italic;"><convert>#label=535<convert><!--Listez les obstacles et la quantité de dispositifs de sécurité--> :</span><br /><?php } ?>
                      <table border="0" cellspacing="1" cellpadding="0" class="rigging_container form_tbl">
                        <tbody id="rc_<?php echo $localRigContId; ?>_rigging_container">
                          <tr>
                            <?php if ($edition) { ?><th></th><?php } ?>
                            <th><convert>#label=356<convert><!--Obstacles--></th>
                            <th><convert>#label=358<convert><!--Cordes--></th>
                            <th><convert>#label=359<convert><!--Amarrages--></th>
                            <th><convert>#label=357<convert><!--Observations--></th>
                          </tr>
                          <?php for ($k=0;$k<$rig_length;$k++) { ?><tr>
                            <?php if ($edition) { ?><td class="field rigging_buttons">
                              <span><img src="../images/icons/delete.png" title="<convert>#label=529<convert>" name="remove_line" alt="<convert>#label=529<convert>" /></span><br /><!--supprimer-->
                              <span><img src="../images/icons/insert.png" title="<convert>#label=526<convert>" name="insert_line" alt="<convert>#label=526<convert>" /></span><br /><!--inserer-->
                              <span><img src="../images/icons/mvup.png" title="<convert>#label=527<convert>" name="move_up_line" alt="<convert>#label=527<convert>" /></span><br /><!--monter-->
                              <span><img src="../images/icons/mvdwn.png" title="<convert>#label=528<convert>" name="move_down_line" alt="<convert>#label=528<convert>" /></span><!--descendre-->
                            </td>
                            <td class="field">
                              <textarea class="input1" name="Obstacle_cell" rows="4" cols=""><?php echo $obstacles[$k]; ?></textarea>
                            </td>
                            <td class="field">
                              <textarea class="input1" name="Ropes_cell" rows="4" cols=""><?php echo $ropes[$k]; ?></textarea>
                            </td>
                            <td class="field">
                              <textarea class="input1" name="Anchors_cell" rows="4" cols=""><?php echo $anchors[$k]; ?></textarea>
                            </td>
                            <td class="field">
                              <textarea class="input1" name="Observations_cell" rows="4" cols=""><?php echo $observations[$k]; ?></textarea>
                            </td><?php } else { ?>
                            <td>
                              <?php echo getRef(nl2br($obstacles[$k])); ?> <!--//htmlentities(-->
                            </td>
                            <td>
                              <?php echo getRef(nl2br($ropes[$k])); ?> <!--//htmlentities(-->
                            </td>
                            <td>
                              <?php echo getRef(nl2br($anchors[$k])); ?> <!--//htmlentities(-->
                            </td>
                            <td>
                              <?php echo getRef(replaceLinks(nl2br($observations[$k]))); ?> <!--//htmlentities(-->
                            </td><?php } ?>
                          </tr><?php } ?></tbody></table>
                      <?php if ($edition) { ?><div style="width:100%;">
                        <span><img src="../images/icons/add.png" class="rigging_button" style="padding-left:5px;" title="<convert>#label=360<convert>" alt="<convert>#label=360<convert>" onclick="JavaScript:addOnClick('rc_<?php echo $localRigContId; ?>_rigging_container');" /></span>
                      </div>
                      <?php echo getLicenseAlert(); ?>
                      <?php } ?>
                  </div>
<?php
            if ($edition) {
?>
                  <input type="hidden" name="obstacles" id="obstacles" value="" />
                  <input type="hidden" name="ropes" id="ropes" value="" />
                  <input type="hidden" name="anchors" id="anchors" value="" />
                  <input type="hidden" name="observations" id="observations" value="" />
                  <input type="hidden" id="oldtitle" name="oldtitle" value="<?php echo $array[$i]["Title"];?>" />
                  <br /><input type="submit" id="save" name="save" onclick="JavaScript:saveOnClick('rigging')" value="<convert>#label=76<convert>" class="button1" /><!--Valider--><input type="submit" id="cancel<?php echo $local_cat.$array[$i]["Id"]; ?>" name="cancel" onclick="" value="<convert>#label=77<convert>" class="button1" /><!--Annuler-->
<?php
            }
?>
                </td>
              </tr>
            </table>
<?php
          if ($edition) {
?>
            </form>
<?php
          }
        }
        //if (allowAccess(rigging_edit_all) && (($array['Count'] < $countEntries) || ($array['Count'] == 0)) && !$edition) {
        if (allowAccess(rigging_edit_all) && !$is_change) { //$edition) {
          $sql_exits = getSqlExits($id, $local_cat);
?>
<!--#################### NEW RIGGING ########################################-->
          <a name="new_<?php echo $local_cat; ?>_anchor"></a>
          <div id="new_<?php echo $local_cat; ?>" style="display:none;background-color:white;">
            <form id="new_<?php echo $local_cat; ?>_form" name="new_<?php echo $local_cat; ?>_form" method="post" action="">
              <table class="container">
                <tr class="container">
                  <td class="sub_prop">
                  </td>
                  <td class="sub_container">
                    <div class="info">
                      <?php echo getTopBubble(); ?>
                      <convert>#label=532<convert><!--Listez ici les obstacles nécessitant l'installation de dispositifs de sécurité, ainsi que la quantité de ces dispositifs.<br />
                      Ex : --><br />
                      <table border="1px" cellspacing="0px">
                        <tr>
                          <th><convert>#label=356<convert><!--Obstacles--></th>
                          <th><convert>#label=358<convert><!--Cordes--></th>
                          <th><convert>#label=359<convert><!--Amarrages--></th>
                          <th><convert>#label=357<convert><!--Observations--></th>
                        </tr>
                        <tr>
                          <convert>#label=533<convert>
                          <!--td>P33 de l'entrée</td>
                          <td>50m</td>
                          <td>2S en Y, descendre de 10m, 2S en Y décalés sur la gauche,descente de 10m, 1S frac, decsendre de de 10m, 1 Dév (S ou AN), descendre de 20m</td>
                          <td>Bien rester sur la gauche du puit, à l'écart des embruns.</td-->
                        </tr>
                      </table>
                      <?php echo getBotBubble(); ?>
                    </div>
                    <div class="sub_title">
                      <label for="title<?php echo $local_cat; ?>"><convert>#label=354<convert><!--Donnez un titre à votre fiche d'équipement (-->300 <convert>#label=355<convert><!--carac. max.)--> :</label><br />
                      <h3>
                        <input type="text" class="input1" name="title" id="title<?php echo $local_cat; ?>" value="" />
                      </h3>
                    </div>
                    <div class="sub_content">
                    <?php if ($countEntries > 1) { ?>
                      <label for="id_exit"><convert>#label=350<convert><!--Autre entrée si traversée--> :</label>
                      <select class="select1" name="id_exit" id="id_exit<?php echo $local_cat; ?>">
<?php
                        $msg = "- <convert>#label=351<convert> -";//N'est pas une traversée
                        $comparedCol = "value";
                        $textCol = "text";
                        $selected = "";
                        echo getOptions($sql_exits, $msg, $selected, $comparedCol, $textCol);
?>
                      </select>
                    <?php } ?>
                    </div>
                    <span style="font-style:italic;"><convert>#label=535<convert><!--Listez les obstacles et la quantité de dispositifs de sécurité--> :</span><br />
                    <table border="0" cellspacing="1" cellpadding="0" class="rigging_container form_tbl">
                      <tbody id="new_rigging_container">
                        <tr>
                          <th></th>
                          <th><convert>#label=356<convert><!--Obstacles--></th>
                          <th><convert>#label=358<convert><!--Cordes--></th>
                          <th><convert>#label=359<convert><!--Amarrages--></th>
                          <th><convert>#label=357<convert><!--Observations--></th>
                        </tr>
                        <tr>
                          <td class="field rigging_buttons">
                            <span><img src="../images/icons/delete.png" title="<convert>#label=529<convert>" name="remove_line" alt="<convert>#label=529<convert>" /></span><br /><!--supprimer-->
                            <span><img src="../images/icons/insert.png" title="<convert>#label=526<convert>" name="insert_line" alt="<convert>#label=526<convert>" /></span><br /><!--inserer-->
                            <span><img src="../images/icons/mvup.png" title="<convert>#label=527<convert>" name="move_up_line" alt="<convert>#label=527<convert>" /></span><br /><!--monter-->
                            <span><img src="../images/icons/mvdwn.png" title="<convert>#label=528<convert>" name="move_down_line" alt="<convert>#label=528<convert>" /></span><!--descendre-->
                          </td>
                          <td class="field">
                            <textarea class="input1" name="Obstacle_cell" rows="4" cols=""></textarea>
                          </td>
                          <td class="field">
                            <textarea class="input1" name="Ropes_cell" rows="4" cols=""></textarea>
                          </td>
                          <td class="field">
                            <textarea class="input1" name="Anchors_cell" rows="4" cols=""></textarea>
                          </td>
                          <td class="field">
                            <textarea class="input1" name="Observations_cell" rows="4" cols=""></textarea>
                          </td>
                        </tr></tbody></table>
                    <div style="width:100%;">
                      <span><img src="../images/icons/add.png" class="rigging_button" style="padding-left:5px;" title="<convert>#label=360<convert>" alt="<convert>#label=360<convert>" onclick="JavaScript:addOnClick('new_rigging_container');" /></span>
                    </div>
                    <?php echo getLicenseAlert(); ?>
                    <input type="hidden" id="new_category<?php echo $local_cat; ?>" name="new_category" value="<?php echo $local_cat; ?>" />
                    <input type="hidden" name="obstacles" id="obstacles" value="" />
                    <input type="hidden" name="ropes" id="ropes" value="" />
                    <input type="hidden" name="anchors" id="anchors" value="" />
                    <input type="hidden" name="observations" id="observations" value="" />
                    <br /><input type="submit" id="new<?php echo $local_cat; ?>" name="new" onclick="JavaScript:saveOnClick('rigging')" value="<convert>#label=360<convert>" class="button1" /><!--Ajouter--><input type="button" id="cancel<?php echo $local_cat; ?>" name="cancel" value="<convert>#label=77<convert>" class="button1" onclick="JavaScript:cancelElement('<?php echo $local_cat; ?>');" />
                  </td>
                </tr>
              </table>
            </form>
          </div>
<?php
        }
?>
        <table style="display:none;">
          <tr id="rigging_source">
            <td class="field rigging_buttons">
              <span><img src="../images/icons/delete.png" title="<convert>#label=529<convert>" name="remove_line" alt="<convert>#label=529<convert>" /></span><br /><!--supprimer-->
              <span><img src="../images/icons/insert.png" title="<convert>#label=526<convert>" name="insert_line" alt="<convert>#label=526<convert>" /></span><br /><!--inserer-->
              <span><img src="../images/icons/mvup.png" title="<convert>#label=527<convert>" name="move_up_line" alt="<convert>#label=527<convert>" /></span><br /><!--monter-->
              <span><img src="../images/icons/mvdwn.png" title="<convert>#label=528<convert>" name="move_down_line" alt="<convert>#label=528<convert>" /></span><!--descendre-->
            </td>
            <td class="field">
              <textarea class="input1" name="Obstacle_cell" rows="4" cols=""></textarea>
            </td>
            <td class="field">
              <textarea class="input1" name="Ropes_cell" rows="4" cols=""></textarea>
            </td>
            <td class="field">
              <textarea class="input1" name="Anchors_cell" rows="4" cols=""></textarea>
            </td>
            <td class="field">
              <textarea class="input1" name="Observations_cell" rows="4" cols=""></textarea>
            </td>
          </tr>
        </table>
        </div>
<?php } ?>
<!--#################### TOPOGRAPHY ##########################################-->
<?php if (($topographies['Count']>0 || allowAccess(request_edit_mine)) && allowAccess(topo_view_all)) {
  $local_cat = "topography";
  $array = $topographies;
?>
        <div id="topography" class="div_2_r">
<?php if (!$is_change) { ?>
          <span class="generic_tools">
<?php if (allowAccess(request_edit_mine)) { ?>
            <a href="JavaScript:openWindow('request_<?php echo $_SESSION['language']; ?>.php?type=edit&amp;entry_id=<?php echo $id; ?>', '', 1150, 600);" title="<convert>#label=54<convert>" class="nothing"><img src="../images/icons/add.png" class="icon" alt="<convert>#label=54<convert>" /></a>
<?php } ?>
            <a href="JavaScript:refresh();" title="<convert>#label=334<convert>" class="nothing"><img src="../images/icons/refresh.png" class="icon" alt="<convert>#label=334<convert>" /></a><!--Actualiser la page-->
          </span>
<?php } ?>
          <h2><convert>#label=815<convert><!--Topographies--></h2>
<?php
        for($i=0;$i<$array['Count'];$i++) {
          $is_same_request = ($i > 0);
          if ($is_same_request) {
            //$is_same_request = ($array[$i]['Author'] == $array[$i-1]['Author']); // && $array[$i]['Date_inscription'] == $array[$i-1]['Date_inscription']);
            $is_same_request = ($array[$i]['Id_request'] == $array[$i-1]['Id_request']);
          }
          //$will_change_request = (($i+1) >= $array['Count'] || $array[$i]['Author'] != $array[$i+1]['Author']); // || $array[$i]['Date_inscription'] != $array[$i+1]['Date_inscription']);
          $will_change_request = (($i+1) >= $array['Count'] || $array[$i]['Id_request'] != $array[$i+1]['Id_request']);
          if (!$is_same_request) {
?>
            <a name="<?php echo $local_cat.$array[$i]["Id"]; ?>"></a>
            <table class="container" id="topographycontainertable<?php echo $i; ?>">
              <tr class="container">
                <td class="sub_prop">
                  <div class="who_when">
                    <convert>#label=337<convert><!--Posté par--> <b><?php echo $array[$i]['Author'];?></b><br />
                    <convert>#label=338<convert><!--Le--> <b><?php echo timeToStr($array[$i]['Date_inscription']);?></b>
                  </div>
                </td>
                <td class="sub_container">
                  <div class="sub_content">
<?php       if (!isset($_POST['print'])) { ?>
                    <ul>
<?php       } ?>
<?php     } ?>
<?php     if (!isset($_POST['print'])) { ?>
                      <li><a href="<?php echo $array[$i]['Path']; ?>" target="_blank" title="<convert>#label=833<convert>"><?php echo $array[$i]['Name']; ?></a></li><!--voir la topographie-->
<?php     } else { ?>
                    <div style="font-weight:bold;border-bottom:1px solid black;"><?php echo $array[$i]['Name']; ?>:</div>
                    <img src="<?php echo $array[$i]['Path']; ?>" alt="<?php echo $array[$i]['Name']; ?>" title="<?php echo $array[$i]['Name']; ?>" class="topography" />
<?php     } ?>
<?php     if ($will_change_request) { ?>
<?php       if (!isset($_POST['print'])) { ?>
                    </ul>
<?php       } ?>
                    <div class="credit"><span id="topo_bad_content"><a href="JavaScript:badContent('<?php echo $local_cat; ?>','<?php echo $array[$i]['Id_request']; ?>');" title="<convert>#label=313<convert>" class="nothing"><img src="../images/icons/warning.png" class="icon" alt="<convert>#label=313<convert>" /></a></span> (#<?php echo $array[$i]['Id_request']; ?>) <b><convert>#label=820<convert><!--Auteurs des topographies-->:</b> <?php echo $array[$i]['Authors']; ?></div>
                  </div>
                </td>
              </tr>
            </table>
<?php
          }
        }
?>
        </div>
<?php
      }
?>
<!--#################### HISTORY ############################################-->
<?php if ($histories['Count']>0 || allowAccess(history_edit_all)) {
  $local_cat = "history";
  $array = $histories;
?>
        <div id="history" class="div_2_r">
<?php if (!$is_change) { ?>
          <span class="generic_tools">
<?php if (allowAccess(history_edit_all)) { ?>
            <a href="JavaScript:addElement('<?php echo $local_cat; ?>');" title="<convert>#label=54<convert>" class="nothing"><img src="../images/icons/add.png" class="icon" alt="<convert>#label=54<convert>" /></a><!--Ajouter une fiche de localisation-->
<?php } ?>
            <a href="JavaScript:refresh();" title="<convert>#label=334<convert>" class="nothing"><img src="../images/icons/refresh.png" class="icon" alt="<convert>#label=334<convert>" /></a><!--Actualiser la page-->
<?php if ($array['Count']>0) { ?>
            <a href="JavaScript:badContent('<?php echo $local_cat; ?>',xtdGetElementById('hidden_name').innerHTML);" title="<convert>#label=313<convert>" class="nothing"><img src="../images/icons/warning.png" class="icon" alt="<convert>#label=313<convert>" /></a>
<?php } ?>
          </span>
<?php } ?>
          <h2><convert>#label=593<convert><!--Historique--></h2>
<?php
        $thisCategoryDeleted = ($dcat == $local_cat);
        if ($thisCategoryDeleted && $contributionDeleted) {
          echo $deletionWarning;
        }
        for($i=0;$i<$array['Count'];$i++) {
          $edition = ($is_change && ($chcat == $local_cat) && ($chid == $array[$i]["Id"]) && allowAccess(history_edit_all));
          $thisContribChanged = (($chcat == $local_cat) && ($chid == $array[$i]["Id"]));
          $thisContribNew = (($ncat == $local_cat) && ($nid == $array[$i]["Id"]));
          $thisContribEvol = ($thisContribChanged || ($thisCategoryDeleted && ($did == $array[$i]["Id"])));
          $no_change = ($no_change_possible && $thisContribEvol && allowAccess(history_edit_all));
          $thisContributionSaved = $contributionSaved && ($thisContribChanged || $thisContribNew);
          if($no_change) {
            echo $noChangePossibleError;
          }
          if($thisContributionSaved) {
            echo $saveWarning;
            echo $scoreMessage;
          }
          if ($edition) {
?>
            <form id="modif" name="modif" method="post" action="#<?php echo $local_cat.$array[$i]["Id"]; ?>">
<?php
          }
?>
            <a name="<?php echo $local_cat.$array[$i]["Id"]; ?>"></a>
            <table class="container" id="historycontainertable<?php echo $i; ?>">
              <tr class="container">
                <td class="sub_prop">
<?php
            if (!$is_change) {
              if (allowAccess(history_edit_all)) {
?>
                  <div class="tools" id="toolshistorycontainertable<?php echo $i; ?>">
                    <?php if($allowed_to_lock){ ?><a href="JavaScript:lockElement('<?php echo $local_cat; ?>',<?php echo $array[$i]["Id"]; ?>,'<?php echo $array[$i]["Locked"];?>');" title="<convert>#label=341<convert>" class="nothing"><?php } ?><img src="../images/icons/locker_<?php echo $array[$i]["Locked"];?>.png" class="icon" alt="<convert>#label=340<convert> : <?php echo $array[$i]["Locked"]; ?>" /><?php if($allowed_to_lock){ ?></a><?php } ?><!--Verrouillé-->
<?php
                if ($array[$i]["Locked"] == "NO") {
?>
                    <a href="JavaScript:editElement('<?php echo $local_cat; ?>',<?php echo $array[$i]["Id"]; ?>);" title="<convert>#label=53<convert>" class="nothing"><img src="../images/icons/edit.png" class="icon" alt="<convert>#label=53<convert>" /></a>
                    <a href="JavaScript:addElement('<?php echo $local_cat; ?>');" title="<convert>#label=54<convert>" class="nothing"><img src="../images/icons/add.png" class="icon" alt="<convert>#label=54<convert>" /></a>
<?php
                  if (allowAccess(history_delete_all)) {
?>
                    <a href="JavaScript:deleteElement('<?php echo $local_cat; ?>',<?php echo $array[$i]["Id"]; ?>);" title="<convert>#label=55<convert>" class="nothing"><img src="../images/icons/delete.png" class="icon" alt="<convert>#label=55<convert>" /></a>
<?php
                  }
                }
?>
                  </div>
<?php
              }
            }
?>
                  <div class="who_when">
                    <convert>#label=337<convert><!--Posté par--> <b><?php echo $array[$i]["Author"];?></b><br />
                    <convert>#label=338<convert><!--Le--> <b><?php echo timeToStr($array[$i]["Date_inscription"]); ?></b><br /><br />
<?php
          if (isset($array[$i]["Date_reviewed"]) && !$edition) {
?>
                    <convert>#label=344<convert><!--Modifié par--> <b><?php echo $array[$i]["Reviewer"];?></b><br />
                    <convert>#label=338<convert><!--Le--> <b><?php echo timeToStr($array[$i]["Date_reviewed"]); ?></b>
<?php
          }
?>
                  </div>
                </td>
                <td class="sub_container">
                  <?php if ($edition) { ?><div class="info">
                    <?php echo getTopBubble(); ?>
                    <convert>#label=594<convert><!--Dans cette section vous pouvez résumer l'historique de la cavité.<br />Ex : Découverte en 1850 par Mr Dupont, les explorations se sont arrêtées sur le colmatage, qui a été désobstrué quelques dizaines d'années plus tard …-->
                    <?php echo getBotBubble(); ?>
                  </div><?php } ?>
                  <div class="sub_content">
                    <?php if ($edition) { ?>
                      <label for="body<?php echo $local_cat.$array[$i]["Id"]; ?>"><convert>#label=595<convert><!--Décrivez l'historique de cette cavité--> :</label><br />
                      <textarea class="input1" id="body<?php echo $local_cat.$array[$i]["Id"]; ?>" name="body" rows="6" cols="" onkeyup="JavaScript:bodyOnKeyUp(this,'length_display_<?php echo $local_cat.$array[$i]["Id"]; ?>');"><?php echo $array[$i]["Body"];?></textarea><br />
                      <span id="length_display_<?php echo $local_cat.$array[$i]["Id"]; ?>">0</span> <convert>#label=238<convert><!--caractères sur--> 20 000.
                      <input type="hidden" id="oldbody" name="oldbody" value="<?php echo $array[$i]["Body"];?>" /> <!--//htmlentities(-->
                    <?php } else {
                      echo getRef(replaceLinks(nl2br($array[$i]["Body"]))); // <!--htmlentities(-->
                    } ?>
                  </div>
<?php
            if ($edition) {
?>
                  <?php echo getLicenseAlert(); ?>
                  <br /><input type="submit" id="save" name="save" onclick="JavaScript:saveOnClick()" value="<convert>#label=76<convert>" class="button1" /><!--Valider--><input type="submit" id="cancel<?php echo $local_cat.$array[$i]["Id"]; ?>" name="cancel" onclick="" value="<convert>#label=77<convert>" class="button1" /><!--Annuler-->
<?php
            }
?>
                </td>
              </tr>
            </table>
<?php
          if ($edition) {
?>
            </form>
<?php
          }
        }
        if (allowAccess(history_edit_all) && !$is_change) { //$edition) { // && ($array['Count'] < 1)
?>
<!--#################### NEW HISTORY ########################################-->
          <a name="new_<?php echo $local_cat; ?>_anchor"></a>
          <div id="new_<?php echo $local_cat; ?>" style="display:none;background-color:white;">
            <form id="new_<?php echo $local_cat; ?>_form" name="new_<?php echo $local_cat; ?>_form" method="post" action="">
              <table class="container">
                <tr class="container">
                  <td class="sub_prop">
                  </td>
                  <td class="sub_container">
                    <div class="info">
                      <?php echo getTopBubble(); ?>
                      <convert>#label=594<convert><!--Dans cette section vous pouvez résumer l'historique de la cavité.<br />Ex : Découverte en 1850 par Mr Dupont, les explorations se sont arrêtées sur le colmatage, qui a été désobstrué quelques dizaines d'années plus tard …-->
                      <?php echo getBotBubble(); ?>
                    </div>
                    <div class="sub_content">
                      <label for="body<?php echo $local_cat; ?>"><convert>#label=595<convert><!--Décrivez l'historique de cette cavité--> :</label><br />
                      <textarea class="input1" id="body<?php echo $local_cat; ?>" name="body" rows="6" cols="" onkeyup="JavaScript:bodyOnKeyUp(this,'length_display_<?php echo $local_cat; ?>');"></textarea><br />
                      <span id="length_display_<?php echo $local_cat; ?>">0</span> <convert>#label=238<convert><!--caractères sur--> 20 000.
                    </div>
                    <input type="hidden" id="new_category<?php echo $local_cat; ?>" name="new_category" value="<?php echo $local_cat; ?>" />
                    <?php echo getLicenseAlert(); ?>
                    <br /><input type="submit" id="new<?php echo $local_cat; ?>" name="new" onclick="JavaScript:saveOnClick()" value="<convert>#label=360<convert>" class="button1" /><!--Ajouter--><input type="button" id="cancel<?php echo $local_cat; ?>" name="cancel" value="<convert>#label=77<convert>" class="button1" onclick="JavaScript:cancelElement('<?php echo $local_cat; ?>');" />
                  </td>
                </tr>
              </table>
            </form>
          </div>
<?php
        }
?>
        </div>
<?php } ?>
<!--#################### BIBLIOGRAPHY #######################################-->
<?php if ($bibliographies['Count']>0 || allowAccess(biblio_edit_all)) {
  $local_cat = "bibliography";
  $array = $bibliographies;
?>
        <div id="bibliography" class="div_2_r">
<?php if (!$is_change) { ?>
          <span class="generic_tools">
<?php if (allowAccess(biblio_edit_all)) { ?>
            <a href="JavaScript:addElement('<?php echo $local_cat; ?>');" title="<convert>#label=54<convert>" class="nothing"><img src="../images/icons/add.png" class="icon" alt="<convert>#label=54<convert>" /></a><!--Ajouter une fiche de localisation-->
<?php } ?>
            <a href="JavaScript:refresh();" title="<convert>#label=334<convert>" class="nothing"><img src="../images/icons/refresh.png" class="icon" alt="<convert>#label=334<convert>" /></a><!--Actualiser la page-->
<?php if ($array['Count']>0) { ?>
            <a href="JavaScript:badContent('<?php echo $local_cat; ?>',xtdGetElementById('hidden_name').innerHTML);" title="<convert>#label=313<convert>" class="nothing"><img src="../images/icons/warning.png" class="icon" alt="<convert>#label=313<convert>" /></a>
<?php } ?>
          </span>
<?php } ?>
          <h2><convert>#label=590<convert><!--Bibliographie--></h2>
<?php
        $thisCategoryDeleted = ($dcat == $local_cat);
        if ($thisCategoryDeleted && $contributionDeleted) {
          echo $deletionWarning;
        }
        for($i=0;$i<$array['Count'];$i++) {
          $edition = ($is_change && ($chcat == $local_cat) && ($chid == $array[$i]["Id"]) && allowAccess(biblio_edit_all));
          $thisContribChanged = (($chcat == $local_cat) && ($chid == $array[$i]["Id"]));
          $thisContribNew = (($ncat == $local_cat) && ($nid == $array[$i]["Id"]));
          $thisContribEvol = ($thisContribChanged || ($thisCategoryDeleted && ($did == $array[$i]["Id"])));
          $no_change = ($no_change_possible && $thisContribEvol && allowAccess(biblio_edit_all));
          $thisContributionSaved = $contributionSaved && ($thisContribChanged || $thisContribNew);
          if($no_change) {
            echo $noChangePossibleError;
          }
          if($thisContributionSaved) {
            echo $saveWarning;
            echo $scoreMessage;
          }
          if ($edition) {
?>
            <form id="modif" name="modif" method="post" action="#<?php echo $local_cat.$array[$i]["Id"]; ?>">
<?php
          }
?>
            <a name="<?php echo $local_cat.$array[$i]["Id"]; ?>"></a>
            <table class="container" id="bibliographycontainertable<?php echo $i; ?>">
              <tr class="container">
                <td class="sub_prop">
<?php
            if (!$is_change) {
              if (allowAccess(biblio_edit_all)) {
?>
                  <div class="tools" id="toolsbibliographycontainertable<?php echo $i; ?>">
                    <?php if($allowed_to_lock){ ?><a href="JavaScript:lockElement('<?php echo $local_cat; ?>',<?php echo $array[$i]["Id"]; ?>,'<?php echo $array[$i]["Locked"];?>');" title="<convert>#label=341<convert>" class="nothing"><?php } ?><img src="../images/icons/locker_<?php echo $array[$i]["Locked"];?>.png" class="icon" alt="<convert>#label=340<convert> : <?php echo $array[$i]["Locked"]; ?>" /><?php if($allowed_to_lock){ ?></a><?php } ?><!--Verrouillé-->
<?php
                if ($array[$i]["Locked"] == "NO") {
?>
                    <a href="JavaScript:editElement('<?php echo $local_cat; ?>',<?php echo $array[$i]["Id"]; ?>);" title="<convert>#label=53<convert>" class="nothing"><img src="../images/icons/edit.png" class="icon" alt="<convert>#label=53<convert>" /></a>
                    <a href="JavaScript:addElement('<?php echo $local_cat; ?>');" title="<convert>#label=54<convert>" class="nothing"><img src="../images/icons/add.png" class="icon" alt="<convert>#label=54<convert>" /></a>
<?php
                  if (allowAccess(biblio_delete_all)) {
?>
                    <a href="JavaScript:deleteElement('<?php echo $local_cat; ?>',<?php echo $array[$i]["Id"]; ?>);" title="<convert>#label=55<convert>" class="nothing"><img src="../images/icons/delete.png" class="icon" alt="<convert>#label=55<convert>" /></a>
<?php
                  }
                }
?>
                  </div>
<?php
              }
            }
?>
                  <div class="who_when">
                    <convert>#label=337<convert><!--Posté par--> <b><?php echo $array[$i]["Author"];?></b><br />
                    <convert>#label=338<convert><!--Le--> <b><?php echo timeToStr($array[$i]["Date_inscription"]); ?></b><br /><br />
<?php
          if (isset($array[$i]["Date_reviewed"]) && !$edition) {
?>
                    <convert>#label=344<convert><!--Modifié par--> <b><?php echo $array[$i]["Reviewer"];?></b><br />
                    <convert>#label=338<convert><!--Le--> <b><?php echo timeToStr($array[$i]["Date_reviewed"]); ?></b>
<?php
          }
?>
                  </div>
                </td>
                <td class="sub_container">
                  <?php if ($edition) { ?><div class="info">
                    <?php echo getTopBubble(); ?>
                    <convert>#label=591<convert><!--<ul><li>Pour les livres : NOM, Prénom (à répéter). Titre. Mention d'édition. Lieu de publication : éditeur, année d'édition. Nombre de pages. (Titre de la collection, n° de la collection)
                    ISBN</li><li>Pour les sites internet :  NOM, Prénom. Nom du site [en ligne]. Lieu de publication : éditeur, (date de consultation). Disponibilité et accès.</li></ul>-->
                    <?php echo getBotBubble(); ?>
                  </div><?php } ?>
                  <div class="sub_content">
                    <?php if ($edition) { ?>
                      <label for="body<?php echo $local_cat.$array[$i]["Id"]; ?>"><convert>#label=592<convert><!--Listez les references bibliographiques--> :</label><br />
                      <textarea class="input1" id="body<?php echo $local_cat.$array[$i]["Id"]; ?>" name="body" rows="6" cols="" onkeyup="JavaScript:bodyOnKeyUp(this,'length_display_<?php echo $local_cat.$array[$i]["Id"]; ?>');"><?php echo $array[$i]["Body"];?></textarea><br />
                      <span id="length_display_<?php echo $local_cat.$array[$i]["Id"]; ?>">0</span> <convert>#label=238<convert><!--caractères sur--> 20 000.
                      <input type="hidden" id="oldbody" name="oldbody" value="<?php echo $array[$i]["Body"];?>" /> <!--htmlentities(-->
                    <?php } else {
                      echo getRef(replaceLinks(nl2br($array[$i]["Body"]))); //<!--htmlentities(-->
                    } ?>
                  </div>
<?php
            if ($edition) {
?>
                  <?php echo getLicenseAlert(); ?>
                  <br /><input type="submit" id="save" name="save" onclick="JavaScript:saveOnClick()" value="<convert>#label=76<convert>" class="button1" /><!--Valider--><input type="submit" id="cancel<?php echo $local_cat.$array[$i]["Id"]; ?>" name="cancel" onclick="" value="<convert>#label=77<convert>" class="button1" /><!--Annuler-->
<?php
            }
?>
                </td>
              </tr>
            </table>
<?php
          if ($edition) {
?>
            </form>
<?php
          }
        }
        if (allowAccess(biblio_edit_all) && !$is_change) { //$edition) { // && ($array['Count'] < 1)
?>
<!--#################### NEW BIBLIOGRAPHY ###################################-->
          <a name="new_<?php echo $local_cat; ?>_anchor"></a>
          <div id="new_<?php echo $local_cat; ?>" style="display:none;background-color:white;">
            <form id="new_<?php echo $local_cat; ?>_form" name="new_<?php echo $local_cat; ?>_form" method="post" action="">
              <table class="container">
                <tr class="container">
                  <td class="sub_prop">
                  </td>
                  <td class="sub_container">
                    <div class="info">
                      <?php echo getTopBubble(); ?>
                      <convert>#label=591<convert><!--<ul><li>Pour les livres : NOM, Prénom (à répéter). Titre. Mention d'édition. Lieu de publication : éditeur, année d'édition. Nombre de pages. (Titre de la collection, n° de la collection)
                      ISBN</li><li>Pour les sites internet :  NOM, Prénom. Nom du site [en ligne]. Lieu de publication : éditeur, (date de consultation). Disponibilité et accès.</li></ul>-->
                      <?php echo getBotBubble(); ?>
                    </div>
                    <div class="sub_content">
                      <label for="body<?php echo $local_cat; ?>"><convert>#label=592<convert><!--Listez les references bibliographiques--> :</label><br />
                      <textarea class="input1" id="body<?php echo $local_cat; ?>" name="body" rows="6" cols="" onkeyup="JavaScript:bodyOnKeyUp(this,'length_display_<?php echo $local_cat; ?>');"></textarea><br />
                      <span id="length_display_<?php echo $local_cat; ?>">0</span> <convert>#label=238<convert><!--caractères sur--> 20 000.
                    </div>
                    <input type="hidden" id="new_category<?php echo $local_cat; ?>" name="new_category" value="<?php echo $local_cat; ?>" />
                    <?php echo getLicenseAlert(); ?>
                    <br /><input type="submit" id="new<?php echo $local_cat; ?>" name="new" onclick="JavaScript:saveOnClick()" value="<convert>#label=360<convert>" class="button1" /><!--Ajouter--><input type="button" id="cancel<?php echo $local_cat; ?>" name="cancel" value="<convert>#label=77<convert>" class="button1" onclick="JavaScript:cancelElement('<?php echo $local_cat; ?>');" />
                  </td>
                </tr>
              </table>
            </form>
          </div>
<?php
        }
?>
        </div>
<?php } ?>
<!--#################### COMMENT ############################################-->
<?php
if ($comments['Count']>0 || allowAccess(comment_edit_all)) {
  $local_cat = "comment";
  $array = $comments;
?>
        <div id="comment" class="div_2_r">
<?php if (!$is_change) { ?>
          <span class="generic_tools">
<?php if (allowAccess(comment_edit_all)) { ?>
            <a href="JavaScript:addElement('<?php echo $local_cat; ?>');" title="<convert>#label=54<convert>" class="nothing"><img src="../images/icons/add.png" class="icon" alt="<convert>#label=54<convert>" /></a>
<?php } ?>
            <a href="JavaScript:refresh();" title="<convert>#label=334<convert>" class="nothing"><img src="../images/icons/refresh.png" class="icon" alt="<convert>#label=334<convert>" /></a><!--Actualiser la page-->
<?php if ($array['Count']>0) { ?>
            <a href="JavaScript:badContent('<?php echo $local_cat; ?>',xtdGetElementById('hidden_name').innerHTML);" title="<convert>#label=313<convert>" class="nothing"><img src="../images/icons/warning.png" class="icon" alt="<convert>#label=313<convert>" /></a>
<?php } ?>
          </span>
<?php } ?>
          <h2><convert>#label=361<convert><!--Commentaires--></h2>
<?php
        $thisCategoryDeleted = ($dcat == $local_cat);
        if ($thisCategoryDeleted && $contributionDeleted) {
          echo $deletionWarning;
        }
        if ($is_change) {
          $keep_open = true;
        } else {
          $keep_open = false;
        }
        for($i=0;$i<$array['Count'];$i++) {
          $change = (($chcat == $local_cat) && ($chid == $array[$i]["Id"]) && allowAccess(comment_edit_all));
          $edition = ($is_change && $change);
          $my_comment = ($array[$i]["Id_author"] == $_SESSION['user_id']);
          if ($array[$i+1]["Thread_status"] == 0 || $array[$i+1]["Thread_status"] == "" || $edition || isset($_POST['print'])) {
            $disp_snip = "display:none;";
            $disp_comm = "";
            $margin_bottom_comm = 0;
            $margin_bottom_snip = 10;
          } else {
            if ($keep_open) {
              $disp_snip = "display:none;";
              $disp_comm = "";
              $margin_bottom_snip = 0;
            } else {
              $disp_snip = "";
              $disp_comm = "display:none;";
              $margin_bottom_snip = -2;
            }
            $margin_bottom_comm = -14;
          }
          $thisContribChanged = (($chcat == $local_cat) && ($chid == $array[$i]["Id"]));
          $thisContribNew = (($ncat == $local_cat) && ($nid == $array[$i]["Id"]));
          $thisContributionSaved = $contributionSaved && ($thisContribChanged || $thisContribNew);
          if($thisContributionSaved) {
            echo $saveWarning;
            echo $scoreMessage;
          }
?>
            <div id="<?php echo $local_cat.$array[$i]["Id"]; ?>_s" onclick="JavaScript:switchDiv('<?php echo $local_cat.$array[$i]["Id"]; ?>',true);" style="<?php echo $disp_snip; ?>margin:0px 0px <?php echo $margin_bottom_snip; ?>px <?php echo $array[$i]["Thread_status"]*20; ?>px;" class="comment_snipet">
              <table border="0" cellspacing="1" cellpadding="0" style="width:100%;">
                <tr>
                <td style="text-align:left;width=30%;"><b><?php echo $array[$i]["Author"];?></b></td>
                <td style="text-align:center;width=30%;"><?php echo substr($array[$i]["Title"],0,40);?>...</td>
                <td style="text-align:right;"><?php echo timeToStr($array[$i]["Date_inscription"]); ?></td>
                </tr>
              </table>
              <!--<div style="float:left;">
                <b><?php echo $array[$i]["Author"];?></b>
              </div>
              <div style="float:right;">
                <?php echo timeToStr($array[$i]["Date_inscription"]); ?>
              </div>
              <span style="color:#808080">
                <?php echo substr($array[$i]["Title"],0,40);?>...
              </span> -->
            </div>
            <div id="<?php echo $local_cat.$array[$i]["Id"]; ?>_c" style="<?php echo $disp_comm; ?>margin:1px 0px <?php echo $margin_bottom_comm; ?>px <?php echo $array[$i]["Thread_status"]*20; ?>px;" class="comment_message">
<?php
          if ($edition) {
?>
            <form id="modif" name="modif" method="post" action="#<?php echo $local_cat.$array[$i]["Id"]; ?>">
<?php
          }
?>
            <a name="<?php echo $local_cat.$array[$i]["Id"]; ?>"></a>
            <table class="container" id="commentcontainertable<?php echo $i; ?>">
              <tr class="container">
                <td class="sub_prop">
<?php
            if (!$is_change) {
              if (allowAccess(comment_edit_all)) {
?>
                  <div class="tools" id="toolscommentcontainertable<?php echo $i; ?>">
                    <?php if($allowed_to_lock){ ?><a href="JavaScript:lockElement('<?php echo $local_cat; ?>',<?php echo $array[$i]["Id"]; ?>,'<?php echo $array[$i]["Locked"];?>');" title="<convert>#label=341<convert>" class="nothing"><?php } ?><img src="../images/icons/locker_<?php echo $array[$i]["Locked"];?>.png" class="icon" alt="<convert>#label=340<convert> : <?php echo $array[$i]["Locked"]; ?>" /><?php if($allowed_to_lock){ ?></a><?php } ?>
<?php
                if ($array[$i]["Locked"] == "NO" && ($my_comment || allowAccess(comment_delete_all))) {
?>
                    <a href="JavaScript:editElement('<?php echo $local_cat; ?>',<?php echo $array[$i]["Id"]; ?>);" title="<convert>#label=53<convert>" class="nothing"><img src="../images/icons/edit.png" class="icon" alt="<convert>#label=53<convert>" /></a>
                    <a href="JavaScript:addElement('<?php echo $local_cat; ?>');" title="<convert>#label=54<convert>" class="nothing"><img src="../images/icons/add.png" class="icon" alt="<convert>#label=54<convert>" /></a>
<?php
                  if (allowAccess(comment_delete_all)) {
?>
                    <a href="JavaScript:deleteElement('<?php echo $local_cat; ?>',<?php echo $array[$i]["Id"]; ?>);" title="<convert>#label=55<convert>" class="nothing"><img src="../images/icons/delete.png" class="icon" alt="<convert>#label=55<convert>" /></a>
<?php
                  }
                }
?>
                    <a href="JavaScript:replyElement('<?php echo $local_cat; ?>',<?php echo $array[$i]["Id"]; ?>);" title="<convert>#label=342<convert>" class="nothing"><img src="../images/icons/reply.png" class="icon" alt="<convert>#label=342<convert>" /></a>
                    <!--<a href="JavaScript:addElement('<?php echo $local_cat; ?>');" title="<convert>#label=54<convert>" class="nothing"><img src="../images/icons/add.png" class="icon" alt="<convert>#label=54<convert>" /></a>-->
                  </div>
<?php
              }
            }
?>
                  <div class="who_when">
                    <convert>#label=337<convert><!--Posté par--> <b><?php echo $array[$i]["Author"];?></b><br />
                    <convert>#label=338<convert><!--Le--> <b><?php echo timeToStr($array[$i]["Date_inscription"]); ?></b><br /><br />
<?php
            if (isset($array[$i]["Date_reviewed"]) && !$edition) {
?>
                    <convert>#label=343<convert><!--Modifié le--> <b><?php echo timeToStr($array[$i]["Date_reviewed"]); ?></b>
<?php
            }
?>
                  </div>
                  <div style="padding-left:27px;margin-top:10px;">
<?php       if (!$edition) {
              if ($array[$i]["E_t_trail"] != "") { ?>
                    <div title="<convert>#label=363<convert>"><img src="../images/icons/hiking.gif" alt="<convert>#label=363<convert>" class="icon" style="vertical-align:middle;" /> <?php echo formatSimpleTime($array[$i]["E_t_trail"]);?></div><!--Temps d'approche (hh:mm)-->
<?php         }
              if ($array[$i]["E_t_underground"] != "") { ?>
                    <div title="<convert>#label=362<convert>"><img src="../images/icons/underground.png" alt="<convert>#label=362<convert>" class="icon" style="vertical-align:middle;" /> <?php echo formatSimpleTime($array[$i]["E_t_underground"]);?></div><!--Temps passé sous terre (hh:mm)-->
<?php         }
            } ?>
                  </div>
                </td>
                <td class="sub_container">
                  <?php if ($edition) { ?><div class="info">
                    <?php echo getTopBubble(); ?>
                    <convert>#label=534<convert><!--Vous pouvez laisser ici votre impression sur la cavité, vos interrogations, vos projets, vos remarques ...-->
                    <?php echo getBotBubble(); ?>
                  </div><?php } ?>
                  <div class="arrow_up" onclick="JavaScript:switchDiv('<?php echo $local_cat.$array[$i]["Id"]; ?>',false);"></div>
                  <div class="sub_title">
<?php
										if ($array[$i]["Id_exit"] == $id) {
                      $exit_id = $array[$i]["Id_entry"]; //getEntryId($local_cat, $array[$i]["Id"]);
                      $exit_name = getEntryName($exit_id);
                    } else {
                      $exit_name = $array[$i]["Exit_name"];
                      $exit_id = $array[$i]["Id_exit"];
                    }
                    if (isset($exit_id) && $exit_id != Select_default && $exit_id != "" && !$edition && $countEntries > 1) {
?>
                      <div style="text-align:left;font-weight:normal">
                        <b><convert>#label=347<convert><!--Traversée--> : </b><?php echo $entryName; ?> - <?php echo $exit_name; ?>
                      </div>
<?php
                    }
?>
                    <?php if ($edition) { ?><label for="title<?php echo $local_cat.$array[$i]["Id"]; ?>"><convert>#label=364<convert><!--Donnez un titre à votre commentaire (-->300 <convert>#label=365<convert><!--carac. max.)--> :</label><br /><?php } ?>
                    <h3 id="<?php echo $local_cat.$array[$i]["Id"]; ?>_title"><?php if ($edition) { ?><input type="text" class="input1" name="title" id="title<?php echo $local_cat.$array[$i]["Id"]; ?>" value="<?php } ?><?php echo $array[$i]["Title"];?><?php if ($edition) { ?>" /><?php } ?></h3>
                  </div>
                  <div class="sub_content">
<?php       if ($edition) {
              if ($countEntries > 1) {
                $sql_exits = getSqlExits($id, $local_cat, $exit_id);
?>
                      <label for="id_exit"><convert>#label=350<convert><!--Autre entrée si traversée--> : </label>
                      <select class="select1" name="id_exit" id="id_exit">
<?php
                        $msg = "- <convert>#label=351<convert> -";//N'est pas une traversée
                        $comparedCol = "value";
                        $textCol = "text";
                        $selected = $exit_id;
                        echo getOptions($sql_exits, $msg, $selected, $comparedCol, $textCol);
?>
                      </select><br /><br />
<?php         } ?>
                      <label for="body<?php echo $local_cat.$array[$i]["Id"]; ?>"><convert>#label=366<convert><!--Votre commentaire--> :</label><br />
                      <textarea class="input1" id="body<?php echo $local_cat.$array[$i]["Id"]; ?>" name="body" rows="6" cols="" onkeyup="JavaScript:bodyOnKeyUp(this,'length_display_<?php echo $local_cat.$array[$i]["Id"]; ?>');"><?php echo $array[$i]["Body"];?></textarea><br />
                      <span id="length_display_<?php echo $local_cat.$array[$i]["Id"]; ?>">0</span> <convert>#label=238<convert><!--caractères sur--> 20 000.<br /><br />
                      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl" style="width:100%;">
                        <tr><th colspan="2"><?php echo getHelpTopic($helpId['votes'], "<convert>#label=23<convert>"); ?></th></tr>
                        <tr><td class="label">
                          <label for="e_t_trail" style="font-style:normal;" >
                            <convert>#label=363<convert><!--Temps d'approche-->
                          </label>
                        </td><td class="field">
                          <input type="text" class="input1" style="width:auto;" name="e_t_trail" id="e_t_trail" value="<?php echo formatSimpleTime($array[$i]["E_t_trail"]);?>" size="5" maxlength="5" />
                          <i><convert>#label=884<convert><!--hh:mm--></i>
                        </td></tr><tr><td class="label">
                          <label for="e_t_underground" style="font-style:normal;">
                            <convert>#label=362<convert><!--Temps passé sous terre-->
                          </label>
                        </td><td class="field">
                          <input type="text" class="input1" style="width:auto;" name="e_t_underground" id="e_t_underground" value="<?php echo formatSimpleTime($array[$i]["E_t_underground"]);?>" size="5" maxlength="5" />
                          <i><convert>#label=884<convert><!--hh:mm--></i>
                        </td></tr><tr><td class="label" style="font-style:normal;">
                          <label for="aestheticism" style="font-style:normal;">
                            <convert>#label=367<convert><!--Note d'estétisme-->
                          </label>
                        </td><td class="field">
                          <input type="text" class="input1" style="width:auto;" name="aestheticism" id="aestheticism" value="<?php echo $array[$i]["Aestheticism"];?>" size="2" maxlength="2" />
                          <i><convert>#label=881<convert><!--Vide = vote blanc, 0 = aucun intérêt, 10 = très esthétique--></i>
                        </td></tr><tr><td class="label">
                          <label for="caving" style="font-style:normal;">
                            <convert>#label=368<convert><!--Facilité de progression sous terre-->
                          </label>
                        </td><td class="field">
                          <input type="text" class="input1" style="width:auto;" name="caving" id="caving" value="<?php echo $array[$i]["Caving"];?>" size="2" maxlength="2" />
                          <i><convert>#label=882<convert><!--Vide = vote blanc, 0 = très difficile, 10 = très facile--></i>
                        </td></tr><tr><td class="label">
                          <label for="approach" style="font-style:normal;">
                            <convert>#label=369<convert><!--Facilité d'accès à l'entrée-->
                          </label>
                        </td><td class="field">
                          <input type="text" class="input1" style="width:auto;" name="approach" id="approach" value="<?php echo $array[$i]["Approach"];?>" size="2" maxlength="2" />
                          <i><convert>#label=883<convert><!--Vide = vote blanc, 0 = très difficile d'accès, 10 = très facile d'accès--></i>
                        </td></tr><tr><td class="label">
                          <label for="alert_me" style="font-style:normal;">
                            <convert>#label=513<convert><!--Je veux recevoir un e-mail lorsqu'une réponse m'est postée.-->
                          </label>
                        </td><td class="field">
                          <input style="vertical-align:middle;" type="checkbox" id="alert_me" name="alert_me" <?php if($array[$i]["Alert"]=="YES"){echo "checked=\"checked\"";} ?> />
                        </td></tr>
                      </table>
                      <input type="hidden" id="oldbody" name="oldbody" value="<?php echo $array[$i]["Body"];?>" /> <!--htmlentities(-->
                      <input type="hidden" id="oldtitle" name="oldtitle" value="<?php echo $array[$i]["Title"];?>" />
<?php 			} else {
                      echo getRef(replaceLinks(nl2br($array[$i]["Body"]))); // <!--htmlentities(-->
						} ?>
                  </div>
<?php
            if ($edition) {
?>
                  <?php echo getLicenseAlert(); ?>
                  <br /><input type="submit" id="save" name="save" onclick="JavaScript:saveOnClick()" value="<convert>#label=76<convert>" class="button1" /><!--Valider--><input type="submit" id="cancel<?php echo $local_cat.$array[$i]["Id"]; ?>" name="cancel" onclick="" value="<convert>#label=77<convert>" class="button1" /><!--Annuler-->
<?php
            }
?>
                </td>
              </tr>
            </table>
<?php
          if ($edition) {
?>
            </form>
<?php
          }
?>
            </div>
<?php
        }
        if (allowAccess(comment_edit_all) && !$is_change) { //$edition) {
?>
<!--#################### NEW COMMENT ########################################-->
            <a name="new_<?php echo $local_cat; ?>_anchor"></a>
            <div id="new_<?php echo $local_cat; ?>" style="display:none;background-color:white;">
            <form id="new_<?php echo $local_cat; ?>_form" name="new_<?php echo $local_cat; ?>_form" method="post" action="">
              <table class="container">
                <tr class="container">
                  <td class="sub_container">
                    <div class="info">
                      <?php echo getTopBubble(); ?>
                      <convert>#label=534<convert><!--Vous pouvez laisser ici votre impression sur la cavité, vos interrogations, vos projets, vos remarques ...-->
                      <?php echo getBotBubble(); ?>
                    </div>
                    <div class="sub_title">
                      <label for="title<?php echo $local_cat; ?>"><convert>#label=364<convert><!--Donnez un titre à votre commentaire (-->300 <convert>#label=365<convert><!--carac. max.)--> :</label><br />
                      <h3>
                        <input type="text" class="input1" name="title" id="title<?php echo $local_cat; ?>" value="" />
                      </h3>
                    </div>
                    <div class="sub_content">
<?php 		if ($countEntries > 1) { ?>
                      <label for="id_exit"><convert>#label=350<convert><!--Autre entrée si traversée--> :</label>
                      <select class="select1" name="id_exit" id="id_exit<?php echo $local_cat; ?>">
<?php
                        $msg = "- <convert>#label=351<convert> -";//N'est pas une traversée
                        $comparedCol = "value";
                        $textCol = "text";
                        $selected = "";
                        echo getOptions($sql_exits, $msg, $selected, $comparedCol, $textCol);
?>
                      </select><br /><br />
<?php 		} ?>
                      <label for="body<?php echo $local_cat; ?>"><convert>#label=366<convert><!--Votre commentaire--> :</label><br />
                      <textarea class="input1" id="body<?php echo $local_cat; ?>" name="body" rows="6" cols="" onkeyup="JavaScript:bodyOnKeyUp(this,'length_display_<?php echo $local_cat; ?>');"></textarea><br />
                      <span id="length_display_<?php echo $local_cat; ?>">0</span> <convert>#label=238<convert><!--caractères sur--> 20 000.<br /><br />

                      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl" style="width:100%;">
                        <tr><th colspan="2"><?php echo getHelpTopic($helpId['votes'], "<convert>#label=23<convert>"); ?></th></tr>
                        <tr><td class="label">
                          <label for="e_t_trail" style="font-style:normal;">
                            <convert>#label=363<convert><!--Temps d'approche-->
                          </label>
                        </td><td class="field">
                          <input type="text" class="input1" style="width:auto;" name="e_t_trail" id="e_t_trail" value="" size="5" maxlength="5" />
                          <i><convert>#label=884<convert><!--hh:mm--></i>
                        </td></tr><tr><td class="label">
                          <label for="e_t_underground" style="font-style:normal;">
                            <convert>#label=362<convert><!--Temps passé sous terre-->
                          </label>
                        </td><td class="field">
                          <input type="text" class="input1" style="width:auto;" name="e_t_underground" id="e_t_underground" value="" size="5" maxlength="5" />
                          <i><convert>#label=884<convert><!--hh:mm--></i>
                        </td></tr><tr><td class="label">
                          <label for="aestheticism" style="font-style:normal;">
                            <convert>#label=367<convert><!--Note d'estétisme-->
                          </label>
                        </td><td class="field">
                          <input type="text" class="input1" style="width:auto;" name="aestheticism" id="aestheticism" value="" size="2" maxlength="2" />
                          <i><convert>#label=881<convert><!--Vide = vote blanc, 0 = aucun intérêt, 10 = très esthétique--></i>
                        </td></tr><tr><td class="label">
                          <label for="caving" style="font-style:normal;">
                            <convert>#label=368<convert><!--Facilité de progression sous terre-->
                          </label>
                        </td><td class="field">
                          <input type="text" class="input1" style="width:auto;" name="caving" id="caving" value="" size="2" maxlength="2"  />
                          <i><convert>#label=882<convert><!--Vide = vote blanc, 0 = très difficile, 10 = très facile--></i>
                        </td></tr><tr><td class="label">
                          <label for="approach" style="font-style:normal;">
                            <convert>#label=369<convert><!--Facilité d'accès à l'entrée-->
                          </label>
                        </td><td class="field">
                          <input type="text" class="input1" style="width:auto;" name="approach" id="approach" value="" size="2" maxlength="2" />
                          <i><convert>#label=883<convert><!--Vide = vote blanc, 0 = très difficile d'accès, 10 = très facile d'accès--></i>
                        </td></tr><tr><td class="label">
                          <label for="alert_me" style="font-style:normal;">
                            <convert>#label=513<convert><!--Je veux recevoir un e-mail lorsqu'une réponse m'est postée.-->
                          </label>
                        </td><td class="field">
                          <input style="vertical-align:middle;" type="checkbox" id="alert_me" name="alert_me" />
                        </td></tr>
                      </table>
                    </div>
                    <input type="hidden" id="id_answered" name="id_answered" value="" />
                    <input type="hidden" id="new_category<?php echo $local_cat; ?>" name="new_category" value="<?php echo $local_cat; ?>" />
                    <?php echo getLicenseAlert(); ?>
                    <br /><input type="submit" id="new<?php echo $local_cat; ?>" name="new" onclick="JavaScript:saveOnClick()" value="<convert>#label=360<convert>" class="button1" /><!--Ajouter--><input type="button" id="cancel<?php echo $local_cat; ?>" name="cancel" value="<convert>#label=77<convert>" class="button1" onclick="JavaScript:cancelElement('<?php echo $local_cat; ?>');" />
                  </td>
                </tr>
              </table>
            </form>
          </div>
<?php
        }
?>
        </div>
<?php } ?>
      </div>
    </div>
    <div id="content_license" class="content_license">
<?php if (isset($_POST['print'])) { ?>
      <?php echo $_SESSION['Application_name']; ?> - <?php echo $_SESSION['Application_url']; ?><br />
<?php } ?>
      <convert>#label=485<convert> <convert>#label=509<convert> <?php echo getLicensePicture(3); ?>
    </div>
    <div id="warnings" style="font-size:8pt;">
      <?php include("description_warning.php"); ?>
    </div>
<?php if (isset($_POST['print'])) { ?>
    <div style="text-align:right;">
      <convert>#label=688<convert> <?php echo date("j M\. Y"); ?>.<!--Imprimé le-->
    </div>
<?php } ?>
<?php
		  break;
		  default:
?>
	</head>
  <body>
    <?php echo getTopFrame(); ?>
    <center>
      <h1>
        <?php echo $_SESSION['Application_message']; ?>
      </h1>
      <a href="JavaScript:refresh();" title="<convert>#label=334<convert>" class="nothing"><img src="../images/icons/refresh.png" class="icon" alt="<convert>#label=334<convert>" /></a><!--Actualiser la page-->
    </center>
<?php
		  break;
		}
?>
    <?php echo getBotFrame(false); ?>
<?php
$virtual_page = $frame."/".$_SESSION['language'];
include_once "../func/suivianalytics.php";
?>
  </body>
</html>
<?php
exit();
$content = ob_get_clean();
require_once(dirname(__FILE__).'/../func/html2pdf/html2pdf.class.php');
$html2pdf = new HTML2PDF('P','A4', 'fr');
$html2pdf->WriteHTML($content, isset($_GET['vuehtml']));
$html2pdf->Output('file.pdf');
?>
