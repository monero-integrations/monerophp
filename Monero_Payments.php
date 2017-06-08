<?php

/**
 *  Monero PHP API
 *  @author     Nicolò Altamura
 *  @version    0.1
 *  @year       2017
 *  
 */
 
 /* WORK IN PROGRESS */
 
 class Monero_Payments{
    private $url;
    private $client; 
    private $ip;
    private $port;
    
     /**
     *  Start the connection with daemon
     *  @param  $ip   IP of Monero RPC
     *  @param  $port Port of Monero RPC
     */
    function __construct ($ip = '127.0.0.1', $port){
        $this->ip = $ip;
        $this->port = $port;
        // I need to implement a sort of validating http or https
        $this->url = 'http://'.$ip.':'.$port.'/json_rpc';
        $this->client = new jsonRPCClient($this->url);
     }
     
     /*
      * Run a method or method + parameters
      * @param  $method   Name of Method
      */
     private function _run($method,$params = null) {
      $result = $this->client->$method(json_encode($params,JSON_UNESCAPED_SLASHES));
       return $result;
    }
    
    /**
     * Print json (for api)
     * @return $json
     */
     private function _print($json){
        $json_parsed = json_encode($json,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo $json_parsed.;
    }
  
   /**
    * Print Monero Address as JSON Array
    */
    public function address(){
        $address = $this->_run('getaddress');
        $this->_print($address);
    }
  
  }
    
    
    
     
     
     
 
