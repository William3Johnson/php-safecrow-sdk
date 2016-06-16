<?php

namespace Safecrow;

use Safecrow\Http\Client;
use Safecrow\Enum\PayerTypes;
use Safecrow\Enum\PaymentTypes;
use Safecrow\Exceptions\BillingException;

class Billing extends \PHPUnit_Framework_TestCase
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
    public function create($fields)
    {
        $this->validateBillingInfo($fields);
        
        $res = $this->getClient()->post("/orders/{$this->getOrderId()}/billing_info", array("billing_info" => $fields));

        return isset($res['billing_info']) ? $res['billing_info'] : $res;
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
        if(!isset($fields['holder_type']) || !in_array($fields['holder_type'], PayerTypes::getPayerTypes())) {
            $arErrors['holder_type'] = '������������ ��� �����������';
        }
        
        if(!isset($fields['billing_type']) ||  !in_array($fields['billing_type'], PaymentTypes::getPaymentTypes())) {
            $arErrors['billing_type'] = '������������ ��� ������';
        }
        
        if(empty($fields['payment_params']['bik'])) {
            $arErrors['payment_params']['bik'] = "�� ������ ���";
        }
        
        if(empty($fields['payment_params']['account'])) {
            $arErrors['payment_params']['account'] = "�� ������ ��������� ����";
        }
        
        
        
        if(isset($fields['holder_type']) && isset($fields['billing_type'])) {
            if(empty($fields['payment_params']['name']) && $fields['holder_type'] == PayerTypes::PERSONAL) {
                $arErrors['payment_params']['name'] = "�� ������� ��� �����������";
            }    
            
            if($fields['holder_type'] == PayerTypes::BUSINESS && $fields['billing_type'] == PaymentTypes::BANK_ACCOUNT) {
                if(empty($fields['payment_params']['organization'])) {
                    $arErrors['payment_params']['organization'] = '�� ������� �������� �����������';
                }
                
                if(empty($fields['payment_params']['ogrn'])) {
                    $arErrors['payment_params']['ogrn'] = '�� ������ ����';
                }
        
                if(empty($fields['payment_params']['inn'])) {
                    $arErrors['payment_params']['inn'] = '�� ������ ���';
                }
            }
        }
        
        if(!empty($arErrors)) {
            $ex = new BillingException('�� ��������� ������������ ����');
            $ex->setData($arErrors);
            
            throw $ex;
        }
    }
}