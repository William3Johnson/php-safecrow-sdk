<?
namespace Safecrow\Tests;

use Safecrow\App;
use Safecrow\Config;
use Safecrow\Users;
use Safecrow\Orders;
use Safecrow\Subscriptions;

class AppTest extends \PHPUnit_Framework_TestCase
{
    /**
     * ������������ �������� ���������� ������ ����������
     * 
     * @test
     * @covers App::getHost()
     */
    public function createApp()
    {
        $host = Config::ENVIROMENT == "dev" ? Config::DEV_HOST : Config::PROD_HOST;
        
        $app = new App();
        
        $this->assertEquals($host, $app->getHost());
    }
    
    /**
     * ��������� ���������� ������ ��� ������ � ��������������
     * 
     * @test
     * @covers App::getUsers()
     */
    public function getUsersObject()
    {
        $app = new App();
        $this->assertInstanceOf(Users::class, $app->getUsers());
    }
    
    /**
     * ��������� ���������� ������ ��� ������ � ��������
     * @test
     * @covers App::getOrders($userId)
     */
    public function getOrdersObject()
    {
        $app = new App();
        $this->assertInstanceOf(Orders::class, $app->getOrders(406));
    }
    
    /**
     * ������ ��� ��������� ���������� ������ ��� ������ � ��������������
     * @test
     * @covers App::getOrders($userId)
     * @expectedException Safecrow\Exceptions\AuthException
     */
    public function getOrdersWithoutUserId()
    {
        $app = new App();
        $app->getOrders(null);
    }
    
    /**
     * ��������� ���������� ������ ��� ������ � ����������
     * @test
     * @covers App::getSubscriptions()
     */
    public function getSubscriptions()
    {
        $app = new App();
        $this->assertInstanceOf(Subscriptions::class, $app->getSubscriptions());
    }
   
    /**
     * �������� ������������ ���� �����
     * 
     * @test
     */
    public function testAllowedFiles()
    {
        $app = new App();
        
        $this->assertTrue(App::IsAllowedFileType("text/plain"));
        $this->assertFalse(App::IsAllowedFileType("text/xml"));
    }
}
