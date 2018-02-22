<?php

class Daemon_RPC
{
	private $client;

	function __construct($host, $port)
	{
	    $this->url = 'http://' .$host.':'.$port. '/json_rpc';
	    $this->client = new jsonRPCClient($this->url);
	}

	protected function _run($method,$params = null)
        {
	    $result = $this->client->_run($method, $params);
            return $result;
        }

	public function getblockcount()
	{
	    $result = $this->_run("getblockcount");
	    return $result["count"];
	}

	public function get_info()
	{
	    $result =  $this->_run("get_info");
	    return $result;
	}

	public function hardfork_info()
	{
	    return $this->_run("hard_fork_info");
	}

	public function getlastblockheader()
	{
	    return $this->_run("getlastblockheader");
	}

	public function getblockheaderbyhash($hash)
	{
	    return $this->_run("getlastblockheader", $hash);
	}

	public function getblock_by_hash($hash)
	{
	    return $this->_run("getblock", $hash);
	}

	// getblock_by_height is basically an alias for getblock_by_hash
	public function getblock_by_height($height)
	{
	    $heightString = (string) $height;
	    return $this->_run("getblock", $heightString);
	}

	public function getbans(){
	    $getbans_method = $this->run("getbans");
	    return $getbans_method;
	}
}
