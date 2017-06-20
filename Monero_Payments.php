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
      $result = $this->client->_run($method, $params);
       return $result;
    }
    
    /**
     * Print json (for api)
     * @return $json
     */
     private function _print($json){
        $json_parsed = json_encode($json,  JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        echo $json_parsed;
    }
  
   /**
    * Print Monero Address as JSON Array
    */
    public function address(){
        $address = $this->_run('getaddress');
        $this->_print($address);
    }
  
   /*
    * Print Monero Balance as JSON Array
    */
    public function getbalance(){
         $balance = $this->_run('getbalance');
         $this->_print($balance);
  }
  
  /*
    * Print Monero Height as JSON Array
    */
    public function getheight(){
         $height = $this->_run('getheight');
         $this->_print($height);
  }
  
     /*
     * Incoming Transfer
     * $type must be All 
     */
   public function incoming_transfer($type){
        $incoming_parameters = array('transfer_type' => $type);
        $incoming_transfer = $this->_run('incoming_transfer', $incoming_parameters);
        $this->_print($incoming_transfer);
    }
  
    public function get_transfers(){
        $get_parameters = array('pool' => true);
        $get_transfers = $this->_run('get_transfers', $get_parameters);
        $this->_print($get_transfers);
    }
     
     public function view_key(){
    $query_key = array('key_type' => 'view_key');
    $query_key_method = $this->_run('query_key', $query_key);
    $this->_print($query_key_method);
     }
     
     public function make_integrated_address($payment_id){
         if(isset($payment_id)){
             $integrate_address_parameters = array('payment_id' => '');
         }
         else{
             $integrate_address_parameters = array('payment_id' => $payment_id);
         }
        $integrate_address_method = $this->_run('make_integrated_address', $integrate_address_parameters);
        $this->_print($integrate_address_method);
     }
     
    public function split_integrated_address($integrated_address){
        if(isset($integrated_address)){
            echo "Error: Integrated_Address mustn't be null";
        }
        else{
        $split_params = array('integrated_address' => $integrated_address);
        $split_methods = $this->_run('split_integrated_address', $split_params);
        $this->_print($split_methods);
        }
    }
 }
 
