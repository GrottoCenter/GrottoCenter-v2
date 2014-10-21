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
 * @copyright Copyright (c) 2009-2012 Clement Ronzon
 * @license http://www.gnu.org/licenses/agpl.txt
 */
include("../conf/config.php");
include("../func/function.php");
include("declaration.php");
$frame = "overview";
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
    <script type="text/javascript" src="<?php echo getScriptJS(__FILE__); ?>"></script>
<?php
/*
--Check:
SELECT DISTINCT T_entry.Id, T_entry.Has_contributions
FROM V_contributions
LEFT OUTER JOIN T_entry ON V_contributions.Id_entry = T_entry.Id
WHERE (V_contributions.Id IS NULL AND T_entry.Has_contributions = 'YES') OR (V_contributions.Id IS NOT NULL AND T_entry.Has_contributions = 'NO')
*/
  	include("application_".$_SESSION['language'].".php");
  	include("mailfunctions_".$_SESSION['language'].".php");
//CRO 2011-10-12
if (false) {
  	$sql = "SELECT Nickname FROM `".$_SESSION['Application_host']."`.`T_caver` WHERE Date_inscription >= STR_TO_DATE(CONCAT_WS('/', YEAR(NOW()), MONTH(NOW())-1, '01'), '%Y/%m/%d') ORDER BY Date_inscription DESC";
  	$newCaversArray = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
  	$new_cavers = array();
  	for ($i=0;$i<$newCaversArray['Count'];$i++) {
      $new_cavers[] .= $newCaversArray[$i]['Nickname'];
    }
    $newCaversStr = implode(", ", array_unique($new_cavers));
  	
  	$sql = "SELECT CONCAT(Nickname,' (',Relevance,' pts)') AS Result FROM `".$_SESSION['Application_host']."`.`T_caver` ORDER BY Relevance DESC, Nickname ASC LIMIT 0,10";
  	$bestCaversArray = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
  	$best_cavers = array();
  	for ($i=0;$i<$bestCaversArray['Count'];$i++) {
      $best_cavers[] .= $bestCaversArray[$i]['Result'];
    }
  	$bestCaversStr = implode(", ", array_unique($best_cavers));
}
    $records_by_page = 20;
    $max_length = 40;
    
    $sql = "SELECT ";
    $sql .= "IFNULL(contrib.Id_entry,contrib.Id) AS `0`, ";
    $sql .= "CASE contrib.Category ";
    $sql .= "WHEN _latin1'T_description' THEN '<convert>#label=497<convert>' ";//Description
    $sql .= "WHEN _latin1'J_entry_description' THEN '<convert>#label=497<convert>' ";//Description
    $sql .= "WHEN _latin1'T_comment' THEN '<convert>#label=638<convert>' ";//Commentaire
    $sql .= "WHEN _latin1'T_location' THEN '<convert>#label=639<convert>' ";//Localisation
    $sql .= "WHEN _latin1'J_entry_rigging' THEN '<convert>#label=640<convert>' ";//Equipement
    $sql .= "WHEN _latin1'T_history' THEN '<convert>#label=593<convert>' ";//Historique
    $sql .= "WHEN _latin1'T_bibliography' THEN '<convert>#label=590<convert>' ";//Bibliographie
    $sql .= "WHEN _latin1'T_entry' THEN '<convert>#label=625<convert>' ";//Entrée
    $sql .= "WHEN _latin1'T_grotto' THEN '<convert>#label=186<convert>' ";//Club
    $sql .= "WHEN _latin1'T_cave' THEN '<convert>#label=119<convert>' ";//Réseau
    $sql .= "WHEN _latin1'T_massif' THEN '<convert>#label=555<convert>' ";//Massif
    $sql .= "WHEN _latin1'T_url' THEN '<convert>#label=669<convert>' ";//Site partenaire
    $sql .= "WHEN _latin1'T_topography' THEN '<convert>#label=845<convert>' ";//Topographie
    $sql .= "ELSE NULL END AS `1`, ";//Categorie
    $sql .= "IF(CHAR_LENGTH(contrib.Title)<".$max_length.",contrib.Title,CONCAT(SUBSTR(contrib.Title,1,".$max_length."),'...')) AS `2`, ";//Titre
    //$sql .= "IF(CHAR_LENGTH(".$_SESSION['language']."_body)<".$max_length.",Body,CONCAT(SUBSTR(Body,1,".$max_length."),'...')) AS `[hidden]|<convert>#label=497<convert>`, ";//Description
    $sql .= "IF(CHAR_LENGTH(contrib.Body)<".$max_length.",contrib.Body,CONCAT(SUBSTR(contrib.Body,1,".$max_length."),'...')) AS `3`, ";//Description
    $sql .= "contrib.Date_inscription AS `4`, ";
    $sql .= "contrib.Date_reviewed AS `5`, ";
    $sql .= "IF(contrib.Category NOT IN (_latin1'T_url', _latin1'T_grotto', _latin1'T_cave', _latin1'T_massif'),'YES','NO') AS `6`, ";
    $sql .= "IF(contrib.Category IN (_latin1'T_url'),'YES','NO') AS `7`, ";
    $sql .= "contrib.Body AS `8`, ";
    $sql .= "DATE_FORMAT(IF(contrib.Date_reviewed>contrib.Date_inscription, contrib.Date_reviewed, contrib.Date_inscription), '%e %b. %Y, %H:%i') AS `9`, ";//Date
    $sql .= "IF(contrib.Date_reviewed>contrib.Date_inscription, rev.Nickname, auth.Nickname) AS `10` ";//Auteur
    $sql .= "FROM `".$_SESSION['Application_host']."`.`V_contributions` contrib ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_caver` rev ON rev.Id = contrib.Id_reviewer ";
    $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_caver` auth ON auth.Id = contrib.Id_author ";
    if (!USER_IS_CONNECTED) {
      $sql .= "WHERE contrib.Is_public = 'YES' OR contrib.Is_public IS NULL ";
    }
    $sql .= "ORDER BY IFNULL(contrib.Date_reviewed, contrib.Date_inscription) DESC, contrib.Title ASC ";
    $sql .= "LIMIT ".$records_by_page;
		
    $columns_params = array(
			0 => "[hidden]|[hidden]Id",
			1 => "[hidden]|<convert>#label=235<convert>",
			2 => "[hidden]|<convert>#label=641<convert>",
			3 => "[hidden]|<convert>#label=497<convert>",
			4 => "[hidden]|[hidden]Di",
			5 => "[hidden]|[hidden]Dr",
			6 => "[hidden]|[hidden]6",
			7 => "[hidden]|[hidden]7",
			8 => "[hidden]|[hidden]8",
			9 => "[hidden]|<convert>#label=682<convert>",
			10 => "[hidden]|<convert>#label=723<convert>"
		);
    
    $category = "entry";
    //$entry_file_link = $_SESSION['Application_url']."/html/file_".$_SESSION['language'].".php?lang=".$_SESSION['language']."&amp;check_lang_auto=false&amp;category=".$category."&amp;id=<Id>";
    $entry_file_link = "javascript:detailMarker(event,'entry','<Id>','".$_SESSION['language']."',true);";
    $links = array (
            2 => array(
                'conditions' =>  array(
                                6 => 'YES'),
                'parameters' => array(
                                '<Id>' => 0),
                'link' => $entry_file_link,
                'target' => 'onclick'),
            3 => array(
                'conditions' =>  array(
                                7 => 'YES'),
                'parameters' => array(
                                '<Url>' => 8),
                'link' => "<Url>",
                'target' => '_blank'));
    $input_type = array(
            'type' => '',
            'conditions' => array());
    $style = array();
    $filter_form = "automatic_form";
    $list_form = "result_form";
    $result = getRowsFromSQL($sql, $columns_params, $links, $records_by_page, $filter_form, $list_form, $_POST, $input_type, $style, 2, false, false, "");
    $resource_id = $result['resource_id'];
    //echo $result['debug'];
//    mysql_free_result($resource_id);
    $rows = $result['rows'];
    $total_count = $result['total_count'];
    $local_count = $result['local_count'];
    $count_page = ceil($total_count/$records_by_page);
?>
    <?php echo getMetaTags(); ?>
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=600<convert></title>
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
    <link rel="stylesheet" type="text/css" href="../css/home.css" />
    <link rel="stylesheet" type="text/css" href="../css/portlet.css" />
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
    //USES FUNCTION switchDOM
    
    function load() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
    }
    
    function switchMe(sId) {
      switchDOM(sId);
      var element = xtdGetElementById(sId+"_s");
      if (element.className == "div_switcher_c") {
        element.className = "div_switcher_o";
      } else {
        element.className = "div_switcher_c";
      }
    }
    
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:load();">
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
    <div class="frame_title"><?php echo setTitle("home_".$_SESSION['language'].".php", "home", "<convert>#label=600<convert>", 1); ?></div><!--Acceuil-->
    <table border="0" cellspacing="1" cellpadding="0" class="main_table">
      <tr><td>
        <!--<div>-->
        <?php include("home_description.php"); ?>
        <!--<ul><li><?php //echo getAddThisButton($_SESSION['language'], 'grottocenter') ?></li></ul>-->
        <!--</div>-->
      </td></tr>
      <tr><td>
        <h3><convert>#label=889<convert><!--Communiqués :--></h3>
        <div class="home_news" style="height:auto;">
            <strong><convert>#label=937<convert></strong><!-- Grottocenter vous intéresse ? Toutes les bonnes volontés sont les bienvenues. -->
            <ul>
                <li><convert>#label=938<convert></li><!-- Vous pouvez marquer votre soutien en devenant membre actif de l’association. -->
                <li><convert>#label=939<convert></li><!-- Pour partager avec le plus grand nombre, nous recherchons des traducteurs. -->
                <li><convert>#label=940<convert></li><!-- Grottocenter c’est aussi un logiciel qui a besoin de développeurs. -->
                <li><convert>#label=941<convert></li><!-- Le projet avance grâce aux clubs et aux CDS qui deviennent nos partenaires. -->
            </ul>
        </div>
        <div class="home_news" style="height:auto;">
            <p><convert>#label=932<convert></p><!-- Par décision du Conseil d’Administration de l'association Wikicaves en date du 12 mai 2013 la licence du site http://www.grottocenter.org a été modifiée de CC-BY-NC à CC-BY-SA. -->
            <p><convert>#label=933<convert></p><!-- Cette décision a été motivée par notre volonté de : -->
            <ul>
                <li><convert>#label=934<convert></li><!-- Permettre un rapprochement avec d’autres projets opensource grâce à l’utilisation de licences compatibles -->
                <li><convert>#label=935<convert></li><!-- Inciter les utilisateurs des données présentes dans Grottocenter, à partager à leur tour leur travail, sous la même licence. Ainsi les données spéléologiques deviendront petit à petit plus facilement accessibles. -->
            </ul>
            <p><convert>#label=936<convert></p><!-- Si vous avez inséré dans Grottocenter des informations qui relèvent du droit d’auteur et si vous ne souhaitez pas que ces informations restent dans le site sous cette nouvelle licence merci de nous contacter. -->
        </div>
      </td></tr>
<?php
//CRO 2011-10-12
if (false) {
    $forum = array('Fr' => 29, 'En' => 28, 'Es' => 30, 'De' => 31);
		$posts = getForumPosts($forum[$_SESSION['language']]);
		if ($posts['Count'] > 0) {
?>
			<tr><td>
				<h3><convert>#label=889<convert><!--Communiqués :--></h3>
				<div class="home_news">
<?php
    $forum = array('Fr' => 29, 'En' => 28, 'Es' => 30, 'De' => 31);
		$posts = getForumPosts($forum[$_SESSION['language']]);
    for($i=0;$i<$posts['Count'];$i++) {
			$text_rows = explode('<br />', $posts[$i]['text']);
			$first_text_row = $text_rows[0];
      echo "<div class=\"news_topic\">";
			echo "<a href=\"JavaScript:openWindow('help_Fr.php?f=".$posts[$i]['forum_id']."&amp;t=".$posts[$i]['topic_id']."', '', 800, 700);\"><span class=\"topic_title\">".$posts[$i]['subject']."</span><br />";
      echo "<span class=\"topic_resume\">".$first_text_row."</span></a> <span class=\"topic_pty\"><convert>#label=734<convert> ".date("l j F Y H:i:s T", $posts[$i]['date'])."</span>";
      echo "</div>";
    }
?>
				</div>
			</td></tr>
<?php } } ?>
			<tr><td>
        <h3><convert>#label=648<convert><!--Accèder au mode de vue "carte"-->: <a href="../index.php?home_page=overview" class="nothing" target="_top"><img src="../images/icons/overview.png" alt="<convert>#label=608<convert>" title="<convert>#label=608<convert>" style="border:0px none;" /></a></h3>
        <h3><convert>#label=730<convert><!--S'abonner au flux RSS-->: <a href="rss_<?php echo $_SESSION['language']; ?>.xml" class="nothing" target="_blank"><img src="../images/icons/rss.png" alt="<convert>#label=730<convert>" title="<convert>#label=730<convert>" style="border:0px none;height:18px;" /></a></h3>
        <h3><convert>#label=633<convert><!--Accèder à la liste des éléments de--> <?php echo $_SESSION['Application_name']; ?>:</h3>
        <ul>
          <li>
            <a href="portlet_<?php echo $_SESSION['language']; ?>.php?type=entry" class="nothing"><img src="../images/icons/bullet_entry.png" alt="<convert>#label=634<convert>" style="border:0px none;height:9pt;" /></a> <a href="portlet_<?php echo $_SESSION['language']; ?>.php?type=entry"><convert>#label=634<convert><!--Liste des cavités--> (<?php echo countByCategory("entry"); ?>)</a> - <a href="JavaScript:openWindow('density_<?php echo $_SESSION['language']; ?>.php?type=entry', '', 592, 379);" title="<convert>#label=806<convert>"><convert>#label=806<convert><!--Densité--></a>
          </li>
          <li>
            <a href="portlet_<?php echo $_SESSION['language']; ?>.php?type=grotto" class="nothing"><img src="../images/icons/bullet_grotto.png" alt="<convert>#label=635<convert>" style="border:0px none;height:9pt;" /></a> <a href="portlet_<?php echo $_SESSION['language']; ?>.php?type=grotto"><convert>#label=635<convert><!--Liste des clubs--> (<?php echo countByCategory("grotto"); ?>)</a> - <a href="JavaScript:openWindow('density_<?php echo $_SESSION['language']; ?>.php?type=grotto', '', 592, 379);" title="<convert>#label=806<convert>"><convert>#label=806<convert><!--Densité--></a>
          </li>
          <li>
            <a href="portlet_<?php echo $_SESSION['language']; ?>.php?type=caver" class="nothing"><img src="../images/icons/bullet_caver.png" alt="<convert>#label=636<convert>" style="border:0px none;height:9pt;" /></a> <a href="portlet_<?php echo $_SESSION['language']; ?>.php?type=caver"><convert>#label=636<convert><!--Liste des spéléos--> (<?php echo countByCategory("caver"); ?>)</a> - <a href="JavaScript:openWindow('density_<?php echo $_SESSION['language']; ?>.php?type=caver', '', 592, 379);" title="<convert>#label=806<convert>"><convert>#label=806<convert><!--Densité--></a>
          </li>
          <li>
            <!--<a href="portlet_<?php echo $_SESSION['language']; ?>.php?type=url" class="nothing"> --><img src="../images/icons/bullet_url.png" alt="<convert>#label=670<convert>" style="height:9pt;" /><!--</a>--> <a href="portlet_<?php echo $_SESSION['language']; ?>.php?type=url"><convert>#label=670<convert><!--Sites partenaires--> (<?php echo countByCategory("url"); ?>)</a>
          </li>
        </ul>
<?php if (isset($newCaversStr) && $newCaversStr != "") { ?>
        <h3><convert>#label=687<convert><!--Bienvenue aux inscrits du mois--></h3>
        <div class="box"><?php echo getTopBox("ffffff", "C3D9FF", "E4EAEF")."<div style=\"height:80px;overflow-x:auto;\">".$newCaversStr.".</div>".getBotBox(); ?></div>
<?php } ?>
      </td></tr><tr><td>
        <h3><convert>#label=637<convert><!--Contributions récentes-->:</h3>
        <table border="0" cellspacing="1" cellpadding="0" id="result_table">
          <?php if ($total_count > 0) { echo $rows; } else { ?><convert>#label=622<convert><!--Aucun résultat n'est disponible--><?php } ?>
        </table>
      </td></tr>
<!--
//CRO 2011-10-12
<tr><td>
        <h3><convert>#label=720<convert>--><!--Bravo aux 10 meilleurs contributeurs--><!--:</h3>
        <div class="box"><?php echo getTopBox("ffffff", "C3D9FF", "E4EAEF").$bestCaversStr.".".getBotBox(); ?></div>
      </td></tr>-->
    </table>
    <?php echo getBotFrame(); ?>
<?php
    $virtual_page = "home/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>