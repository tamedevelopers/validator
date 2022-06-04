<?php


class TameValidator{

    public  $param; 
    public  $message; 

    private  $type; 
    protected $proceed;
    protected $error = false;


    /**
    * @param  array    $param form array like $_POST or $_GET, case-insensitive
    * @param  string   $type string like POST or GET.
    * @return object   returns an object for chaining
    */

    public function __construct(array $param, string $type) 
    {
        $this->param = $param;
        $this->type = $this->getType($type);
        return $this;
    }
    

    /**
    * @param  array $data    Constant like INPUT_XXX.
    * datatype::inputname    i.e string::fullname
    * @return int             The value of the filtered super global var.
    */

    public function submit(array $data) 
    {
        //get params from input type
        $this->param = filter_input_array($this->type);

        //only begin validation when submitted
        if(isset($this->param)){
            $this->proceed = false;
            foreach($data as $key => $message){
                $keyPair = $this->getKeyPairs($key);

                //check response error from input flags
                $flags = $this->createFlags($keyPair);
                if($flags ===  false){
                    $this->message = $message;
                    $this->error = false;
                    break;
                }else{
                    $this->error = true;
                }
            }

            //if validation succeeds without error | proceed to success
            if($this->error){
                $this->proceed = true;
            }
            return $this;
        }
        return $this;
    }


    /**
    * @param  function     var $response into function to get access to the params.
    * @return response     $response data on success call.
    */

    public function error($function)
    {
        if(!is_null($this->proceed)  && $this->proceed == false){
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
    }


    /**
    * @param  int/float  $response interger or float passer.
    * @param  string     $message string message "Invalid phone number".
    * @return json       Returns encoded JSON object of response and message
    */

    public function echoJson(int $response, string $message)
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
        switch($flag['filter']){
            case 'email':
                $type = filter_input($this->type, $flag['variable'], FILTER_VALIDATE_EMAIL);
                break;
                
            case 'int':
                $type = filter_input($this->type, $flag['variable'], FILTER_VALIDATE_INT);
                break;
                
            case 'float':
                $type = filter_input($this->type, $flag['variable'], FILTER_VALIDATE_FLOAT);
                break;
                
            case 'url':
                $type = filter_input($this->type, $flag['variable'], FILTER_VALIDATE_URL);
                break;

            default:
                $type = filter_input($this->type, $flag['variable'], FILTER_SANITIZE_STRING);
                if(empty($type))
                    $type = false;
                break;
        }
        return $type;
    }
    

    /**
    * @param  string $key    string like :string::name .
    * @return array          filter_type and param name.
    */

    private function getKeyPairs(string $key)
    {
        $flags = explode(":", $key);

        $flags['filter'] = $flags[0];
        $flags['variable'] = $flags[2];

        unset($flags[0]);
        unset($flags[1]);
        unset($flags[2]);
        return $flags;
    }


    /**
    * @param  string $type    string like POST or GET.
    * @return int            The value of request type.
    */

    private function getType($type)
    {
        $type = strtolower($type);
        switch($type){
            case 'post':
                $this->type = INPUT_POST;
                break;

            default:
                $this->type = INPUT_GET;
                break;
        }
        return $this->type;
    }

}