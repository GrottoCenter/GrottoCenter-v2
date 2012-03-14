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
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=316<convert></title>
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
<?php
    $frame = (isset($_GET['frame'])) ? $_GET['frame'] : '';
?>
    <script type="text/javascript">
    <?php include("../scripts/events.js"); ?>
    </script>
  </head>
  <body>
    <?php echo getTopFrame(); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
  	<?php if ($frame != "function") { echo getCloseBtn($frame."_".$_SESSION['language'].".php","<convert>#label=371<convert>"); } ?><!-- Fermer -->
  	<div class="frame_title"><?php echo setTitle("error_".$_SESSION['language'].".php?frame=".$frame, $frame, "<convert>#label=80<convert>", 2); ?></div><!--Erreur-->
  	<div class="error"><?php echo getTopBubble(); ?>
      <p><convert>#label=317<convert><!--Une erreur est survenue, un e-mail a été envoyé au webmasteur de--> <?php echo $_SESSION['Application_name']; ?>.</p>
      <p><convert>#label=318<convert><!--Veuillez retenter cette action plus tard.--></p>
      <p><convert>#label=319<convert><!--L'équipe de--> <?php echo $_SESSION['Application_name']; ?> <convert>#label=320<convert><!--s'excuse pour le désagrément occasionné.--></p>
    <?php echo getBotBubble(); ?></div>
    <?php echo getBotFrame(); ?>
<?php
    $virtual_page = "error/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>