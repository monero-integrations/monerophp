<?php
/**
 *
 * monerophp/walletRPC
 *
 * A class for making calls to monero-wallet-rpc using PHP
 * https://github.com/monero-integrations/monerophp
 *
 * Using work from
 *	 CryptoChangements [Monero_RPC] <bW9uZXJv@gmail.com> (https://github.com/cryptochangements34)
 *	 Serhack [Monero Integrations] <nico@serhack.me> (https://serhack.me)
 *	 TheKoziTwo [xmr-integration] <thekozitwo@gmail.com>
 *	 Kacper Rowinski [jsonRPCClient] <krowinski@implix.com>
 *
 * @author		 Monero Integrations Team <support@monerointegrations.com> (https://github.com/monero-integrations)
 * @copyright	 2018
 * @license		 MIT
 *
 * ============================================================================
 *
 * // See example.php for more examples
 *
 * // Initialize class
 * $walletRPC = new walletRPC();
 *
 * // Examples:
 * $address = $walletRPC->get_address();
 * $signed = $walletRPC->sign('The Times 03/Jan/2009 Chancellor on brink of second bailout for banks');
 *
 */

namespace MoneroIntegrations\MoneroPhp;

use Exception;

class walletRPC
{
	private $client;

	private $protocol;
	private $url;

	/**
	 *
	 * Start a connection with the Monero wallet RPC interface (monero-wallet-rpc)
	 *
	 * @param	string	$host		monero-wallet-rpc hostname					(optional)
	 * @param	int		$port		monero-wallet-rpc port						(optional)
	 * @param	string	$protocol	monero-wallet-rpc protocol (eg. 'http')		(optional)
	 * @param	string	$user		monero-wallet-rpc username					(optional)
	 * @param	string	$password	monero-wallet-rpc passphrase				(optional)
	 *
	 */
	function __construct(
		private readonly string $host = '127.0.0.1',
		private readonly int $port = 18081,
		private readonly bool $check_SSL = true,
		private readonly ?string $user = null,
		private readonly ?string $password = null
	) {
		$this->protocol = $SSL ? 'https' : 'http';
		
		$this->url = "$this->protocol://$host:$port/";
		
		$this->client = new jsonRPCClient($this->url, $this->user, $this->password, $this->check_SSL);
	}

	/**
	 *
	 * Execute command via jsonRPCClient
	 *
	 * @param	string	$method	RPC method to call
	 * @param	object	$params	Parameters to pass	(optional)
	 *
	 * @return	string	Call result
	 *
	 */
	private function _run(string $method, array $params = null, string $path = 'json_rpc') : string
	{
		$result = $this->client->_run($method, $params, $path);
		return $result;
	}

	/**
	 *
	 * Print JSON object (for API)
	 *
	 * @param	object	 $json	JSON object to print
	 *
	 * @return	none
	 *
	 */
	public function _print($json)
	{
		echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}

	/**
	 *
	 * Convert from moneroj to tacoshi (piconero)
	 *
	 * @param	number	 $amount	Amount (in monero) to transform to tacoshi (piconero)	 (optional)
	 *
	 * @return	number
	 *
	 */
	public function _transform($amount = 0)
	{
		return intval(bcmul($amount, 1000000000000));
	}

	/**
	 *
	 * Look up an account's balance
	 *
	 * @param	number	$account_index	Index of account to look up	(optional)
	 *
	 * @return object	 Example: {
	 *	 "balance": 140000000000,
	 *	 "unlocked_balance": 50000000000
	 * }
	 *
	 */
	public function get_balance(int $account_index = 0)
	{
		$params = ['account_index' => $account_index];
		return $this->_run('get_balance', $params);
	}

	/**
	 *
	 * Alias of get_balance()
	 * @deprecated	Start using get_balance, as it follows the naming convention (snake case) of monero wallet RPC methods
	 *
	 */
	public function getbalance(int $account_index = 0)
	{
		return $this->get_balance($account_index);
	}

	/**
	 *
	 * Look up wallet address(es)
	 *
	 * @param	number	$account_index	Index of account to look up		(optional)
	 * @param	number	$address_index	Index of subaddress to look up	(optional)
	 *
	 * @return object	 Example: {
	 *	 "address": "A2XE6ArhRkVZqepY2DQ5QpW8p8P2dhDQLhPJ9scSkW6q9aYUHhrhXVvE8sjg7vHRx2HnRv53zLQH4ATSiHHrDzcSFqHpARF",
	 *	 "addresses": [
	 *		 {
	 *			 "address": "A2XE6ArhRkVZqepY2DQ5QpW8p8P2dhDQLhPJ9scSkW6q9aYUHhrhXVvE8sjg7vHRx2HnRv53zLQH4ATSiHHrDzcSFqHpARF",
	 *			 "address_index": 0,
	 *			 "label": "Primary account",
	 *			 "used": true
	 *		 }, {
	 *			 "address": "Bh3ttLbjGFnVGCeGJF1HgVh4DfCaBNpDt7PQAgsC2GFug7WKskgfbTmB6e7UupyiijiHDQPmDC7wSCo9eLoGgbAFJQaAaDS",
	 *			 "address_index": 1,
	 *			 "label": "",
	 *			 "used": true
	 *		 }
	 *	 ]
	 * }
	 *
	 */
	public function get_address(int $account_index = 0, int $address_index = 0)
	{
		$params = ['account_index' => $account_index, 'address_index' => $address_index];
		return $this->_run('get_address', $params);
	}

	/**
	 * @param	string	$address Monero address
	 * @return	object	Example: {
	 * 	"index": {
	 * 	"major": 0,
	 * 	"minor": 1
	 * 	}
	 * }
	 */
	public function get_address_index(string $address)
	{
		$params = ['address' => $address];
		return $this->_run('get_address_index', $params);
	}

	/**
	 *
	 * Alias of get_address()
	 *
	 * @param	number	$account_index	Index of account to look up		(optional)
	 * @param	number	$address_index	Index of subaddress to look up	(optional)
	 *
	 * @return object	Example: {
	 *	"address": "427ZuEhNJQRXoyJAeEoBaNW56ScQaLXyyQWgxeRL9KgAUhVzkvfiELZV7fCPBuuB2CGuJiWFQjhnhhwiH1FsHYGQGaDsaBA"
	 * }
	 *
	 */
	public function getaddress(int $account_index = 0, int $address_index = 0)
	{
		return $this->get_address($account_index = 0, $address_index = 0);
	}

	/**
	 *
	 * Create a new subaddress
	 *
	 * @param	number	$account_index	The subaddress account index
	 * @param	string	$label			A label to apply to the new subaddress
	 *
	 * @return object	 Example: {
	 *	"address": "Bh3ttLbjGFnVGCeGJF1HgVh4DfCaBNpDt7PQAgsC2GFug7WKskgfbTmB6e7UupyiijiHDQPmDC7wSCo9eLoGgbAFJQaAaDS"
	 *	"address_index": 1
	 * }
	 *
	 */
	public function create_address(int $account_index = 0, string $label = '')
	{
		$params = ['account_index' => $account_index, 'label' => $label];
		$create_address_method = $this->_run('create_address', $params);

		$save = $this->store(); // Save wallet state after subaddress creation

		return $create_address_method;
	}

