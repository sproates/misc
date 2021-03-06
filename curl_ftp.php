<?php

/* Copyright 2011 Ian Sproates <ian@sproates.net> */

/**
 * @file curl_ftp.php
 */

/**
 * Upload a file via FTP using cURL.
 * @param string $filename Name of the file to upload; can be a full path.
 * @param string $host Hostname of the FTP server (IP address should work too).
 * @param string $username FTP username.
 * @param string $password FTP password.
 * @param string $remote Remote directory to upload to.
 * @return Zero on success; FALSE if cURL couldn't be initialised; cURL error
 * number on any other cURL failure.
 */
function curl_ftp_upload($filename, $host, $username, $password, $remote) {
  $ch = curl_init();
  if(!$ch) {
    return false;
  }
  $host = urlencode($host);
  $username = urlencode($username);
  $password = urlencode($password);
  $remote = urlencode($remote);
  $file_bits = explode('/', $filename);
  $remote_filename = $file_bits[(count($file_bits) - 1)];
  $fp = fopen($filename, 'r');
  curl_setopt($ch, CURLOPT_UPLOAD, true);
  curl_setopt($ch, CURLOPT_URL, "ftp://$username:$password@$host/$remote/$remote_filename");
  curl_setopt($ch, CURLOPT_INFILE, $fp);
  curl_setopt($ch, CURLOPT_INFILESIZE, filesize($filename));
  curl_exec($ch);
  fclose($fp);
  return (0 != ($error_no = curl_errno($ch))) ? $error_no : true;
}
