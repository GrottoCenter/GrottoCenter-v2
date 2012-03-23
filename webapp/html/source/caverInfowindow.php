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
$frame = "infowindow";
include("application_".$_SESSION['language'].".php");
include("mailfunctions_".$_SESSION['language'].".php");
$id = (isset($_GET['id'])) ? $_GET['id'] : '';
$caver_is_connected = (isset($_GET['connected'])) ? ($_GET['connected'] == "true") : false;
$caver_is_referent = in_array($id, getReferentCavers(LEADER_GROUP_ID));
$clustered = (isset($_GET['clustered'])) ? ($_GET['clustered'] == "c") : false;

if (!$clustered) {
	if ($id != "") {
		$sql = "SELECT cr.*, ct.Id Id_comment, lbl.".$_SESSION['language']." AS Label, grp.Level FROM `".$_SESSION['Application_host']."`.`T_caver` cr ";
		$sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_comment` ct ON cr.Id = ct.Id_author ";
		$sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_caver_group` cg ON cg.Id_caver = cr.Id ";
		$sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_group` grp ON grp.Id = cg.Id_group ";
		$sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_label` lbl ON grp.Id_label = lbl.Id ";
		$sql .= "WHERE cr.Id = ".$id." ";
		$sql .= "ORDER BY grp.Level";
		$caver = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
		$facebook = $caver[0]['Facebook'];
		$contact = "<a href='mailto:".$caver[0]['Contact']."'>".$caver[0]['Contact']."</a>";
		switch ($caver[0]['Contact_is_public']) {
			case 1: //Contact visible uniquement par les inscrits a GrottoCenter
				if (!USER_IS_CONNECTED) {
					$contact = "<a href='connection_".$_SESSION['language'].".php?type=login' target='filter'><i><convert>#label=164<convert></i></a>";//Connectez vous
				}
				break;
			case 2: //Contact toujours visible
				break;
			default:
				$contact = "<i><convert>#label=165<convert></i>";//Privé
				break;
		}
		//Number of grottoes
		$sql = "SELECT GROUP_CONCAT(DISTINCT go.Name ORDER BY go.Name SEPARATOR ', ') As Grottoes, COUNT(*) AS Count ";
		$sql .= "FROM `".$_SESSION['Application_host']."`.`J_grotto_caver` gc ";
		$sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_grotto` go ON go.Id = gc.Id_grotto ";
		$sql .= "WHERE gc.Id_caver = ".$id;
		$grottoes = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
		$grottoes_list = $grottoes[0]['Grottoes'];
		if (strlen($grottoes_list)>30) {
			$grottoes_list = substr($grottoes_list,0,30)."...";
		}
		//Number of entries
		$sql = "SELECT COUNT(*) Count FROM `".$_SESSION['Application_host']."`.`J_entry_caver` ";
		$sql .= "WHERE Id_caver = ".$id;
		$entries = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
		//Number of comments
		$sql = "SELECT COUNT(*) Count FROM `".$_SESSION['Application_host']."`.`T_comment` ";
		$sql .= "WHERE Id_author = ".$id;
		$comments = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
		
		if ($caver[0]['Picture_file_name'] == "") {
			$avatar_filename = "default_avatar.png";
		} else {
			$avatar_filename = $caver[0]['Picture_file_name'];
		}
	}
} else {
	$sql = "SELECT * FROM `".$_SESSION['Application_host']."`.`T_caver` ";
	$sql .= "WHERE Id IN (".$id.") ";
	$cavers = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
}
?>
<?php if (!$clustered) { ?>
  	<div id="GC_IW_header_div" class="headercaver <?php if ($caver_is_referent) { echo "referent_"; } ?>caver<?php if ($caver_is_connected) { echo "_connected"; } ?>">
  		<table cellspacing="0" cellpadding="0" border="0" width="100%"><tr><td style="width:50%;text-align:right;line-height:32px;"><convert>#label=172<convert><!--Spéléologue--></td>
      <td><div class="grouplabel<?php echo $caver[0]['Level'] ?>" style="font-size:7pt;"><?php echo getTopBubble(); ?><?php echo $caver[0]['Label']; ?><?php echo getBotBubble(); ?></div></td></tr></table>
  	</div>
  	<div class="avatar">
      <img src="../upload/avatars/<?php echo $avatar_filename; ?>" alt="avatar" />
    </div>
    <div class="content">
			<div class="label">
				<span class="value">
          <?php echo $caver[0]['Surname']; ?> <?php echo $caver[0]['Name']; ?>
        </span> 
        <a href="JavaScript:showRelationList('<?php echo $_SESSION['language']; ?>','entry','caver','<?php echo $id; ?>');" title="<convert>#label=539<convert>"><!--Voir la liste-->
          <span class="value_entry">
            <?php echo $entries[0]['Count']; ?>
          </span>
        </a> 
        <a href="JavaScript:showRelationList('<?php echo $_SESSION['language']; ?>','grotto','caver','<?php echo $id; ?>');" title="<convert>#label=539<convert>"><!--Voir la liste-->
          <span class="value_grotto">
            <?php echo $grottoes[0]['Count']; ?>
          </span>
        </a> 
        <a href="JavaScript:showRelationList('<?php echo $_SESSION['language']; ?>','contribution','caver','<?php echo $id; ?>');" title="<convert>#label=539<convert>"><!--Voir la liste-->
					<span class="value_comment">
						<?php echo $comments[0]['Count']; ?>
					</span>
				</a>
			</div>
    	<div class="label">
    		<convert>#label=34<convert><!--Alias--> : <span class="value"><?php echo $caver[0]['Nickname']; ?></span> <?php echo getFacebookATag($facebook, true); ?>
        <!--<span class="value" id="GC_IW_isConnected" style="display:none;float:right;"><convert>#label=566<convert></span>--><!--Est connecté-->
    	</div>
    	<div class="label">
    		<convert>#label=173<convert><!--E-mail--> : <span class="value"><?php echo $contact; ?></span>
    	</div>
<?php if ($grottoes_list != "") { ?>
    	<div class="label">
        <convert>#label=386<convert><!--Clubs--> : <span class="value"><?php echo $grottoes_list; ?></span>
    	</div>
<?php } ?>
<?php if ($caver[0]['Custom_message'] != "") { ?>
    	<div class="custom_message">
        <?php echo replaceLinks(nl2br($caver[0]['Custom_message'])); ?>
    	</div>
<?php } ?>
    	<div id="GC_IW_directions" class="directions">
        <convert>#label=166<convert><!--Itinéraire--> : <a href="JavaScript:getDirectionsForm(false);"><convert>#label=167<convert><!--Vers ce lieu--></a> - <a href="JavaScript:getDirectionsForm(true);"><convert>#label=168<convert><!--Depuis ce lieu--></a>
      </div>
		</div>
		<div class="footer">
			<label for="link"><input type="checkbox" name="link" id="GC_IW_link" class="inputIW" title="<convert>#label=174<convert>" style="border: none;" onclick="Javascript:switchLines(<?php echo $id; ?>, 'caver', this.checked);" /><convert>#label=175<convert><!--Liens--></label><!--Afficher les liens-->
			<span class="details">
<?php
      if ($caver[0]['Id_comment'] != "" && False) { //DESACTIVE !!!
?>
        <a href="comments_<?php echo $_SESSION['language']; ?>.php?caver_id=<?php echo $id; ?>" target="_blank" title="<convert>#label=176<convert>"><!--Voir les commentaires déposés par ce spéléologue-->
				  <convert>#label=177<convert><!--Commentaires...-->
			  </a><br />
<?php
      }
?>
        <a href="#" onclick="JavaScript:detailMarker(event, 'caver', '<?php echo $id; ?>', '<?php echo $_SESSION['language']; ?>');"  title="<convert>#label=178<convert>"><!--Obtenir les propriétés de ce marqueur-->
				  <convert>#label=179<convert><!--Propriétés-->
			  </a><br />
			  <a href="contact_<?php echo $_SESSION['language']; ?>.php?type=message&amp;subject=bad_content&amp;category=caver&amp;name=<?php echo $caver[0]['Login']; ?>" target="filter" title="<convert>#label=180<convert>"><!--Signaler du contenu hors-charte-->
				  <convert>#label=181<convert><!--Hors-charte-->
        </a>
      </span>
		</div>
<?php
    $virtual_page = "caver_infowindow/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
<?php 
//##############################################################################
} else {
//##############################################################################
?>
  	<div class="header cavers_clustered" style="height:42px;">
  		<convert>#label=385<convert><!--Speleologues-->
  	</div>
    <div class="content" style="height:170px;">
      <div class="info">
        <?php echo getTopBubble(); ?>
        <convert>#label=643<convert><!--Pour détailler chaque marqueur, veuillez zoomer.-->
        <?php echo getBotBubble(); ?>
      </div>
			<div class="label">
        <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
<?php while(list($k,$v) = each($cavers)) { if ($v['Nickname'] != "") { ?>
  				<tr><td class="label" style="white-space:nowrap;">
  				  <a href="JavaScript:openMe('<?php echo $v['Id']; ?>', 'caver', false, undefined, true);">
              <?php echo $v['Nickname']; ?>
            </a>
          </td><td style="white-space:nowrap;">
            <a href="#" onclick="JavaScript:detailMarker(event, 'caver', '<?php echo $v['Id']; ?>', '<?php echo $_SESSION['language']; ?>');" title="<convert>#label=178<convert>"><!--Obtenir les propriétés de ce marqueur-->
    				  <convert>#label=179<convert><!--Propriétés-->
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
