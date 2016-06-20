<?php

namespace Safecrow;

use Safecrow\Http\Client;
use Safecrow\Helpers\FilesHelper;

class Shipping
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
     * �������� ������� �� �������/��������
     * 
     * @param array $fields
     * @param bool $return - ���� true, �� ������ �� �������
     * @return array
     */
    public function create($fields, $return=false)
    {
        if(!empty($fields['attachment'])) {
            $fields['attachment'] = $this->processFiles($fields['attachment']);
        }
        
        $shipping = $return ? "shipping_back" : "shippng";
        $res = $this->getClient()->post("/orders/{$this->getOrderId()}/{$shipping}", array("tracking" => $fields));
        
        return $res['tracking'] ?: $res;
    }
    
    /**
     * ��������� ���������� � ��������/��������
     * @param bool $return - ���� true, �� ������ �� ������� 
     * @return array
     */
    public function get($return = false)
    {
        $shipping = $return ? "shipping_back" : "shippng";
        $res = $this->getClient()->get("/orders/{$this->getOrderId()}/{$shipping}");
        
        return $res['tracking'] ?: $res;
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
     * ��������� ���������� �������
     * @param array files
     * @throws \Safecrow\Exceptions\IncorrectAttachmentException
     * @return array
     */
    private function processFiles(array $files)
    {
        //���� �������� ����, �� ���������� �������� ���� � �����
        foreach ($files as $k => $file) {
            if(is_string($file)) {
                $files[$k] = FilesHelper::prepareFile($file);
            }
        }
        
        foreach($files as $k => $file) {
            if(!is_array($file)) {
                unset($files[$k]);
            }
            
            if(!App::IsAllowedFileType($file['content_type'])) {
                throw new IncorrectAttachmentException;
            }
        }
    }
}