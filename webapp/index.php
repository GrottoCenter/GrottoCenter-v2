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
 * @copyright Copyright (c) 2009-1912 Clément Ronzon
 * @license http://www.gnu.org/licenses/agpl.txt
 */
include("conf/config.php");
include("func/function.php");
include("html/declaration.php");
$refreshCache = false;
if (allowAccess(cache_refresh_all)) {
  $refreshCache = (isset($_GET['refreshCache'])) ? ($_GET['refreshCache'] == "True") : false;
}
if ($refreshCache) {
  if (file_exists("func/genScriptJS.php")) {
		include("func/genScriptJS.php");
	}
  resetConvertedFiles($ConvertedFilesArray);
  $langArray = getAvailableLanguages();
  foreach ($langArray as $shortLang => $largeLang) {
  	convertFiles($shortLang, $ConvertedFilesArray);
  }
  if ($_SESSION['Application_host'] == 'clementronzon') {
    //Refresh the JS cache
    refreshJSCache();
  }
} else {
  convertFiles($_SESSION['language'], $ConvertedFilesArray);
}
$idComplement = (isset($_GET['entry']) && isset($_GET['entryId'])) ? $_GET['entryId'] : '';
if ($idComplement != "") {
  $sql = "SELECT Name FROM `".$_SESSION['Application_host']."`.`T_entry` WHERE Is_public = 'YES' AND Id = ".$idComplement." ";
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  $titleComplement = ($data[0]['Name'] != '') ? ' - '.$data[0]['Name']: '';
}
?>
<?php echo getDoctype(true)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
	<head>
<?php
		include("html/application_".$_SESSION['language'].".php");
		include("html/mailfunctions_".$_SESSION['language'].".php");
?>
    <?php echo getMetaTags(); ?>
    <!-- RSS Flow -->
    <link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php echo $_SESSION['Application_url']; ?>/rss_<?php echo $_SESSION['language']; ?>.xml" />
	  <!-- version IE //-->
	  <link rel="shortcut icon" type="image/x-icon" href="<?php echo $_SESSION['Application_url']; ?>/favicon.ico" />
	  <!-- version standart //-->
	  <link rel="shortcut icon" type="image/png" href="<?php echo $_SESSION['Application_url']; ?>/favicon.png" />
	  <title><?php echo $_SESSION['Application_title'].$titleComplement; ?></title>
    <?php
    $virtual_page = "index/".$_SESSION['language'];
    include_once "func/suivianalytics.php";
    ?>
	</head>
	<frameset rows="100%" cols="100%" id="master">
		<frame src="html/site.php" id="site" name="site" scrolling="auto" marginheight="0" marginwidth="0" frameborder="0" noresize="noresize" />
		<noframes>
			<body>
				<p><?php echo $_SESSION['Application_noframe']; ?></p>
        <?php
        $virtual_page = "index/noframes/".$_SESSION['language'];
        include_once "func/suivianalytics.php";
        ?>
			</body>
		</noframes>
	</frameset>
</html>