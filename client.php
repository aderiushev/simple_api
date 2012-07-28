<?php
/**
 * Client-side script. Sending POST or GET via curl
*/

function sendGet()
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "http://yoursite/api?request=$request");
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
  $data = curl_exec($ch);
  curl_close($ch);
  
  return $data;
}

function sendPost()
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "http://yoursite/api");
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, "request=$request");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
  $data = curl_exec($ch);
  curl_close($ch);
  
  return $data;
}

//$post = sendPost();
//$get = sendGet();

 
 