	/**
	 *
	 * Label a subaddress
	 *
	 * @param	number	The index of the subaddress to label
	 * @param	string	The label to apply
	 *
	 * @return none
	 *
	 */
	public function label_address(int $index, string $label)
	{
		$params = ['index' => $index ,'label' => $label];
		return $this->_run('label_address', $params);
	}

	/**
	 *
	 * Look up wallet accounts
	 *
	 * @param	string $tag Optional filtering by tag
	 *
	 * @return	object	Example: {
	 *	 "subaddress_accounts": {
	 *		 "0": {
	 *			 "account_index": 0,
	 *			 "balance": 2808597352948771,
	 *			 "base_address": "A2XE6ArhRkVZqepY2DQ5QpW8p8P2dhDQLhPJ9scSkW6q9aYUHhrhXVvE8sjg7vHRx2HnRv53zLQH4ATSiHHrDzcSFqHpARF",
	 *			 "label": "Primary account",
	 *			 "tag": "",
	 *			 "unlocked_balance": 2717153096298162
	 *		 },
	 *		 "1": {
	 *			 "account_index": 1,
	 *			 "balance": 0,
	 *			 "base_address": "BcXKsfrvffKYVoNGN4HUFfaruAMRdk5DrLZDmJBnYgXrTFrXyudn81xMj7rsmU5P9dX56kRZGqSaigUxUYoaFETo9gfDKx5",
	 *			 "label": "Secondary account",
	 *			 "tag": "",
	 *			 "unlocked_balance": 0
	 *		},
	 *		"total_balance": 2808597352948771,
	 *		"total_unlocked_balance": 2717153096298162
	 * }
	 *
	 */
	public function get_accounts(string $tag = null)
	{
		return (is_null($tag)) ? $this->_run('get_accounts') : $this->_run('get_accounts', ['tag' => $tag]);
	}

	/**
	 *
	 * Create a new account
	 *
	 * @param	string	$label	Label to apply to new account
	 *
	 * @return	none
	 *
	 */
	public function create_account(string $label = '')
	{
		$params = ['label' => $label];
		$create_account_method = $this->_run('create_account', $params);

		$save = $this->store(); // Save wallet state after account creation

		return $create_account_method;
	}

	/**
	 *
	 * Label an account
	 *
	 * @param	number $account_index	Index of account to label
	 * @param	string $label			Label to apply
	 *
	 * @return	none
	 *
	 */
	public function label_account(int $account_index, string $label)
	{
		$params = ['account_index' => $account_index, 'label' => $label];
		$label_account_method = $this->_run('label_account', $params);

		$save = $this->store(); // Save wallet state after account label

		return $label_account_method;
	}

	/**
	 *
	 * Look up account tags
	 *
	 * @param	 none
	 *
	 * @return object	 Example: {
	 *	"account_tags": {
	 *		"0": {
	 *			"accounts": {
	 *				"0": 0,
	 *				"1": 1
	 *			},
	 *			"label": "",
	 *			"tag": "Example tag"
	 *		}
	 *	}
	 * }
	 *
	 */
	public function get_account_tags()
	{
		return $this->_run('get_account_tags');
	}

	/**
	 *
	 * Tag accounts
	 *
	 * @param	 array	 $accounts	The indices of the accounts to tag
	 * @param	 string	 $tag				Tag to apply
	 *
	 * @return none
	 *
	 */
	public function tag_accounts(array $accounts, string $tag)
	{
		$params = ['accounts' => $accounts, 'tag' => $tag];
		$tag_accounts_method = $this->_run('tag_accounts', $params);

		$save = $this->store(); // Save wallet state after account tagging

		return $tag_accounts_method;
	}

	/**
	 *
	 * Untag accounts
	 *
	 * @param	array	$accounts	The indices of the accounts to untag
	 *
	 * @return	none
	 *
	 */
	public function untag_accounts(array $accounts)
	{
		$params = ['accounts' => $accounts];
		$untag_accounts_method = $this->_run('untag_accounts', $params);

		$save = $this->store(); // Save wallet state after untagging accounts

		return $untag_accounts_method;
	}

	/**
	 *
	 * Describe a tag
	 *
	 * @param	string	$tag					Tag to describe
	 * @param	string	$description	Description to apply to tag
	 *
	 * @return object	Example: {
	 *	// TODO example
	 * }
	 *
	 */
	public function set_account_tag_description(string $tag, string $description)
	{
		$params = ['tag' => $tag, 'description' => $description];
		$set_account_tag_description_method = $this->_run('set_account_tag_description', $params);

		$save = $this->store(); // Save wallet state after describing tag

		return $set_account_tag_description_method;
	}

	/**
	 *
	 * Look up how many blocks are in the longest chain known to the wallet
	 *
	 * @param	none
	 *
	 * @return object	Example: {
	 *	 "height": 994310
	 * }
	 *
	 */
	public function get_height()
	{
		return $this->_run('get_height');
	}

	/**
	 *
	 * Alias of get_height()
	 * @deprecated	Start using get_height, as it follows the naming convention (snake case) of monero wallet RPC methods
	 *
	 */
	public function getheight()
	{
		return $this->get_height();
	}

	/**
	 *
	 * Send monero
	 * Parameters can be passed in individually (as listed below) or as an object/dictionary (as listed at bottom)
	 * To send to multiple recipients, use the object/dictionary (bottom) format and pass an array of recipient addresses and amount arrays in the destinations field (as in "destinations = [['amount' => 1, 'address' => ...], ['amount' => 2, 'address' => ...]]")
	 *
	 * @param	string		$amount				Amount of monero to send
	 * @param	string		$address			Address to receive funds
	 * @param	string		$payment_id			Payment ID													(optional)
	 * @param	number		$mixin				Mixin number (ringsize - 1)									(optional)
	 * @param	number		$account_index		Account to send from										(optional)
	 * @param	string		$subaddr_indices	Comma-separated list of subaddress indices to spend from	(optional)
	 * @param	number		$priority			Transaction priority										(optional)
	 * @param	number		$unlock_time		UNIX time or block height to unlock output					(optional)
	 * @param	boolean		$do_not_relay		Do not relay transaction									(optional)
	 *
	 * 
	 * @return object	Example: {
	 *	"amount": "1000000000000",
	 *	"fee": "1000020000",
	 *	"tx_hash": "c60a64ddae46154a75af65544f73a7064911289a7760be8fb5390cb57c06f2db",
	 *	"tx_key": "805abdb3882d9440b6c80490c2d6b95a79dbc6d1b05e514131a91768e8040b04"
	 * }
	 *
	 */
	public function transfer(int $amount, string $address, string $payment_id = '', int $mixin = 15, int $account_index = 0, string $subaddr_indices = '', int $priority = 2, int $unlock_time = 0, bool $do_not_relay = false, int $ringsize = 11)
	{
		$destinations = [['amount' => $this->_transform($amount), 'address' => $address]];

		$params = ['destinations' => $destinations,
		           'mixin' => $mixin,
		           'get_tx_key' => true,
		           'account_index' => $account_index, 
		           'subaddr_indices' => $subaddr_indices,
		           'priority' => $priority,
		           'do_not_relay' => $do_not_relay, 
		           'ringsize' => $ringsize];

		$transfer_method = $this->_run('transfer', $params);

		$save = $this->store(); // Save wallet state after transfer

		return $transfer_method;
	}

