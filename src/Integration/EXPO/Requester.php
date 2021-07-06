<?php

namespace Lifepet\Wallet\SDK\Integration\EXPO;


class Requester
{



    private $payload;
    private $url;
    private $response;
    private $info;

    /**
     * Requester constructor.
     * @param $url
     * @throws \Exception
     */
    public function __construct($url){
        $this->setUrl($url);
        $this->payload = [];
    }

    /**
     * @param string $url
     * @throws \Exception
     */
    public function setUrl(string $url) {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Exception('URL para requisição inválida');
        }

        $this->url = $url;
    }

    /**
     * @param Payload $payload
     * @return bool
     * @throws \Exception
     */
    public function request(Payload $payload) {
        $this->payload = array_merge($this->payload, $payload->toArray());

        $ch = curl_init( $this->url );
        $payload = json_encode( $this->payload );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Accept:application/json','Accept-encoding:gzip, deflate'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $this->response = curl_exec($ch);
        $this->info = curl_getinfo($ch);
        curl_close($ch);


        if($this->info['http_code'] != 200) {
            throw new \Exception($this->response);
        }

        return true;
    }

    public function response(){
        return json_decode($this->response);
    }
}