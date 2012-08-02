<?php

/** server-side Api processor
 *  Expect parameters comes with GET or POST
 */

////////////////////////////

function __autoload($class)
{
    require_once ($class . ".class.php");
}
/** base class of our api */
class Api
{
    /** I think it's the most flexible variant to differ income params into 2 arrays */

    private $_income_params = array();
    private $_validation_rules = array();

    private $required = array();
    private $optional = array();

    public function __construct($settings)
    {
        $this->_required = $settings["required"];
        $this->_optional = $settings["optional"];
        $this->_validation_rules = $settings["validation_rules"];
    }

    /** Here you can add your validation Rules */
    public function getValidState($params)
    {
        $this->_income_params = $params;
        /////////////////////////////////////////////////////
        /** first check. On correct coming parameters */
        if ($this->_validation_rules["check_incomeIsNotEmptyArray"])
        {
            if (!is_array($params) || empty($params) || !isset($params))
                return array("Status" => "Error", "Code" => 600);
        }
        
        if ($this->_validation_rules["check_emptyParams"])
        {
            $empty_params = array();
            foreach ($this->_income_params as $key=>$val)
            {
                if (empty($val))
                    array_push($empty_params, $key);
            }
            
            if (count($empty_params) > 0)
            {
                $empty_params_str = implode($empty_params, ", ");
                return array(
                    "Status" => "Error",
                    "Code" => 605,
                    "Data" => $empty_params_str);
            }
        }

        /////////////////////////////////////////////////////
        if ($this->_validation_rules["check_unnecesarryParams"])
        {
            /** second check. If comes unexpected parameter. */
            $external_params = array();
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
                return array(
                    "Status" => "Error",
                    "Code" => 602,
                    "Data" => $external_params_str);
            }
        }
        /////////////////////////////////////////////////////
        if ($this->_validation_rules["check_requiredParams"])
        {
            /** third check. On requireness. All the parameters in required array shoulld be set */
            $required_params = array();
            $required_params = array_diff(array_keys($this->_required), array_keys($this->
                _income_params));
            /** getting the list of unseted params */
            if (count($required_params) > 0)
            {
                $required_params_str = implode($required_params, ", ");
                return array(
                    "Status" => "Error",
                    "Code" => 601,
                    "Data" => $required_params_str);
            }
        }
        /////////////////////////////////////////////////////
        /** fourth check. Dependencies between parameters */
       if ($this->_validation_rules["check_dependenciesParams"] && !empty($this->_validation_rules["dependencies"]))
       {
           $dependence_params = array();
            foreach($this->_validation_rules["dependencies"] as $dep_key=>$dep_val)
            {
                if (array_key_exists($dep_key, $this->_income_params))
                     $dependence_params = array_diff($dep_val, array_keys($this->_income_params));
            
                if (count($dependence_params) > 0)
                {
                    $dependence_params_str = implode($dependence_params, ", ");
                    return array(
                        "Status" => "Error",
                        "Code" => 604,
                        "Data" => $dependence_params_str,
                        "OtherInfo"=>$dep_key );
                }
            
            
            }
           
          
       }
       
       /////////////////////////////////////////////////////
        /** If all the levels passed, sending success one with the now correct params */

        return serialize(array(
            "Status" => "Success",
            "Code" => 603,
            "Data" => $this->_income_params));
        /////////////////////////////////////////////////////
    }

    /** function of preparing and sending message */
    public function sendMsg($type, $text = "", $answer = "")
    {
        $convertable = array(
            "Type" => $type,
            "Text" => $text,
            "Answer" => $answer);

        if (isset($this->_income_params["answerType"]) && !empty($this->_income_params["answerType"]))
            $at = $this->_income_params["answerType"];
        else
            $at = $this->_optional["answerType"];

        switch ($at)
        {
            case "json":
                return $this->getJson($convertable);
                break;

            case "xml":
                return $this->getXml($convertable);
                break;
            default:
                return $this->getJson($convertable);
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
