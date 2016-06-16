<?php

namespace Safecrow;

use Safecrow\Http\Client;

class Subscriptions
{
    private $client;
    
    public function __construct(Client $client)
    {
        $this->client = $client;
    }
    
    /**
     * �������� ��������
     * 
     * @param string $url
     * @param array $states
     * @param string $subscribeId
     * @return array
     */
    public function subscribe($url, array $states, $subscribeId = null)
    {
        $data = array(
            'url' => $url,
            'to_states' => $states
        );
        
        if($subscribeId !== null) {
            $data['subscription_id'] = $subscribeId;
        }
        
        $res = $this->getClient()->post("/subscriptions", $data);
        
        return $res['app_subscription'] ?: $res;
    }
    
    /**
     * ��������� ������ ��������
     * 
     * @return array
     */
    public function getList()
    {
        $res = $this->getClient()->get("/subscriptions");
        
        return $res['app_subscriptions'] ?: $res;
    }
    
    /**
     * �������� ��������
     * 
     * @param string $subscribeId
     * @return bool|array
     */
    public function unsubscribe($subscribeId)
    {
        $status = false;
        $res = $this->getClient()->delete("/subscriptions/{$subscribeId}", null, $status);
        
        return $status ?: $res;
    }
    
    /**
     * ������������ ��������
     * 
     * @param string $subscribeId
     * @return bool|array
     */
    public function confirm($subscribeId)
    {
        $status = false;
        $res = $this->getClient()->post("/subscription/{$subscribeId}/confirm", null, $status);
        
        return $status ?: $res;
    }
    
    private function getClient()
    {
        return $this->client;
    }
}