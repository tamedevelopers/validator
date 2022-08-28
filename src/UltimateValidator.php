<?php

namespace UltimateValidator;

/***********************************************************
* #### PHP - Ultimate Form Validator Class ####
***********************************************************/

class UltimateValidator{

    /**
    * params
    */
    public  $param; 

    /**
    * public message notice
    */
    public  $message; 

    /**
    * data type
    */
    private  $type; 

    /**
    * operator check
    */
    private  $operator; 

    /**
    * proceed
    */
    private $proceed;

    /**
    * error 
    */
    private $error    = false;


    /**
    * @param  array    $param form array like $_POST or $_GET.
    * @param  string   $type string like POST or GET - case-insensitive
    * @return object   returns an object for chaining
    */
    public function __construct(?array $param, ?string $type = null) 
    {
        $this->message  = [];
        $this->param    = $param;
        $this->type     = $this->getType($type);
        return $this;
    }

    /**
    * @param  array $data    Constant like INPUT_XXX.
    * @param boolean  $allowedType --  if to display all error once or one after another
    * @param callable $beforeIssetFunc  before isset function if form is not set
    * @param callable $afterIssetFunc after isset function if form is set
    * @return object      entire object data
    */
    public function submit(array $data, ?bool $allowedType = false, callable $beforeIssetFunc = null, callable $afterIssetFunc = null) 
    {
        //get params from input type
        $this->param = filter_input_array($this->type);

        //only begin validation when submitted
        if(isset($this->param))
        {
            //set to false
            $this->proceed = false;

            /**
            * after isset function call
            */
            if(is_callable($afterIssetFunc)){
                $afterIssetFunc($this);
            }

            /**
            * if allowed error type is true
            */
            if($allowedType){
                $this->message  = [];
            }else{
                $this->message  = "";
            }


            //start loop process
            foreach($data as $key => $message){
                $keyPair = $this->getKeyPairs($key);

                /**
                * configuration error
                */
                if($keyPair === "exception"){
                    $this->message = ("Configuration error:: Indicator `:` type passed not valid");
                    break;
                }

                //check response error from input flags
                $flags = $this->createFlags($keyPair);

                //allowed error handling type
                if($allowedType == false){
                    if($flags ===  false){
                        $this->message  = $message;
                        $this->error    = false;
                        break;
                    }else{
                        $this->error = true;
    
                        //operator function checker
                        $this->operator     = $this->operatorMethod($keyPair);
                        if(!is_null($this->operator) && $this->operator){
                            $this->message  = $message;
                            $this->error    = false;
                            break;
                        }
                    }
                }
                else
                {
                    if($flags ===  false){
                        if(!in_array($keyPair['variable'], array_keys($this->message))){
                            $this->message[$keyPair['variable']]    = $message;
                        }
                        $this->error        = false;
                    }else{
                        //operator function checker
                        $this->operator = $this->operatorMethod($keyPair);

                        if($this->operator === 'error'){
                            $this->error        = false;
                            $this->message[$keyPair['variable']]    = sprintf("Comparison Operator error for this variable `%s`", $keyPair['variable']);
                            break;
                        }

                        elseif(!is_null($this->operator) && $this->operator){
                            if(!in_array($keyPair['variable'], array_keys($this->message))){
                                $this->message[$keyPair['variable']]    = $message;
                            }
                            $this->error        = false;
                        }
                        else{
                            if(count($this->message) === 0){
                                $this->error = true;
                            }
                        }
                    }
                }
            }

            /**
            * if validation succeeds without error | proceed to success
            */
            if($this->error){
                $this->proceed = true;
            }
            return $this;
        }
        
        /**
        * before isset function call
        */
        else{
            if(is_callable($beforeIssetFunc)){
                $beforeIssetFunc($this);
            }
        }
        return $this;
    }


    /**
    * @param  function     var $response into function to get access to the params.
    * @return response     $response data on success call.
    */
    public function error($function)
    {
        if(!is_null($this->proceed)  && $this->proceed === false){
            if(is_callable($function)){
                $function($this);
            }
        }
        return $this;
    }

    
    /**
    * @param  function     var $response into function to get access to the params.
    * @return response     $response data on success call.
    */
    public function success($function)
    {
        if(!is_null($this->proceed)  && $this->proceed){
            if(is_callable($function)){
                $function($this);
            }
        }
        return $this;
    }


    /**
    * @param  int|float  $response interger or float passer.
    * @param  string|array|object     $message can be any data type for display.
    * @return json       Returns encoded JSON object of response and message
    */
    public function echoJson(int $response, $message = null)
    {
        echo json_encode(['response' => $response, 'message' => $message]);
    }


