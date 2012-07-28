<?php
/**
 * client-side. Sending POST or GET via curl
 * doesnt matter how to send req.
 * pay more attention to api.php
 * Sending 5 params: userid, productid, price, description, answerType, redirect
 * redirct description and answerType are optional. 
*/

function sendGet($paramsStr)
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "http://hudziamasuki.com/testapi/api/api.php?$paramsStr");
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
  $data = curl_exec($ch);
  curl_close($ch);
  
  return $data;
}

function sendPost($paramsStr)
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "http://hudziamasuki.com/testapi/api/api.php");
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, "$paramsStr");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
  $data = curl_exec($ch);
  curl_close($ch);
  
  return $data;
}

$paramsStr = "userid=777&productid=555&price=222&description=allright&answerType=json&qwe=qwe";
//$post = sendPost($paramsStr);
$result = sendGet($paramsStr);
echo $result;


 
 
