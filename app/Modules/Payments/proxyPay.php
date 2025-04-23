<?php

namespace App\Modules\Payments;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

class proxyPay
{
    private $reference;
    private $allReference;

    public function __construct()
    {   
        return $this->reference = $this->getReferenceId();
    }

    private function httpRequest($method, $uri, $body = null)
    { 
        $client = new Client(['base_uri' => 'https://api.sandbox.proxypay.co.ao/']);

        $headers = [
            'Authorization' => 'Token j8gc8b3s7mdnusa2hudqrcdn019emkk2',
            'Accept' => 'application/vnd.proxypay.v2+json',
            'Content-Type' => 'application/json'
        ];

        $options = [
            'headers' => $headers
        ];

        if ($body) {
            $options['body'] = json_encode($body);
        }

        try {
            return $client->request($method, $uri, $options);
        } catch (GuzzleException $e) {
            Log::error($e);
            die($e);
        }
    }

    private function getReferenceId()
    {
        try {
             $response = $this->httpRequest('POST', 'reference_ids');
            if ($response->getStatusCode() === 200) {
                 $contents = $response->getBody()->getContents();
                if (is_string($contents) && strlen($contents) === 9) {
                    return $contents;
                } else {
                    throw new \Exception('Got invalid response from proxyPay');
                }
            } else {
                throw new \Exception('Could not get a reference from proxyPay: ', $response->getStatusCode());
            }
        } catch (\Exception $e) {
            Log::error($e);
            die($e);
        }
    }

    public function createReferecia($body)
    {
        try {
            $reference = $this->reference;
            if (is_string($reference) && strlen($reference) === 9) {
                $response = $this->httpRequest('PUT', 'references/' . $reference, $body);
                if ($response->getStatusCode() === 204) {
                    return $reference;
                }
            }
        } catch (\Exception $e) {
            return $e;
            Log::error($e);
            die($e);
        }
    }
    public function getAllReferencia(){
        // VERIFICAR METODO QUE VAI CONSUMIR DA API TODOS AS REFERENCIAS DISPONÍVEIS
        // $allReference=$this->allReference;
        // $response=$this->httpRequest('PUT', 'references/'."168098940");
        // return $response->getStatusCode();
    }
   
    public function createPamenty($body)
    {
        try{
            // return $body;  
            $data = json_encode($body);
            $response = $this->httpRequest('PUT', 'payments'.$data);
            if ($response->getStatusCode() === 200) {
                return $response;
            } 
        } catch (\Exception $e) {
            return $e;
            Log::error($e);
            die($e);
        }
    }
}
