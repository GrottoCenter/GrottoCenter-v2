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
if (strpos($_SERVER["PHP_SELF"], 'banner_') !== false) { //'index.php') !== false) {
?>
<script type="text/javascript">
//<![CDATA[
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
//]]>
</script>
<script type="text/javascript">
//<![CDATA[
try{
var my_key = "<?php echo Analytics_key; ?>";
var pageTracker = _gat._getTracker(my_key);
pageTracker._trackPageview("/<?php echo $virtual_page; ?>");
} catch(err) {}
//]]>
</script>
<?php
// End Analytics tracking code
}
?>
<?php /*
<script type="text/javascript">
//<![CDATA[
var my_key = "<?php echo Analytics_key; ?>";
var pageTracker = _gat._getTracker(my_key);
pageTracker._initData();
pageTracker._trackPageview("/<?php echo $virtual_page; ?>");
//]]>
</script>
*/ ?>