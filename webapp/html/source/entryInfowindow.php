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
$frame = "infowindow";
include("application_".$_SESSION['language'].".php");
include("mailfunctions_".$_SESSION['language'].".php");
include("../func/loader_func.php");
$id = (isset($_GET['id'])) ? $_GET['id'] : '';
$clustered = (isset($_GET['clustered'])) ? ($_GET['clustered'] == "c") : false;

if (!$clustered) {
	if ($id != "") {
		if (USER_IS_CONNECTED){
			if (isset($_GET['add'])) {
				$sql = "INSERT INTO `".$_SESSION['Application_host']."`.`J_entry_caver` (Id_entry, Id_caver) VALUES (".$id.",".$_SESSION['user_id'].")";
				$req = execSQL($sql, $frame, __FILE__, __FUNCTION__);
			}
			if (isset($_GET['remove'])) {
				$sql = "DELETE FROM `".$_SESSION['Application_host']."`.`J_entry_caver` WHERE Id_entry = ".$id." AND Id_caver =".$_SESSION['user_id'];
				$req = execSQL($sql, $frame, __FILE__, __FUNCTION__); 
			}
		}
		$sql = "SELECT ey.*, ce.Id_cave,ma.Id AS Massif_id, ma.Name AS Massif_name FROM `".$_SESSION['Application_host']."`.`T_entry` ey ";
		$sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_cave_entry` ce ON ey.Id = ce.Id_entry "; 
		$sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_massif_cave` mc ON ce.Id_cave = mc.Id_cave ";
		$sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_massif_cave` me ON ey.Id = me.Id_entry ";
		$sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_massif` ma ON ma.Id IN (mc.Id_massif, me.Id_massif) ";
		$sql .= "WHERE ey.Id = ".$id." ";
		$sql .= "ORDER BY Massif_name DESC ";
		$entry = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
		if ($entry[0]['Id_cave'] == "") {
			$sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`T_single_entry` ";
			$sql .= "WHERE `Id` = ".$id;
			$cave = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
		} else {
			$sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`T_cave` ";
			$sql .= "WHERE `Id` = ".$entry[0]['Id_cave'];
			$cave = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
		}
		//Number of cavers
		$sql = "SELECT COUNT(*) Count FROM `".$_SESSION['Application_host']."`.`J_entry_caver` ";
		$sql .= "WHERE Id_entry = ".$id;
		$cavers = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
		//Number of grottoes
		$sql = "SELECT COUNT(*) Count FROM `".$_SESSION['Application_host']."`.`J_grotto_entry` ";
		$sql .= "WHERE Id_entry = ".$id;
		$grottoes = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
		//Number of contributions files
		$sql = "SELECT COUNT(*) Count FROM `".$_SESSION['Application_host']."`.`V_contributions` ";
		$sql .= "WHERE Id_entry = ".$id;
		$contributions = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
		//Partner's websites
		$sql = "SELECT u.* FROM `".$_SESSION['Application_host']."`.`T_url` u ";
		$sql .= "INNER JOIN `".$_SESSION['Application_host']."`.`J_entry_url` eu ON u.Id = eu.Id_url "; 
		$sql .= "WHERE eu.Id_entry = ".$id;
		$url = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
		//Check for my list of entries
		if (USER_IS_CONNECTED){
			//Is it in my list ?
			$sql = "SELECT COUNT(*) Count FROM `".$_SESSION['Application_host']."`.`J_entry_caver` ";
			$sql .= "WHERE Id_entry = ".$id." AND Id_caver = ".$_SESSION['user_id'];
			$inMyList = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
		}
	}
} else {
	$sql = "SELECT Name, Id FROM `".$_SESSION['Application_host']."`.`T_entry` ";
	$sql .= "WHERE Id IN (".$id.") ";
	$entries = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
}
?>
<?php if (!$clustered) { ?>
  	<div class="header entry">
  		<convert>#label=182<convert><!--Cavité-->
<?php if (USER_IS_CONNECTED){ ?>
      <form id="GC_IW_manage_my_list" name="manage_my_list" method="post" action="#" style="display:inline;">
<?php if ($inMyList[0]['Count'] > 0) { ?>
        <input class="button_remove" type="button" id="GC_IW_remove" name="remove" onclick="JavaScript:infowindowReload(this)" value="" title="<convert>#label=596<convert>" /><!--Retirer de ma liste-->
<?php } else { ?>
        <input class="button_add" type="button" id="GC_IW_add" name="add" onclick="JavaScript:infowindowReload(this)" value="" title="<convert>#label=597<convert>" /><!--Ajouter à ma liste-->
<?php } ?>
      </form>
<?php }
      if ($entry[0]['Country'] == 'FR') { ?>
    <!--<a href="JavaScript:openWindow('<?php echo $_SESSION['Application_url']; ?>/html/geoportail_<?php echo $_SESSION['language']; ?>.php?z=17&lang=<?php echo $_SESSION['language']; ?>&id=<?php echo $id; ?>', '', 828, 625);" title="Geoportail" class="nothing" style="bottom:-3px;position:relative;"><img src="../images/icons/geoportail.png" alt="Geoportail" style="border:0px none;" /></a>-->
    <a href="JavaScript:openWindow('geoportail_<?php echo $_SESSION['language']; ?>.php?z=17&lang=<?php echo $_SESSION['language']; ?>&id=<?php echo $id; ?>', '', 828, 625);" title="Geoportail" class="nothing" style="bottom:-3px;position:relative;"><img src="../images/icons/geoportail.png" alt="Geoportail" style="border:0px none;" /></a>
<?php } ?>
  	</div>
  	<div onclick="JavaScript:infoRadar();" class="radar">
      <img src="../images/gen/getChart.php?type=radar&amp;data=<?php echo getAvgAestheticism($id); ?>|<?php echo getAvgCaving($id); ?>|<?php echo getAvgApproach($id); ?>&amp;label=<convert>#label=328<convert>|<convert>#label=329<convert>|<convert>#label=330<convert>" alt="image" />
    </div>
    <div class="content">
			<div class="label">
				<span class="value">
<?php
        if (allowAccess(entry_edit_all)) {
?>
          <a href="entry_<?php echo $_SESSION['language']; ?>.php?type=edit&id=<?php echo $id; ?>" target="filter" title="<convert>#label=53<convert>"><!--Modifier--><?php echo $entry[0]['Name']; ?></a>
<?php
        } else {
          echo $entry[0]['Name'];
        }
?>
        </span> 
        <a href="JavaScript:showRelationList('<?php echo $_SESSION['language']; ?>','caver','entry','<?php echo $id; ?>');" title="<convert>#label=539<convert>"><!--Voir la liste-->
          <span class="value_caver">
            <?php echo $cavers[0]['Count']; ?>
          </span>
        </a> 
        <a href="JavaScript:showRelationList('<?php echo $_SESSION['language']; ?>','grotto','entry','<?php echo $id; ?>');" title="<convert>#label=539<convert>"><!--Voir la liste-->
          <span class="value_grotto">
            <?php echo $grottoes[0]['Count']; ?>
          </span>
        </a>
        <a href="#" onclick="JavaScript:detailMarker(event, 'entry', '<?php echo $id; ?>', '<?php echo $_SESSION['language']; ?>',true);" title="<convert>#label=184<convert>"><!--Voir la fiche détaillée de cette entrée-->
          <span class="value_comment">
            <?php echo $contributions[0]['Count']; ?>
          </span>
        </a> 
			</div>
<?php
if ($entry[0]['Massif_name'] != "") {
?>
    	<div class="label">
    		<convert>#label=555<convert><!--Massif--> : <span class="value"><a href="#" onclick="JavaScript:detailMarker(event, 'massif', '<?php echo $entry[0]['Massif_id']; ?>', '<?php echo $_SESSION['language']; ?>');" title="<convert>#label=178<convert>"><!--Obtenir les propriétés de ce marqueur--><?php echo $entry[0]['Massif_name']; ?></a></span>
    	</div>
<?php
}
if ($cave[0]['Name'] != "") {
?>
    	<div class="label">
    		<convert>#label=183<convert><!--Réseau--> : <span class="value"><a href="#" onclick="JavaScript:detailMarker(event, 'cave', '<?php echo $cave[0]['Id']; ?>', '<?php echo $_SESSION['language']; ?>');" title="<convert>#label=178<convert>"><!--Obtenir les propriétés de ce marqueur--><?php echo $cave[0]['Name']; ?></a></span>
    	</div>
<?php
}
if ($cave[0]['Depth'] != "") {
?>
      <div class="label">
      	<convert>#label=64<convert><!--Profondeur--> : <span class="value"><?php echo $cave[0]['Depth']; ?></span> <convert>#label=66<convert><!--metres-->
    	</div>
<?php
}
if ($cave[0]['Length'] != "") {
?>
    	<div class="label">
      	<convert>#label=68<convert><!--Développement--> : <span class="value"><?php echo $cave[0]['Length']; ?></span> <convert>#label=66<convert><!--metres-->
    	</div>
<?php
}
if (false) {
?>
    	<div class="label">
      	<convert>#label=106<convert><!--Altitude--> : <span class="value"><?php echo $entry[0]['Altitude']; ?></span> <convert>#label=66<convert><!--metres-->
    	</div>
<?php
}
?>
    	<div id="GC_IW_directions" class="directions">
        <convert>#label=166<convert><!--Itinéraire--> : <a href="JavaScript:getDirectionsForm(false);"><convert>#label=167<convert><!--Vers ce lieu--></a> - <a href="JavaScript:getDirectionsForm(true);"><convert>#label=168<convert><!--Depuis ce lieu--></a>
      </div>
		</div>
		<div class="footer">
		  <table>
		    <tr>
		      <td>
            <input type="checkbox" name="link" id="GC_IW_link" class="inputIW" title="<convert>#label=174<convert>" style="border: none;" onclick="Javascript:switchLines(<?php echo $id; ?>, 'entry', this.checked);" /><label for="GC_IW_link"><convert>#label=175<convert><!--Liens--></label><!--Afficher les liens-->
          </td>
          <td>
            <a href="#" onclick="JavaScript:detailMarker(event, 'entry', '<?php echo $id; ?>', '<?php echo $_SESSION['language']; ?>');" title="<convert>#label=178<convert>"><!--Obtenir les propriétés de ce marqueur-->
    				  <convert>#label=179<convert><!--Propriétés-->
    			  </a><br />
            <a href="#" onclick="JavaScript:detailMarker(event, 'entry', '<?php echo $id; ?>', '<?php echo $_SESSION['language']; ?>',true);" title="<convert>#label=184<convert>" style="color:red;"><!--Voir la fiche détaillée de cette entrée-->
    				  <convert>#label=185<convert><!--Fiche détaillée...-->
    			  </a><br />
    			  <a href="contact_<?php echo $_SESSION['language']; ?>.php?type=message&amp;subject=bad_content&amp;category=entry&amp;name=<?php echo $entry[0]['Name']; ?>" target="filter" title="<convert>#label=180<convert>"><!--Signaler du contenu hors-charte-->
    				  <convert>#label=181<convert><!--Hors-charte-->
            </a>
          </td>
          <td>
            <div class="overflown_box">
<?php
for ($i=0;$i<$url['Count'];$i++) {
  if ($url[$i]['Url'] != "") {
    // cut :
    $name = substr($url[$i]['Name'],0,15)."...";
?>
              <a href="<?php echo $url[$i]['Url']; ?>" title="<convert>#label=669<convert> : <?php echo $url[$i]['Name']; ?>" target="_blank" style="color:red;"><!--Site partenaire-->
      				  <?php echo $name; ?>
      			  </a>
<?php if ($url[$i+1]['Url'] != "") { ?>
              <br />
<?php } } } ?>
            </div>
          </td>
        </tr>
      </table>
		</div>
<?php
    $virtual_page = "entry_infowindow/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
<?php 
//##############################################################################
} else {
//##############################################################################
?>
  	<div class="header entries_clustered" style="height:42px;">
  		<convert>#label=642<convert><!--Cavités-->
  	</div>
    <div class="content" style="height:170px;">
      <div class="info">
        <?php echo getTopBubble(); ?>
        <convert>#label=643<convert><!--Pour détailler chaque marqueur, veuillez zoomer.-->
        <?php echo getBotBubble(); ?>
      </div>
			<div class="label">
        <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
<?php while(list($k,$v) = each($entries)) { if ($v['Name'] != "") { ?>
  				<tr><td class="label" style="white-space:nowrap;">
  				  <a href="JavaScript:openMe('<?php echo $v['Id']; ?>', 'entry', false, undefined, true);">
              <?php echo $v['Name']; ?>
            </a>
          </td><td style="white-space:nowrap;">
            <a href="#" onclick="JavaScript:detailMarker(event, 'entry', '<?php echo $v['Id']; ?>', '<?php echo $_SESSION['language']; ?>');" title="<convert>#label=178<convert>"><!--Obtenir les propriétés de ce marqueur-->
    				  <convert>#label=179<convert><!--Propriétés-->
    			  </a>
    			</td><td style="white-space:nowrap;">
            <a href="#" onclick="JavaScript:detailMarker(event, 'entry', '<?php echo $v['Id']; ?>', '<?php echo $_SESSION['language']; ?>',true);" title="<convert>#label=184<convert>" style="color:red;"><!--Voir la fiche détaillée de cette entrée-->
    				  <convert>#label=185<convert><!--Fiche détaillée...-->
    			  </a>
    			</td></tr>
<?php } } ?>
        </table>
			</div>
		</div>
<?php
    $virtual_page = "entry_infowindow/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
<?php } ?>