	/**
	 *
	 * Same as transfer, but splits transfer into more than one transaction if necessary
	 *
	 */
	public function transfer_split(int $amount, string $address, string $payment_id = '', int $mixin = 15, int $account_index = 0, string $subaddr_indices = '', int $priority = 2, int $unlock_time = 0, bool $do_not_relay = false)
	{
		$destinations = [['amount' => $this->_transform($amount), 'address' => $address]];

		$params = ['destinations' => $destinations,
		           'mixin' => $mixin,
		           'get_tx_key' => true,
		           'account_index' => $account_index,
		           'subaddr_indices' => $subaddr_indices,
		           'payment_id' => $payment_id,
		           'priority' => $priority,
		           'unlock_time' => $unlock_time,
		           'do_not_relay' => $do_not_relay];

		$transfer_method = $this->_run('transfer_split', $params);

		$save = $this->store(); // Save wallet state after transfer

		return $transfer_method;
	}

	/**
	 *
	 * Send all dust outputs back to the wallet
	 *
	 * @param	none
	 *
	 * @return object	 Example: {
	 *	 // TODO example
	 * }
	 *
	 */
	public function sweep_dust()
	{
		return $this->_run('sweep_dust');
	}

	/**
	 *
	 * Send all unmixable outputs back to the wallet
	 *
	 * @param	 none
	 *
	 * @return object	 Example: {
	 * 	"id": "0",
	 * 	"jsonrpc": "2.0",
	 * 	"result": {
	 * 		"multisig_txset": "",
	 * 		"unsigned_txset": ""
	 * 	}
	 * }
	 *
	 */
	public function sweep_unmixable()
	{
		return $this->_run('sweep_unmixable');
	}

	/**
	 *
	 * Send all unlocked outputs from an account to an address
	 *
	 * @param	string	$address			Address to receive funds
	 * @param	string	$subaddr_indices	Comma-separated list of subaddress indices to sweep	(optional)
	 * @param	number	$account_index		Index of the account to sweep						(optional)
	 * @param	string	$payment_id			Payment ID											(optional)
	 * @param	number	$mixin				Mixin number (ringsize - 1)							(optional)
	 * @param	number	$priority			Payment ID											(optional)
	 * @param	number	$below_amount		Only send outputs below this amount					(optional)
	 * @param	number	$unlock_time		UNIX time or block height to unlock output			(optional)
	 * @param	boolean	$do_not_relay		Do not relay transaction							(optional)
	 *
	 * @return object	Example: {
	 *	 "amount": "1000000000000",
	 *	 "fee": "1000020000",
	 *	 "tx_hash": "c60a64ddae46154a75af65544f73a7064911289a7760be8fb5390cb57c06f2db",
	 *	 "tx_key": "805abdb3882d9440b6c80490c2d6b95a79dbc6d1b05e514131a91768e8040b04"
	 * }
	 *
	 */
	public function sweep_all(string $address, string $subaddr_indices = '', int $account_index = 0, string $payment_id = '', int $mixin = 15, int $priority = 2, int $below_amount = 0, int $unlock_time = 0, bool $do_not_relay = false)
	{

		$params = [
			'address' => $address,
			'mixin' => $mixin,
			'get_tx_key' => true,
			'subaddr_indices' => $subaddr_indices,
			'account_index' => $account_index,
			'payment_id' => $payment_id,
			'priority' => $priority,
			'below_amount' => $this->_transform($below_amount),
			'unlock_time' => $unlock_time,
			'do_not_relay' => $do_not_relay
		];

		$sweep_all_method = $this->_run('sweep_all', $params);

		$save = $this->store(); // Save wallet state after transfer

		return $sweep_all_method;
	}

	/**
	 *
	 * Sweep a single key image to an address
	 *
	 * @param	string	$key_image		Key image to sweep
	 * @param	string	$address		Address to receive funds
	 * @param	string	$payment_id		Payment ID									(optional)
	 * @param	number	$below_amount	Only send outputs below this amount			(optional)
	 * @param	number	$mixin			Mixin number (ringsize - 1)					(optional)
	 * @param	number	$priority		Payment ID									(optional)
	 * @param	number	$unlock_time	UNIX time or block height to unlock output	(optional)
	 * @param	boolean	$do_not_relay	Do not relay transaction					(optional)
	 *
	 * @return	object	Example: {
	 * 	"amount": "1000000000000",
	 * 	"fee": "1000020000",
	 * 	"tx_hash": "c60a64ddae46154a75af65544f73a7064911289a7760be8fb5390cb57c06f2db",
	 * 	"tx_key": "805abdb3882d9440b6c80490c2d6b95a79dbc6d1b05e514131a91768e8040b04"
	 * }
	 *
	 */
	public function sweep_single(string $key_image, string $address, string $payment_id = '', int $mixin = 15, int $priority = 2, int $below_amount = 0, int $unlock_time = 0, int $do_not_relay = 0)
	{
		$params = [
			'address' => $address,
			'mixin' => $mixin,
			'get_tx_key' => true,
			'account_index' => $account_index,
			'payment_id' => $payment_id,
			'priority' => $priority,
			'below_amount' => $this->_transform($below_amount),
			'unlock_time' => $unlock_time,
			'do_not_relay' => $do_not_relay
		];

		$sweep_single_method = $this->_run('sweep_single', $params);

		$save = $this->store(); // Save wallet state after transfer

		return $sweep_single_method;
	}

	/**
	 *
	 * Relay a transaction
	 *
	 * @param	string	$hex	Blob of transaction to relay
	 *
	 * @return	object	Example: {
	 * 	"id": "0",
	 * 	"jsonrpc": "2.0",
	 * 	"result": {
	 * 		"tx_hash": "1c42dcc5672bb09bccf33fb1e9ab4a498af59a6dbd33b3d0cfb289b9e0e25fa5"
	 * 	}
	 * }
	 *
	 */
	public function relay_tx(string $hex)
	{
		$params = ['hex' => $hex];
		$relay_tx_method = $this->_run('relay_tx_method', $params);

		$save = $this->store(); // Save wallet state after transaction relay

		return $this->_run('relay_tx');
	}

	/**
	 *
	 * Save wallet
	 *
	 * @param	none
	 *
	 * @return	object	Example:
	 *
	 */
	public function store()
	{
		return $this->_run('store');
	}

