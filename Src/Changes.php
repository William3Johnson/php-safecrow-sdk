<?php

namespace Safecrow;

use Safecrow\Enum\ChangeTypes;
use Safecrow\Exceptions\ChangesException;
use Safecrow\Http\Client;

class Changes
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
     * �������� ������� �� ���������
     * 
     * @param array $fields
     * @return array
     */
    public function create(array $fields)
    {
        $this->validate($fields);
        
        $res = $this->getClient()->post("/orders/{$this->getOrderId()}/order_changes", array("order_change" => $fields));
        
        return isset($res['order_change']) ? $res['order_change'] : $res;
    }
    
    /**
     * ������������� ��������� ������
     * @param int $changeId
     * @throws \Safecrow\Exceptions\ChangesException
     * @return array
     */
    public function confirm($changeId)
    {
        if(!(int)$changeId) {
            throw new ChangesException("������������ id ������� �� ���������");
        }
        
        $res = $this->getClient()->post("/orders/{$this->getOrderId()}/order_changes/{$changeId}/confirm");
        
        return isset($res['order_change']) ? $res['order_change'] : $res;
    }
    
    /**
     * ���������� ������� �� ���������
     * @param int $changeId
     * @throws \Safecrow\Exceptions\ChangesException
     * @return array
     */
    public function reject($changeId)
    {
        if(!(int)$changeId) {
            throw new ChangesException("������������ id ������� �� ���������");
        }
        
        $res = $this->getClient()->post("/orders/{$this->getOrderId()}/order_changes/{$changeId}/reject");
        
        return isset($res['order_change']) ? $res['order_change'] : $res;
    }
    
    /**
     * ��������� ����� ������� �� ���������
     * 
     * @param array $fields
     * @throws \Safecrow\Exceptions\ChangesException
     */
    private function validate(array $fields)
    {
        $arErrors = array();
        
        if(!isset($fields['change_type']) || !in_array($fields['change_type'], ChangeTypes::getChangeTypes())) {
            $arErrors['change_type'] = "�� ������ ��� ���������";
        }
        
        if(
            isset($fields['change_type']) && $fields['change_type'] == ChangeTypes::PROLONG_PROTECTION && 
            (!isset($fields['prolong_protection_to']) || !strtotime($fields['prolong_protection_to']))
        ) {
            $arErrors['prolong_protection_to'] = "������� ������������ ����";
        }
        
        if(isset($fields['change_type']) && $fields['change_type'] == ChangeTypes::CHANGE_CONDITIONS && empty($fields['new_cost'])) {
            $arErrors['new_cost'] = "�� ������� ����� ���������";
        }
        
        if(!empty($arErrors)) {
            $ex = new ChangesException("�� ������� ������������ ����");
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