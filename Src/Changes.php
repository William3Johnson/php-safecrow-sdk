<?php

namespace Safecrow;

use Safecrow\Enum\ChangeTypes;
use Safecrow\Exceptions\ChangesException;

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
        
        return $res['order_change'] ?: $res;
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
        
        return $res['order_change'] ?: $res;
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
        
        return $res['order_change'] ?: $res;
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
        
        if(!in_array($fields['change_type'], ChangeTypes::getChangeTypes())) {
            $arErrors['change_type'] = "�� ������ ��� ���������";
        }
        
        if($fields['change_type'] == ChangeTypes::PROLONG_PROTECTION && !strtotime($fields['prolong_protection_to'])) {
            $arErrors['prolong_protection_to'] = "������� ������������ ����";
        }
        
        if($fields['change_type'] == ChangeTypes::CHANGE_CONDITIONS && empty($fields['new_cost'])) {
            $arErrors['new_cost'] = "�� ������� ����� ���������";
        }
        
        if(!empty($arErrors)) {
            $ex = new ChangesException("�� ������� ������������ ����");
            $ex->setData($arErrors);
            
            throw new $ex;
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