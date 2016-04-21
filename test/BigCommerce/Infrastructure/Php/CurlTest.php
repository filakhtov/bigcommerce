<?php namespace test\BigCommerce\Infrastructure\Php;

use \BigCommerce\Infrastructure\Php\Curl;
use \BigCommerce\Infrastructure\Php\CurlProxy;
use \PHPUnit_Framework_MockObject_MockObject;

class CurlTest extends \PHPUnit_Framework_TestCase
{

    /** @var PHPUnit_Framework_MockObject_MockObject */
    private $curlAdapter;

    protected function setUp()
    {
        parent::setUp();
        $this->curlAdapter = $this->getMock(CurlProxy::class);
    }

    public function testInvoke()
    {
        $curlHandle = 'FakeCurlResource#1';
        $this->curlAdapter->expects($this->once())->method('init')->willReturn($curlHandle);

        $url = 'http://localhost/api/';
        $this->curlAdapter->expects($this->at(1))->method('setopt')->with($curlHandle, CURLOPT_RETURNTRANSFER, TRUE)->willReturn(TRUE);
        $this->curlAdapter->expects($this->at(2))->method('setopt')->with($curlHandle, CURLOPT_URL, $url)->willReturn(TRUE);

        $expectedResponse = '{"result": "hello world"}';
        $this->curlAdapter->expects($this->once())->method('exec')->with($curlHandle)->willReturn($expectedResponse);

        $curl = new Curl($this->curlAdapter);
        $curl->setUrl($url);
        $this->assertEquals($expectedResponse, $curl());
    }

    /** @expectedException \BigCommerce\Infrastructure\Php\CurlException */
    public function testConstructFails()
    {
        $this->curlAdapter->expects($this->once())->method('init')->willReturn(false);
        new Curl($this->curlAdapter);
    }

    public function testDestruct()
    {
        $curlHandle = 'FakeCurlResource#2';
        $this->curlAdapter->expects($this->once())->method('init')->willReturn($curlHandle);
        $this->curlAdapter->expects($this->once())->method('close')->with($curlHandle);

        new Curl($this->curlAdapter);
    }

    /** @expectedException \BigCommerce\Infrastructure\Php\CurlException */
    public function testInvokeFails()
    {
        $curlHandle = 'FakeCurlResource#3';
        $this->curlAdapter->expects($this->once())->method('init')->willReturn($curlHandle);
        $this->curlAdapter->expects($this->once())->method('exec')->willReturn(FALSE);

        $curl = new Curl($this->curlAdapter);
        $curl();
    }

    /** @expectedException \BigCommerce\Infrastructure\Php\CurlException */
    public function testSetUrlFail()
    {
        $curlHandle = 'FakeCurlResource#4';
        $url = 'htps://google.com/';

        $this->curlAdapter->expects($this->once())->method('init')->willReturn($curlHandle);
        $this->curlAdapter->expects($this->at(2))->method('setopt')->with($curlHandle, CurlProxy::URL, $url)->willReturn(FALSE);

        $curl = new Curl($this->curlAdapter);
        $curl->setUrl($url);
    }

}
