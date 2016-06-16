<?php

namespace Safecrow;

use Safecrow\Http\Client;
use Safecrow\Exceptions\ClaimsException;
use Safecrow\Enum\ClaimReasons;

class Claims
{
    private 
        $client,
        $orderId
    ;
    
    public function __construct(Client $client, $orderId)
    {
        $this->client = $client;
        $this->orderId = $orderId;
    }
    
    /**
     * �������� ������
     * 
     * @param array $fields
     * @return array
     */
    public function create(array $fields)
    {
        $this->validate($fields);
        
        $res = $this->getClient()->post("/orders/{$this->getOrderId()}/claim", array("claim" => $fields));
        
        return isset($res["claim"]) ? $res["claim"] : $res;
    }
    
    /**
     * ��������� ������ �� �����
     * @return unknown
     */
    public function getClaim()
    {
        $res = $this->getClient()->get("/orders/{$this->getOrderId()}/claim", array("claim" => $fields));
        
        return $res["claim"] ?: $res;
    }
    
    /**
     * ��������� ����� ������
     * 
     * @param array $fields
     * @throws \Safecrow\Exceptions\ClaimsException
     * @return void
     */
    private function validate(array $fields)
    {
        $arErrors = array();
        
        if(!isset($fields['reason']) || !in_array($fields['reason'], ClaimReasons::getClaimReasons())) {
            $arErrors['reason'] = "������������ ��� ������";
        }
        
        if(empty($fields['description'])) {
            $arErrors['description'] = "�� ������ ������������ � ������";
        }
        
        if(!empty($arErrors)) {
            $ex = new ClaimsException("�� ��������� ������������ ����");
            $ex->setData($arErrors);
            
            throw $ex;
        }
    }
    
    private function getClient()
    {
        return $this->client;
    }
    
    private function getOrderId()
    {
        return $this->orderId;
    }
}