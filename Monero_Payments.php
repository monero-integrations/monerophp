<?php

/**
 *  Monero PHP API
 *  @author     SerHack
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
    function __construct ($ip = '127.0.0.1', $port, $host){
        $this->ip = $ip;
        $this->port = $port;
        // I need to implement a sort of validating http or https
        $this->url = $host'://'.$ip.':'.$port.'/json_rpc';
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
        $incoming_transfers = $this->_run('incoming_transfers', $incoming_parameters);
        $this->_print($incoming_transfers);
    }
  
    public function get_transfers($input_type, $input_value){
        $get_parameters = array($input_type => $input_value);
        $get_transfers = $this->_run('get_transfers', $get_parameters);
        $this->_print($get_transfers);
    }
     
     public function view_key(){
        $query_key = array('key_type' => 'view_key');
        $query_key_method = $this->_run('query_key', $query_key);
        $this->_print($query_key_method);
     }
     
        /* A payment id can be passed as a string
           A random payment id will be generatd if one is not given */
     public function make_integrated_address($payment_id){
        $integrate_address_parameters = array('payment_id' => $payment_id);
        $integrate_address_method = $this->_run('make_integrated_address', $integrate_address_parameters);
        $this->_print($integrate_address_method);
     }
     
    public function split_integrated_address($integrated_address){
        if(!isset($integrated_address)){
            echo "Error: Integrated_Address mustn't be null";
        }
        else{
        $split_params = array('integrated_address' => $integrated_address);
        $split_methods = $this->_run('split_integrated_address', $split_params);
        $this->_print($split_methods);
        }
    }
  
  public function make_uri($address, $amount, $recipient_name, $description){
        // If I pass 1, it will be 0.0000001 xmr. Then 
        $new_amount = $amount * 10000000;
       
         $uri_params = array('address' => $address, 'amount' => $new_amount, 'payment_id' => '', 'recipient_name' => $recipeint_name, 'tx_description' => $description);
        $uri = $this->_run('make_uri', $uri_params);
        $this->_print($uri);
    }
     
     
    public function parse_uri($uri){
         $uri_parameters = array('uri' => $uri);
        $uri_pars = $this->_run('parse_uri', $uri_parameters);
        $this->_print($uri_pars);
    }
     
    public function transfer($amount, $address){
        $new_amount = $amount * 1000000;
        $destinations = array('amount' => $new_amount, 'address' => $address);
        $transfer_parameters = array('destinations' => $destinations, 'mixin' => 4, 'get_tx_key' => true, 'unlock_time' => 0);
        $transfer_method = $this->_run('transfer', $transfer_parameters);
        $this->_print($transfer_method);
    }
  
  public function get_payments($payment_id){
   $get_payments_parameters = array('payment_id' => $payment_id);
   $get_payments = $this->_run('get_payments', $get_payments_parameters);
   $this->_print($get_payments);
  }
  
     public function get_bulk_payments($payment_id, $min_block_height){
      $get_bulk_payments_parameters = array('payment_id' => $payment_id, 'min_block_height' => $min_block_height);
      $get_bulk_payments = $this->_run('get_bulk_payments', $get_bulk_payments_parameters);
      $this->print($get_bulk_payments);
 }
 
