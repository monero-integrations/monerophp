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
	
	public function get_info(){
		$get_info_method = $this->run("get_info");
		return $get_info_method;
	}

	public function getblockcount()
	{
	    $getblockcount_method = $this->_run("getblockcount");
	    return $getblockcount_method;
	}
	
	public function on_getblockhash($block_height)
	{
	    $on_getblockhash_parameters = array($block_height);
	    $on_getblockhash_method = $this->run("on_getblockhash", $on_getblockhash_parameters);
	    return $on_getblockhash_method;
	}
	
	public function hard_fork_info(){
	    $hard_fork_info_method = $this->run("hard_fork_info");
	    return $hard_fork_info_method;
	}
}
