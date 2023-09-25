<?php
/**
 * jsonRPCClient.php
 *
 * Written using the JSON RPC specification -
 * http://json-rpc.org/wiki/specification
 *
 * @author Kacper Rowinski <krowinski@implix.com>
 * http://implix.com
 */
namespace MoneroIntegrations\MoneroPhp;

use InvalidArgumentException;
use RuntimeException;

class jsonRPCClient
{
	protected bool $is_debug = false;

	protected $curl_options = [
		CURLOPT_CONNECTTIMEOUT => 90,
		CURLOPT_TIMEOUT => 90
	];

	private $httpErrors = [
		400 => '400 Bad Request',
		401 => '401 Unauthorized',
		403 => '403 Forbidden',
		404 => '404 Not Found',
		405 => '405 Method Not Allowed',
		406 => '406 Not Acceptable',
		408 => '408 Request Timeout',
		500 => '500 Internal Server Error',
		502 => '502 Bad Gateway',
		503 => '503 Service Unavailable'
	];

	public function __construct(
		protected readonly ?string $url,
		private readonly ?string $username,
		private readonly ?string $password,
		private readonly bool $SSL
	) {
		$this->validate(!extension_loaded('curl'), 'The curl extension must be loaded to use this class!');
		$this->validate(!extension_loaded('json'), 'The json extension must be loaded to use this class!');
	}

	public function setDebug($pIsDebug)
	{
		$this->is_debug = !empty($pIsDebug);
		return $this;
	}

	public function setCurlOptions(array $pOptionsArray)
	{
		$this->curl_options = $pOptionsArray + $this->curl_options;
		
		return $this;
	}

	public function _run(?string $pMethod, ?array $pParams, string $path) : array
	{
		// send params as an object or an array
		// Request (method invocation)
		$request = json_encode(['jsonrpc' => '2.0', 'method' => $pMethod, 'params' => $pParams]);

		// if is_debug mode is true then add url and request to is_debug
		$this->debug('Url: ' . $this->url . "\r\n", false);
		$this->debug('Request: ' . $request . "\r\n", false);
		$responseMessage = $this->getResponse($request, $path);

		// if is_debug mode is true then add response to is_debug and display it
		$this->debug('Response: ' . $responseMessage . "\r\n", true);

		// decode and create array ( can be object, just set to false )
		$responseDecoded = json_decode($responseMessage, true);

		// check if decoding json generated any errors
		$jsonErrorMsg = json_last_error_msg();
		$this->validate( !is_null($jsonErrorMsg) && $jsonErrorMsg !== 'No error' , $jsonErrorMsg . ': ' . $responseMessage);

		if (isset($responseDecoded['error']))
		{
			$errorMessage = 'Request have return error: ' . $responseDecoded['error']['message'] . '; ' . "\n" .
				'Request: ' . $request . '; ';
			if (isset($responseDecoded['error']['data']))
			{
				$errorMessage .= "\n" . 'Error data: ' . $responseDecoded['error']['data'];
			}
			$this->validate( !is_null($responseDecoded['error']), $errorMessage);
		}
		return $responseDecoded['result'] ?? -1;
	}

	protected function & getResponse(string &$pRequest, string &$path) : string
	{
		// do the actual connection
		$ch = curl_init();
		if (!$ch)
		{
			throw new RuntimeException('Could\'t initialize a cURL session');
		}
		curl_setopt($ch, CURLOPT_URL, $this->url.$path);

		if(!is_null($this->username) & is_null($this->password)) {
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
			curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
		}

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $pRequest);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		if ($this->SSL)
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, '2');
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		} else {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		}

		if (!curl_setopt_array($ch, $this->curl_options))
		{
			throw new RuntimeException('Error while setting curl options');
		}

		// send the request
		$response = curl_exec($ch);

		// check http status code
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if (isset($this->httpErrors[$httpCode]))
		{
			throw new RuntimeException('Response Http Error - ' . $this->httpErrors[$httpCode]);
		}

		// check for curl error
		if (0 < curl_errno($ch))
		{
			throw new RuntimeException('Unable to connect to '.$this->url . ' Error: ' . curl_error($ch));
		}
		
		// close the connection
		curl_close($ch);
		return $response;
	}

	public function validate(bool $failed, $errorMessage)
	{
		if ($failed)
		{
			throw new RuntimeException($errorMessage);
		}
	}

	protected function debug($pAdd, $pShow = false) : void
	{
		static $debug, $startTime;
		// is_debug off return
		if (false === $this->is_debug)
		{
			return;
		}
		// add
		$debug .= $pAdd;
		// get starttime
		$startTime = empty($startTime) ? array_sum(explode(' ', microtime())) : $startTime;
		if (true === $pShow and !empty($debug))
		{
			// get endtime
			$endTime = array_sum(explode(' ', microtime()));
			// performance summary
			$debug .= 'Request time: ' . round($endTime - $startTime, 3) . ' s Memory usage: ' . round(memory_get_usage() / 1024) . " kb\r\n";
			echo nl2br($debug);
			// send output immediately
			flush();
			// clean static
			$debug = $startTime = null;
		}
	}
}
