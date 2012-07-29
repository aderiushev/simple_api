<?php
/**
 * client-side. Sending POST or GET via curl
 * doesnt matter how to send request
 * more attention should be paid to api.php
 * Sending 6 params: userid, productid, price, description, answerType, redirect
 * redirct description and answerType are optional. 
*/

function sendGet($paramsStr)
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "http://hudziamasuki.com/testapi/api/Api.php?$paramsStr");
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
  curl_setopt($ch, CURLOPT_URL, "http://hudziamasuki.com/testapi/Api/api.php");
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, "$paramsStr");
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
  $data = curl_exec($ch);
  curl_close($ch);
  
  return $data;
}

$paramsStr = "userid=777&productid=asd&price=222&description=allright&answerType=xml";
//$post = sendPost($paramsStr);
$result = sendGet($paramsStr);
echo $result;


 
 
