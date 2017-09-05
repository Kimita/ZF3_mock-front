<?php

namespace Common\Utility;

use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Decoder as JsonDecoder;
use Zend\Json\Json;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

class ApiClient {

    /**
     * Holds the client we will reuse in this class
     *
     * @var Client
     */
    protected $client = null;

    /**
     * Holds the endpoint urls
     *
     * @var string
     */
    protected $endpointHost = 'http://localhost/mock-api';
    protected $endpointLoginTmp = '/login/%s';
    protected $endpointUserLogin = '/login';

    /**
     * Create a new instance of the Client if we don't have it or
     * return the one we already have to reuse
     *
     * @return Client
     */
    protected function getClientInstance()
    {
        if ($this->client === null) {
            $this->client = new Client();
            $this->client->setEncType(Client::ENC_URLENCODED);
        }

        return $this->client;
    }

    public function getUser($id)
    {
        $url = $this->endpointHost . sprintf($this->endpointLoginTmp, $id);
        return $this->doRequest($url);
    }

    /**
     * Perform an API request to user login
     *
     * @param array $postData
     * @return Zend\Http\Response
     */
    public function authenticate($postData)
    {
        $url = $this->endpointHost . $this->endpointUserLogin;
        return $this->doRequest($url, $postData, Request::METHOD_POST);
    }

    /**
     * Perform a request to the API
     *
     * @param string $url
     * @param array $postData
     * @param Client $client
     * @return Zend\Http\Response
     * @author Christopher
     */
    protected function doRequest($url, array $postData = null, $method = Request::METHOD_GET)
    {
        $client = $this->getClientInstance();
        $client->setUri($url);
        $client->setMethod($method);

        if ($postData !== null) {
            $client->setParameterPost($postData);
        }

        $response = $client->send();

        if ($response->isSuccess()) {
            return JsonDecoder::decode($response->getBody(), Json::TYPE_ARRAY);
        } else {
            $logger = new Logger;
            $logger->addWriter(new Stream('data/logs/apiclient.log'));
            $logger->debug($response->getBody());
            return FALSE;
        }
    }
}