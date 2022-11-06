<?php

namespace UltimateValidator;

/***********************************************************
* #### PHP - Ultimate Form Validator Class ####
***********************************************************/

class UltimateValidator{

    /**
    * param
    */ 
    public  $param; 

    /**
    * params object replica of $param
    */ 
    public  $params; 

    /**
    * attribute 
    * @var string|array|object
    */
    public $attribute;

    /**
    * public message notice
    * @var string|array|object
    */
    public  $message; 

    /**
    * data type
    * @var boolean|null
    */
    private  $type; 

    /**
    * operator check
    */
    private  $operator; 

    /**
    * proceed
    * @var boolean
    */
    private $proceed;

    /**
    * error 
    * @var boolean
    */
    private $error    = false;


    /**
    * @param  array    $param form array like $_POST or $_GET.
    * @param  string   $type string like POST or GET - case-insensitive
    * @param  string|int|array|object|resource   $attribute any outside param you would want to use within the form
    * @return object   returns an object for chaining
    */
    public function __construct(?array $param = null, ?string $type = null, $attribute = null) 
    {
        $this->message      = [];
        $this->type         = $this->getType($type);
        $this->attribute    = $attribute;
        $this->setParams($param);

        return $this;
    }

    /**
    * @param  array     $data Constant like INPUT_XXX.
    * @param  boolean   $allowedType --  if to display all error once or one after another
    * @return object|response class object return.
    */
    public function submit(array $data, ?bool $allowedType = false) 
    {
        //only begin validation when submitted
        if(isset($this->param) && count($this->param) > 0)
        {
            //set to false
            $this->proceed = false;

            /**
            * after isset function call
            */
            $this->afterSubmit($this);

            /**
            * if allowed error type is true
            * Error type converted to arrays
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
                    if($flags === false || $flags === '!isset'){
                        $this->message  = $message;
                        $this->error    = true;
                        break;
                    }else{
                        $this->error = false; // set to false

                        //operator function checker
                        $this->operator     = $this->operatorMethod($keyPair);

                        if($this->operator === 'error'){
                            $this->error      = true;
                            $this->message    = sprintf("Comparison Operator error for this variable `%s`", $keyPair['variable']);
                            break;
                        }
                        else{
                            if(!is_null($this->operator) && $this->operator){
                                $this->message  = $message;
                                $this->error    = true;
                                break;
                            }
                        }
                    }
                }
                else{
                    if($flags === false || $flags === '!isset'){
                        if(!in_array($keyPair['variable'], array_keys($this->message))){
                            $this->message[$keyPair['variable']]    = $message;
                        }
                        $this->error        = true;
                    }else{
                        //operator function checker
                        $this->operator = $this->operatorMethod($keyPair);

                        if($this->operator === 'error'){
                            $this->error        = true;
                            $this->message[$keyPair['variable']]    = sprintf("Comparison Operator error for this variable `%s`", $keyPair['variable']);
                            break;
                        }

                        elseif(!is_null($this->operator) && $this->operator){
                            if(!in_array($keyPair['variable'], array_keys($this->message))){
                                $this->message[$keyPair['variable']]    = $message;
                            }
                            $this->error        = true;
                        }
                        else{
                            // if no message error exist 
                            // meaning array count is 0
                            if(!count($this->message)){
                                $this->error = false; // set to false
                            }
                        }
                    }
                }
            }

            /**
            * if validation succeeds without error | 
            * set proceed value to | true
            */
            if(!$this->error){
                $this->proceed = true;
            }
            return $this;
        }
        
        /**
        * before isset function call
        */
        else{
            $this->beforeSubmit($this);
        }
        return $this;
    }

    /**
    * @param  callable  $function.
    * @param  null|void  pass a $var into the funtion to access the returned object
    * usage   ->error(  function($response){}  );
    * @return object|response class object on error.
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
    * @param  callable  $function.
    * @param  null|void  pass a $var into the funtion to access the returned object
    * usage   ->success(  function($response){}  );
    * @return object|response class object on success.
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
    * @param  callable  $function.
    * Before form is set
    * usage   ->beforeSubmit(  function($response){}  );
    * @return void\Response class object on success
    */
    public function beforeSubmit($function)
    {
        if(!isset($this->param)){
            if(is_callable($function)){
                $function($this);
            }
        }
        return $this;
    }

    /**
    * @param  callable  $function.
    * After form is set
    * usage   ->afterSubmit(  function($response){}  );
    * @return void\Response class object on success
    */
    public function afterSubmit($function)
    {
        if(isset($this->param) && count($this->param) > 0){
            if(is_callable($function)){
                $function($this);
            }
        }
        return $this;
    }
    
    /**
    * @param  array  $keys of needed parameters
    * @return array of all needed params
    */
    public function only(?array $keys = null)
    {
        $data = [];
        if(is_array($keys)){
            foreach($keys as $key){
                if(in_array($key, array_keys($this->param))){
                    $data[$key] = $this->param[$key];
                }
            }
            return $data;
        }
    }

    /**
    * @param  array  $keys of to remove from parameters
    * @return array of all needed params except the removed onces
    */
    public function except(?array $keys = null)
    {
        $data = $this->param;
        if(is_array($keys)){
            foreach($keys as $key){
                if(in_array($key, array_keys($data))){
                    unset($data[$key]);
                }
            }
            return $data;
        }
    }

    /**
    * @param  string|key  $key param to check for
    * @return boolean if param is set or not
    */
    public function has(?string $key = null)
    {
        if(in_array($key, array_keys($this->param))){
            return true;
        }
        return false;
    }

