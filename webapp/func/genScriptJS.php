<?php
function listAvailableFunctions($path)
{
  $functionNames = array();
  $functionSources = array();
  $functionRequired = array();
  $handleR = @fopen($path, "r");
  $required = false;
  $flag = false;
  $requiredSeparator = "/*required*/";
  if ($handleR) {
    while(!feof($handleR)) {
      $buffer = trim(fgets($handleR, 4096));
      if (strpos($buffer, $requiredSeparator) !== false) {
        $required = !$required;
      } else {
        if ($required) {
          $functionRequired[] .= $buffer."\n";
        }
      }
      $regExp = "@^\bfunction\b\s+([^\(]*)@"; //FOR PHP Functions
      //$regExp = "@^([^:;/\*{}]+)$@"; //FOR CSS
      preg_match_all($regExp, $buffer, $out, PREG_PATTERN_ORDER);
      if (isset($out[1][0]) && $out[1][0] != "") {
        if (!$flag) {
          $flag = true;
          $open = substr_count($buffer, '{');
          $close = substr_count($buffer, '}');
          $functionNames[] .= $out[1][0];
          $functionSources[] .= "";
        }
      } else {
        if ($flag) {
          $open += substr_count($buffer, '{');
          $close += substr_count($buffer, '}');
        }
      }
      if ($flag) {
        $last_index = count($functionSources)-1;
        $functionSources[$last_index] = $functionSources[$last_index].$buffer."\n";
        if ($open == $close && $open != 0) {
          $flag = false;
        }
      }
    }
  }
  fclose($handleR);
  return array("Names" => $functionNames, "Sources" => $functionSources, "Required" => $functionRequired);
}

function listNeededFunctions($path, $availableFunctions)
{
  $functionNames = $availableFunctions['Names'];
  $neededFunctions = array();
  $handleD = @fopen($path, "r");
  if ($handleD) {
    while(!feof($handleD)) {
      $buffer = trim(fgets($handleD, 4096));
      $words = preg_split("/[^[:alnum:]_]+/",trim($buffer));
      $neededFunctions = array_merge($neededFunctions, array_intersect($words, $functionNames));
    }
  }
  $neededFunctions[] .= "hideCtxtMenu";
  $neededFunctions[] .= "manageKey";
  return array_unique($neededFunctions);
}

function getFunctionsSrc($neededFunctions, $availableFunctions)
{
  $functionNames = $availableFunctions['Names'];
  $functionSources = $availableFunctions['Sources'];
  $functionRequired = $availableFunctions['Required'];
  $functionNames = array_flip($functionNames);
  $src = "";
  foreach($neededFunctions as $value) {
    $src .= $functionSources[$functionNames[$value]];
  }
  $src = $functionRequired[0].$src.$functionRequired[1];
  return $src;
}

function listFilesFromType($folder, $type)
{
  $array = array();
  if ($d = opendir($folder)) {
    while (false !== ($file = readdir($d))) {
      if ($file != '.' && $file != '..' && getFileExtension($file) == $type) {
      	$array[] .= $folder."/".$file;
      }
    }
  }
  return $array;
}

function listDependences($neededFunctions, $availableFunctions)
{
  $functions = array();
  $addFunctions = $neededFunctions;
  $tempAvailableFunctions = $availableFunctions;
  while (count($addFunctions) != 0) {
    $src = getFunctionsSrc($addFunctions, $availableFunctions);
    $words = preg_split("/[^[:alnum:]_]+/",trim($src));
    $functions = array_merge($functions, $addFunctions);
    $tempAvailableFunctions['Names'] = array_diff($tempAvailableFunctions['Names'], $functions);
    $addFunctions = array_intersect($words, $tempAvailableFunctions['Names']);
  }
  return array_unique($functions);
}

function refreshJSCache()
{
  $dest = listFilesFromType("html", "php");
  $avFuncs = array();
  $needFuncs = array();
  $destFiles = array();
  foreach($dest as $file) {
    $destPath = "html/".getScriptJS($file);
    if (!in_array($destPath, $destFiles)) {
      $destFiles[] .= $destPath;
      $avFuncs = listAvailableFunctions("scripts/source/script.js");
      $needFuncs = listNeededFunctions($file, $avFuncs);
      $needFuncs = listDependences($needFuncs, $avFuncs);
      $source = getFunctionsSrc($needFuncs, $avFuncs);
      if (count($needFuncs) != 0) {
        if (fileExists($destPath)) {
          if (@unlink($destPath)) {
          	echo start_comment."File ".$destPath." unlinked !".end_comment."\n";
          };
        }
        $handleW = @fopen($destPath, "w");
        if ($handleW) {
          fwrite($handleW, $source);
          echo start_comment."File ".$destPath." done !".end_comment."\n";
          foreach ($needFuncs as $function) {
            echo start_comment.$function.end_comment."\n";
          }
        }else{
            echo "error khudsf87098df<br>";
        }
        @fclose($handleW);
      }
    }
  }
}
?>