    /**
    * @param  array $key    string like :string::name .
    * @return boolean       on error.
    * @return data          on success.
    */
    private function createFlags(array $flag)
    {
        //if input parameter is isset -- proceed to error validating
        $type = true;
        if($this->checkParamIsset($flag['variable'])){
            switch($flag['data_type']){
                case (in_array($flag['data_type'], ['email', 'e'])):
                    $type = filter_input($this->type, $flag['variable'], FILTER_VALIDATE_EMAIL);
                    break;
                    
                case (in_array($flag['data_type'], ['int', 'i'])):
                    $type = filter_input($this->type, $flag['variable'], FILTER_VALIDATE_INT);
                    break;
                    
                case (in_array($flag['data_type'], ['float', 'f'])):
                    $type = filter_input($this->type, $flag['variable'], FILTER_VALIDATE_FLOAT);
                    break;
                    
                case (in_array($flag['data_type'], ['url', 'u'])):
                    $type = filter_input($this->type, $flag['variable'], FILTER_VALIDATE_URL);
                    break;
                    
                case (in_array($flag['data_type'], ['array', 'a'])):
                    $array = json_decode($this->param[$flag['variable']], TRUE);
                    $type = isset($this->param[$flag['variable']]) && is_array($array) && count($array) > 0 ? true : false;
                    break;
                    
                case (in_array($flag['data_type'], ['bool', 'b'])):
                    $type = filter_input($this->type, $flag['variable'], FILTER_VALIDATE_BOOLEAN);
                    break;
    
                default:
                    $type = htmlspecialchars(filter_input($this->type, $flag['variable']), ENT_HTML5);
                    if(empty($type))
                        $type = false;
                    break;
            }
        }
        
        return $type;
    }


    /**
    * @param  array $flag  array.
    * @return boolean or false.
    */
    private function operatorMethod(?array $flag = null)
    {
        $this->operator = null;
        //comparison operator command
        if(isset($flag['operator']) && !empty($flag['operator'])){
            $this->operator = 'error';
            //value check command
            if(isset($flag['value']))
                $this->operator = $this->createOperator($flag);
        }
        return $this->operator;
    }


    /**
    * @param  array $flag  array.
    * @return boolean or false.
    */
    private function createOperator($flag)
    {
        $this->operator = false;
        $flagOperator = $flag['operator'];
        $flagValue = $flag['value'];

        if($this->checkParamIsset($flag['variable'])){
            //equal to operator
            if(substr_count($flagOperator, "=") === 2)
            {
                $dataString = $this->param[$flag['variable']];
                if($dataString == $flagValue){
                    $this->operator = true;
                }
            }

            //strictly equal to operator
            elseif(substr_count($flagOperator, "=") === 3)
            {
                $dataString = $this->param[$flag['variable']];
                if($dataString === $flagValue){
                    $this->operator = true;
                }
            }

            //not equal to operator
            elseif(substr_count($flagOperator, "!=") === 1)
            {
                $dataString = $this->param[$flag['variable']];
                if($dataString != $flagValue){
                    $this->operator = true;
                }
            }

            //strictly not equal to operator
            elseif(substr_count($flagOperator, "!==") === 1)
            {
                $dataString = $this->param[$flag['variable']];
                if($dataString !== $flagValue){
                    $this->operator = true;
                }
            }

            //greater than operator
            elseif(substr_count($flagOperator, ">") === 1)
            {
                $dataString = $this->param[$flag['variable']];
                //string data type
                if((float) $dataString == 0){
                    if(strlen($dataString)  > $flagValue){
                        $this->operator = true;
                    }
                }else{
                    if((float) $dataString  > $flagValue){
                        $this->operator = true;
                    }
                }
            }

            //less than operator
            elseif(substr_count($flagOperator, "<") === 1)
            {
                $dataString = $this->param[$flag['variable']];
                //for string data type
                if((float) $dataString == 0){
                    if(strlen($dataString)  < $flagValue){
                        $this->operator = true;
                    }
                }else{
                    if((float) $dataString  < $flagValue){
                        $this->operator = true;
                    }
                }
            }
        }

        return $this->operator;
    }
    

    /**
    * @param  string $key    string like :string::name .
    * @return array          filter_type and param name.
    */
    private function getKeyPairs(string $key)
    {
        //explode data
        $flags = explode(":", $key);

        //explode indicator
        $find_occur = substr_count($key, ':'); 

        if(count($flags) > 4 || $find_occur > 3 || !isset($flags[1])){
            return "exception";
        }

        $flags['data_type'] = $flags[0];
        $flags['variable'] = $flags[1];

        //create operator
        if(isset($flags[2])){
            $flags['operator'] = $flags[2];
        }
        if(isset($flags[3])){
            $flags['value'] = $flags[3];
        }

        unset($flags[0]);
        unset($flags[1]);
        unset($flags[2]);
        unset($flags[3]);

        return $flags;
    }


    /**
    * @param  string $type    string like POST or GET.
    * @return int            The value of request type.
    */
    private function getType($type = null)
    {
        $type = strtolower($type);
        switch($type){
            case 'get':
                $this->type = INPUT_GET;
                break;

            default:
                $this->type = INPUT_POST;
                break;
        }
        return $this->type;
    }

    /**
    * @param  string $param  form input name.
    * @return bool on request true|false
    */
    private function checkParamIsset($param = null)
    {
        if($this->type == INPUT_POST){
            return isset($_POST[$param]);
        }else{
            return isset($_GET[$param]);
        }
    }

}
