<?php

namespace Safecrow;

use Safecrow\Http\Client;
use Safecrow\Enum\PayerTypes;
use Safecrow\Enum\PaymentTypes;
use Safecrow\Exceptions\BillingException;

class Billing
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
     * ������������ ��������� ���������� �������� ������������
     * ��������! ��������� �� ����� ������������ � ����������� billing_info
     * 
     * @param array $fields
     * @return array
     */
    public function createBillingInfo($fields)
    {
        $this->validateBillingInfo($fields);
        
        $res = $this->getClient()->post("/orders/{$this->getOrderId()}/billing_info", array("billing_info" => $fields));

        return $res['billing_info'] ?: $res;
    }
    
    /**
     * ��������� ��������� ���������� �������� ������������ � ������� ������
     * 
     * @return array
     */
    public function getBillingInfo()
    {
        $res = $this->getClient()->get("/orders/{$this->getOrderId()}/billing_info");
        
        return $res['billing_info'] ?: $res;
    }
    
    private function getClient()
    {
        return $this->client;
    }
    
    private function getOrderId()
    {
        return $this->orderId;
    }
    
    /**
     * ��������� ����� ��������� ����������
     * 
     * @param array $fields
     * @throws \Safecrow\Exceptions\BillingException
     */
    private function validateBillingInfo($fields)
    {
        $arErrors = array();
        if(!in_array($fields['holder_type'], PayerTypes::getPayerTypes())) {
            $arErrors['holder_type'] = '������������ ��� �����������';
        }
        
        if(!int_array($fields['billing_type'], PaymentTypes::getPaymentTypes())) {
            $arErrors['billing_type'] = '������������ ��� ������';
        }
        
        if(
            $fields['holder_type'] == PayerTypes::PERSONAL && 
            $fields['billing_type'] == PaymentTypes::BANK_ACCOUNT &&
            empty($fields['payment_params']['name'])
        ) {
            $arErrors['payment_params']['name'] = "�� ������� ��� �����������";
        }
        
        if(empty($fields['payment_params']['bik'])) {
            $arErrors['payment_params']['bik'] = "�� ������ ���";
        }
        
        if(empty($fields['payment_params']['account'])) {
            $arErrors['payment_params']['account'] = "�� ������ ��������� ����";
        }
        
        if($fields['holder_type'] == PayerTypes::BUSINESS && $fields['billing_type'] == PaymentTypes::BANK_ACCOUNT) {
            if(empty($fields['payment_params']['ogrn'])) {
                $arErrors['payment_params']['ogrn'] = '�� ������ ����';
            }
            
            if(empty($fields['payment_params']['inn'])) {
                $arErrors['payment_params']['inn'] = '�� ������ ���';
            }
        }
        
        if(!empty($arErrors)) {
            $ex = new BillingException('�� ��������� ������������ ����');
            $ex->setData($arErrors);
            
            throw $ex;
        }
    }
}