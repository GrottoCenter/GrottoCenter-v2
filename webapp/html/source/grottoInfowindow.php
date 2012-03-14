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
/*
- remove html header
- change ids with prefix GC_IW_
- remove parent. JS
- change $_POST to $_GET
- move functions to overview.php
- change submit to buttons
*/

include("../conf/config.php");
include("../func/function.php");
include("declaration.php");
$frame = "infowindow";
include("application_".$_SESSION['language'].".php");
include("mailfunctions_".$_SESSION['language'].".php");
$id = (isset($_GET['id'])) ? $_GET['id'] : '';
$clustered = (isset($_GET['clustered'])) ? ($_GET['clustered'] == "c") : false;
	
if (!$clustered) {
	if ($id != "") {
		if (USER_IS_CONNECTED){
			if (isset($_GET['add'])) {
				$sql = "INSERT INTO `".$_SESSION['Application_host']."`.`J_grotto_caver` (Id_grotto, Id_caver) VALUES (".$id.",".$_SESSION['user_id'].")";
				$req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
			}
			if (isset($_GET['remove'])) {
				$sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_grotto_caver` WHERE Id_grotto = ".$id." AND Id_caver =".$_SESSION['user_id'];
				$req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
			}
		}
		$sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`T_grotto` ";
		$sql .= "WHERE Id = ".$id;
		$grotto = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
		//Number of cavers
		$sql = "SELECT COUNT(*) Count FROM `".$_SESSION['Application_host']."`.`J_grotto_caver` ";
		$sql .= "WHERE Id_grotto = ".$id;
		$cavers = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
		//Number of entries
		$sql = "SELECT COUNT(*) Count FROM `".$_SESSION['Application_host']."`.`J_grotto_entry` ";
		$sql .= "WHERE Id_grotto = ".$id;
		$entries = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
		//Check for my list of grottoes
		if (USER_IS_CONNECTED){
			//Is it in my list ?
			$sql = "SELECT COUNT(*) Count FROM `".$_SESSION['Application_host']."`.`J_grotto_caver` ";
			$sql .= "WHERE Id_grotto = ".$id." AND Id_caver = ".$_SESSION['user_id'];
			$inMyList = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
		}
		
		if ($grotto[0]['Picture_file_name'] == "") {
			$logo_filename = "default_logo.png";
		} else {
			$logo_filename = $grotto[0]['Picture_file_name'];
		}
	}
} else {
	$sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`T_grotto` ";
	$sql .= "WHERE Id IN (".$id.") ";
	$grottoes = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
}
?>
<?php if (!$clustered) { ?>
  	<div class="header grotto">
  		<convert>#label=186<convert><!--Club-->
<?php if (USER_IS_CONNECTED){ ?>
      <form id="GC_IW_manage_my_list" name="manage_my_list" method="post" action="#" style="display:inline;">
<?php if ($inMyList[0]['Count'] > 0) { ?>
        <input class="button_remove" type="button" id="GC_IW_remove" name="remove" onclick="JavaScript:infowindowReload(this)" value="" title="<convert>#label=596<convert>" /><!--Retirer de ma liste-->
<?php } else { ?>
        <input class="button_add" type="button" id="GC_IW_add" name="add" onclick="JavaScript:infowindowReload(this)" value="" title="<convert>#label=597<convert>" /><!--Ajouter à ma liste-->
<?php } ?>
      </form>
<?php } ?>
  	</div>
  	<div class="avatar">
      <img src="../upload/logos/<?php echo $logo_filename; ?>" alt="logo" />
    </div>
    <div class="content">
			<div class="label">
  				<span class="value">
<?php
        if (allowAccess(grotto_edit_all)) {
?>
          <a href="grotto_<?php echo $_SESSION['language']; ?>.php?type=edit&id=<?php echo $id; ?>" target="filter" title="<convert>#label=53<convert>"><!--Modifier--><?php echo $grotto[0]['Name']; ?></a>
<?php
        } else {
          echo $grotto[0]['Name'];
        }
?>
          </span> 
        <a href="JavaScript:showRelationList('<?php echo $_SESSION['language']; ?>','caver','grotto','<?php echo $id; ?>');" title="<convert>#label=539<convert>"><!--Voir la liste-->
          <span class="value_caver">
            <?php echo $cavers[0]['Count']; ?>
          </span>
        </a> 
        <a href="JavaScript:showRelationList('<?php echo $_SESSION['language']; ?>','entry','grotto','<?php echo $id; ?>');" title="<convert>#label=539<convert>"><!--Voir la liste-->
          <span class="value_entry">
            <?php echo $entries[0]['Count']; ?>
          </span>
        </a>
			</div>
<?php if ($grotto[0]['Custom_message'] != "") { ?>
    	<div class="custom_message" style="max-height:75px;">
        <?php echo replaceLinks(nl2br($grotto[0]['Custom_message'])); ?>
    	</div>
<?php } ?>
    	<div id="GC_IW_directions" class="directions">
        <convert>#label=166<convert><!--Itinéraire--> : <a href="JavaScript:getDirectionsForm(false);"><convert>#label=167<convert><!--Vers ce lieu--></a> - <a href="JavaScript:getDirectionsForm(true);"><convert>#label=168<convert><!--Depuis ce lieu--></a>
      </div>
		</div>
		<div class="footer">
			<label for="link"><input type="checkbox" name="link" id="GC_IW_link" class="inputIW" title="<convert>#label=174<convert>" style="border: none;" onclick="Javascript:switchLines(<?php echo $id; ?>, 'grotto', this.checked);" /><convert>#label=175<convert><!--Liens--></label><!--Afficher les liens-->
			<span class="details">
        <a href="#" onclick="JavaScript:detailMarker(event, 'grotto', '<?php echo $id; ?>', '<?php echo $_SESSION['language']; ?>');" title="<convert>#label=178<convert>"><!--Obtenir les propriétés de ce marqueur-->
				  <convert>#label=179<convert><!--Propriétés-->
        </a><br />
        <a href="contact_<?php echo $_SESSION['language']; ?>.php?type=message&amp;subject=bad_content&amp;category=grotto&amp;name=<?php echo $grotto[0]['Name']; ?>" target="filter" title="<convert>#label=180<convert>"><!--Signaler du contenu hors-charte-->
				  <convert>#label=181<convert><!--Hors-charte-->
        </a>
      </span>
		</div>
<?php
    $virtual_page = "grotto_infowindow/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
<?php 
//##############################################################################
} else {
//##############################################################################
?>
  	<div class="header grottoes_clustered" style="height:42px;">
  		<convert>#label=386<convert><!--Clubs-->
  	</div>
    <div class="content" style="height:170px;">
      <div class="info">
        <?php echo getTopBubble(); ?>
        <convert>#label=643<convert><!--Pour détailler chaque marqueur, veuillez zoomer.-->
        <?php echo getBotBubble(); ?>
      </div>
			<div class="label">
        <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
<?php while(list($k,$v) = each($grottoes)) { if ($v['Name'] != "") { ?>
  				<tr><td class="label" style="white-space:nowrap;">
  				  <a href="JavaScript:openMe('<?php echo $v['Id']; ?>', 'grotto', false, undefined, true);">
              <?php echo $v['Name']; ?>
            </a>
          </td><td style="white-space:nowrap;">
            <a href="#" onclick="JavaScript:detailMarker(event, 'grotto', '<?php echo $v['Id']; ?>', '<?php echo $_SESSION['language']; ?>');" title="<convert>#label=178<convert>"><!--Obtenir les propriétés de ce marqueur-->
    				  <convert>#label=179<convert><!--Propriétés-->
    			  </a>
    			</td></tr>
<?php } } ?>
        </table>
			</div>
		</div>
<?php
    $virtual_page = "grotto_infowindow/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
<?php } ?>