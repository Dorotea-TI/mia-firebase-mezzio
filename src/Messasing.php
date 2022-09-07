<?php

namespace Mia\Firebase;

use GuzzleHttp\Psr7\Request;

class Messasing
{
    /**
     * URL de la API
     */
    const BASE_URL = 'https://fcm.googleapis.com/fcm/send';
    /**
     * 
     * @var string
     */
    protected $apiKey = '';
    /**
     * @var \GuzzleHttp\Client
     */
    protected $guzzle;
    /**
     * 
     * @param string $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->guzzle = new \GuzzleHttp\Client();
    }
    /**
     * Funcion que se encarga de enviar una notificacion a un topico
     * @param array $topic
     * @param int $pushType
     * @param array $data
     * @return boolean
     */
    public function sendToTopic($topic, $pushType, $data = array())
    {
        // Agregamos el type a los datos
        $data['push_type'] = $pushType;
        // Creamos la peticion con los parametros necesarios
        return $this->generateRequest('POST', '', array(
            'to' => '/topics/'.$topic,
            'data' => $data,
        ));
    }
    /**
     * Funcion que se encarga de enviar una notificacion a un topico
     * @param array $topic
     * @param int $pushType
     * @param array $data
     * @return boolean
     */
    public function sendToTopicWithNotification($topic, $pushType, $data = array(), $title = '', $body = '')
    {
        // Agregamos el type a los datos
        $data['push_type'] = $pushType;
        // Creamos la peticion con los parametros necesarios
        return $this->generateRequest('POST', '', array(
            'to' => '/topics/'.$topic,
            'data' => $data,
            'notification' => array(
                'title' => $title,
                'body' => $body,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            )
        ));
    }
    /**
     * Funcion que se encarga de enviar una notificacion a los dispositivos elegidos
     * @param array $tokens
     * @param int $pushType
     * @param array $data
     * @return boolean
     */
    public function sendToDevices($tokens, $pushType, $data = array())
    {
        // Verificar si se enviaron tokens
        if(count($tokens) == 0){
            return false;
        }
        // Agregamos el type a los datos
        $data['push_type'] = $pushType;
        // Creamos la peticion con los parametros necesarios
        return $this->generateRequest('POST', '', array(
            'registration_ids' => $tokens,
            'data' => $data
        ));
    }
    /**
     * Funcion que se encarga de enviar una notificacion a los dispositivos elegidos
     * @param array $tokens
     * @param int $pushType
     * @param array $data
     * @return boolean
     */
    public function sendToDevicesWithNotification($tokens, $pushType, $data = array(), $title = '', $body = '')
    {
        // Verificar si se enviaron tokens
        if(count($tokens) == 0){
            return false;
        }
        // Agregamos el type a los datos
        $data['push_type'] = $pushType;
        // Creamos la peticion con los parametros necesarios
        return $this->generateRequest('POST', '', array(
            'registration_ids' => $tokens,
            'data' => $data,
            'notification' => array(
                'title' => $title,
                'body' => $body,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            )
        ));
    }
    /**
     * Funcion ara generar request
     */
    protected function generateRequest($method, $path, $params = null)
    {
        $body = null;
        if($params != null){
            $body = json_encode($params);
        }

        $request = new Request(
            $method, 
            self::BASE_URL . $path, 
            [
                'Content-Type' => 'application/json',
                'Authorization' => 'key=' . $this->apiKey
            ], $body);

        $response = $this->guzzle->send($request);
        if($response->getStatusCode() == 200){
            return json_decode($response->getBody()->getContents());
        }

        return null;
    }

    /**
     * 
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }
}