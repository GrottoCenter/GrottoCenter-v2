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
ob_start("ob_gzhandler");
header ("Content-type: image/png");
//Global parameters
$fgColorHex = (isset($_GET['fgc'])) ? $_GET['fgc'] : 'ffffff';
$bgColorHex = "ffffff";
$width = 4;
$height = 4;
$bgRed = hexdec(substr($bgColorHex, 0, 2));
$bgGreen = hexdec(substr($bgColorHex, 2, 2));
$bgBlue = hexdec(substr($bgColorHex, -2));
$fgRed = hexdec(substr($fgColorHex, 0, 2));
$fgGreen = hexdec(substr($fgColorHex, 2, 2));
$fgBlue = hexdec(substr($fgColorHex, -2));
//Create the image
$image = ImageCreate($width*2, $height*2) or die ("Error creating picture");
//Set bg color
$bgColor = ImageColorAllocate($image, $bgRed, $bgGreen, $bgBlue);
//Transparency
ImageColorTransparent($image, $bgColor);
//Set of colors
$fgColorAlpha = array();
$fgColorAlpha['y'] = imagecolorallocatealpha($image, $fgRed, $fgGreen, $fgBlue, 0);
$fgColorAlpha['x'] = imagecolorallocatealpha($image, $fgRed, $fgGreen, $fgBlue, 127);
$fgColorAlpha[5] = imagecolorallocatealpha($image, $fgRed, $fgGreen, $fgBlue, 5);
$fgColorAlpha[6] = imagecolorallocatealpha($image, $fgRed, $fgGreen, $fgBlue, 6);
$fgColorAlpha[8] = imagecolorallocatealpha($image, $fgRed, $fgGreen, $fgBlue, 8);
$fgColorAlpha[12] = imagecolorallocatealpha($image, $fgRed, $fgGreen, $fgBlue, 12);
$fgColorAlpha[33] = imagecolorallocatealpha($image, $fgRed, $fgGreen, $fgBlue, 33);
$fgColorAlpha[34] = imagecolorallocatealpha($image, $fgRed, $fgGreen, $fgBlue, 34);
$fgColorAlpha[37] = imagecolorallocatealpha($image, $fgRed, $fgGreen, $fgBlue, 37);
$fgColorAlpha[38] = imagecolorallocatealpha($image, $fgRed, $fgGreen, $fgBlue, 38);
$fgColorAlpha[46] = imagecolorallocatealpha($image, $fgRed, $fgGreen, $fgBlue, 46);
$fgColorAlpha[107] = imagecolorallocatealpha($image, $fgRed, $fgGreen, $fgBlue, 107);
$fgColorAlpha[111] = imagecolorallocatealpha($image, $fgRed, $fgGreen, $fgBlue, 111);
$fgColorAlpha[117] = imagecolorallocatealpha($image, $fgRed, $fgGreen, $fgBlue, 117);
//Pixel matrix
$matrix = array(array('x', 111, 37, 'y', 'y', 37, 111, 'x'),
                array(107, 5, 'y', 'y', 'y', 'y', 5, 107),
                array(33, 'y', 'y', 'y', 'y', 'y', 'y', 33),
                array('y', 'y', 'y', 'y', 'y', 'y', 'y', 'y'),
                array('y', 'y', 'y', 'y', 'y', 'y', 'y', 'y'),
                array(33, 'y', 'y', 'y', 'y', 'y', 'y', 33),
                array(107, 5, 'y', 'y', 'y', 'y', 5, 107),
                array('x', 111, 37, 'y', 'y', 37, 111, 'x'));
//Plot points
for($y=0; $y<($height*2); $y++) {
  for($x=0; $x<($width*2); $x++) {
    imagesetpixel($image, $x, $y, $fgColorAlpha[$matrix[$y][$x]]);
  }
}
//Return de picture
imagepng($image);
imagedestroy($image);
?>
