<?php
/**
 * 
 * monerophp/walletRPC
 * 
 * A class for making calls to monero-wallet-rpc using PHP.
 * https://github.com/monero-integrations/monerophp
 *
 * Using work from
 *   CryptoChangements (Monero_RPC) <https://github.com/cryptochangements34>
 *   Serhack (Monero Integrations) <https://github/serhack>
 *   TheKoziTwo (xmr-integration) <thekozitwo@gmail.com>
 *   Kacper Rowinski (jsonRPCClient) <krowinski@implix.com>
 * 
 * @author     Monero Integrations Team <https://github.com/monero-integrations>
 * @copyright  2018
 * @license    MIT
 *  
 * ============================================================================
 * 
 * // Initialize Monero connection/object
 * $walletRPC = new walletRPC();
 * 
 * // Examples:
 * $address = $walletRPC->getaddress();
 * $walletRPC->sign('The Times 03/Jan/2009 Chancellor on brink of second bailout for banks');
 * 
 */

require_once('jsonRPCClient.php');

class walletRPC {
  private $client;

  private $protocol; 
  private $host;
  private $port;
  private $url;
  private $user;
  private $password;
  
  /**
   *
   * Start a connection with monero-wallet-rpc
   *
   * @param  string  $host      IP address of monero-wallet-rpc to connect to  (optional)
   * @param  int     $port      Port to use when accessing monero-wallet-rpc   (optional)
   * @param  string  $protocol  Protocol to acces daemon over (eg. 'http')     (optional)
   * @param  string  $user      Username                                       (optional)
   * @param  string  $password  Password                                       (optional)
   *
   */
  function __construct ($host = '127.0.0.1', $port = '18081', $protocol = 'http', $user = null, $password = null) {
    // TODO input validation
    
    $this->host = $host;
    $this->port = $port;
    $this->protocol = $protocol; // TODO: validate protocol (http, https, etc.)
    $this->user = $user;
    $this->password = $password;

    $this->url = $protocol.'://'.$host.':'.$port.'/json_rpc';
    $this->client = new jsonRPCClient($this->url, $this->user, $this->password);
  }
   
  /**
   *
   * Execute command on the monero-wallet-rpc API
   *
   * @param  string  $method  RPC method to call
   * @param  string  $params  Parameters to include with call  (optional)
   *
   * @return string  Call result
   *
   */
  private function _run($method, $params = null) {
    // TODO input validation

    $result = $this->client->_run($method, $params);
    return $result;
  }
  
  /**
   *
   * Convert between moneroj and tacoshi (piconero)
   *
   * @param  string  $amount  Monero amount to convert to tacoshi (piconero)
   *
   * @return string  Call result
   *
   */
  private function _transform($amount) {
    // TODO input validation

    // Convert from moneroj to tacoshi (piconero)
    $new_amount = $amount * 100000000;
    return $new_amount;
  }
    
  /**
   *
   * Print JSON object (for API)
   *
   * @param  object  $json  JSON object to print
   *
   */
  public function _print($json) {
    // TODO input validation

    $json_parsed = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    echo $json_parsed;
  }
  
  /**
   *
   * Look up wallet address
   *
   * @return object  Example: {
   *   "address": "427ZuEhNJQRXoyJAeEoBaNW56ScQaLXyyQWgxeRL9KgAUhVzkvfiELZV7fCPBuuB2CGuJiWFQjhnhhwiH1FsHYGQGaDsaBA"
   * }
   *
   */
  public function getaddress() {
    return $this->_run('getaddress');
  }
  
  /**
   *
   * Look up wallet balance
   *
   * @return object  Example: {
   *   "balance": 140000000000,
   *   "unlocked_balance": 50000000000
   * }
   *
   */
  public function getbalance() {
    return $this->_run('getbalance');
  }
  
  /**
   *
   * Look up current height of wallet
   *
   * @return object  Example: {
   *   "height": 994310
   * }
   *
   */
  public function getheight() {
    return $this->_run('getheight');
  }
  
