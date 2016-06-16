<?php

namespace Safecrow;

use Safecrow\Http\Client;
use Safecrow\Exceptions\SubscriptionsException;

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
        if(empty($url)) {
            throw new SubscriptionsException("�� ������ url");
        }
        
        if(empty($states) || !is_array($states)) {
            throw new SubscriptionsException("�� ������� �������");
        }
        
        $data = array(
            'url' => $url,
            'to_states' => $states
        );
        
        if($subscribeId !== null) {
            $data['subscription_id'] = $subscribeId;
        }
        
        $res = $this->getClient()->post("/subscriptions", array("app_subscription" => $data));
        
        return isset($res['app_subscription']) ? $res['app_subscription'] : $res;
    }
    
    /**
     * ��������� ������ ��������
     * 
     * @return array
     */
    public function getList()
    {
        $res = $this->getClient()->get("/subscriptions");
        
        return isset($res['app_subscriptions']) ? $res['app_subscriptions'] : $res;
    }
    
    /**
     * �������� ��������
     * 
     * @param string $subscribeId
     * @return bool|array
     */
    public function unsubscribe($subscribeId)
    {
        $res = $this->getClient()->delete("/subscriptions/{$subscribeId}");
        
        return !$res ? true : $res;
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
        $res = $this->getClient()->post("/subscriptions/{$subscribeId}/confirm");
        
        return !$res ? true : $res;
    }
    
    private function getClient()
    {
        return $this->client;
    }
}