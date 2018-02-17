<?php

class Daemon
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
	    return $this->_run("getblockcount");
	}
}