  /**
   *
   * Look up transfers
   *
   * @param  string  $input_type   Transfer type; must be 'in', 'out', 'pending', 'failed', 'pool', 'filter_by_height', 'min_height', or 'max_height'
   * @param  string  $input_value  Input value of above
   *
   * @return object  Example: {
   *   "pool": [{
   *     "amount": 500000000000,
   *     "fee": 0,
   *     "height": 0,
   *     "note": "",
   *     "payment_id": "758d9b225fda7b7f",
   *     "timestamp": 1488312467,
   *     "txid": "da7301d5423efa09fabacb720002e978d114ff2db6a1546f8b820644a1b96208",
   *     "type": "pool"
   *   }]
   * }
   *
   */
  public function get_transfers($input_type, $input_value) {
    // TODO input validation

    $get_parameters = array($input_type => $input_value);
    $get_transfers = $this->_run('get_transfers', $get_parameters);
    return $get_transfers;
  }
  
  /**
   *
   * Look up incoming transfers
   *
   * @param  string  $type  Type of transfer to look up; must be 'all', 'available', or 'unavailable' (incoming transfers which have already been spent)
   *
   * @return object  Example: {
   *   "transfers": [{
   *     "amount": 10000000000000,
   *     "global_index": 711506,
   *     "spent": false,
   *     "tx_hash": "&lt;c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1&gt;",
   *     "tx_size": 5870
   *   },{
   *     "amount": 300000000000,
   *     "global_index": 794232,
   *     "spent": false,
   *     "tx_hash": "&lt;c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1&gt;",
   *     "tx_size": 5870
   *   },{
   *     "amount": 50000000000,
   *     "global_index": 213659,
   *     "spent": false,
   *     "tx_hash": "&lt;c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1&gt;",
   *     "tx_size": 5870
   *   }]
   * }
   */
  public function incoming_transfers($type = 'all') {
    // TODO input validation

    $incoming_parameters = array('transfer_type' => $type);
    $incoming_transfers = $this->_run('incoming_transfers', $incoming_parameters);
    return $incoming_transfers;
  }
  
  /**
   *
   * Look up wallet view key
   *
   * @return object  Example: {
   *   "key": "7e341d..."
   * }
   *
   */
  public function view_key() {
    $query_key = array('key_type' => 'view_key');
    $query_key_method = $this->_run('query_key', $query_key);
    return $query_key_method;
  }
  
  /**
   *
   * Look up wallet spend key
   *
   * @return object  Example: {
   *   "key": "2ab810..."
   * }
   *
   */
  public function spend_key() {
    $query_key = array('key_type' => 'spend_key');
    $query_key_method = $this->_run('query_key', $query_key);
    return $query_key_method;
  }
  
  /**
   *
   * Make an integrated address from the wallet address and a payment ID
   *
   * @param  string  $payment_id  Payment ID to use when generating an integrated address (optional)
   *
   * @return object  Example: {
   *   "integrated_address": "4BpEv3WrufwXoyJAeEoBaNW56ScQaLXyyQWgxeRL9KgAUhVzkvfiELZV7fCPBuuB2CGuJiWFQjhnhhwiH1FsHYGQQ8H2RRJveAtUeiFs6J"
   * }
   *
   */
  public function make_integrated_address($payment_id = null) {
    // TODO input validation

    $integrate_address_parameters = array('payment_id' => $payment_id);
    $integrate_address_method = $this->_run('make_integrated_address', $integrate_address_parameters);
    return $integrate_address_method;
  }
  
  /**
   *
   * Retrieve the standard address and payment ID corresponding to an integrated address.
   *
   * @param  string  $integrated_address  Integrated address to split
   *
   * @return object  Example: {
   *   "payment_id": "&lt;420fa29b2d9a49f5&gt;",
   *   "standard_address": "427ZuEhNJQRXoyJAeEoBaNW56ScQaLXyyQWgxeRL9KgAUhVzkvfiELZV7fCPBuuB2CGuJiWFQjhnhhwiH1FsHYGQGaDsaBA"
   * }
   *
   */
  public function split_integrated_address($integrated_address) {
    // TODO full input validation
    
    if (!isset($integrated_address)) {
      throw new Exception('Error: Integrated address required');
    }

    $split_params = array('integrated_address' => $integrated_address);
    $split_methods = $this->_run('split_integrated_address', $split_params);
    return $split_methods;
  }

