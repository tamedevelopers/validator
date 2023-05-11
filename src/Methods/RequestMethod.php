<?php

declare(strict_types=1);

namespace UltimateValidator;

class RequestMethod {
  
    private $request;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct(?string $key = null) 
    {
        $this->request  = $this->request();
    }

    /**
     * The value of request type.
     * 
     * @return int  
     */
    private function request()
    {
        // get form request type
        $type = strtoupper((string) $_SERVER['REQUEST_METHOD']);

        switch(strtoupper($type)){
            case 'GET':
                $request = INPUT_GET;
                break;
                
            case 'COOKIE':
                $request = INPUT_COOKIE;
                break;
                
            case 'SERVER':
                $request = INPUT_SERVER;
                break;
                
            case 'ENV':
                $request = INPUT_ENV;
                break;
                
            default:
                $request = INPUT_POST;
                break;
        }

        return $request;
    }

    /**
     * GET Request Data From Server
     * @param string $key \Optional
     * - $key => Array data key value if accesible or returns entire data 
     * 
     * @return mixed\RequestMethod\get
     */
    public function get(?string $key = null)
    {
        return $this->request === INPUT_GET
                        ? $_GET[$key] ?? $_GET
                        : null;
    }

    /**
     * POST Request Data From Server
     * @param string $key \Optional
     * - $key => Array data key value if accesible or returns entire data 
     * 
     * @return mixed\RequestMethod\post
     */
    public function post(?string $key = null)
    {
        return $this->request === INPUT_POST
                        ? $_POST[$key] ?? $_POST
                        : null;
    }

    /**
     * COOKIE Request Data From Server
     * @param string $key \Optional
     * - $key => Array data key value if accesible or returns entire data 
     * 
     * @return mixed\RequestMethod\cookie
     */
    public function cookie(?string $key = null)
    {
        return $this->request === INPUT_COOKIE
                        ? $_COOKIE[$key] ?? $_COOKIE
                        : null;
    }

    /**
     * SERVER Request Data From Server
     * @param string $key \Optional
     * - $key => Array data key value if accesible or returns entire data 
     * 
     * @return mixed\RequestMethod\server
     */
    public function server(?string $key = null)
    {
        return $this->request === INPUT_SERVER
                        ? $_SERVER[$key] ?? $_SERVER
                        : null;
    }

    /**
     * ENV Request Data From Server
     * @param string $key \Optional
     * - $key => Array data key value if accesible or returns entire data 
     * 
     * @return mixed\RequestMethod\env
     */
    public function env(?string $key = null)
    {
        return $this->request === INPUT_ENV
                        ? $_ENV[$key] ?? $_ENV
                        : null;
    }

    /**
     * All Request Data From Server
     * @param string $key \Optional
     * - $key => Array data key value if accesible or returns entire data 
     * 
     * @return mixed\RequestMethod\all
     */
    public function all(?string $key = null)
    {
        return $_REQUEST[$key] ?? $_REQUEST ?? null;
    }

    
}