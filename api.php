<?php

/** server-side Api processor */
switch ($_SERVER["REQUEST_METHOD"])
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
    private $required = array(
        "userid" => null,
        "productid" => null,
        "price" => null);
    private $optional = array(
        "description" => "",
        "answerType" => "json",
        "redirect" => "");
    /** getting get/post array of params adn processing it */
    public function __construct($params)
    {
        print_r($this->required);
        foreach ($params as $pkey => $pvalue)
        {
            if(!array_key_exists($pkey, $this->required) && !array_key_exists($pkey, $this->optional))
                $this->error("wrong_param", $pkey);
        }
    }

    /** Method of processing errors */
    private function error($kind, $param = "")
    {
        switch ($kind)
        {
            case "wrong_param":
                exit($this->sendMsg("Error", "wrong parameter comes: $param"));
                break;
        }
    }

    /** Sending Message in case of type of answerType */
    private function sendMsg($type, $text = "")
    {
        switch ($this->optional["answerType"])
        {
            case "json":
                return $this->getJson(array("Type" => $type, "Text" => $text));
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