  /**
   *
   * Create a payment URI using the official URI spec.
   *
   * @param  string  $address         Address to include
   * @param  string  $amount          Amount to request
   * @param  string  $recipient_name  Name of recipient    (optional)
   * @param  string  #description     Payment description  (optional)
   *
   * @return object  Example: 
   *
   */
  public function make_uri($address, $amount, $recipient_name = null, $description = null) {
    // TODO full input validation
    
    if (!isset($address)) {
      throw new Exception('Error: Address required');
    }
    if (!isset($amount)) {
      throw new Exception('Error: Amount required');
    }

    // Convert from moneroj to tacoshi (piconero)
    $new_amount = $amount * 1000000000000;
       
    $uri_params = array('address' => $address, 'amount' => $new_amount, 'payment_id' => '', 'recipient_name' => $recipient_name, 'tx_description' => $description);
    $uri = $this->_run('make_uri', $uri_params);
    return $uri;
  }

  /**
   *
   * Parse a payment URI to get payment information.
   *
   * @param  string  $uri  Payment URI
   *
   * @return object  Example: {
   *   "uri": {
   *     "address": "44AFFq5kSiGBoZ4NMDwYtN18obc8AemS33DBLWs3H7otXft3XjrpDtQGv7SqSsaBYBb98uNbr2VBBEt7f2wfn3RVGQBEP3A",
   *     "amount": 10,
   *     "payment_id": "0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef",
   *     "recipient_name": "Monero Project donation address",
   *     "tx_description": "Testing out the make_uri function."
   *   }
   * }
   *
   */
  public function parse_uri($uri) {
    // TODO input validation
    
    if (!isset($uri)) {
      throw new Exception('Error: Payment URI required');
    }

    $uri_parameters = array('uri' => $uri);
    $parsed_uri = $this->_run('parse_uri', $uri_parameters);
    return $parsed_uri;
  }
  
  /**
   *
   * Send monero to a number of recipients.
   *
   * @param  string  $amount   Amount to transfer
   * @param  string  $address  Address to transfer to
   * @param  string  $mixin    Mixin number            (optional)
   *
   * @return object  Example: {
   *   "fee": 48958481211,
   *   "tx_hash": "985180f468637bc6d2f72ee054e1e34b8d5097988bb29a2e0cb763e4464db23c",
   *   "tx_key": "8d62e5637f1fcc9a8904057d6bed6c697618507b193e956f77c31ce662b2ee07"
   * }
   *
   */
  public function transfer($amount, $address, $mixin = 4) {
    // TODO full input validation
    
    if (!isset($amount)) {
      throw new Exception('Error: Amount required');
    }
    if (!isset($address)) {
      throw new Exception('Error: Address required');
    }
    
    // Convert from moneroj to tacoshi (piconero)
    $new_amount = $amount  * 1000000000000;

    $destinations = array('amount' => $new_amount, 'address' => $address);
    $transfer_parameters = array('destinations' => array($destinations), 'mixin' => $mixin, 'get_tx_key' => true, 'unlock_time' => 0, 'payment_id' => '');
    $transfer_method = $this->_run('transfer', $transfer_parameters);
    return $transfer_method;
  }
  
  /**
   *
   * Get a list of incoming payments using a given payment id.
   *
   * @param  string  $payment_id  Payment ID to look up
   *
   * @return object  Example: {
   *   "payments": [{
   *     "amount": 10350000000000,
   *     "block_height": 994327,
   *     "payment_id": "4279257e0a20608e25dba8744949c9e1caff4fcdafc7d5362ecf14225f3d9030",
   *     "tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1",
   *     "unlock_time": 0
   *   }]
   * }
   *
   */
  public function get_payments($payment_id) {
    // TODO input validation

    $get_payments_parameters = array('payment_id' => $payment_id);
    $get_payments = $this->_run('get_payments', $get_payments_parameters);
    return $get_payments;
  }
  