	/**
	 *
	 * Look up incoming payments by payment ID
	 *
	 * @param	string	$payment_id	Payment ID to look up
	 *
	 * @return object	Example: {
	 *	"payments": [{
	 *		"amount": 10350000000000,
	 *		"block_height": 994327,
	 *		"payment_id": "4279257e0a20608e25dba8744949c9e1caff4fcdafc7d5362ecf14225f3d9030",
	 *		"tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1",
	 *		"unlock_time": 0
	 *	}]
	 * }
	 *
	 */
	public function get_payments(string $payment_id)
	{
		$params = ['payment_id' => $payment_id];
		return $this->_run('get_payments', $params);
	}

	/**
	 *
	 * Look up incoming payments by payment ID (or a list of payments IDs) from a given height
	 *
	 * @param	array	$payment_ids		Array of payment IDs to look up
	 * @param	number	$min_block_height	Height to begin search
	 *
	 * @return	object	Example: {
	 *	"payments": [{
	 *		"amount": 10350000000000,
	 *		"block_height": 994327,
	 *		"payment_id": "4279257e0a20608e25dba8744949c9e1caff4fcdafc7d5362ecf14225f3d9030",
	 *		"tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1",
	 *		"unlock_time": 0
	 *	}]
	 * }
	 *
	 */
	public function get_bulk_payments(array $payment_ids, int $min_block_height)
	{
		$params = ['payment_ids' => $payment_ids, 'min_block_height' => $min_block_height];

		return $this->_run('get_bulk_payments', $params);
	}

	/**
	 *
	 * Look up incoming transfers
	 *
	 * @param	string	$type				Type of transfer to look up; must be 'all', 'available', or 'unavailable' (incoming transfers which have already been spent)	(optional)
	 * @param	number	$account_index		Index of account to look up																										(optional)
	 * @param	array	$subaddr_indices	List of subaddress indices to look up																			(optional)
	 *
	 * @return object
	 * 
	 */
	public function incoming_transfers(string $type = 'all', int $account_index = 0, array $subaddr_indices = [])
	{
		$params = ['transfer_type' => $type, 'account_index' => $account_index, 'subaddr_indices' => $subaddr_indices];
		return $this->_run('incoming_transfers', $params);
	}

	/**
	 *
	 * Look up a wallet key
	 *
	 * @param	string	$key_type	Type of key to look up; must be 'view_key', 'spend_key', or 'mnemonic'
	 *
	 * @return	object	Example: {
	 *	 "key": "7e341d..."
	 * }
	 *
	 */
	public function query_key(string $key_type)
	{
		$params = ['key_type' => $key_type];
		return $this->_run('query_key', $params);
	}

	/**
	 *
	 * Look up wallet view key
	 *
	 * @param	none
	 *
	 * @return	object	 Example: {
	 * 	"key": "7e341d..."
	 * }
	 *
	 */
	public function view_key()
	{
		$params = ['key_type' => 'view_key'];
		return $this->_run('query_key', $params);
	}

	/**
	 *
	 * Look up wallet spend key
	 *
	 * @param	none
	 *
	 * @return object	Example: {
	 *	 "key": "2ab810..."
	 * }
	 *
	 */
	public function spend_key()
	{
		$params = ['key_type' => 'spend_key'];
		return $this->_run('query_key', $params);
	}

	/**
	 *
	 * Look up wallet mnemonic seed
	 *
	 * @param	none
	 *
	 * @return	object	Example: {
	 *	 "key": "2ab810..."
	 * }
	 *
	 */
	public function mnemonic()
	{
		$params = ['key_type' => 'mnemonic'];
		return $this->_run('query_key', $params);
	}

	/**
	 *
	 * Create an integrated address from a given payment ID
	 *
	 * @param	string	$payment_ids	Payment ID	(optional)
	 *
	 * @return	object	Example: {
	 *	"integrated_address": "4BpEv3WrufwXoyJAeEoBaNW56ScQaLXyyQWgxeRL9KgAUhVzkvfiELZV7fCPBuuB2CGuJiWFQjhnhhwiH1FsHYGQQ8H2RRJveAtUeiFs6J"
	 * }
	 *
	 */
	public function make_integrated_address(string $payment_id = null)
	{
		$params = ['payment_id' => $payment_id];
		return $this->_run('make_integrated_address', $params);
	}

	/**
	 *
	 * Look up the wallet address and payment ID corresponding to an integrated address
	 *
	 * @param	 string	 $integrated_address	Integrated address to split
	 *
	 * @return object	 Example: {
	 *	 "payment_id": "420fa29b2d9a49f5",
	 *	 "standard_address": "427ZuEhNJQRXoyJAeEoBaNW56ScQaLXyyQWgxeRL9KgAUhVzkvfiELZV7fCPBuuB2CGuJiWFQjhnhhwiH1FsHYGQGaDsaBA"
	 * }
	 *
	 */
	public function split_integrated_address($integrated_address)
	{
		$params = ['integrated_address' => $integrated_address];
		return $this->_run('split_integrated_address', $params);
	}

	/**
	 *
	 * Stop the wallet, saving the state
	 *
	 * @param	none
	 *
	 * @return	none
	 *
	 */
	public function stop_wallet()
	{
		return $this->_run('stop_wallet');
	}

	/**
	 *
	 * Rescan the blockchain from scratch
	 *
	 * @param	none
	 *
	 * @return	none
	 *
	*/

	public function rescan_blockchain()
	{
		return $this->_run('rescan_blockchain');
	}

	/**
	 *
	 * Add notes to transactions
	 *
	 * @param	array	$txids	Array of transaction IDs to note
	 * @param	array	$notes	Array of notes (strings) to add
	 *
	 * @return	none
	 *
	 */
	public function set_tx_notes(array $txids, array $notes)
	{
		$params = ['txids' => $txids, 'notes' => $notes];
		return $this->_run('set_tx_notes', $params);
	}

	/**
	 *
	 * Look up transaction note
	 *
	 * @param	array	$txids	Array of transaction IDs (strings) to look up
	 *
	 * @return	obect	Example: {
	 *	 // TODO example
	 * }
	 *
	 */
	public function get_tx_notes(array $txids)
	{
		$params = ['txids' => $txids];
		return $this->_run('get_tx_notes', $params);
	}

	/**
	 *
	 * Set a wallet option
	 *
	 * @param	string	$key	Option to set
	 * @param	string	$value	Value to set
	 *
	 * @return	none
	 *
	 */
	public function set_attribute(string $key, string $value)
	{
		$params = ['key' => $key, 'value' => $value];
		return $this->_run('set_attribute', $params);
	}

	/**
	 *
	 * Look up a wallet option
	 *
	 * @param	string	 $key	 Wallet option to query
	 *
	 * @return	object	 Example: {
	 *	"id": "0",
	 *	"jsonrpc": "2.0",
	 *	"result": {
	 *		"value": "my_value"
	 * 	}
	 * }
	 *
	 */
	public function get_attribute($key)
	{
		$params = ['key' => $key];
		return $this->_run('get_attribute', $params);
	}