    /**
    * @param  array $keys data to merge
    * @param  array $data this will be the main data to merge the key with
    * @return array of all needed params
    */
    public function merge(?array $keys = null, ?array $data = null)
    {
        if(is_array($keys) && is_array($data)){
            return array_merge($keys,  $data);
        }
    }
    
    /**
    * @param  array|keys  $keys of needed parameters
    * @param  array|data  data param to check from
    * @return array of all needed params
    */
    public function onlyData(?array $keys = null, ?array $all_data = null)
    {
        $data = [];
        if(is_array($keys)){
            foreach($keys as $key){
                if(in_array($key, array_keys($all_data))){
                    $data[$key] = $all_data[$key];
                }
            }
            return $data;
        }
    }

    /**
    * @param  array|keys  $keys of to remove from parameters
    * @param  array|data  data param to check from
    * @return array of all needed params except the removed onces
    */
    public function exceptData(?array $keys = null, ?array $data = null)
    {
        if(is_array($keys)){
            foreach($keys as $key){
                if(in_array($key, array_keys($data))){
                    unset($data[$key]);
                }
            }
            return $data;
        }
    } 

    /**
    * @param string $type type of data needed
    * .ie form (array) of param | attributes (object) of param 
    * @return array|object of form if isset
    */
    public function getForm($type = null)
    {
        // create form structure
        $data = [
            'attribute' => $this->param,
            'attributes' => (object) $this->param
        ];

        // in array keys
        if(in_array($type, array_keys($data))){
            return $data[$type];
        }
        return $data;
    }

    /**
    * @param string $key of param name
    * @return array|object|string|null
    */
    public function old($key = null)
    {
        // in array keys
        $formData = $this->getForm()['attribute'];
        if(is_array($formData) && in_array($key, array_keys($formData))){
            return $formData[$key];
        }
        return;
    }

    /**
    * @param  int|float  $response interger or float passer.
    * @param  string|array|object     $message can be any data type for display.
    * @return echo|json       Returns encoded JSON object of response and message
    */
    public function echoJson(?int $response = 0, $message = null)
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
                    // mostly for value of 0
                    if(empty($type) && $type != '0') {
                        $type = false;
                    }
                    break;
            }
        }else{
            $type = '!isset';
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
            if($flagOperator == '=')
            {
                $dataString = $this->param[$flag['variable']];
                if($dataString == $flagValue){
                    $this->operator = true;
                }
            }

            //strictly equal to operator
            elseif($flagOperator == '==')
            {
                $dataString = $this->param[$flag['variable']];
                if($dataString === $flagValue){
                    $this->operator = true;
                }
            }

            //not equal to operator
            elseif($flagOperator == '!=')
            {
                $dataString = $this->param[$flag['variable']];
                if($dataString != $flagValue){
                    $this->operator = true;
                }
            }

            //strictly not equal to operator
            elseif($flagOperator == '!==')
            {
                $dataString = $this->param[$flag['variable']];
                if($dataString !== $flagValue){ 
                    $this->operator = true;
                }
            }

            //greater than operator
            elseif($flagOperator == '>')
            {
                $dataString = $this->param[$flag['variable']];
                // if str_len | sl
                if(in_array($flag['data_type'], ['str_len', 'sl'])){
                    $dataString = strlen($dataString);
                    if($dataString > (float) $flagValue){
                        $this->operator = true;
                    }
                }else{
                    $dataString = (float) $dataString;
                    if($dataString > (float) $flagValue){
                        $this->operator = true;
                    }
                }
            }

            //greater than or equal to operator
            elseif($flagOperator == '>=')
            {
                $dataString = $this->param[$flag['variable']];
                // if str_len | sl
                if(in_array($flag['data_type'], ['str_len', 'sl'])){
                    $dataString = strlen($dataString);
                    if($dataString >= (float) $flagValue){
                        $this->operator = true;
                    }
                }else{
                    $dataString = (float) $dataString;
                    if($dataString >= (float) $flagValue){
                        $this->operator = true;
                    }
                }
            }

            //less than operator
            elseif($flagOperator == '<')
            {
                $dataString = $this->param[$flag['variable']];
                // if str_len | sl
                if(in_array($flag['data_type'], ['str_len', 'sl'])){
                    $dataString = strlen($dataString);
                    if($dataString  < (float) $flagValue){
                        $this->operator = true;
                    }
                }else{
                    $dataString = (float) $dataString;
                    if($dataString  < (float) $flagValue){
                        $this->operator = true;
                    }
                }
            }

            //less than or equal to operator
            elseif($flagOperator == '<=')
            {
                $dataString = $this->param[$flag['variable']];
                // if str_len | sl
                if(in_array($flag['data_type'], ['str_len', 'sl'])){
                    $dataString = strlen($dataString);
                    if($dataString  <= (float) $flagValue){
                        $this->operator = true;
                    }
                }else{
                    $dataString = (float) $dataString;
                    if($dataString  <= (float) $flagValue){
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
    * Set default params on load
    * Determine if there's a parampassed to __contructor or not
    * @param array|object $param form param or constructed params data
    * @return object
    */
    private function setParams($param)
    {
        // if the param is not set then we use request method to 
        // determine data received data from forms
        if(is_null($param)){
            // POST | GET | COOKIE
            $this->param    = $_REQUEST;
            $this->params   = (object) $this->param;
        }else{
            $this->param    = filter_input_array($this->type);
            $this->params   = (object) $this->param;
        }
        return $this;
    }

    /**
    * @param  string $type    string like POST or GET.
    * @return int            The value of request type.
    */
    private function getType($type = null)
    {
        // if type is not passed then we detect the method type
        if(empty($type) || !in_array($type, ['get', 'GET', 'post', 'POST'])){
            $type = $_SERVER['REQUEST_METHOD'];
        }

        switch(strtolower($type)){
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
