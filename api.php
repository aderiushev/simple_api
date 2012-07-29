<?php
/** server-side Api processor
 * parameters comes from client.php with GET or POST
*/
switch ($_SERVER["REQUEST_METHOD"])
{
    case "POST":
        $api = new api($_POST);
        break;

    case "GET":
        $api = new api($_GET);
        break;
}

function __autoload($class)
{
    require_once($class.".class.php");
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
        foreach ($params as $pkey => $pvalue)
        {
            /** cheking on external params */
            if (!array_key_exists($pkey, $this->required) && !array_key_exists($pkey, $this->optional))
                $this->error("wrong_param", $pkey);
        }
        /** checking on all required params are in */
        $required_params = array_diff(array_keys($this->required), array_keys($params));
        if (count($required_params) > 0)
            $this->error("required_param", $required_params[0]);
        
        /** Here may be another validation rules */
        /** When all of them passed successfully - creating and object-responser witch will be an array of fetching data */
      //  $responser = new response($params);
    }

    /** Method of processing errors */
    private function error($kind, $param = "")
    {
        switch ($kind)
        {
            case "wrong_param":
                exit($this->sendMsg("Error", "wrong parameter comes: $param"));
                break;
            case "required_param":
                exit($this->sendMsg("Error", "Parameter: $param is required"));
                break;
            case "wrong_param_type":
                exit($this->sendMsg("Error", "Parameter: $param[0] has wrong type. Should be $param[1]"));
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
