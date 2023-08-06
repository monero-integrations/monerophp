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
require 'vendor/autoload.php';

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

use RuntimeException;

class JsonRpcClient
{
    protected $client;
    protected $isDebug = false;

    public function __construct(string $url, string $username, string $password, bool $checkSSL = true)
    {
        $this->client = new GuzzleClient([
            'base_uri' => $url,
            'auth' => [$username, $password, 'digest'],
            'verify' => $checkSSL,
        ]);
    }

    public function setDebug(bool $isDebug): self
    {
        $this->isDebug = $isDebug;
        return $this;
    }

    public function call(string $method, array $params = [], string $path = 'json_rpc'): ?array
    {
        $requestData = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
        ];

        $this->debug('Request: ' . json_encode($requestData));

        try {
            $response = $this->client->post('/' . $path, [
                'json' => $requestData,
            ]);

            $responseBody = $response->getBody()->getContents();
            $this->debug('Response: ' . $responseBody);

            return $this->handleResponse($responseBody);
        } catch (GuzzleException $e) {
            throw new RuntimeException('Response HTTP Error - ' . $e->getMessage());
        }
    }

    protected function handleResponse(string $responseBody): ?array
    {
        $response = json_decode($responseBody, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Invalid JSON response');
        }

        if (!isset($response['result'])) {
            $error = $response['error']['message'] ?? 'Unknown error';
            throw new RuntimeException($error);
        }

        return $response['result'];
    }

    protected function debug(string $message): void
    {
        if ($this->isDebug) {
            echo $message . PHP_EOL;
        }
    }
}
