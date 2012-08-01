<?php

/** server-side Api processor
 *  Expect parameters comes with GET or POST
 */
switch ($_SERVER["REQUEST_METHOD"])
{
    case "POST":
        $incoming = $_POST;
        break;

    case "GET":
        $incoming = $_GET;
        break;
}

$api = new Api();

/** returns an array of [Status, Code, Data] of validation */
$validStatus = unserialize($api->getValidState($incoming));
/** processing the code of validation */
switch ($validStatus["Code"])
{
    case 0:
        $response = $api->sendMsg($validStatus["Status"], "Params not set or empty",
            "Please, try again");
        echo $response;
        break;

    case 1:
        $response = $api->sendMsg($validStatus["Status"], "External params: " . $validStatus["Data"],
            "Please, try again");
        echo $response;
        break;

    case 2:
        $response = $api->sendMsg($validStatus["Status"],
            "Required parameters should be set: " . $validStatus["Data"],
            "Please, try again");
        echo $response;
        break;

    case 3:
        $response = $api->sendMsg($validStatus["Status"], "Congratulation. Ty For using our service!", $validStatus["Data"]);
        echo $response;
        break;
}

////////////////////////////

function __autoload($class)
{
    require_once ($class . ".class.php");
}
/** base class of our api */
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

    private static $_income_params = array();

    public static $status;

    /** Here you can add your validation Rules */
    public function getValidState($params)
    {
        /** first check. On correct coming parameters */
        if (!is_array($params) || empty($params) || !isset($params))
            return self::$status = serialize(array("Status" => "Error", "Code" => 0));
        else
            $this->_income_params = $params;

        $external_params = array();
        $required_params = array();
        /** second check. If comes unexpected parameter. */
        foreach ($this->_income_params as $pkey => $pvalue)
        {
            if (!array_key_exists($pkey, $this->_required) && !array_key_exists($pkey, $this->
                _optional))
                array_push($external_params, $pkey);
        }
        /** Getting the list of external params */
        if (count($external_params) > 0)
        {
            $external_params_str = implode($external_params, ", ");
            return self::$status = serialize(array(
                "Status" => "Error",
                "Code" => 1,
                "Data" => $external_params_str));
        }
        /** third check. On requireness. All the parameters in required array shoulld be set */
        $required_params = array_diff(array_keys($this->_required), array_keys($this->
            _income_params));
        /** getting the list of unseted params */ 
        if (count($required_params) > 0)
        {
            $required_params_str = implode($required_params, ", ");
            return self::$status = serialize(array(
                "Status" => "Error",
                "Code" => 2,
                "Data" => $required_params_str));
        }
        /** If all the levels passed, sending success one with the now correct params */
        return self::$status = serialize(array(
            "Status" => "Success",
            "Code" => 3,
            "Data" => $this->_income_params));
    }
    /** function of preparing and sending message */
    public function sendMsg($type, $text = "", $answer = "")
    {
        $convertable = array(
            "Type" => $type,
            "Text" => $text,
            "Answer" => $answer);
        
        $at = $this->_income_params["answerType"] ? $this->_income_params["answerType"] :
            $this->_optional["answerType"];

        switch ($at)
        {
            case "json":
                return $this->getJson($convertable);
                break;

            case "xml":
                return $this->getXml($convertable);
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
