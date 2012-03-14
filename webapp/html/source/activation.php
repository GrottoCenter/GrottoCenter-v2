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
$frame = "filter";
include("application_".$_SESSION['language'].".php");
include("mailfunctions_".$_SESSION['language'].".php");
$type = (isset($_GET['type'])) ? $_GET['type'] : 'manual';
$activated = false;
$manu_err = false;
$id = "";
$login = "";
$code = "";
$helpId = array("activation" => 18);
if (isset($_POST['activate']) || $type == "auto") {
  if ($type == "auto") {
    $id = (isset($_GET['i'])) ? $_GET['i'] : '';
    $code = (isset($_GET['c'])) ? $_GET['c'] : '';
  } else {
    $code = (isset($_POST['a_code'])) ? $_POST['a_code'] : '';
    $login = (isset($_POST['a_login'])) ? $_POST['a_login'] : '';
    $sql = "SELECT Id FROM T_caver WHERE Login = ".returnDefault($login, 'text');
    $data = getDataFromSQL($sql, __FILE__, $frame, __FUNCTION__);
    $id = $data[0]['Id'];
    if ($id == "") {
      $manu_err = true;
    }
  }
  if (!$manu_err) {
    $activated = activateAccount($id, $code);
  }
}
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
    <?php echo getMetaTags(); ?>
    <!-- version IE //-->
	  <link rel="shortcut icon" type="image/x-icon" href="<?php echo $_SESSION['Application_url']; ?>/favicon.ico" />
	  <!-- version standart //-->
	  <link rel="shortcut icon" type="image/png" href="<?php echo $_SESSION['Application_url']; ?>/favicon.png" />
    <title><?php echo $_SESSION['Application_title']; ?> <?php echo $entryName; ?></title>
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
		//Gona need those functions: openWindow
		
    function activateSubmit(event) {
    
    }
    <?php echo getCDataTag(false); ?>
    </script>
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
  </head>
  <body>
    <?php echo getTopFrame(false); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
<?php if ((isset($_POST['activate']) || $type == "auto") && !$manu_err) {
        if ($activated) { ?>
    <div class="warning"><?php echo getTopBubble(); ?><convert>#label=190<convert><?php echo getBotBubble(); ?></div>
<?php   } else { ?>
    <div class="error"><?php echo getTopBubble(); ?><convert>#label=191<convert><?php echo getBotBubble(); ?></div>
<?php   }
      }
      if ($manu_err) {
?>
      <div class="error"><?php echo getTopBubble(); ?><convert>#label=780<convert><!--Erreur de saisie, veuiller essayer à nouveau.--><?php echo getBotBubble(); ?></div>
<?php
      }
      if (!$activated) {
?>
    <!--Activation form-->
    <form id="activate_account" name="activate_account" method="post" action="" onsubmit="JavaScript:activateSubmit(event);">
      <table border="0" cellspacing="1" cellpadding="0" class="form_tbl">
        <tr><th colspan="2"><?php echo getHelpTopic($helpId['activation'], "<convert>#label=23<convert>"); ?></th></tr>
        <tr><td width="170" class="label">
  				<label for="a_login">
					  <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=192<convert><!--Pseudonyme-->
  				</label>
  			</td><td class="field">
				  <input class="input1" type="text" name="a_login" id="a_login" value="<?php echo stripslashes($login); ?>" size="10" maxlength="20" />
				</td></tr><tr><td width="170" class="label">
  				<label for="a_code">
					  <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=778<convert><!--Code d'activation-->
					</label>
				</td><td class="field">
          <input class="input1" type="text" name="a_code" id="a_code" value="<?php echo $code; ?>" size="10" maxlength="32" />
        </td></tr><tr><td width="170" class="label">
		      <label for="connection">
	    		</label>
	    	</td><td class="field">
          <input class="button1" type="submit" name="activate" id="activate" value="<convert>#label=779<convert>" /><!--Activer-->
        </td></tr><tr><td colspan="2">
          <div class="notes">
            <?php echo getTopBubble(); ?>
            <img src="../images/icons/FlagRequired.gif" alt="*" /><convert>#label=78<convert><!--Champs nécessaires.--><br />
            <?php echo getBotBubble(); ?>
          </div>
        </td></tr>
      </table>
    </form>
<?php } ?>
    <div style="text-align:center;margin:10px;"><a href="<?php echo $_SESSION['Application_url']; ?>/index.php?lang=<?php echo $_SESSION['language']; ?>"><?php echo $_SESSION['Application_name']; ?></a></div>
    <?php echo getBotFrame(false); ?>
<?php
$virtual_page = $frame."/".$_SESSION['language'];
include_once "../func/suivianalytics.php";
?>
  </body>
</html>