<?php

/* Copyright 2011 Ian Sproates <ian@sproates.net> */

/**
 * @file visio-svg-convert.php
 *
 * Fixes the crap SVG format that Visio exports.
 * Outputs the fixed SVG to standard output, so you might want to pipe it into a
 * file.
 *
 * Usage:
 * php visio-svg-convert.php <filename>
 * OR
 * php visio-svg-convert.php <standard input>
 * 
 */

error_reporting(E_ALL & E_STRICT);

/**
 * Convert SVG as exported by Visio into something that:
 * 1) Can be viewed in a sensible SVG viewer (e.g. Firefox).
 * 2) Doesn't hide arrow heads.
 * 3) Doesn't use a stupidly font size for labels.
 * @param string $data SVG data.
 * @return string The converted data.
 */
function fix_visio_svg($data) {
  $data = str_replace("<svg ", "<svg xmlns:xlink=\"http://www.w3.org/1999/xlink\" ", $data);
  $data = preg_replace("/\.st5 \{(.*)?\}/", ".st5 {fill:#000000;font-family:Arial;font-size:0.5em}", $data);
  $data = str_replace("orient=\"auto\"", "orient=\"auto\" style=\"overflow:visible;\"", $data);
  return $data;
}

/**
 * Write a message to standard error.
 * @param string $message The message to write.
 * @return mixed The number of bytes written, or FALSE on error.
 */
function error($message) {
  $handle = fopen('php://stderr', 'w');
  if($handle) {
    return fwrite($handle, $message);
  }
}

$handle = null;
$data = '';

if($argc < 2) {
  $handle = fopen('php://stdin', 'r');
  if(false === $handle) {
    error("Unable to read from standard input\n");
    exit;
  }
} else {
  $handle = fopen($argv[1], 'r');
  if(false === $handle) {
    error("Unable to read from input file $argc[1]\n");
    exit;
  }
}
while($line = fgets($handle)) {
  $data .= $line;
}

if(!strlen($data)) {
  error("No data to convert!\n");
  exit;
}

$data = fix_visio_svg($data);
echo $data;
flush();
exit;