  /**
   *
   * Get a list of incoming payments using a given payment ID (or a list of payments IDs) from a given height.
   *
   * @param  string  $payment_id        Payment ID to look up
   * @param  string  $min_block_height  Height to begin search
   *
   * @return object  Example: {
   *   "payments": [{
   *     "amount": 10350000000000,
   *     "block_height": 994327,
   *     "payment_id": "4279257e0a20608e25dba8744949c9e1caff4fcdafc7d5362ecf14225f3d9030",
   *     "tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1",
   *     "unlock_time": 0
   *   }]
   * }
   *
   */
  public function get_bulk_payments($payment_id, $min_block_height) {
    // TODO input validation

    $get_bulk_payments_parameters = array('payment_id' => $payment_id, 'min_block_height' => $min_block_height);
    $get_bulk_payments = $this->_run('get_bulk_payments', $get_bulk_payments_parameters);
    return $get_bulk_payments;
  }
  
  /**
   *
   * Show information about a transfer with a given transaction ID.
   *
   * @param  string  $txid  Transaction ID to look up
   *
   * @return object  Example: {
   *   "transfer": {
   *     "amount": 10000000000000,
   *     "fee": 0,
   *     "height": 1316388,
   *     "note": "",
   *     "payment_id": "0000000000000000",
   *     "timestamp": 1495539310,
   *     "txid": "f2d33ba969a09941c6671e6dfe7e9456e5f686eca72c1a94a3e63ac6d7f27baf",
   *     "type": "in"
   *   }
   * }
   *
   */
  public function get_transfer_by_txid($txid) {
    // TODO input validation

    $get_transfer_by_txid_parameters = array('txid' => $txid);
    $get_transfer_by_txid = $this->_run('get_transfer_by_txid', $get_transfer_by_txid_parameters);
    return $get_transfer_by_txid;
  }

  
  /**
   *
   * Rescan blockchain from scratch.
   *
   */
  public function rescan_blockchain() {
    return $this->_run('rescan_blockchain');
  }
  
  /**
   *
   * Create a new wallet
   *
   * @param  string  $filename  Filename to use for new wallet
   * @param  string  $password  Password to use for new wallet
   *
   */
  public function create_wallet($filename = 'monero_wallet', $password = null) {
    // TODO test "You need to have set the argument "–wallet-dir" when launching monero-wallet-rpc to make this work."
    $create_wallet_parameters = array('filename' => $filename, 'password' => $password, 'language' => 'English');
    $create_wallet_method = $this->_run('create_wallet', $create_wallet_parameters);
    return $create_wallet_method;
  }
  
  /**
   *
   * Open a wallet.
   *
   * @param  string  $filename  Filename to use for new wallet
   * @param  string  $password  Password to use for new wallet
   *
   * @return object  Example: 
   *
   */
  public function open_wallet($filename = 'monero_wallet', $password = null) {
    // TODO test "You need to have set the argument "–wallet-dir" when launching monero-wallet-rpc to make this work."
    $open_wallet_parameters = array('filename' => $filename, 'password' => $password);
    $open_wallet_method = $this->_run('open_wallet',$open_wallet_parameters);
    return $open_wallet_method;
  }
  
  /**
   *
   * Sign a string
   *
   * @param  string  $data  Data to sign
   *
   * @return object  Example: {
   *   "signature": "SigV1Xp61ZkGguxSCHpkYEVw9eaWfRfSoAf36PCsSCApx4DUrKWHEqM9CdNwjeuhJii6LHDVDFxvTPijFsj3L8NDQp1TV"
   * }
   *
   */
  public function sign($data) {
    // TODO input validation
    
    $sign_parameters = array('string' => $data);
    $sign_method = $this->_run('sign',$sign_parameters);
    return $sign_method;
  }
  
}

?>
