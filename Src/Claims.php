<?php

namespace Safecrow;

use Safecrow\Http\Client;
use Safecrow\Enum\Claims;
use Safecrow\Exceptions\ClaimsException;

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
    public function createClaim(array $fields)
    {
        $this->validate($fields);
        
        $res = $this->getClient()->post("/orders/{$this->getOrderId()}/claim", array("claim" => $fields));
        
        return $res["claim"] ?: $res;
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
        
        if(!in_array($fields['reason'], Claims::getClaims())) {
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