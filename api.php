<?php
/** server-side 
 * 
*/ 
switch($_SERVER["REQUEST_METHOD"])
{
    case "POST":
    $api = new api($_POST);
    break;
    
    case "GET":
    $api = new api($_GET);   
    break;
}

class api
{   
    /** I think it's the most flexible variant to differ income params into 2 arrays */
    private $required = array("userid", "productid", "price");
    private $optional = array("description"=>"", "answerType"=>"json", "redirect"=>"");
    /** getting get/post array of params adn processing it */
    public function __construct($params)
    {
        foreach($params as $param)
        {
            if (!in_array($param, $required) && !in_array($param, $optional))
                $this->error("wrong_param", $param);
        }
    }
    
    /** Method of processing errors */
    private function error($kind, $param="")
    {
        switch($kind)
        {
            case "wrong_param":
            $this->sendMsg("Error", "wrong parameter comes: $param");
            exit();
            break;
        }
    }
    
    /** Sending Message in case of type of answerType */
    private function sendMsg($type, $text="")
    {
        switch($optional["answerType"])
        {
            case "json":
            $this->getJson($type[$text]);
            break;
            
            case "xml":
            $this->getXml($text);
        }
    }
   
    /** Json-encoding string or array */
    private function getJson($data)
    {
        return json_encode($data);
    }
}
