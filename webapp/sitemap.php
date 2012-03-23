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
 * @copyright Copyright (c) 2009-2012 Clément Ronzon
 * @license http://www.gnu.org/licenses/agpl.txt
 */
$doListFiles = false;
include("conf/config.php");
include("func/function.php");
$_GET['check_lang_auto'] = "False";
include("html/declaration.php");
$app_prop = appProp();
$root = $app_prop['Url'];
header("Content-type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?".">"."\n";
?>
<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">
  <url>
    <loc><?php echo $root; ?>/index.php?lang=Fr</loc>
    <changefreq>monthly</changefreq>
    <priority>1</priority>
  </url>
  <url>
    <loc><?php echo $root; ?>/index.php?lang=En</loc>
    <changefreq>monthly</changefreq>
    <priority>1</priority>
  </url>
  <url>
    <loc><?php echo $root; ?>/index.php?lang=Es</loc>
    <changefreq>monthly</changefreq>
    <priority>1</priority>
  </url>
  <url>
    <loc><?php echo $root; ?>/index.php?lang=De</loc>
    <changefreq>monthly</changefreq>
    <priority>1</priority>
  </url>
  <url>
    <loc><?php echo $root; ?>/html/communication2.1.0_En.html</loc>
    <changefreq>monthly</changefreq>
    <priority>1</priority>
  </url>
  <url>
    <loc><?php echo $root; ?>/html/communication2.1.0_Es.html</loc>
    <changefreq>monthly</changefreq>
    <priority>1</priority>
  </url>
  <url>
    <loc><?php echo $root; ?>/html/communication2.1.0_Fr.html</loc>
    <changefreq>monthly</changefreq>
    <priority>1</priority>
  </url>
  <url>
    <loc><?php echo $root; ?>/html/communication2.1.0_De.html</loc>
    <changefreq>monthly</changefreq>
    <priority>1</priority>
  </url>
  <url>
    <loc><?php echo $root; ?>/html/legal_and_privacy_En.php?lang=En</loc>
    <changefreq>monthly</changefreq>
    <priority>0.1</priority>
  </url>
  <url>
    <loc><?php echo $root; ?>/html/legal_and_privacy_Es.php?lang=Es</loc>
    <changefreq>monthly</changefreq>
    <priority>0.1</priority>
  </url>
  <url>
    <loc><?php echo $root; ?>/html/legal_and_privacy_Fr.php?lang=Fr</loc>
    <changefreq>monthly</changefreq>
    <priority>0.1</priority>
  </url>
  <url>
    <loc><?php echo $root; ?>/html/legal_and_privacy_De.php?lang=De</loc>
    <changefreq>monthly</changefreq>
    <priority>0.1</priority>
  </url>
  <url>
    <loc><?php echo $root; ?>/phpBB3/</loc>
    <changefreq>monthly</changefreq>
    <priority>0.1</priority>
  </url>
<?php
if ($doListFiles) {
  $sql = "SELECT Id, Name FROM `".$app_prop['Host']."`.`T_entry` WHERE Is_public = 'YES' ORDER BY Name ";
  $data = getDataFromSQL($sql, __FILE__, "function", __FUNCTION__);
  $languagesArray = getAvailableLanguages();
  for ($i=0;$i<$data['Count'];$i++) {
    $changefreq = "monthly";
    $priority = 0.5;
    foreach($languagesArray as $shortLang => $largeLang) {
      $entry = $data[$i]['Name'];
      $entry = preg_replace('/([^\(\)]*)(\(.*\))/', '$2 $1', $entry);
      $entry = cleanString($entry);
      $loc = $root."/?lang=".$shortLang."&amp;entryId=".$data[$i]['Id']."&amp;entry=".urlencode($entry);
?>
  <url>
    <loc><?php echo $loc; ?></loc>
    <changefreq><?php echo $changefreq; ?></changefreq>
    <priority><?php echo $priority; ?></priority>
  </url>
<?php
    }
  }
}
?>
</urlset>
