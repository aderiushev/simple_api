<?php

/** server-side Api processor
 *  Expect parameters comes with GET or POST
 */
switch ($_SERVER["REQUEST_METHOD"])
{
    case "POST":
        $incoming = $_POST
        break;

    case "GET":
        $incoming = $_POST
        break;
}

if (empty($incoming) || !is_array($incoming) || !isset($incoming))
    exit($api::sendMsg("Error", "Failure income parameters", "I dont know what should you do.."));
        
$api = new Api($incoming);
$api->getValidState();

$responser = new Response();
$resp = $responser->getData($responser);
exit($api::sendMsg("Success", "Parameters are OK", $resp));

function __autoload($class)
{
    require_once ($class . ".class.php");
}

class Api
{
    /** I think it's the most flexible variant to differ income params into 2 arrays */
    private $_required = array(
        "userid" => null,
        "productid" => null,
        "price" => null);

    private $_optional = array(
        "description" => "no description",
        "answerType" => "json",
        "redirect" => "");

    private $_income_params = array();

    /** getting get/post array of params adn processing it */
    public function __construct($params)
    {
        return $this->_income_params = $params;   
        
    }
    
    public function getValidState()
    {
        $external_params = array();
        $required_params = array();

        foreach ($params as $pkey => $pvalue)
        {
            /** cheking on external params */
            if (!array_key_exists($pkey, $this->_required) && !array_key_exists($pkey, $this->_optional))
                array_push($external_params, $pkey);
        }
        if (count($external_params) > 0)
        {
            $external_params_str = implode($external_params, ", ");
            exit($this->error("wrong_param", $external_params_str));
        }

        /** checking on all required params are in */
        $required_params = array_diff(array_keys($this->_required), array_keys($this->_income_params));
        if (count($required_params) > 0)
        {
            $required_params_str = implode($required_params, ", ");
            exit($this->error("required_param", $required_params_str));
        }
        return true;
    }

    /** Method of processing errors */
    private function error($kind, $param = "")
    {
        switch ($kind)
        {
            case "wrong_param":
                exit(self::sendMsg("Error", "wrong parameter(s) comes: $param",
                    "Try again with correct parameters"));
                break;
            case "required_param":
                exit(self::sendMsg("Error", "Parameter(s): $param is required",
                    "Try again with correct parameters"));
                break;
        }
    }

    /** Sending Message in case of type of answerType */
    public static function sendMsg($type, $text = "", $answer = "")
    {
        if (!isset($this->_income_params) || !is_array($this->_income_params) || empty($this->_income_params))
            $at = "json";
        else
            $at = $this->_income_params["answerType"] ? $this->_income_params["answerType"] : $this->_optional["answerType"];

        switch ($at)
        {
            case "json":
                return $this->getJson(array(
                    "Type" => $type,
                    "Text" => $text,
                    "Answer" => $answer));
                break;

            case "xml":
                return $this->getXml(array(
                    "Type" => $type,
                    "Message" => $text,
                    "Answer" => $answer));
                break;
        }
    }

    /** Json-encoding string or array */
    private function getJson($data)
    {
        return json_encode($data);
    }

    /** Getting result in XML */
    private function getXml($data)
    {
        header('Content-type: application/xml');
        $converter = new Array2XML();
        return $converter->convert($data);
    }
}
