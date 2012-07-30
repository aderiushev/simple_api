<?php

/** server-side Api processor
 *  Expect parameters comes with GET or POST
 */
switch ($_SERVER["REQUEST_METHOD"])
{
    case "POST":
        $api = new Api($_POST);
        break;

    case "GET":
        $api = new Api($_GET);
        break;
}

function __autoload($class)
{
    require_once ($class . ".class.php");
}

class Api
{
    /** I think it's the most flexible variant to differ income params into 2 arrays */
    private $required = array(
        "userid" => null,
        "productid" => null,
        "price" => null);

    private $optional = array(
        "description" => "no description",
        "answerType" => "json",
        "redirect" => "");

    private $income_params = array();

    /** getting get/post array of params adn processing it */
    public function __construct($params)
    {
        $this->income_params = $params;

        $external_params = array();
        $required_params = array();

        foreach ($params as $pkey => $pvalue)
        {
            /** cheking on external params */
            if (!array_key_exists($pkey, $this->required) && !array_key_exists($pkey, $this->
                optional))
                array_push($external_params, $pkey);
        }
        if (count($external_params) > 0)
        {
            $external_params_str = implode($external_params, ", ");
            $this->error("wrong_param", $external_params_str);
        }

        /** checking on all required params are in */
        $required_params = array_diff(array_keys($this->required), array_keys($this->
            income_params));
        if (count($required_params) > 0)
        {
            $required_params_str = implode($required_params, ", ");
            exit($this->error("required_param", $required_params_str));
        }

        /** Here may be another validation rules */
        /** When all of them passed successfully - creating and object-responser witch will be an array of fetching data */
        $responser = new Response();
        $resp = $responser->getData($this->income_params);
        exit($this->sendMsg("Success", "Parameters are OK", $resp));
    }

    /** Method of processing errors */
    private function error($kind, $param = "")
    {
        switch ($kind)
        {
            case "wrong_param":
                exit($this->sendMsg("Error", "wrong parameter(s) comes: $param",
                    "Try again with correct parameters"));
                break;
            case "required_param":
                exit($this->sendMsg("Error", "Parameter(s): $param is required",
                    "Try again with correct parameters"));
                break;
        }
    }

    /** Sending Message in case of type of answerType */
    private function sendMsg($type, $text = "", $answer = "")
    {
        $at = $this->income_params["answerType"] ? $this->income_params["answerType"] :
            $this->optional["answerType"];

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
