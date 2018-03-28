<?php
/**
 * 
 * monerophp/walletRPC
 * 
 * A class for making calls to monero-wallet-rpc using PHP
 * https://github.com/monero-integrations/monerophp
 *
 * Using work from
 *   CryptoChangements [Monero_RPC] <bW9uZXJv@gmail.com> (https://github.com/cryptochangements34)
 *   Serhack [Monero Integrations] <nico@serhack.me> (https://serhack.me)
 *   TheKoziTwo [xmr-integration] <thekozitwo@gmail.com>
 *   Kacper Rowinski [jsonRPCClient] <krowinski@implix.com>
 * 
 * @author     Monero Integrations Team <support@monerointegrations.com> (https://github.com/monero-integrations)
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

class walletRPC
{
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
  function __construct ($host = '127.0.0.1', $port = '18080', $protocol = 'http', $user = null, $password = null)
  {
    // TODO input validation
    
    $this->host = $host;
    $this->port = $port;
    $this->protocol = $protocol;
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
  private function _run($method, $params = null)
  {
    $result = $this->client->_run($method, $params);
    return $result;
  }
    
  /**
   *
   * Print JSON object (for API)
   *
   * @param  object  $json  JSON object to print
   *
   */
  public function _print($json)
  {

    $json_parsed = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    echo $json_parsed;
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
  public function get_balance()
  {
    return $this->_run('get_balance');
  }
  
  /**
   *
   * Alias of get_balance()
   *
   * @return object  Example: {
   *   "balance": 140000000000,
   *   "unlocked_balance": 50000000000
   * }
   *
   */
  public function getbalance()
  {
    return $this->_run('getbalance');
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
  public function get_address()
  {
    return $this->_run('get_address');
  }
  
  /**
   *
   * Alias of get_address()
   *
   * @return object  Example: {
   *   "address": "427ZuEhNJQRXoyJAeEoBaNW56ScQaLXyyQWgxeRL9KgAUhVzkvfiELZV7fCPBuuB2CGuJiWFQjhnhhwiH1FsHYGQGaDsaBA"
   * }
   *
   */
  public function getaddress()
  {
    return $this->_run('getaddress');
  }
  
  /**
   *
   * Create a new subaddress
   *
   * @param  number  $account_index  The subaddress account index
   * @param  string  $label          The label to use
   *
   * @return object  Example: {
   *   "address": "Bh3ttLbjGFnVGCeGJF1HgVh4DfCaBNpDt7PQAgsC2GFug7WKskgfbTmB6e7UupyiijiHDQPmDC7wSCo9eLoGgbAFJQaAaDS"
   *   "address_index": 1
   * }
   *
   */
  public function create_address($account_index = 0, $label = '')
  {
    $create_address_parameters = array('account_index' => $account_index ,'label' => $label);
    $create_address_method = $this->_run('create_address', $create_address_parameters);

    $save = $this->store(); // Save wallet state after transfer

    return $create_address_method;
  }
  
  /**
   *
   * Label a subaddress
   *
   * @param  number  The subaddress index to label
   * @param  string  The label to use
   *
   * @return none
   *
   */
  public function label_address($index, $label)
  {
    if (!isset($index)) {
      throw new Exception('Error: Subaddress index required');
    }
    if (!isset($label)) {
      throw new Exception('Error: Label required');
    }

    $label_address_parameters = array('index' => $index ,'label' => $label);
    return $this->_run('label_address', $label_address_parameters);
  }

  /**
   *
   * Get current accounts from wallet
   *
   * @param  none
   *
   * @return object  Example: {
   *   "subaddress_accounts": {
   *     "0": {
   *       "account_index": 0,
   *       "balance": 2808597352948771,
   *       "base_address": "A2XE6ArhRkVZqepY2DQ5QpW8p8P2dhDQLhPJ9scSkW6q9aYUHhrhXVvE8sjg7vHRx2HnRv53zLQH4ATSiHHrDzcSFqHpARF",
   *       "label": "Primary account",
   *       "tag": "",
   *       "unlocked_balance": 2717153096298162
   *     },
   *     "1": {
   *       "account_index": 1,
   *       "balance": 0,
   *       "base_address": "BcXKsfrvffKYVoNGN4HUFfaruAMRdk5DrLZDmJBnYgXrTFrXyudn81xMj7rsmU5P9dX56kRZGqSaigUxUYoaFETo9gfDKx5",
   *       "label": "Secondary account",
   *       "tag": "",
   *       "unlocked_balance": 0 )
   *    },
   *    "total_balance": 2808597352948771,
   *    "total_unlocked_balance": 2717153096298162
   * }
   *
   */
  public function get_accounts()
  {
    return $this->_run('get_accounts');
  }
  
  /**
   *
   * Create a new sub-account from your wallet
   *
   * @param string A label for the account
   *
   */
  public function create_account($label)
  {
    $create_account_parameters = array('label' => $label);
    return $this->_run('create_account', $create_account_parameters);
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function label_account()
  {
    return $this->_run('label_account');
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function get_account_tags()
  {
    return $this->_run('get_account_tags');
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function tag_accounts()
  {
    return $this->_run('tag_accounts');
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function untag_accounts()
  {
    return $this->_run('untag_accounts');
  }
  
  /**
   *
   * 
   * 
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function set_account_tag_description()
  {
    return $this->_run('set_account_tag_description');
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
  public function getheight()
  {
    return $this->_run('getheight');
  }

  /**
   *
   * Send monero to a number of recipients.  Parameters can be passed in individually (as listed below) or as an array (as listed at bottom.)  If multiple destinations are required, use the array format and use
   * 
   * @param  string  $amount       Amount to transfer
   * @param  string  $address      Address to transfer to
   * @param  number  $mixin        Mixin number                                (optional)
   * @param  number  $index        Account to send from                        (optional)
   * @param  number  $priority     Payment ID                                  (optional)
   * @param  string  $pid          Payment ID                                  (optional)
   * @param  number  $unlock_time  UNIX time or block height to unlock output  (optional)
   * 
   *   OR
   * 
   * @param  object  $params        Array containing any of the options listed above, where only amount and address are required
   *
   * @return object  Example: {
   *   "amount": "1000000000000",
   *   "fee": "1000020000",
   *   "tx_hash": "c60a64ddae46154a75af65544f73a7064911289a7760be8fb5390cb57c06f2db",
   *   "tx_key": "805abdb3882d9440b6c80490c2d6b95a79dbc6d1b05e514131a91768e8040b04"
   * }
   *
   */
  public function transfer($amount, $address = '', $mixin = 6, $index = 0, $priority = 2, $pid = '', $unlock_time = 0)
  {
    if (is_array($amount)) { // Parameters passed in as object
      $params = $amount;

      if (array_key_exists('destinations', $params)) {
        $destinations = $params['destinations'];

        foreach ($destinations as $key => $amount) {
          if ($key == 'amount') {
            // Convert from moneroj to tacoshi (piconero)
            $destinations[$key] = $amount * 1000000000000;
          }
        }
      } else {
        if (array_key_exists('amount', $params)) {
          $amount = $params['amount'];
        } else {
          throw new Exception('Error: Amount required');
        }
        if (array_key_exists('address', $params)) {
          $address = $params['address'];
        } else {
          throw new Exception('Error: Address required');
        }
    
        // Convert from moneroj to tacoshi (piconero)
        $new_amount = $amount  * 1000000000000;

        $destinations = array('amount' => $new_amount, 'address' => $address);
      }
      if (array_key_exists('mixin', $params)) {
        $mixin = $params['mixin'];
      }
      if (array_key_exists('index', $params)) {
        $index = $params['index'];
      }
      if (array_key_exists('priority', $params)) {
        $priority = $params['priority'];
      }
      if (array_key_exists('pid', $params)) {
        $pid = $params['pid'];
      }
      if (array_key_exists('unlock_time', $params)) {
        $unlock_time = $params['unlock_time'];
      }
      if (array_key_exists('do_not_relay', $params)) {
        $do_not_relay = $params['do_not_relay'];
      }
    } else { // Legacy parameters used
      if (!isset($amount)) {
        throw new Exception('Error: Amount required');
      }
      if (!isset($address) || !$address) {
        throw new Exception('Error: Address required');
      }
    
      // Convert from moneroj to tacoshi (piconero)
      $new_amount = $amount  * 1000000000000;

      $destinations = array('amount' => $new_amount, 'address' => $address);
    }

    $transfer_parameters = array('destinations' => array($destinations), 'mixin' => $mixin, 'get_tx_key' => true);
    if (isset($index)) {
      if ($index) {
        $transfer_parameters['index'] = $index;
      }
    }
    if (isset($pid)) {
      if ($pid) {
        $transfer_parameters['payment_id'] = $pid;
      }
    }
    if (isset($priority)) {
      if ($priority) {
        $transfer_parameters['priority'] = $priority;
      }
    }
    if (isset($do_not_relay)) {
      if ($do_not_relay) {
        $transfer_parameters['do_not_relay'] = $do_not_relay;
      }
    }

    $transfer_method = $this->_run('transfer', $transfer_parameters);

    $save = $this->store(); // Save wallet state after transfer

    return $transfer_method;
  }
  
  /**
   *
   * Same as transfer, but splits transfer into more than one transaction if necessary
   *
   */
  public function transfer_split($amount, $address = '', $mixin = 6, $index = 0, $priority = 2, $pid = '', $unlock_time = 0)
  {
    if (is_array($amount)) { // Parameters passed in as object
      $params = $amount;

      if (array_key_exists('destinations', $params)) {
        $destinations = $params['destinations'];

        foreach ($destinations as $destination => $recipient) {
          if (!array_key_exists('amount', $destinations[$destination])) {
            throw new Exception('Error: Amount required for each destination');
          }
          if (!array_key_exists('address', $destinations[$destination])) {
            throw new Exception('Error: Address required for each destination');
          }

          // Convert from moneroj to tacoshi (piconero)
          $destinations[$destination]['amount'] = $destinations[$destination]['amount'] * 1000000000000;
        }
      } else {
        if (array_key_exists('amount', $params)) {
          $amount = $params['amount'];
        } else {
          throw new Exception('Error: Amount required');
        }
        if (array_key_exists('address', $params)) {
          $address = $params['address'];
        } else {
          throw new Exception('Error: Address required');
        }
    
        // Convert from moneroj to tacoshi (piconero)
        $new_amount = $amount * 1000000000000;

        $destinations = array('amount' => $new_amount, 'address' => $address);
      }
      if (array_key_exists('mixin', $params)) {
        $mixin = $params['mixin'];
      }
      if (array_key_exists('index', $params)) {
        $index = $params['index'];
      }
      if (array_key_exists('priority', $params)) {
        $priority = $params['priority'];
      }
      if (array_key_exists('pid', $params)) {
        $pid = $params['pid'];
      }
      if (array_key_exists('unlock_time', $params)) {
        $unlock_time = $params['unlock_time'];
      }
      if (array_key_exists('unlock_time', $params)) {
        $unlock_time = $params['unlock_time'];
      }
      if (array_key_exists('do_not_relay', $params)) {
        $do_not_relay = $params['do_not_relay'];
      }
    } else { // Legacy parameters used
      if (!isset($amount)) {
        throw new Exception('Error: Amount required');
      }
      if (!isset($address) || !$address) {
        throw new Exception('Error: Address required');
      }
    
      // Convert from moneroj to tacoshi (piconero)
      $new_amount = $amount * 1000000000000;

      $destinations = array('amount' => $new_amount, 'address' => $address);
    }

    $transfer_parameters = array('destinations' => array($destinations), 'mixin' => $mixin, 'get_tx_key' => true);
    if (isset($index)) {
      if ($index) {
        $transfer_parameters['index'] = $index;
      }
    }
    if (isset($pid)) {
      if ($pid) {
        $transfer_parameters['payment_id'] = $pid;
      }
    }
    if (isset($priority)) {
      if ($priority) {
        $transfer_parameters['priority'] = $priority;
      }
    }
    if (isset($unlock_time)) {
      if ($unlock_time) {
        $transfer_parameters['unlock_time'] = $unlock_time;
      }
    }
    if (isset($do_not_relay)) {
      if ($do_not_relay) {
        $transfer_parameters['do_not_relay'] = $do_not_relay;
      }
    }

    $transfer_method = $this->_run('transfer_split', $transfer_parameters);

    $save = $this->store(); // Save wallet state after transfer

    return $transfer_method;
  }
  
  /**
   *
   * Send all dust outputs back to the wallet's, to make them easier to spend (and mix)
   *
   */
  public function sweep_dust()
  {
    return $this->_run('sweep_dust');
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function sweep_unmixable()
  {
    return $this->_run('sweep_unmixable');
  }
  
  /**
   *
   * Send all unlocked balance to an address
   * 
   * @param  string  $address       Address to transfer to
   * @param  number  $below_amount  Only send outputs below this amount         (optional)
   * @param  number  $mixin         Mixin number                                (optional)
   * @param  number  $index         Account to send from                        (optional)
   * @param  number  $priority      Payment ID                                  (optional)
   * @param  string  $pid           Payment ID                                  (optional)
   * @param  number  $unlock_time   UNIX time or block height to unlock output  (optional)
   * 
   *   OR
   * 
   * @param  object  $params        Array containing any of the options listed above, where only amount and address are required
   *
   * @return object  Example: {
   *   "amount": "1000000000000",
   *   "fee": "1000020000",
   *   "tx_hash": "c60a64ddae46154a75af65544f73a7064911289a7760be8fb5390cb57c06f2db",
   *   "tx_key": "805abdb3882d9440b6c80490c2d6b95a79dbc6d1b05e514131a91768e8040b04"
   * }
   *
   */
  public function sweep_all($address, $below_amount = 0, $mixin = 6, $index = 0, $priority = 2, $pid = '', $unlock_time = 0)
  {
    if (is_array($address)) { // Parameters passed in as object
      $params = $address;

      if (array_key_exists('address', $params)) {
        $address = $params['address'];
      } else {
        throw new Exception('Error: Address required');
      }

      if (array_key_exists('below_amount', $params)) {
        $below_amount = $params['below_amount'];

        // Convert from moneroj to tacoshi (piconero)
        $new_below_amount = $below_amount * 1000000000000;
      }
      if (array_key_exists('mixin', $params)) {
        $mixin = $params['mixin'];
      }
      if (array_key_exists('index', $params)) {
        $index = $params['index'];
      }
      if (array_key_exists('priority', $params)) {
        $priority = $params['priority'];
      }
      if (array_key_exists('pid', $params)) {
        $pid = $params['pid'];
      }
      if (array_key_exists('unlock_time', $params)) {
        $unlock_time = $params['unlock_time'];
      }
      if (array_key_exists('unlock_time', $params)) {
        $unlock_time = $params['unlock_time'];
      }
      if (array_key_exists('do_not_relay', $params)) {
        $do_not_relay = $params['do_not_relay'];
      }
    } else { // Legacy parameters used
      if (!isset($address) || !$address) {
        throw new Exception('Error: Address required');
      }

      // Convert from moneroj to tacoshi (piconero)
      $new_below_amount = $below_amount * 1000000000000;
    }

    $transfer_parameters = array('address' => $address, 'mixin' => $mixin, 'get_tx_key' => true);
    if (isset($new_below_amount)) {
      if ($new_below_amount) {
        $transfer_parameters['below_amount'] = $new_below_amount;
      }
    }
    if (isset($index)) {
      if ($index) {
        $transfer_parameters['index'] = $index;
      }
    }
    if (isset($pid)) {
      if ($pid) {
        $transfer_parameters['payment_id'] = $pid;
      }
    }
    if (isset($priority)) {
      if ($priority) {
        $transfer_parameters['priority'] = $priority;
      }
    }
    if (isset($unlock_time)) {
      if ($unlock_time) {
        $transfer_parameters['unlock_time'] = $unlock_time;
      }
    }
    if (isset($do_not_relay)) {
      if ($do_not_relay) {
        $transfer_parameters['do_not_relay'] = $do_not_relay;
      }
    }

    $sweep_all_method = $this->_run('sweep_all', $transfer_parameters);

    $save = $this->store(); // Save wallet state after transfer

    return $sweep_all_method;
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function sweep_single()
  {
    return $this->_run('sweep_single');
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function relay_tx()
  {
    return $this->_run('relay_tx');
  }
  
  /**
   *
   * Save wallet
   *
   */
  public function store()
  {
    return $this->_run('store');
  }
  
  /**
   *
   * Get a list of incoming payments using a given payment id
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
  public function get_payments($payment_id)
  {
    if (!isset($payment_id)) {
      throw new Exception('Error: Payment ID required');
    }

    $get_payments_parameters = array('payment_id' => $payment_id);
    return $this->_run('get_payments', $get_payments_parameters);
  }
  
  /**
   *
   * Get a list of incoming payments using a given payment ID (or a list of payments IDs) from a given height
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
  public function get_bulk_payments($payment_id, $min_block_height)
  {
    if (!isset($payment_id)) {
      throw new Exception('Error: Payment ID required');
    }
    if (!isset($min_block_height)) {
      throw new Exception('Error: Minimum block height required');
    }

    $get_bulk_payments_parameters = array('payment_id' => $payment_id, 'min_block_height' => $min_block_height);
    return $this->_run('get_bulk_payments', $get_bulk_payments_parameters);
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
  public function incoming_transfers($type = 'all')
  {
    $incoming_parameters = array('transfer_type' => $type);
    return $this->_run('incoming_transfers', $incoming_parameters);
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
  public function view_key()
  {
    $query_key = array('key_type' => 'view_key');
    return $this->_run('query_key', $query_key);
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
  public function spend_key()
  {
    $query_key = array('key_type' => 'spend_key');
    return $this->_run('query_key', $query_key);
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
  public function mnemonic()
  {
    $query_key = array('key_type' => 'mnemonic');
    return $this->_run('query_key', $query_key);
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
  public function make_integrated_address($payment_id = null)
  {
    $integrate_address_parameters = array('payment_id' => $payment_id);
    return $this->_run('make_integrated_address', $integrate_address_parameters);
  }
  
  /**
   *
   * Retrieve the standard address and payment ID corresponding to an integrated address
   *
   * @param  string  $integrated_address  Integrated address to split
   *
   * @return object  Example: {
   *   "payment_id": "&lt;420fa29b2d9a49f5&gt;",
   *   "standard_address": "427ZuEhNJQRXoyJAeEoBaNW56ScQaLXyyQWgxeRL9KgAUhVzkvfiELZV7fCPBuuB2CGuJiWFQjhnhhwiH1FsHYGQGaDsaBA"
   * }
   *
   */
  public function split_integrated_address($integrated_address)
  {
    if (!isset($integrated_address)) {
      throw new Exception('Error: Integrated address required');
    }
    
    if (!isset($integrated_address)) {
      throw new Exception('Error: Integrated address required');
    }

    $split_parameters = array('integrated_address' => $integrated_address);
    return $this->_run('split_integrated_address', $split_parameters);
  }
  
  /**
   *
   * Stop the wallet, saving the state
   *
   */
  public function stop_wallet()
  {
    return $this->_run('stop_wallet');
  }
  
  /**
   *
   * Rescan blockchain from scratch
   *
   */
  public function rescan_blockchain()
  {
    return $this->_run('rescan_blockchain');
  }
  
  /**
   *
   * Set arbitrary string notes for transactions
   *
   * @param  array  $txids  Array of transaction IDs (strings) to apply notes to
   * @param  array  $notes  Array of notes (strings) to add 
   *
   */
  public function set_tx_notes($txids, $notes)
  {
    if (!isset($txids)) {
      throw new Exception('Error: Transaction IDs required');
    }
    if (!isset($notes)) {
      throw new Exception('Error: Notes required');
    }

    $notes_parameters = array('txids' => $txids, 'notes' => $notes);

    return $this->_run('set_tx_notes', $notes_parameters);
  }
  
  /**
   *
   * Get string notes for transactions
   *
   * @param  array  $txids  Array of transaction IDs (strings) to look up
   *
   */
  public function get_tx_notes($txids)
  {
    if (!isset($txids)) {
      throw new Exception('Error: Transaction IDs required');
    }

    $notes_parameters = array('txids' => $txids);

    return $this->_run('get_tx_notes', $notes_parameters);
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function set_attribute()
  {
    return $this->_run('set_attribute');
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function get_attribute()
  {
    return $this->_run('get_attribute');
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function get_tx_key()
  {
    return $this->_run('get_tx_key');
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function check_tx_key()
  {
    return $this->_run('check_tx_key');
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function get_tx_proof()
  {
    return $this->_run('get_tx_proof');
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function check_tx_proof()
  {
    return $this->_run('check_tx_proof');
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function get_spend_proof()
  {
    return $this->_run('get_spend_proof');
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function check_spend_proof()
  {
    return $this->_run('check_spend_proof');
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function get_reserve_proof()
  {
    return $this->_run('get_reserve_proof');
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function check_reserve_proof()
  {
    return $this->_run('check_reserve_proof');
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
  public function get_transfers($input_type, $input_value)
  {
    if (!isset($input_type)) {
      throw new Exception('Error: Input type required');
    }
    if (!isset($input_value)) {
      throw new Exception('Error: Input value required');
    }

    $get_parameters = array($input_type => $input_value);
    return $this->_run('get_transfers', $get_parameters);
  }
  
  /**
   *
   * Show information about a transfer with a given transaction ID
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
  public function get_transfer_by_txid($txid)
  {
    if (!isset($txid)) {
      throw new Exception('Error: TX ID required');
    }

    $get_transfer_by_txid_parameters = array('txid' => $txid);
    return $this->_run('get_transfer_by_txid', $get_transfer_by_txid_parameters);
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
  public function sign($data)
  {
    if (!isset($data)) {
      throw new Exception('Error: Data to sign required');
    }
    
    $sign_parameters = array('string' => $data);
    return $this->_run('sign',$sign_parameters);
  }
  
  /**
   *
   * Verify a signature on a string
   *
   * @param  string   $data       Signed data
   * @param  string   $address    Address that signed data
   * @param  string   $signature  Signature to verify
   *
   * @return boolean  $good       Verification status
   * 
   */
  public function verify($data, $address, $signature)
  {
    if (!isset($data)) {
      throw new Exception('Error: Signed data required');
    }
    if (!isset($address)) {
      throw new Exception('Error: Signing address required');
    }
    if (!isset($signature)) {
      throw new Exception('Error: Signature required');
    }

    $notes_parameters = array('data' => $data, 'address' => $address, 'signature' => $signature);

    return $this->_run('verify', $notes_parameters);
  }
  
  /**
   *
   * Export a signed set of key images
   *
   * @return  array  $signed_key_images  Array of signed key images
   *
   */
  public function export_key_images()
  {
    return $this->_run('export_key_images');
  }
  
  /**
   *
   * Import a signed set of key images
   *
   * @param  array   $signed_key_images  Array of signed key images
   *
   * @return number  $height
   * @return number  $spent
   * @return number  $unspent
   * 
   */
  public function import_key_images($signed_key_images)
  {
    if (!isset($signed_key_images)) {
      throw new Exception('Error: Signed key images required');
    }

    $import_parameters = array('signed_key_images' => $signed_key_images);

    return $this->_run('import_key_images', $import_parameters);
  }

  /**
   *
   * Create a payment URI using the official URI spec
   *
   * @param  string  $address         Address to include
   * @param  string  $amount          Amount to request
   * @param  string  $recipient_name  Name of recipient    (optional)
   * @param  string  #description     Payment description  (optional)
   *
   * @return object  Example: 
   *
   */
  public function make_uri($address, $amount, $recipient_name = null, $description = null)
  {
    if (!isset($address)) {
      throw new Exception('Error: Address required');
    }
    if (!isset($amount)) {
      throw new Exception('Error: Amount required');
    }

    // Convert from moneroj to tacoshi (piconero)
    $new_amount = $amount * 1000000000000;
       
    $uri_parameters = array('address' => $address, 'amount' => $new_amount, 'payment_id' => '', 'recipient_name' => $recipient_name, 'tx_description' => $description);
    return $this->_run('make_uri', $uri_parameters);
  }

  /**
   *
   * Parse a payment URI to get payment information
   *
   * @param  string  $uri  Payment URI
   *
   * @return object  Example: {
   *   "uri": {
   *     "address": "44AFFq5kSiGBoZ4NMDwYtN18obc8AemS33DBLWs3H7otXft3XjrpDtQGv7SqSsaBYBb98uNbr2VBBEt7f2wfn3RVGQBEP3A",
   *     "amount": 10,
   *     "payment_id": "0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef",
   *     "recipient_name": "Monero Project donation address",
   *     "tx_description": "Testing out the make_uri function
   "
   *   }
   * }
   *
   */
  public function parse_uri($uri)
  {
    if (!isset($uri)) {
      throw new Exception('Error: Payment URI required');
    }

    $uri_parameters = array('uri' => $uri);
    return $this->_run('parse_uri', $uri_parameters);
  }
  
  /**
   *
   * Retrieve entries from the address book
   *
   * @param  array   $entries  Array of indices to return from the address book
   *
   * @return array   $entries  Array of entries returned from the address book
   * 
   */
  public function get_address_book($entries)
  {
    if (!isset($entries)) {
      throw new Exception('Error: Entry indices required');
    }

    $entries_parameters = array('entries' => $entries);

    return $this->_run('get_address_book', $entries_parameters);
  }
  
  /**
   *
   * Retrieve entries from the address book
   *
   * @param  string  $address      Address to add to address book
   * @param  string  $payment_id   Payment ID to use with address in address book (optional)
   * @param  string  $description  Description of address                         (optional)
   *
   * @return number  $index        Index of address in address book
   * 
   */
  public function add_address_book($address, $payment_id, $description)
  {
    if (!isset($address)) {
      throw new Exception('Error: Address required');
    }
    if (isset($payment_id)) {
      if ($payment_id) {
        $transfer_parameters['payment_id'] = $payment_id;
      }
    }
    if (isset($description)) {
      if ($description) {
        $transfer_parameters['description'] = $description;
      }
    }

    $address_parameters = array('address' => $address);

    return $this->_run('add_address_book', $address_parameters);
  }
  
  /**
   *
   * Delete an entry from the address book
   *
   * @param  array   $index  Index of the address book entry to remove
   * 
   */
  public function delete_address_book($index)
  {
    if (!isset($index)) {
      throw new Exception('Error: Entry index required');
    }

    $delete_parameters = array('index' => $index);

    return $this->_run('delete_address_book', $delete_parameters);
  }
  
  /**
   *
   * Rescan the blockchain for spent outputs
   * 
   */
  public function rescan_spent()
  {
    return $this->_run('rescan_spent');
  }
  
  /**
   *
   * Start mining in the Monero daemon
   *
   * @param  number   $threads_count         Number of threads with which to mine
   * @param  boolean  $do_background_mining  Mine in backgound?
   * @param  boolean  $ignore_battery        Ignore battery?  
   * 
   */
  public function start_mining($threads_count, $do_background_mining, $ignore_battery)
  {
    if (!isset($threads_count)) {
      throw new Exception('Error: Threads required');
    }
    if (!isset($do_background_mining)) {
      throw new Exception('Error: Background mining boolean required');
    }
    if (!isset($ignore_battery)) {
      throw new Exception('Error: Inore battery boolean required');
    }

    $mining_parameters = array('threads_count' => $threads_count, 'do_background_mining' => $do_background_mining, 'ignore_battery' => $ignore_battery);

    return $this->_run('start_mining', $mining_parameters);
  }
  
  /**
   *
   * Stop mining
   * 
   */
  public function stop_mining()
  {
    return $this->_run('stop_mining');
  }
  
  /**
   *
   * Get a list of available languages for your wallet's seed
   * 
   * @return array  List of available languages
   *
   */
  public function get_languages()
  {
    return $this->_run('get_languages');
  }
  
  /**
   *
   * Create a new wallet
   *
   * @param  string  $filename  Filename to use for new wallet
   * @param  string  $password  Password to use for new wallet
   *
   */
  public function create_wallet($filename = 'monero_wallet', $password = null)
  {
    $create_wallet_parameters = array('filename' => $filename, 'password' => $password, 'language' => 'English');
    return $this->_run('create_wallet', $create_wallet_parameters);
  }
  
  /**
   *
   * Open a wallet
   *
   * @param  string  $filename  Filename to use for new wallet
   * @param  string  $password  Password to use for new wallet
   *
   * @return object  Example: 
   *
   */
  public function open_wallet($filename = 'monero_wallet', $password = null)
  {
    $open_wallet_parameters = array('filename' => $filename, 'password' => $password);
    return $this->_run('open_wallet',$open_wallet_parameters);
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function is_multisig()
  {
    return $this->_run('is_multisig');
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function prepare_multisig()
  {
    return $this->_run('prepare_multisig');
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function make_multisig()
  {
    return $this->_run('make_multisig');
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function export_multisig_info()
  {
    return $this->_run('export_multisig_info');
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function import_multisig_info()
  {
    return $this->_run('import_multisig_info');
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function finalize_multisig()
  {
    return $this->_run('finalize_multisig');
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function sign_multisig()
  {
    return $this->_run('sign_multisig');
  }
  
  /**
   *
   * 
   *
   * @param 
   *
   * @return   Example: {
   * }
   *
   */
  public function submit_multisig()
  {
    return $this->_run('submit_multisig');
  }

}

?>
