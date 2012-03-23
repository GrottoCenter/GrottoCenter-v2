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
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
    <script type="text/javascript" src="<?php echo getScriptJS(__FILE__); ?>"></script>
    <script type="text/javascript" src="../scripts/calendar.js"></script>
<?php
  	include("application_".$_SESSION['language'].".php");
  	include("mailfunctions_".$_SESSION['language'].".php");
    $type = (isset($_GET['type'])) ? $_GET['type'] : '';
    $callback_function = (isset($_GET['callback'])) ? $_GET['callback'] : '';
    $category = (isset($_GET['category'])) ? $_GET['category'] : '';
    $exit = false;
    $close_window = false;
    $reload_overview = false;
    
    if (isset($_POST['overview_filter'])) { //Save the selected filter, then close the window
      $_SESSION[$category.'_load_conditions'] = getWhereClause($_POST);
      $_SESSION[$category.'_filter_set'] = $_POST;
      $reload_overview = true;
      $exit = true;
    }
    
    if ($type == "filter") { //Show the selected filter
      if (!(isset($_POST['submit_filter']) || isset($_POST['order']) || isset($_POST['current']) || isset($_POST['overview_filter']) || isset($_POST['records_by_page']))) {
        $_POST = $_SESSION[$category.'_filter_set'];
      }
    }
?>

<?php
if ($exit) {
?>
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
    //gona need those functions: checkAll
    //function load() {
<?php if ($reload_overview) { ?>
    window.opener.visibilityOnClick("advanced", true);
    //window.opener.focus();
<?php } ?>
    //window.close();
    self.close();
    //}
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body><!-- onload="JavaScript:load();"-->
  </body>
</html>
<?php
  exit();
}
?>