	/**
	 *
	 * Look up a transaction key
	 *
	 * @param	string	$txid	 Transaction ID to look up
	 *
	 * @return	object	Example: {
	 *	 "tx_key": "e8e97866b1606bd87178eada8f995bf96d2af3fec5db0bc570a451ab1d589b0f"
	 * }
	 *
	 */
	public function get_tx_key($txid)
	{
		$params = ['txid' => $txid];
		return $this->_run('get_tx_key', $params);
	}

	/**
	 *
	 * Check a transaction key
	 *
	 * @param	string	$address	Address that sent transaction
	 * @param	string	$txid			Transaction ID
	 * @param	string	$tx_key		Transaction key
	 *
	 * @return	object	Example: {
	 *	 "confirmations": 1,
	 *	 "in_pool": ,
	 *	 "received": 0
	 * }
	 *
	 */
	public function check_tx_key(string $address, string $txid, string $tx_key)
	{
		$params = ['address' => $address, 'txid' => $txid, 'tx_key' => $tx_key];
		return $this->_run('check_tx_key', $params);
	}

	/**
	 *
	 * Create proof (signature) of transaction
	 *
	 * @param	string	$address	Address that spent funds
	 * @param	string	$txid		Transaction ID
	 *
	 * @return	object	Example: {
	 *	 "signature": "InProofV1Lq4nejMXxMnAdnLeZhHe3FGCmFdnSvzVM1AiGcXjngTRi4hfHPcDL9D4th7KUuvF9ZHnzCDXysNBhfy7gFvUfSbQWiqWtzbs35yUSmtW8orRZzJpYKNjxtzfqGthy1U3puiF"
	 * }
	 *
	 */
	public function get_tx_proof(string $address, string $txid)
	{
		$params = ['address' => $address, 'txid' => $txid];
		return $this->_run('get_tx_proof', $params);
	}

	/**
	 *
	 * Verify transaction proof
	 *
	 * @param	string	$address		Address that spent funds
	 * @param	string	$txid			Transaction ID
	 * @param	string	$signature	Signature (tx_proof)
	 *
	 * @return	Example: {
	 *	 "confirmations": 2,
	 *	 "good": 1,
	 *	 "in_pool": ,
	 *	 "received": 15752471409492,
	 * }
	 *
	 */
	public function check_tx_proof(string $address, string $txid, string $signature)
	{
		$params = ['address' => $address, 'txid' => $txid, 'signature' => $signature];
		return $this->_run('check_tx_proof', $params);
	}

	/**
	 *
	 * Create proof of a spend
	 *
	 * @param	string	$txid		Transaction ID
	 * @param	string	$message	A message to the signature to further authenticate the prooving process. (Optional)
	 *
	 * @return object	 Example: {
	 *	 "signature": "SpendProofV1RnP6ywcDQHuQTBzXEMiHKbe5ErzRAjpUB1h4RUMfGPNv4bbR6V7EFyiYkCrURwbbrYWWxa6Kb38ZWWYTQhr2Y1cRHVoDBkK9GzBbikj6c8GWyKbu3RKi9hoYp2fA9zze7UEdeNrYrJ3tkoE6mkR3Lk5HP6X2ixnjhUTG65EzJgfCS4qZ85oGkd17UWgQo6fKRC2GRgisER8HiNwsqZdUTM313RmdUX7AYaTUNyhdhTinVLuaEw83L6hNHANb3aQds5CwdKCUQu4pkt5zn9K66z16QGDAXqL6ttHK6K9TmDHF17SGNQVPHzffENLGUf7MXqS3Pb6eijeYirFDxmisZc1n2mh6d5EW8ugyHGfNvbLEd2vjVPDk8zZYYr7NyJ8JjaHhDmDWeLYy27afXC5HyWgJH5nDyCBptoCxxDnyRuAnNddBnLsZZES399zJBYHkGb197ZJm85TV8SRC6cuYB4MdphsFdvSzygnjFtbAcZWHy62Py3QCTVhrwdUomAkeNByM8Ygc1cg245Se1V2XjaUyXuAFjj8nmDNoZG7VDxaD2GT9dXDaPd5dimCpbeDJEVoJXkeEFsZF85WwNcd67D4s5dWySFyS8RbsEnNA5UmoF3wUstZ2TtsUhiaeXmPwjNvnyLif3ASBmFTDDu2ZEsShLdddiydJcsYFJUrN8L37dyxENJN41RnmEf1FaszBHYW1HW13bUfiSrQ9sLLtqcawHAbZWnq4ZQLkCuomHaXTRNfg63hWzMjdNrQ2wrETxyXEwSRaodLmSVBn5wTFVzJe5LfSFHMx1FY1xf8kgXVGafGcijY2hg1yw8ru9wvyba9kdr16Lxfip5RJGFkiBDANqZCBkgYcKUcTaRc1aSwHEJ5m8umpFwEY2JtakvNMnShjURRA3yr7GDHKkCRTSzguYEgiFXdEiq55d6BXDfMaKNTNZzTdJXYZ9A2j6G9gRXksYKAVSDgfWVpM5FaZNRANvaJRguQyqWRRZ1gQdHgN4DqmQ589GPmStrdfoGEhk1LnfDZVwkhvDoYfiLwk9Z2JvZ4ZF4TojUupFQyvsUb5VPz2KNSzFi5wYp1pqGHKv7psYCCodWdte1waaWgKxDken44AB4k6wg2V8y1vG7Nd4hrfkvV4Y6YBhn6i45jdiQddEo5Hj2866MWNsdpmbuith7gmTmfat77Dh68GrRukSWKetPBLw7Soh2PygGU5zWEtgaX5g79FdGZg"
	 * }
	 *
	 */
	public function get_spend_proof(string $txid, string $message = null)
	{
		$params = ['txid' => $txid];
		if( $message !== null ) {
			$params['message'] = $message;
		}
		return $this->_run('get_spend_proof', $params);
	}

	/**
	 *
	 * Verify spend proof
	 *
	 * @param	string	$txid		Transaction ID
	 * @param	string	$signature	Spend proof to verify
	 * @param	string	$message	A message to the signature to further authenticate the prooving process. (Optional)
	 *
	 * @return object	Example: {
	 *	 "good": 1
	 * }
	 *
	 */
	public function check_spend_proof(string $txid, string $signature, string $message = null)
	{
		$params = ['txid' => $txid, 'signature' => $signature];
		if( $message !== null ) {
			$params['message'] = $message;
		}
		return $this->_run('check_spend_proof', $params);
	}

