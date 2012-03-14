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
include("../conf/config.php");
include("../func/function.php");
include("declaration.php");
if (!USER_IS_CONNECTED) {
  exit();
}
$frame = "function";
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
    <script type="text/javascript" src="<?php echo getScriptJS(__FILE__); ?>"></script>
    <script type="text/javascript" src="../scripts/grottoChat.js"></script>
    <?php echo getMetaTags(); ?>
    <title><?php echo $_SESSION['Application_title']; ?> <convert>#label=727<convert></title>
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/grottochat.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
    //gona need getKeyCode, getTargetNode, xtdGetElementById and disableField functions.
    var grottochat = new grottochat("chatusers", "chatdialogs", "chatmessage", "chatbutton", "<?php echo $_SESSION['user_nickname']; ?>", 1, 5000, "webservices/grottoChat.php?lang=<?php echo $_SESSION['language']; ?>");
    <?php echo getCDataTag(false); ?>
    </script>
  </head>
  <body style="height:0;">
    <?php echo getTopFrame(false); ?>
    <?php echo getNoScript("<convert>#label=22<convert>","<convert>#label=23<convert>"); ?>
    <div class="grottochat">
      <div id="chatdialogs" class="chatdialogs" onscroll="JavaScript:setScrollStatus(event);">
      </div>
      <div id="chatusers" class="chatusers">
      </div>	
      <div class="chatinputs">
        <form id="grottochatform" name="grottochatform" method="post" action="" onsubmit="JavaScript:return false;">
          <input class="input1" style="width:310px;" type="text" id="chatmessage" name="chatmessage" value=""/>
          <input class="button1" type="button" id="chatbutton" name="chatbutton" value="OK" onclick="JavaScript:grottochat.send();"/> <input class="button1" type="button" id="close" name="close" onclick="JavaScript:self.close();" value="<convert>#label=885<convert>" /><!--Quitter le chat-->
        </form>
      </div>	
      <div id="link">	
      </div>
    </div>
    <?php echo getBotFrame(); ?>
    <script type="text/javascript">
    <?php echo getCDataTag(true); ?>
    grottochat.refresh();
    <?php echo getCDataTag(false); ?>
    </script>
<?php
    $virtual_page = "grottoChat/".$_SESSION['language'];
    include_once "../func/suivianalytics.php";
?>
  </body>
</html>