<?php
    switch ($type.$category) {
      case "entry":
        $sql = "SELECT DISTINCT ";
        $sql .= "T_entry.Id AS `0`, ";
        $sql .= "T_entry.Name AS `1`, ";//Nom de la cavité
        $sql .= "ROUND(T_entry.Latitude,5) AS `2`, ";//Latitude
        $sql .= "ROUND(T_entry.Longitude,5) AS `3`, ";//Longitude
        $sql .= "T_country.".$_SESSION['language']."_name AS `4`, "; //Pays
        $sql .= "T_entry.Region AS `5`, ";//Etat/Région
        $sql .= "T_entry.City AS `6`, ";//Commune
//CRO 2011-10-12
//        $sql .= "T_massif.Name AS `7`, ";//Massif
        $sql .= "T_cave.Name AS `8`, ";//Réseau
        $sql .= "T_type.".$_SESSION['language']."_type AS `9` ";//Type de cavité
        $sql .= "FROM `".$_SESSION['Application_host']."`.`T_entry` ";
        $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_cave_entry` ON J_cave_entry.Id_entry = T_entry.Id ";
//CRO 2011-10-12
//        $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_massif_cave` ON (J_massif_cave.Id_cave = J_cave_entry.Id_cave OR J_massif_cave.Id_entry = T_entry.Id) ";
//        $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_massif` ON T_massif.Id = J_massif_cave.Id_massif ";
        $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_cave` ON T_cave.Id = J_cave_entry.Id_cave ";
        $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_type` on T_type.Id = T_entry.Id_type ";
        $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_country` ON T_country.Iso = T_entry.Country ";
        $links = array();
				$columns_params = array(
					0 => "[hidden]|[hidden]Id",
					1 => "T_entry*Name|<convert>#label=613<convert>",
					2 => "T_entry*Latitude|[hidden]<convert>#label=103<convert>",
					3 => "T_entry*Longitude|[hidden]<convert>#label=105<convert>",
					4 => "T_entry*Country|<convert>#label=98<convert>|SELECT Iso AS value,".$_SESSION['language']."_name AS text FROM ".$_SESSION['Application_host'].".T_country ORDER BY text",
					5 => "T_entry*Region|<convert>#label=100<convert>",
					6 => "T_entry*City|<convert>#label=101<convert>",
//CRO 2011-10-12
//					7 => "T_massif*Name|<convert>#label=555<convert>",
					7 => "[hidden]|[hidden]Massif_name",
					8 => "T_cave*Name|<convert>#label=119<convert>",
					9 => "T_entry*Id_type|<convert>#label=114<convert>|SELECT Id AS value,".$_SESSION['language']."_type AS text FROM ".$_SESSION['Application_host'].".T_type ORDER BY text"
				);
      break;
      case "url":
        $sql = "SELECT DISTINCT ";
        $sql .= "T_url.Id AS `0`, ";
        $sql .= "T_url.Name AS `1`, ";//Nom du site
        $sql .= "T_url.Url AS `2`, ";//Lien (URL)
        $sql .= "T_url.Comments AS `3` ";//Commentaires
        $sql .= "FROM `".$_SESSION['Application_host']."`.`T_url` ";
        $links = array (
                1 => array(
                    'conditions' =>  array(),
                    'parameters' => array(
                                    '<Url>' => 2),
                    'link' => '<Url>',
                    'target' => '_blank'));
				$columns_params = array(
					0 => "[hidden]|[hidden]Id",
					1 => "T_url*Name|<convert>#label=671<convert>",
					2 => "T_url*Name|<convert>#label=672<convert>",
					3 => "T_url*Comments|<convert>#label=638<convert>"
				);
      break;
      case "filterentry":
        $sql = "SELECT DISTINCT ";
        $sql .= "T_entry.Id AS `0`, ";
        $sql .= "T_entry.Name AS `1`, ";//Nom de la cavité
        $sql .= "IF((T_topography.Id IS NOT NULL),'<convert>#label=626<convert>','<convert>#label=627<convert>') AS `2`, ";//Topographies
        $sql .= "IF(T_entry.Has_contributions='YES','<convert>#label=626<convert>','<convert>#label=627<convert>') AS `3`, ";//Fiche détaillée
//        $sql .= "ROUND((V_entry_avg.Aestheticism+V_entry_avg.Caving+V_entry_avg.Approach)/3,1) AS `(V_entry_avg*Aestheticism+V_entry_avg*Caving+V_entry_avg*Approach)/3|<convert>#label=886<convert>`, ";//Evaluation (moy. sur 10)
        $sql .= "IFNULL(T_cave.Depth,T_single_entry.Depth) AS `4`, ";//Dénivellation
        $sql .= "IFNULL(T_cave.Length,T_single_entry.Length) AS `5`, ";//Développement
        //$sql .= "ROUND(T_entry.Latitude,5) AS `[hidden]|<convert>#label=103<convert>`, ";//Latitude
        //$sql .= "ROUND(T_entry.Longitude,5) AS `[hidden]|<convert>#label=105<convert>`, ";//Longitude
        $sql .= "ROUND(T_entry.Latitude,5) AS `6`, ";//Latitude
        $sql .= "ROUND(T_entry.Longitude,5) AS `7`, ";//Longitude
				$sql .= "T_country.".$_SESSION['language']."_name AS `8`, ";//Pays
        $sql .= "T_entry.Region AS `9`, ";//Etat/Région
        $sql .= "T_entry.City AS `10`, ";//Commune
        $sql .= "T_massif.Name AS `11`, ";//Massif
        $sql .= "T_cave.Name AS `12`, ";//Réseau
        $sql .= "T_type.".$_SESSION['language']."_type AS `13`, ";//Type de cavité
        $sql .= "IF(IFNULL(T_cave.Is_diving,T_single_entry.Is_diving)='YES','<convert>#label=626<convert>','<convert>#label=627<convert>') AS `14`, ";//Plongée sout.
        $sql .= "ROUND(V_entry_avg.Aestheticism,1) AS `15`, ";//Esthétisme (moy. sur 10)
        $sql .= "ROUND(V_entry_avg.Caving,1) AS `16`, ";//Facilité de progression (moy. sur 10)
        $sql .= "ROUND(V_entry_avg.Approach,1) AS `17`, ";//Facilité d'accès (moy. sur 10)
        $sql .= "T_entry.Year_discovery AS `18` ";//Année de découverte
        $sql .= "FROM `".$_SESSION['Application_host']."`.`T_entry` ";
        $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_cave_entry` ON J_cave_entry.Id_entry = T_entry.Id ";
        $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_massif_cave` ON (J_massif_cave.Id_cave = J_cave_entry.Id_cave OR J_massif_cave.Id_entry = T_entry.Id) ";
        $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_massif` ON T_massif.Id = J_massif_cave.Id_massif ";
        $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_cave` ON T_cave.Id = J_cave_entry.Id_cave ";
        $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_type` on T_type.Id = T_entry.Id_type ";
        $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_country` ON T_country.Iso = T_entry.Country ";
        $sql .= "LEFT OUTER JOIN (SELECT T_topography.*, J_topo_cave.Id_cave, J_topo_cave.Id_entry ";
        $sql .= "FROM `".$_SESSION['Application_host']."`.`T_topography` ";
        $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_topo_cave` ON (T_topography.Id = J_topo_cave.Id_topography AND T_topography.Enabled = 'YES')) T_topography ";
        $sql .= "ON ((T_topography.Id_cave = J_cave_entry.Id_cave OR T_topography.Id_entry = T_entry.Id) ";
        if (USER_IS_CONNECTED) {
        } else {
          $sql .= "AND T_topography.Is_public = 'YES' ";
        }
        $sql .= ") ";
        $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_single_entry` ON T_single_entry.Id = T_entry.Id ";
        $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`V_entry_avg` ON T_entry.Id = V_entry_avg.Id_entry ";
        //$entry_file_link = $_SESSION['Application_url']."/html/file_".$_SESSION['language'].".php?lang=".$_SESSION['language']."&amp;check_lang_auto=false&amp;category=entry&amp;id=<Id>";
        $entry_file_link = "javascript:detailMarker(event,'entry','<Id>','".$_SESSION['language']."',true);";
        $links = array (
                1 => array(
                    'conditions' =>  array(),
                    'parameters' => array(
                                    '<Id>' => 0),
                    'link' => $entry_file_link,
                    'target' => 'onclick'),
                3 => array(
                    'conditions' =>  array(
                                    3 => '<convert>#label=626<convert>'),
                    'parameters' => array(
                                    '<Id>' => 0),
                    'link' => $entry_file_link,
                    'target' => 'onclick'));
				$columns_params = array(
					0 => "[hidden]|[hidden]Id",
					1 => "T_entry*Name|<convert>#label=613<convert>",
					2 => "IF((T_topography*Id@IS@NOT@NULL),'<convert>#label=626<convert>','<convert>#label=627<convert>')|<convert>#label=815<convert>|<convert>#label=626<convert>;<convert>#label=627<convert>",
					3 => "IF(T_entry*Has_contributions='YES','<convert>#label=626<convert>','<convert>#label=627<convert>')|<convert>#label=614<convert>|<convert>#label=626<convert>;<convert>#label=627<convert>",
					4 => "IFNULL(T_cave*Depth,T_single_entry*Depth)|<convert>#label=64<convert>",
					5 => "IFNULL(T_cave*Length,T_single_entry*Length)|<convert>#label=68<convert>",
					6 => "T_entry*Latitude|<convert>#label=103<convert>",
					7 => "T_entry*Longitude|<convert>#label=105<convert>",
					8 => "T_entry*Country|<convert>#label=98<convert>|SELECT Iso AS value,".$_SESSION['language']."_name AS text FROM ".$_SESSION['Application_host'].".T_country ORDER BY text",
					9 => "T_entry*Region|<convert>#label=100<convert>",
					10 => "T_entry*City|<convert>#label=101<convert>",
					11 => "T_massif*Name|<convert>#label=555<convert>",
					12 => "T_cave*Name|<convert>#label=119<convert>",
					13 => "T_entry*Id_type|<convert>#label=114<convert>|SELECT Id AS value,".$_SESSION['language']."_type AS text FROM ".$_SESSION['Application_host'].".T_type ORDER BY text",
					14 => "IF(IFNULL(T_cave*Is_diving,T_single_entry*Is_diving)='YES','<convert>#label=626<convert>','<convert>#label=627<convert>')|<convert>#label=71<convert>|<convert>#label=626<convert>;<convert>#label=627<convert>",
					15 => "V_entry_avg*Aestheticism|<convert>#label=615<convert>",
					16 => "V_entry_avg*Caving|<convert>#label=616<convert>",
					17 => "V_entry_avg*Approach|<convert>#label=617<convert>",
					18 => "T_entry*Year_discovery|<convert>#label=109<convert>"
				);
      break;
      case "grotto":
      case "filtergrotto":
        $sql = "SELECT DISTINCT ";
        $sql .= "T_grotto.Id AS `0`, ";
        $sql .= "T_grotto.Name AS `1`, ";//Nom du club
        $sql .= "T_country.".$_SESSION['language']."_name AS `2`, "; //Pays
        $sql .= "T_grotto.Region AS `3`, ";//Etat/Région
        $sql .= "T_grotto.City AS `4`, ";//Commune
        $sql .= "COUNT(J_grotto_caver.Id_caver) AS `5` ";//Nombre de spéléologues
        $sql .= "FROM `".$_SESSION['Application_host']."`.`T_grotto` ";
        $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_country` ON T_country.Iso = T_grotto.Country ";
        $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_grotto_caver` ON J_grotto_caver.Id_grotto = T_grotto.Id ";
        $sql .= "GROUP BY T_grotto.Id ";
        $links = array ();
				$columns_params = array(
					0 => "[hidden]|[hidden]Id",
					1 => "T_grotto*Name|<convert>#label=144<convert>",
					2 => "T_grotto*Country|<convert>#label=98<convert>|SELECT Iso AS value,".$_SESSION['language']."_name AS text FROM ".$_SESSION['Application_host'].".T_country ORDER BY text",
					3 => "T_grotto*Region|<convert>#label=100<convert>",
					4 => "T_grotto*City|<convert>#label=101<convert>",
					5 => "[hidden]|<convert>#label=663<convert>"
				);
      break;
      case "entryncave":
        $sql = "SELECT DISTINCT ";
        $sql .= "IF(V_cave_n_entry.Category = _latin1'Cave',CONCAT(V_cave_n_entry.Id,'*','0'),CONCAT('0','*',V_cave_n_entry.Id)) AS `0`, ";
        $sql .= "V_cave_n_entry.Name AS `1`, ";//Nom
        $sql .= "IF(V_cave_n_entry.Category = _latin1'Cave','<convert>#label=119<convert>','<convert>#label=625<convert>') AS `2`, ";//Catégorie
        $sql .= "T_country.".$_SESSION['language']."_name AS `3`, "; //Pays
        $sql .= "V_cave_n_entry.Region AS `4`, ";//Etat/Région
        $sql .= "V_cave_n_entry.City AS `5` ";//Commune
        $sql .= "FROM `".$_SESSION['Application_host']."`.`V_cave_n_entry` ";
        $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_country` ON T_country.Iso = V_cave_n_entry.Country ";
        $links = array();
				$columns_params = array(
					0 => "[hidden]|[hidden]Id",
					1 => "V_cave_n_entry*Name|<convert>#label=199<convert>",
					2 => "IF(V_cave_n_entry*Category=_latin1'Cave','<convert>#label=119<convert>','<convert>#label=625<convert>')|<convert>#label=235<convert>|<convert>#label=119<convert>;<convert>#label=625<convert>",
					3 => "V_cave_n_entry*Country|<convert>#label=98<convert>|SELECT Iso AS value,".$_SESSION['language']."_name AS text FROM ".$_SESSION['Application_host'].".T_country ORDER BY text",
					4 => "V_cave_n_entry*Region|<convert>#label=100<convert>",
					5 => "V_cave_n_entry*City|<convert>#label=101<convert>"
				);
      break;
      case "caver":
      case "filtercaver":
				$columns_params = array(
					0 => "[hidden]|[hidden]Id",
					1 => "T_caver*Nickname|<convert>#label=578<convert>",
					2 => "T_caver*Surname|<convert>#label=200<convert>",
					3 => "T_caver*Name|<convert>#label=199<convert>",
					5 => "T_caver*Country|<convert>#label=98<convert>|SELECT Iso AS value,".$_SESSION['language']."_name AS text FROM ".$_SESSION['Application_host'].".T_country ORDER BY text",
					11 => "T_grotto*Name|<convert>#label=386<convert>"
				);
        $sql = "SELECT DISTINCT ";
        $sql .= "T_caver.Id AS `0`, ";
        $sql .= "T_caver.Nickname AS `1`, ";//Pseudo
        $sql .= "T_caver.Surname AS `2`, ";//Prénom
        $sql .= "T_caver.Name AS `3`, ";//Nom
        if (USER_IS_CONNECTED) {
					$columns_params[4] = "IF(T_caver*Contact_is_public@in@(1,2),T_caver*Contact,NULL)|<convert>#label=146<convert>";
          $sql .= "IF(T_caver.Contact_is_public in (1,2),T_caver.Contact,NULL) AS `4`, ";//E-mail
        } else {
					$columns_params[4] = "IF(T_caver*Contact_is_public=1,T_caver*Contact,NULL)|<convert>#label=146<convert>";
          $sql .= "IF(T_caver.Contact_is_public in (2),T_caver.Contact,NULL) AS `4`, ";//E-mail
        }
        $sql .= "T_country.".$_SESSION['language']."_name AS `5`, "; //Pays
        if (USER_IS_CONNECTED) {
					$columns_params[6] = "IF(T_caver*Contact_is_public@in@(1,2),T_caver*Region,NULL)|<convert>#label=100<convert>";
          $sql .= "IF(T_caver.Contact_is_public in (1,2),T_caver.Region,NULL) AS `6`, ";//Etat/Région
        } else {
					$columns_params[6] = "IF(T_caver*Contact_is_public=1,T_caver*Region,NULL)|<convert>#label=100<convert>";
          $sql .= "IF(T_caver.Contact_is_public in (2),T_caver.Region,NULL) AS `6`, ";//Etat/Région
        }
        if (USER_IS_CONNECTED) {
					$columns_params[7] = "IF(T_caver*Contact_is_public@in@(1,2),T_caver*Postal_code,NULL)|<convert>#label=145<convert>[hidden]";
					$columns_params[8] = "IF(T_caver*Contact_is_public@in@(1,2),T_caver*City,NULL)|<convert>#label=101<convert>[hidden]";
					$columns_params[9] = "[hidden]|<convert>#label=145<convert> - <convert>#label=101<convert>";
					$columns_params[10] = "IF(T_caver*Contact_is_public@in@(1,2),T_caver*Address,NULL)|<convert>#label=102<convert>";
          $sql .= "T_caver.Postal_code AS `7`, ";//CP
          $sql .= "T_caver.City AS `8`, ";//Commune
          $sql .= "IF(T_caver.Contact_is_public in (1,2),CONCAT_WS(' - ',T_caver.Postal_code,T_caver.City),NULL) AS `9`, ";//CP - Commune
          $sql .= "IF(T_caver.Contact_is_public in (1,2),T_caver.Address,NULL) AS `10`, ";//Adresse
        } else {
					$columns_params[7] = "IF(T_caver*Contact_is_public=1,T_caver*Postal_code,NULL)|<convert>#label=145<convert>[hidden]";
					$columns_params[8] = "IF(T_caver*Contact_is_public=1,T_caver*City,NULL)|<convert>#label=101<convert>[hidden]";
					$columns_params[9] = "[hidden]|<convert>#label=145<convert> - <convert>#label=101<convert>";
					$columns_params[10] = "IF(T_caver*Contact_is_public=1,T_caver*Address,NULL)|<convert>#label=102<convert>";
          $sql .= "T_caver.Postal_code AS `7`, ";//CP
          $sql .= "T_caver.City AS `8`, ";//Commune
          $sql .= "IF(T_caver.Contact_is_public in (2),CONCAT_WS(' - ',T_caver.Postal_code,T_caver.City),NULL) AS `9`, ";//CP - Commune
          $sql .= "IF(T_caver.Contact_is_public in (2),T_caver.Address,NULL) AS `10`, ";//Adresse
        }
        $sql .= "GROUP_CONCAT(DISTINCT T_grotto.Name ORDER BY T_grotto.Name SEPARATOR ', ') AS `11` ";//Clubs
        $sql .= "FROM `".$_SESSION['Application_host']."`.`T_caver` ";
        $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_country` ON T_country.Iso = T_caver.Country ";
        $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_grotto_caver` ON J_grotto_caver.Id_caver = T_caver.Id ";
        $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`T_grotto` ON T_grotto.Id = J_grotto_caver.Id_grotto ";
        $sql .= "GROUP BY T_caver.Id ";
        $links = array (
                4 => array(
                    'conditions' =>  array(),
                    'parameters' => array(
                                    '<email>' => 4),
                    'link' => "mailto:<email>",
                    'target' => ''));
      break;
      case "group":
        $sql = "SELECT DISTINCT ";
        $sql .= "T_group.Id AS `0`, ";
        $sql .= "T_group.Name AS `1`, ";//Nom du groupe
        $sql .= "COUNT(J_caver_group.Id_caver) AS `2` ";//Nombre d'utilisateurs
        $sql .= "FROM `".$_SESSION['Application_host']."`.`T_group` ";
        $sql .= "LEFT OUTER JOIN `".$_SESSION['Application_host']."`.`J_caver_group` ON J_caver_group.Id_group = T_group.Id ";
        $sql .= "GROUP BY T_group.Id ";
        $links = array ();
				$columns_params = array(
					0 => "[hidden]|[hidden]Id",
					1 => "T_group*Name|<convert>#label=706<convert>",
					2 => "[hidden]|<convert>#label=707<convert>"
				);
      break;
      case "right":
        $sql = "SELECT DISTINCT ";
        $sql .= "T_right.Id AS `0`, ";
        $sql .= "T_right.Name AS `1` ";//Nom du droit
        $sql .= "FROM `".$_SESSION['Application_host']."`.`T_right` ";
        $links = array ();
				$columns_params = array(
					0 => "[hidden]|[hidden]Id",
					1 => "T_right*Name|<convert>#label=702<convert>"
				);
      break;
		  default:
		    exit();
		  break;
    }
?>

<?php
    if ($type == "filter") {
      $input_type = array(
              'type' => '',
              'conditions' => array());
    } else {
      $input_type = array(
              'type' => 'checkbox',
              'conditions' => array());
    }
    $length_page = 10;
    $records_by_page_array = array(5, 10, 15, 20, 25, 30, 40, 50);
    $records_by_page = (isset($_POST['records_by_page'])) ? $_POST['records_by_page'] : 15;
    $filter_form = "automatic_form";
    $list_form = "result_form";
    $style = array();
    $default_order = 2;
    $result = getRowsFromSQL($sql, $columns_params, $links, $records_by_page, $filter_form, $list_form, $_POST, $input_type, $style, $default_order, true, true, "");
    $resource_id = $result['resource_id'];
    $filter_fields = getFilterFields($sql,$columns_params,$_POST, $filter_form,"<convert>#label=542<convert>", false, $resource_id);//Tous
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
    <?php echo getMetaTags(); ?>
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=619<convert></title>
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
    <link rel="stylesheet" type="text/css" href="../css/portlet.css" />
<?php if ($type == "filter") { ?>
    <link rel="stylesheet" type="text/css" href="../css/tab.css" />
    <!--[if IE]><link rel="stylesheet" type="text/css" href="../css/tab_ie.css" /><![endif]-->
    <!--[if lte IE 6]><link rel="stylesheet" type="text/css" href="../css/tab_ie6.css" /><![endif]-->
<?php } ?>
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
      
    function load() {
      mySite.setSessionTimer("<?php echo USER_IS_CONNECTED; ?>");
<?php if ($reload_overview) { ?>
      window.opener.visibilityOnClick("advanced", true);
      //window.opener.focus();
<?php } ?>
<?php if ($close_window) { ?>
      //window.close();
      self.close();
<?php } ?>
    }
    
    function unload() {
    }
    
    function saveOnClick(oForm, bClose) {
<?php if ($callback_function != "") { ?>
      window.opener.<?php echo $callback_function; ?>(oForm);
<?php } ?>
      if (bClose) {
        closeMe();
      } else {
        uncheckAll(oForm);
      }
    }
    
    function filterOnClick() {
      
    }
    
    function closeMe() {
      //window.opener.focus();
      //window.close();
      self.close();
    }
      
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body onload="JavaScript:load();" onunload="JavaScript:unload();" >
    <?php echo getTopFrame(false); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
<?php if ($type == "filter") {
        if ($callback_function != "") {
          $callback_argument = "&amp;callback=".$callback_function;
        } else {
          $callback_argument = "";
        }
?>
		<div class="tab">
		  <ul>
			  <li <?php if($category == "entry") { ?>id="actif"<?php } ?>><?php if($category != "entry") { ?><a href="?type=<?php echo $type; ?><?php echo $callback_argument; ?>&amp;category=entry"><?php } ?><span><img src="../images/icons/entry2.png" alt="" style="height:9pt;margin:0px 3px;border:0px none;" /> <convert>#label=48<convert><!--Cavités--></span><?php if($category != "entry") { ?></a><?php } ?></li>
        <li <?php if($category == "grotto") { ?>id="actif"<?php } ?>><?php if($category != "grotto") { ?><a href="?type=<?php echo $type; ?><?php echo $callback_argument; ?>&amp;category=grotto"><?php } ?><span><img src="../images/icons/grotto1.png" alt="" style="height:9pt;border:0px none;" /> <convert>#label=386<convert><!--Clubs--></span><?php if($category != "grotto") { ?></a><?php } ?></li>
        <li <?php if($category == "caver") { ?>id="actif"<?php } ?>><?php if($category != "caver") { ?><a href="?type=<?php echo $type; ?><?php echo $callback_argument; ?>&amp;category=caver"><?php } ?><span><img src="../images/icons/caver2.png" alt="" style="height:9pt;border:0px none;" /> <convert>#label=385<convert><!--Spéléos--></span><?php if($category != "caver") { ?></a><?php } ?></li>
      </ul>
    </div>
<?php } ?>
    <div>
      <form id="<?php echo $filter_form; ?>" name="<?php echo $filter_form; ?>" method="post" action="">
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
<?php if ($type == "filter") { ?>
        <input type="submit" name="overview_filter" class="button1" value="<convert>#label=646<convert>" /><!--Utiliser ces critères-->
<?php } ?>
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
        <div>
<?php if ($type != "filter") { ?>
          <input class="button1" type="button" id="save" name="save" onclick="JavaScript:saveOnClick(this.form, true);" value="<convert>#label=360<convert>" /><!--Ajouter-->
          <input class="button1" type="button" id="continue" name="continue" onclick="JavaScript:saveOnClick(this.form, false);" value="<convert>#label=620<convert>" /><!--Ajouter et poursuivre la sélection-->
<?php } ?>
          <input class="button1" type="button" id="close" name="close" onclick="JavaScript:closeMe();" value="<convert>#label=371<convert>" /><!--Fermer-->
        </div>
      </form>
    </div>
    <?php echo getBotFrame(false); ?>
<?php
    $virtual_page = "pick_window/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>