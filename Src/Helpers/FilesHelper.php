<?php

namespace Safecrow\Helpers;

class FilesHelper
{
    /**
     * �������������� ���� ��� ����������
     * @param src - ���� � ����� ������������ ����� �����
     */
    public static function prepareFile($src)
    {
        $src = $_SERVER['DOCUMENT_ROOT'] . "/" . trim($src, "/");
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        
        if(!file_exists($src)) {
            return false;
        }
        
        return array(
            'file_name' => basename($src),
            'content_type' => finfo_file($finfo, $src),
            'content' => base64_decode(file_get_contents($src))
        );
    }
}