	/**
	 *
	 * Create proof of reserves
	 *
	 * @param	string	$account_index	Comma-separated list of account indices of which to prove reserves (proves reserve of all accounts if empty)	(optional)
	 *
	 * @return	Example: {
	 *	 "signature": "ReserveProofV11BZ23sBt9sZJeGccf84mzyAmNCP3KzYbE111111111111AjsVgKzau88VxXVGACbYgPVrDGC84vBU61Gmm2eiYxdZULAE4yzBxT1D9epWgCT7qiHFvFMbdChf3CpR2YsZj8CEhp8qDbitsfdy7iBdK6d5pPUiMEwCNsCGDp8AiAc6sLRiuTsLEJcfPYEKe"
	 * }
	 *
	 */
	public function get_reserve_proof($account_index = 'all')
	{
		if ($account_index == 'all') {
			$params = ['all' => true];
		} else {
			$params = ['account_index' => $account_index];
		}

		return $this->_run('get_reserve_proof');
	}

	/**
	 *
	 * Verify a reserve proof
	 *
	 * @param	string	$address	Wallet address
	 * @param	string	$signature	Reserve proof
	 *
	 * @return object	 Example: {
	 *	 "good": 1,
	 *	 "spent": 0,
	 *	 "total": 0
	 * }
	 *
	 */
	public function check_reserve_proof(string $address, string $signature)
	{
		$params = ['address' => $address, 'signature' => $signature];
		return $this->_run('check_reserve_proof', $params);
	}

	/**
	 *
	 * Look up transfers
	 *
	 * @param	array	$input_types		Array of transfer type strings; possible values include 'all', 'in', 'out', 'pending', 'failed', and 'pool'	(optional)
	 * @param	number	$account_index		Index of account to look up																					(optional)
	 * @param	string	$subaddr_indices	Comma-separated list of subaddress indices to look up														(optional)
	 * @param	number	$min_height			Minimum block height to use when looking up transfers														(optional)
	 * @param	number	$max_height			Maximum block height to use when looking up transfers														(optional)
	 *
	 * @return object	 Example: {
	 *	 "pool": [{
	 *		 "amount": 500000000000,
	 *		 "fee": 0,
	 *		 "height": 0,
	 *		 "note": "",
	 *		 "payment_id": "758d9b225fda7b7f",
	 *		 "timestamp": 1488312467,
	 *		 "txid": "da7301d5423efa09fabacb720002e978d114ff2db6a1546f8b820644a1b96208",
	 *		 "type": "pool"
	 *	 }]
	 * }
	 *
	 */
	public function get_transfers(array $input_types = ['all'], int $account_index = 0, string $subaddr_indices = '', int $min_height = 0, int $max_height = 4206931337)
	{
		$params = ['account_index' => $account_index, 'subaddr_indices' => $subaddr_indices, 'min_height' => $min_height, 'max_height' => $max_height];
		for ($i = 0, $iMax = count($input_types); $i < $iMax; $i++) {
			$params[$input_types[$i]] = true;
		}

		if (array_key_exists('all', $params)) {
			unset($params['all']);
			$params['in'] = true;
			$params['out'] = true;
			$params['pending'] = true;
			$params['failed'] = true;
			$params['pool'] = true;
		}

		if (($min_height || $max_height) && $max_height != 4206931337) {
			$params['filter_by_height'] = true;
		}

		return $this->_run('get_transfers', $params);
	}

	/**
	 *
	 * Look up transaction by transaction ID
	 *
	 * @param	string	$txid			Transaction ID to look up
	 * @param	string	$account_index	Index of account to query	(optional)
	 *
	 * @return object	 Example: {
	 *	 "transfer": {
	 *		 "amount": 10000000000000,
	 *		 "fee": 0,
	 *		 "height": 1316388,
	 *		 "note": "",
	 *		 "payment_id": "0000000000000000",
	 *		 "timestamp": 1495539310,
	 *		 "txid": "f2d33ba969a09941c6671e6dfe7e9456e5f686eca72c1a94a3e63ac6d7f27baf",
	 *		 "type": "in"
	 *	 }
	 * }
	 *
	 */
	public function get_transfer_by_txid(string $txid, int $account_index = 0)
	{
		$params = ['txid' => $txid, 'account_index' => $account_index];
		return $this->_run('get_transfer_by_txid', $params);
	}

	/**
	 *
	 * Sign a string
	 *
	 * @param	string	$data	Data to sign
	 *
	 * @return	object	Example: {
	 *	 "signature": "SigV1Xp61ZkGguxSCHpkYEVw9eaWfRfSoAf36PCsSCApx4DUrKWHEqM9CdNwjeuhJii6LHDVDFxvTPijFsj3L8NDQp1TV"
	 * }
	 *
	 */
	public function sign(string $data)
	{
		$params = ['string' => $data];
		return $this->_run('sign', $params);
	}

	/**
	 *
	 * Verify a signature
	 *
	 * @param	string	$data		Signed data
	 * @param	string	$address	Address that signed data
	 * @param	string	$signature	Signature to verify
	 *
	 * @return object	 Example: {
	 *	 "good": true
	 * }
	 *
	 */
	public function verify(string $data, string $address, string $signature)
	{
		$params = ['data' => $data, 'address' => $address, 'signature' => $signature];
		return $this->_run('verify', $params);
	}

	/**
	 *
	 * Export an array of signed key images
	 *
	 * @param	none
	 *
	 * @return	array	Example: {
	 *		"id": "0",
	 *		"jsonrpc": "2.0",
	 *		"result": {
	 *			"signed_key_images": [
	 * 				{
	 *					"key_image": "cd35239b72a35e26a57ed17400c0b66944a55de9d5bda0f21190fed17f8ea876",
	 *					"signature": "c9d736869355da2538ab4af188279f84138c958edbae3c5caf388a63cd8e780b8c5a1aed850bd79657df659422c463608ea4e0c730ba9b662c906ae933816d00"
	 *				},
	 * 				{
	 *					"key_image": "65158a8ee5a3b32009b85a307d85b375175870e560e08de313531c7dbbe6fc19",
	 *					"signature": "c96e40d09dfc45cfc5ed0b76bfd7ca793469588bb0cf2b4d7b45ef23d40fd4036057b397828062e31700dc0c2da364f50cd142295a8405b9fe97418b4b745d0c"
	 *				},
	 * 				...
	 * 			]
	 * 		}
	 *	}
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
	 * @param	array	 $signed_key_images	 Array of signed key images
	 *
	 * @return	object	 Example: {
	 *	 // TODO example
	 *	 height: ,
	 *	 spent: ,
	 *	 unspent:
	 * }
	 *
	 */
	public function import_key_images(array $signed_key_images)
	{
		$params = ['signed_key_images' => $signed_key_images];
		return $this->_run('import_key_images', $params);
	}

	/**
	 *
	 * Create a payment URI using the official URI specification
	 *
	 * @param	string	$address		Address to receive funds
	 * @param	string	$amount			Amount of monero to request
	 * @param	string	$payment_id		Payment ID						(Optional)
	 * @param	string	$recipient_name	Name of recipient				(Optional)
	 * @param	string	$tx_description	Payment description				(Optional)
	 *
	 * @return object	 Example: {
	 *	 // TODO example
	 * }
	 *
	 */
	public function make_uri(string $address, int $amount, string $payment_id = null, string $recipient_name = null, string $tx_description = null)
	{
		$params = ['address' => $address, 'amount' => $this->_transform($amount), 'payment_id' => $payment_id, 'recipient_name' => $recipient_name, 'tx_description' => $tx_description];
		return $this->_run('make_uri', $params);
	}

