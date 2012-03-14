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
$forum = (isset($_GET['f'])) ? $_GET['f'] : 0;
$topic = (isset($_GET['t'])) ? $_GET['t'] : 0;
$phpbb_root_path = '../phpBB3/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
?>
<?php echo getDoctype(false)."\n"; ?>
<html <?php echo getHTMLTagContent(); ?>>
  <head>
<?php
  	include("application_".$_SESSION['language'].".php");
  	include("mailfunctions_".$_SESSION['language'].".php");
?>
    <?php echo getMetaTags(); ?>
    <title><?php echo $_SESSION['Application_title']?> <convert>#label=23<convert></title>
    <link rel="stylesheet" type="text/css" href="../css/global.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_p.css" media="print" />
  </head>
  <body>
<?php
	if (!is_dir($phpbb_root_path)) {
		echo "<iframe src=\"http://www.grottocenter.org/html/help_".$_SESSION['language'].".php?f=".$forum."&t=".$topic."\" frameborder=\"no\" width=\"100%\" height=\"100%\" scrolling=\"auto\"></iframe>";
		//include("http://www.grottocenter.org/html/help_".$_SESSION['language'].".php?f=".$forum."&t=".$topic);
	} else {
    $posts = getForumPosts($forum, $topic);
    for($i=0;$i<$posts['Count'];$i++) {
      echo getTopFrame(false);
      echo "<h3>".$posts[$i]['subject']."</h3>";
      echo $posts[$i]['text'];
      echo "<br/><br/><div class=\"credit\"><convert>#label=734<convert> ".date("l j F Y H:i:s T", $posts[$i]['date'])."<br/>";
      echo "<a href=\"../phpBB3/viewtopic.php?f=".$forum."&amp;t=".$topic."\" target=\"_blank\"><convert>#label=737<convert><!--Voir ce topic sur le forum-->.</a></div>";
      echo getBotFrame(false);
    }
	}
?>
  </body>
</html>
