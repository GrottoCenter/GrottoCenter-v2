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
ob_start("ob_gzhandler");
//Header
header ("Content-type: image/png");
//Global parameters
$type = (isset($_GET['type'])) ? $_GET['type'] : '';
if ($type == "radar") {
  $width = 150;
  $height = 150;
}
if ($type == "histo") {
  $width = 200;
  $height = 200;
}
$myData = (isset($_GET['data'])) ? $_GET['data'] : '';
$myLabel = (isset($_GET['label'])) ? $_GET['label'] : '';
$mainSeparator = "|";
$data = explode($mainSeparator,$myData);
$label = explode($mainSeparator,$myLabel);
$num_points = count($data);
//Create the image
$im = ImageCreate($width, $height)
        or die ("Error creating picture");
//Set bg color
$white = ImageColorAllocate($im,255,255,255);
//Transparent
ImageColorTransparent($im, $white);
//Set of colors
$black = ImageColorAllocate($im,0,0,0);
$orange = ImageColorAllocate($im,255,167,13);
$blue = ImageColorAllocate($im,34,55,138);
$grey = ImageColorAllocate($im,226,226,226);

if ($type == "radar") {
  //Compute the angles
  $angles = array();
  $single_angle = 360/$num_points;
  for($i=0;$i<$num_points;$i++){
    $angles[$i] = $i * $single_angle;
  }
  $text_size = 1;
  //Compute the pic's center and the radius
  $x_center = $width/2;
  $y_center = $height/2;
  $radius = $width/2 - 30;
  $div_length = 10;
  $subdiv_length = 6;
  
  //Plot the data
  $points = array();
  for($i=0;$i<$num_points;$i++){
    //Compute the point
    $points[$i*2] = $x_center + $data[$i]*$radius/10*cos(deg2rad($angles[$i]));
    $points[$i*2+1] = $y_center - $data[$i]*$radius/10*sin(deg2rad($angles[$i]));
  }
  ImageFilledPolygon($im, $points, $num_points, $orange);
  ImagePolygon($im, $points, $num_points, $blue);
  
  //Plot the sub-areas
  $suba_nb = 4;
  for ($i=0;$i<$suba_nb;$i++) {
    for($j=0;$j<$num_points;$j++){
      //Compute the point
      $points[$j*2] = $x_center + $radius/$suba_nb*($i+1)*cos(deg2rad($angles[$j]));
      $points[$j*2+1] = $y_center - $radius/$suba_nb*($i+1)*sin(deg2rad($angles[$j]));
    }
    ImagePolygon($im, $points, $num_points, $grey);
  }
  
  //Plot the axis
  for($i=0;$i<$num_points;$i++){
    //Axis
    $x = $x_center + $radius*cos(deg2rad($angles[$i]));
    $y = $y_center - $radius*sin(deg2rad($angles[$i]));
    ImageLine($im, $x_center, $y_center, $x, $y, $black);
    //Division
    $x1 = $x - $div_length/2*sin(deg2rad($angles[$i]));
    $x2 = $x + $div_length/2*sin(deg2rad($angles[$i]));
    $y1 = $y - $div_length/2*cos(deg2rad($angles[$i]));
    $y2 = $y + $div_length/2*cos(deg2rad($angles[$i]));
    ImageLine($im, $x1, $y1, $x2, $y2, $black);
    //Write the value & label
    $offset_center = -6;
    $offset_radius = 10;
    $x = $x_center + $offset_center + ($radius + $offset_radius)*cos(deg2rad($angles[$i]));
    $y = $y_center + $offset_center - ($radius + $offset_radius)*sin(deg2rad($angles[$i]));
    ImageString($im, $text_size, $x, $y, $label[$i]."=".round($data[$i],1), $black);
    //Subdivision
    $x = $x_center + $radius/2*cos(deg2rad($angles[$i]));
    $y = $y_center - $radius/2*sin(deg2rad($angles[$i]));
    $x1 = $x - $subdiv_length/2*sin(deg2rad($angles[$i]));
    $x2 = $x + $subdiv_length/2*sin(deg2rad($angles[$i]));
    $y1 = $y - $subdiv_length/2*cos(deg2rad($angles[$i]));
    $y2 = $y + $subdiv_length/2*cos(deg2rad($angles[$i]));
    ImageLine($im, $x1, $y1, $x2, $y2, $black);
  }
}

if($type == "histo") {
  //Local parameters
  $xmax = 10;
  if(max($data)>=10){
    $ymax = max($data);
  } else {
    $ymax = 10;
  }
  $ystep = round($ymax/10,1);
  $xpadding = 40;
  $xoffset = 30;
  $ypadding = 20;
  $xspare = ($width-2*$xpadding)/($xmax+2);
  $yspare = ($height-2*$ypadding)/($ymax+$ystep);
  $DataWidth = 1/2*$xspare;
  //Plot the data
  for($x=0;$x<=$xmax;$x++) {
    $DataHeight = $data[$x]*$yspare+$ypadding;
    ImageFilledRectangle ($im, ($x+1)*$xspare+$xpadding, $height-$DataHeight, ($x+1)*$xspare+$xpadding+$DataWidth, $height-$ypadding, $orange);
    ImageRectangle ($im, ($x+1)*$xspare+$xpadding, $height-$DataHeight, ($x+1)*$xspare+$xpadding+$DataWidth, $height-$ypadding, $blue);
    //ImageString ($im, 0, $x*$xspare-$DataWidth/2, $height-$DataHeight-10, $visites[$mois-1], $noir);
  }
  //Horizontal axis  
  ImageLine($im, $xpadding, $height-$ypadding, $width-$xpadding, $height-$ypadding, $black);
  //Horizontal subdiv
  for($x=0;$x<=$xmax;$x++) {
    ImageString ($im, 3, ($x+1)*$xspare+$xpadding, $height-$ypadding, $x, $black);
  }
  //Vertical axis
  ImageLine ($im, $xpadding, $ypadding, $xpadding, $height-$ypadding, $black);
  //Vertical subdiv
  for($y=0;round($y)<=round($ymax);$y+=$ystep) {
    ImageString ($im, 3, $xpadding-$xoffset, ($ymax-$y)*$yspare+$ypadding, round($y), $black);
  }
}

//Return de picture
ImagePng ($im);
imagedestroy($im);
?>