	/**
	 *
	 * Parse a payment URI
	 *
	 * @param	string	$uri	Payment URI
	 *
	 * @return	object	Example: {
	 *	 "uri": {
	 *		 "address": "44AFFq5kSiGBoZ4NMDwYtN18obc8AemS33DBLWs3H7otXft3XjrpDtQGv7SqSsaBYBb98uNbr2VBBEt7f2wfn3RVGQBEP3A",
	 *		 "amount": 10,
	 *		 "payment_id": "0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef",
	 *		 "recipient_name": "Monero Project donation address",
	 *		 "tx_description": "Testing out the make_uri function"
	 *	 }
	 * }
	 *
	 */
	public function parse_uri(string $uri)
	{
		$params = ['uri' => $uri];
		return $this->_run('parse_uri', $params);
	}

	/**
	 *
	 * Look up address book entries
	 *
	 * @param	array	$entries	Array of address book entry indices to look up
	 *
	 * @return	object	Example: {
	 *	 // TODO example
	 * }
	 *
	 */
	public function get_address_book(array $entries)
	{
		$params = ['entries' => $entries];
		return $this->_run('get_address_book', $params);
	}

	/**
	 *
	 * Add entry to the address book
	 *
	 * @param	string	$address		Address to add to address book
	 * @param	string	$payment_id		Payment ID to use with address in address book	(optional)
	 * @param	string	$description	Description of address							(optional)
	 *
	 * @return object	 Example: {
	 *	 // TODO example
	 * }
	 *
	 */
	public function add_address_book(string $address, string $payment_id, string $description)
	{
		$params = ['address' => $address, 'payment_id' => $payment_id, 'description' => $description];
		return $this->_run('add_address_book', $params);
	}

	/**
	 *
	 * Delete an entry from the address book
	 *
	 * @param	array	$index	Index of the address book entry to remove
	 *
	 * @return	none
	 *
	 */
	public function delete_address_book(array $index)
	{
		$params = ['index' => $index];
		return $this->_run('delete_address_book', $params);
	}

