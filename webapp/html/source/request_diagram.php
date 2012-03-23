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
$diag_new_inst = (isset($_GET['diag_status']));
if ($diag_new_inst) {
  include("../conf/config.php");
  include("../func/function.php");
  include("declaration.php");
  $frame = "filter";
}
if (!allowAccess(request_view_mine)) {
  exit();
}
if ($diag_new_inst) {
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
    <script type="text/javascript" charset="UTF-8" src="<?php echo getScriptJS(__FILE__); ?>"></script>
<?php
  	include("application_".$_SESSION['language'].".php");
  	include("mailfunctions_".$_SESSION['language'].".php");
?>
    <?php echo getMetaTags(); ?>
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=863<convert></title><!--Diagramme du processus-->
    <link rel="stylesheet" type="text/css" href="../css/request.css" />
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
<?php
  $diag_status = (isset($_GET['diag_status'])) ? $_GET['diag_status'] : '';
}
$diag_array = array('new' => array(0),
                    'draft' => array(2),
                    'submitted' => array(3,5,6,7),
                    'rejected' => array(1),
                    'approved' => array(8,9,10),
                    'canceled' => array(4,5,6,7));
$diag_status_array = array('in','in','in','in','in','in','in','in','in','in','in');
for ($i=0; $i<count($diag_array[$diag_status]); $i++) {
  $diag_status_array[$diag_array[$diag_status][$i]] = '';
}
if ($diag_new_inst) {
?>
  </head>
  <body>
    <?php echo getTopFrame(false); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
    <div class="frame_title"><?php echo setTitle("#", "2ndpopup", "<convert>#label=864<convert>", 1); ?></div><!--Ce diagramme explique l'évolution du processus de demande.<br />Les étapes en surbrillance reflêtent l'état actuel de cette demande.-->
<?php } ?>
    <div style="height:491px;">
<?php if (!$diag_new_inst) { ?>
      <div><convert>#label=864<convert></div><!--Ce diagramme explique l'évolution du processus de demande.<br />Les étapes en surbrillance reflêtent l'état actuel de cette demande.-->
<?php } ?>
      <img src="../images/requests/WF_form_<?php echo $diag_status; ?>.png" alt="diagram" width="409px" />
      <div class="diagcell diagcell<?php echo $diag_status_array[0]; ?>active" style="height:53px;left:151px;top:-452px;"><convert>#label=865<convert></div><!--Formulaire vierge-->
      <div class="diagcell diagcell<?php echo $diag_status_array[1]; ?>active" style="height:53px;left:20px;top:-429px;"><convert>#label=866<convert></div><!--Demande refusée-->
      <div class="diagcell diagcell<?php echo $diag_status_array[2]; ?>active" style="height:53px;left:151px;top:-482px;"><convert>#label=867<convert></div><!--Brouillon-->
      <div class="diagcell diagcell<?php echo $diag_status_array[3]; ?>active" style="height:53px;left:151px;top:-459px;"><convert>#label=868<convert></div><!--Demande en attente de validation-->
      <div class="diagcell diagcell<?php echo $diag_status_array[4]; ?>active" style="height:53px;left:282px;top:-512px;"><convert>#label=869<convert></div><!--Topographie signalée comme illicite-->
      <div class="diagcell diagcell<?php echo $diag_status_array[5]; ?>active" style="height:15px;left:88px;top:-476px;"><convert>#label=627<convert></div><!--Non-->
      <div class="diagcell diagcell<?php echo $diag_status_array[6]; ?>active" style="height:15px;left:151px;top:-484px;"><convert>#label=870<convert></div><!--Validée ?-->
      <div class="diagcell diagcell<?php echo $diag_status_array[7]; ?>active" style="height:15px;left:168px;top:-457px;"><convert>#label=626<convert></div><!--Oui-->
      <div class="diagcell diagcell<?php echo $diag_status_array[8]; ?>active" style="height:53px;left:151px;top:-445px;"><convert>#label=871<convert></div><!--Demande validée / en ligne-->
      <div class="diagcell diagcell<?php echo $diag_status_array[9]; ?>active" style="height:15px;left:151px;top:-403px;"><convert>#label=872<convert></div><!--Illicite ?-->
      <div class="diagcell diagcell<?php echo $diag_status_array[10]; ?>active" style="height:15px;left:216px;top:-425px;"><convert>#label=626<convert></div><!--Oui-->
    </div>
<?php if ($diag_new_inst) { ?>
    <?php echo getBotFrame(false); ?>
<?php
    $virtual_page = "request_diagram/".$diag_status."/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>
<?php } ?>