<?php
/**
 * 
 * monerophp/daemonRPC
 * 
 * A class for making calls to Monero's RPC API using PHP.
 * https://github.com/monero-integrations/monerophp
 *
 * Using work from
 *   CryptoChangements (Daemon_RPC) <https://github.com/cryptochangements34>
 *   Serhack (Monero Integrations) <https://github/serhack>
 *   TheKoziTwo (xmr-integration) <thekozitwo@gmail.com>
 *   Andrew LeCody (EasyBitcoin-PHP)
 *   Kacper Rowinski (jsonRPCClient) <krowinski@implix.com>
 * 
 * @author     Monero Integrations Team <https://github.com/monero-integrations>
 * @copyright  2018
 * @license    MIT
 *  
 * ============================================================================
 * 
 * // Initialize Monero connection/object
 * $daemonRPC = new daemonRPC();
 * 
 * // Examples:
 * $height = $daemonRPC->getblockcount();
 * $block = $daemonRPC->getblock_by_height(1);
 * 
 */

require_once('jsonRPCClient.php');

class daemonRPC {
  private $client;

  private $protocol;
  private $host;
  private $port;
  private $url;
  private $user;
  private $password;

  /**
   *
   * Start a connection with the Monero daemon
   * 
   * @param  string  $host      IP address of Monero daemon to connect to  (optional)
   * @param  int     $port      Port to use when accessing Monero daemon   (optional)
   * @param  string  $protocol  Protocol to acces daemon over (eg. 'http') (optional)
   * @param  string  $user      Username                                   (optional)
   * @param  string  $password  Password                                   (optional)
   *
   */
  function __construct($host = '127.0.0.1', $port = 18081, $protocol = 'http', $user = null, $password = null) {
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
   * Execute command on the Monero RPC API
   *
   * @param  string  $method  RPC method to call
   * @param  string  $params  Options to include (optional)
   *
   * @return string  Call result
   *
   */
  protected function _run($method, $params = null) {
    // TODO input validation
    
    return $this->client->_run($method, $params);
  }

  /**
   *
   * Look up how many blocks are in the longest chain known to the node.
   *
   * @return object  Example: {  
   *   "count": 993163,  
   *   "status": "OK"  
   * }  
   *
   */
  public function getblockcount() {
    return $this->_run('getblockcount');
  }

  /**
   *
   * Retrieve general information about the state of your node and the network.
   *
   * @return object  Example: {
   *   "alt_blocks_count": 5,
   *   "difficulty": 972165250,
   *   "grey_peerlist_size": 2280,
   *   "height": 993145,
   *   "incoming_connections_count": 0,
   *   "outgoing_connections_count": 8,
   *   "status": "OK",
   *   "target": 60,
   *   "target_height": 993137,
   *   "testnet": false,
   *   "top_block_hash": "",
   *   "tx_count": 564287,
   *   "tx_pool_size": 45,
   *   "white_peerlist_size": 529
   * }
   *
   */
  public function get_info() {
    return $this->_run('get_info');
  }

  /**
   *
   * Look up a block's hash by its height
   *
   * @return string  Example: 'e22cf75f39ae720e8b71b3d120a5ac03f0db50bba6379e2850975b4859190bc6'
   *
   */
  public function on_getblockhash() {
    return $this->_run('on_getblockhash');
  }

  /**
   *
   * Retrieve a block template that can be mined upon
   *
   * @param  string  $wallet_address  Address of wallet to receive coinbase transactions if block is successfully mined
   * @param  int     $reserve_size   Reserve size 
   *
   * @return object  Example: {
   *   "blocktemplate_blob": "01029af88cb70568b84a11dc9406ace9e635918ca03b008f7728b9726b327c1b482a98d81ed83000000000018bd03c01ffcfcf3c0493d7cec7020278dfc296544f139394e5e045fcda1ba2cca5b69b39c9ddc90b7e0de859fdebdc80e8eda1ba01029c5d518ce3cc4de26364059eadc8220a3f52edabdaf025a9bff4eec8b6b50e3d8080dd9da417021e642d07a8c33fbe497054cfea9c760ab4068d31532ff0fbb543a7856a9b78ee80c0f9decfae01023ef3a7182cb0c260732e7828606052a0645d3686d7a03ce3da091dbb2b75e5955f01ad2af83bce0d823bf3dbbed01ab219250eb36098c62cbb6aa2976936848bae53023c00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000001f12d7c87346d6b84e17680082d9b4a1d84e36dd01bd2c7f3b3893478a8d88fb3",
   *   "difficulty": 982540729,
   *   "height": 993231,
   *   "prev_hash": "68b84a11dc9406ace9e635918ca03b008f7728b9726b327c1b482a98d81ed830",
   *   "reserved_offset": 246,
   *   "status": "OK"
   * }
   *
   */
  public function getblocktemplate($wallet_address, $reserve_size) {
    // TODO full input validation
    
    if (!isset($wallet_address)) {
      throw new Exception('Error: Wallet address required');
    }
    if (!isset($reserve_size)) {
      throw new Exception('Error: Reserve size required');
    }
       
    $params = array('wallet_address' => $wallet_address, 'reserve_size' => $reserve_size);

    return $this->client->_run('getblocktemplate', $params);
  }

  /**
   *
   * Look up information regarding hard fork voting and readiness.
   *
   * @return object  Example: {
   *   "earliest_height": 1009827,
   *   "enabled": false,
   *   "state": 2,
   *   "status": "OK",
   *   "threshold": 0,
   *   "version": 1,
   *   "votes": 7277,
   *   "voting": 2,
   *   "window": 10080
   * }
   *
   */
  public function hardfork_info() {
    return $this->_run('hard_fork_info');
  }

  /**
   *
   * Block header information for the most recent block is easily retrieved with this method.
   *
   * @return object  Example: {
   *   "block_header": {
   *     "depth": 0,
   *     "difficulty": 746963928,
   *     "hash": "ac0f1e226268d45c99a16202fdcb730d8f7b36ea5e5b4a565b1ba1a8fc252eb0",
   *     "height": 990793,
   *     "major_version": 1,
   *     "minor_version": 1,
   *     "nonce": 1550,
   *     "orphan_status": false,
   *     "prev_hash": "386575e3b0b004ed8d458dbd31bff0fe37b280339937f971e06df33f8589b75c",
   *     "reward": 6856609225169,
   *     "timestamp": 1457589942
   *   },
   *   "status": "OK"
   * }
   *
   */
  public function getlastblockheader() {
    return $this->_run('getlastblockheader');
  }

  /**
   *
   * Block header information can be retrieved using either a block's hash or height.
   *
   * @param  string  $hash  The block's SHA256 hash
   *
   * @return object  Example: {
   *   "block_header": {
   *     "depth": 78376,
   *     "difficulty": 815625611,
   *     "hash": "e22cf75f39ae720e8b71b3d120a5ac03f0db50bba6379e2850975b4859190bc6",
   *     "height": 912345,
   *     "major_version": 1,
   *     "minor_version": 2,
   *     "nonce": 1646,
   *     "orphan_status": false,
   *     "prev_hash": "b61c58b2e0be53fad5ef9d9731a55e8a81d972b8d90ed07c04fd37ca6403ff78",
   *     "reward": 7388968946286,
   *     "timestamp": 1452793716
   *   },
   *   "status": "OK"
   * }
   *
   */
  public function getblockheaderbyhash($hash) {
    // TODO input validation
    
    return $this->_run('getlastblockheader', $hash);
  }

  /**
   *
   * Get block information by its SHA256 hash
   *
   * @param  string  The block's SHA256 hash
   *
   * @return object  Example: {
   *   "blob": "...",
   *   "block_header": {
   *     "depth": 12,
   *     "difficulty": 964985344,
   *     "hash": "510ee3c4e14330a7b96e883c323a60ebd1b5556ac1262d0bc03c24a3b785516f",
   *     "height": 993056,
   *     "major_version": 1,
   *     "minor_version": 2,
   *     "nonce": 2036,
   *     "orphan_status": false,
   *     "prev_hash": "0ea4af6547c05c965afc8df6d31509ff3105dc7ae6b10172521d77e09711fd6d",
   *     "reward": 6932043647005,
   *     "timestamp": 1457720227
   *   },
   *   "json": "...",
   *   "status": "OK"
   * }
   *
   */
  public function getblock_by_hash($hash) {
    // TODO input validation
    
    return $this->_run('getblock', $hash);
  }

  /**
   *
   * Get block information by its height
   *
   * @param  int     $height  The block's height
   *
   * @return object  Example: {
   *   "blob": "...",
   *   "block_header": {
   *     "depth": 80694,
   *     "difficulty": 815625611,
   *     "hash": "e22cf75f39ae720e8b71b3d120a5ac03f0db50bba6379e2850975b4859190bc6",
   *     "height": 912345,
   *     "major_version": 1,
   *     "minor_version": 2,
   *     "nonce": 1646,
   *     "orphan_status": false,
   *     "prev_hash": "b61c58b2e0be53fad5ef9d9731a55e8a81d972b8d90ed07c04fd37ca6403ff78",
   *     "reward": 7388968946286,
   *     "timestamp": 1452793716
   *   },
   *   "json": "...",
   *   "status": "OK"
   * }
   *
   */
  public function getblock_by_height($height) {
    // TODO input validation
    
    $heightString = (string) $height; // Cast input to string
    return $this->_run('getblock', $heightString);
  }

  /**
   *
   * Get list of banned IPs
   *
   * @return object  Example: {
   *   "bans": [{
   *     "ip": 838969536,
   *     "seconds": 1457748792
   *   }],
   *   "status": "OK"
   * }
   *
   */
  public function getbans() {
    return $this->run('getbans');
  }

}