	/**
	 *
	 * Refresh the wallet after opening
	 *
	 * @param	int	$start_height	 Block height from which to start		 (optional)
	 *
	 * @return	object	 Example: {
	 *	 // TODO example
	 * }
	 *
	 */
	public function refresh(int $start_height = null)
	{
		$params = ['start_height' => $start_height];
		return $this->_run('refresh', $params);
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
	 * Start mining
	 *
	 * @param	number	$threads_count			Number of threads with which to mine
	 * @param	boolean	$do_background_mining	Mine in background?
	 * @param	boolean	$ignore_battery			Ignore battery level?
	 *
	 * @return	none
	 *
	 */
	public function start_mining(int $threads_count, bool $do_background_mining, bool $ignore_battery)
	{
		$params = ['threads_count' => $threads_count, 'do_background_mining' => $do_background_mining, 'ignore_battery' => $ignore_battery];
		return $this->_run('start_mining', $params);
	}

	/**
	 *
	 * Stop mining
	 *
	 * @param	none
	 *
	 * @return	none
	 *
	 */
	public function stop_mining()
	{
		return $this->_run('stop_mining');
	}

	/**
	 *
	 * Look up a list of available languages for your wallet's seed
	 *
	 * @param	none
	 *
	 * @return	object	Example: {
	 *	"id": "0",
	 *	"jsonrpc": "2.0",
	 *	"result": {
	 *		"languages": ["Deutsch","English","Español","Français","Italiano","Nederlands","Português","русский язык","日本語","简体中文 (中国)","Esperanto","Lojban"]
	 *	}
	 *}
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
	 * @param	string	$filename	Filename of new wallet to create
	 * @param	string	$password	Password of new wallet to create
	 * @param	string	$language	Language of new wallet to create
	 *
	 * @return	none
	 *
	 */
	public function create_wallet(string $filename = 'monero_wallet', string $password = null, string $language = 'English')
	{
		$params = ['filename' => $filename, 'password' => $password, 'language' => $language];
		return $this->_run('create_wallet', $params);
	}

	/**
	 *
	 * Open a wallet
	 *
	 * @param	string	$filename	Filename of wallet to open
	 * @param	string	$password	Password of wallet to open
	 *
	 * @return	none
	 *
	 */
	public function open_wallet(string $filename = 'monero_wallet', string $password = null)
	{
		$params = ['filename' => $filename, 'password' => $password];
		return $this->_run('open_wallet', $params);
	}

	/**
	 *
	 * Check if wallet is multisig
	 *
	 * @param	none
	 *
	 * @return	object	Example: (non-multisignature wallet) {
	 *	 "multisig": ,
	 *	 "ready": ,
	 *	 "threshold": 0,
	 *	 "total": 0
	 * } // TODO multisig wallet example
	 *
	 */
	public function is_multisig()
	{
		return $this->_run('is_multisig');
	}

	/**
	 *
	 * Create information needed to create a multisignature wallet
	 *
	 * @param	none
	 *
	 * @return	object	 Example: {
	 *	 "multisig_info": "MultisigV1WBnkPKszceUBriuPZ6zoDsU6RYJuzQTiwUqE5gYSAD1yGTz85vqZGetawVvioaZB5cL86kYkVJmKbXvNrvEz7o5kibr7tHtenngGUSK4FgKbKhKSZxVXRYjMRKEdkcbwFBaSbsBZxJFFVYwLUrtGccSihta3F4GJfYzbPMveCFyT53oK"
	 * }
	 *
	 */
	public function prepare_multisig()
	{
		return $this->_run('prepare_multisig');
	}

	/**
	 *
	 * Create a multisignature wallet
	 *
	 * @param	string	$multisig_info	Multisignature information (from eg. prepare_multisig)
	 * @param	string	$threshold		Threshold required to spend from multisignature wallet
	 * @param	string	$password		Passphrase to apply to multisignature wallet
	 *
	 * @return	object	Example: {
	 *	 // TODO example
	 * }
	 *
	 */
	public function make_multisig(string $multisig_info, string $threshold, string $password = '')
	{
		$params = ['multisig_info' => $multisig_info, 'threshold' => $threshold, 'password' => $password];
		return $this->_run('make_multisig', $params);
	}

	/**
	 *
	 * Export multisignature information
	 *
	 * @param	none
	 *
	 * @return	object	 Example: {
	 *	 // TODO example
	 * }
	 *
	 */
	public function export_multisig_info()
	{
		return $this->_run('export_multisig_info');
	}

	/**
	 *
	 * Import mutlisignature information
	 *
	 * @param	string	$info	Multisignature info (from eg. prepare_multisig)
	 *
	 * @return	Example: {
	 *	 // TODO example
	 * }
	 *
	 */
	public function import_multisig_info($info)
	{
		$params = ['info' => $info];
		return $this->_run('import_multisig_info', $params);
	}

	/**
	 *
	 * Finalize a multisignature wallet
	 *
	 * @param	string	$multisig_info	Multisignature info (from eg. prepare_multisig)
	 * @param	string	$password		Multisignature info (from eg. prepare_multisig)
	 *
	 * @return	Example: {
	 *	 // TODO example
	 * }
	 *
	 */
	public function finalize_multisig($multisig_info, $password = '')
	{
		$params = ['multisig_info' => $multisig_info, 'password' => $password];
		return $this->_run('finalize_multisig', $params);
	}

	/**
	 *
	 * Sign a multisignature transaction
	 *
	 * @param	string	$tx_data_hex	Blob of transaction to sign
	 *
	 * @return object	 Example: {
	 *	 // TODO example
	 * }
	 *
	 */
	public function sign_multisig($tx_data_hex)
	{
		$params = ['tx_data_hex' => $tx_data_hex];
		return $this->_run('sign_multisig', $params);
	}

	/**
	 *
	 * Submit (relay) a multisignature transaction
	 *
	 * @param	string	$tx_data_hex	Blob of transaction to submit
	 *
	 * @return	Example: {
	 *	 // TODO example
	 * }
	 *
	 */
	public function submit_multisig($tx_data_hex)
	{
		$params = ['tx_data_hex' => $tx_data_hex];
		return $this->_run('submit_multisig', $params);
	}

	/**
	 * @return jsonRPCClient
	 */
	public function get_client() : jsonRPCClient
	{
		return $this->client;
	}

	/**
	 * @return jsonRPCClient
	 */
	public function getClient() : jsonRPCClient
	{
		return $this->client;
	}

	/**
	 *
	 * Validate a wallet address
	 *
	 * @param	string	address			The address to validate. 
	 * @param	bool	any_net_type	If true, consider addresses belonging to any of the three Monero networks (mainnet, stagenet, and testnet) valid. Otherwise, only consider an address valid if it belongs to the network on which the rpc-wallet's current daemon is running (Defaults to false)	(Optional)
	 * @param	boolean	allow_openalias	If true, consider OpenAlias-formatted addresses valid (Defaults to false).			(Optional)
	 *
	 * @return	valid - boolean; True if the input address is a valid Monero address. 
	 *				 integrated - boolean; True if the given address is an integrated address. 
	 *				 subaddress - boolean; True if the given address is a subaddress 
	 *				 nettype - string; Specifies which of the three Monero networks (mainnet, stagenet, and testnet) the address belongs to. 
	 *				 openalias_address - boolean; True if the address is OpenAlias-formatted.
	 *
	 */
	public function validate_address(string $address, bool $strict_nettype = false, bool $allow_openalias = false)
	{
		$params = [
			'address' => $address,
			'any_net_type' => $strict_nettype,
			'allow_openalias' => $allow_openalias
		];
		return $this->_run('validate_address', $params);
	}

	/**
	 *
	 * Create a wallet on the RPC server from an address, view key, and (optionally) spend key.
	 * 
	 * @param	string	filename is the name of the wallet to create on the RPC server
	 * @param	string	password is the password encrypt the wallet
	 * @param	string	address is the address of the wallet to construct
	 * @param	string	viewKey is the view key of the wallet to construct
	 * @param	string	spendKey is the spend key of the wallet to construct or null to create a view-only wallet
	 * @param	string	language is the wallet and mnemonic's language (default = "English")
	 * @param	integer	restoreHeight is the block height to restore (i.e. scan the chain) from (default = 0)
	 * @param	bool	saveCurrent specifies if the current RPC wallet should be saved before being closed (default = true)
	 *
	 * @return	Example: {
	 * 	"id": "0",
	 * 	"jsonrpc": "2.0",
	 * 	result": {
	 * 		"address":"42gt8cXJSHAL4up8XoZh7fikVuswDU7itAoaCjSQyo6fFoeTQpAcAwrQ1cs8KvFynLFSBdabhmk7HEe3HS7UsAz4LYnVPYM",
	 * 		"info":"Wallet has been generated successfully."   
	 * 	}
	 * }
	 *
	 */
	public function generate_from_keys(string $filename, string $password, string $address, string $viewKey, string $spendKey = '', string $language = 'English', int $restoreHeight = 0, bool $saveCurrent = true)
	{
		$params = [
			'filename' => $filename,
			'password' => $password,
			'address' => $address,
			'viewkey' => $viewKey,
			'spendkey' => $spendKey,
			'language' => $language,
			'restore_height' => $restoreHeight,
			'autosave_current' => $saveCurrent
		];
		return $this->_run('generate_from_keys', $params);
	}

	/**
	 *
	 * Exchange mutlisignature information
	 *
	 * @param	string	password wallet password
	 * @param	string	multisig_info info (from eg. prepare_multisig)
	 *
	 */
	public function exchange_multisig_keys(string $password, string $multisig_info, bool $force_update_use_with_caution = false)
	{
		$params = [
			'password' => $password,
			'multisig_info' => $multisig_info
		];
		return $this->_run('exchange_multisig_keys', $params);
	}

	/**
	 *
	 * Obtain information (destination, amount) about a transfer
	 *
	 * @param 	array txinfo txinfo
	 *
	 */
	public function describe_transfer($txinfo)
	{
		$params = [
			'multisig_txset' => $txinfo,
		];
		return $this->_run('describe_transfer', $params);
	}

	/**
	 * Export all outputs in hex format
	 */
	public function export_outputs()
	{
		return $this->_run('export_outputs');
	}

	/**
	 *
	 * Import outputs in hex format
	 *
	 * @param outputs_data_hex wallet outputs in hex format
	 *
	 *
	 */
	public function import_outputs($outputs_data_hex)
	{
		$params = [
			'outputs_data_hex' => $outputs_data_hex,
		];
		return $this->_run('import_outputs', $params);
	}

	/**
	 * Set whether and how often to automatically refresh the current wallet
	 * 
	 * @param enable Enable or disable automatic refreshing (default = true)
	 * @param period The period of the wallet refresh cycle (i.e. time between refreshes) in seconds
	 * 
	 */
	public function auto_refresh(bool $enable = true, int $period = 10)
	{
		$params = [
			'enable' => $enable,
			'period' => $period
		];
		return $this->_run('auto_refresh', $params);
	}

	/**
	 * Change a wallet password
	 * 
	 * @param	string	old_password
	 * @param	string	new_password
	 */
	public function change_wallet_password(string $old_password = '', string $new_password = '')
	{
		$params = [
			'old_password' => $old_password,
			'new_password' => $new_password
		];
		return $this->_run('change_wallet_password', $params);
	}

	/**
	 * Close wallet
	 */
	public function close_wallet()
	{
		return $this->_run('close_wallet');
	}

	/**
	 * Get RPC version Major & Minor integer-format, where Major is the first 16 bits and Minor the last 16 bits.
	 */
	public function get_version()
	{
		return $this->_run('get_version');
	}
}
