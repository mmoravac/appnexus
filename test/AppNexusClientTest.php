<?php
namespace MMoravac\AppNexusClient;

use stdClass;

class AppNexusClientTest extends \PHPUnit_Framework_TestCase
{
    private $http;
    private $storage;

    protected function setUp()
    {
        $this->http = $this->getMockBuilder('MMoravac\\AppNexusClient\\HttpClient')
            ->setMethods(array('call'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->storage = $this->getMockBuilder('MMoravac\\AppNexusClient\\TokenStorage')
        ->getMock();
    }

    public function testGetNewToken()
    {
        $client = new AppNexusClient('user', 'pass', 'http://example.com', $this->storage, $this->http);
        $this->http->expects($this->once())
            ->method('call')
            ->with(
                HttpMethod::POST,
                'http://example.com/auth',
                array(
                    'auth' => array(
                        'username' => 'user',
                        'password' => 'pass',
                    ),
                )
            )
            ->will($this->returnValue((object) array(
                'status' => 'OK',
                'token' => 'my_token',
            )));
        $this->assertEquals('my_token', $client->getNewToken());
    }

    public function testHotCache()
    {
        $this->storage->expects($this->once())
            ->method('get')
            ->with('user')
            ->will($this->returnValue('my_token'));

        $client = $this->getMockBuilder('MMoravac\\AppNexusClient\\AppNexusClient')
            ->setMethods(array('getNewToken'))
            ->setConstructorArgs(array('user', 'pass', 'http://example.com', $this->storage, $this->http))
            ->getMock();
        $client->expects($this->never())
            ->method('getNewToken');
        $response = (object) array('status' => 'OK', 'foo' => 'bar');
        $this->http->expects($this->once())
            ->method('call')
            ->with('PUT', 'http://example.com/my_path', array('my_foo' => 'my_bar'), array('Authorization: my_token'))
            ->will($this->returnValue($response));
        $this->assertEquals($response, $client->call('PUT', '/my_path', array('my_foo' => 'my_bar')));
    }

    public function testColdCache()
    {
        $this->storage->expects($this->once())
            ->method('get')
            ->with('user')
            ->will($this->returnValue(false));
        $this->storage->expects($this->once())
            ->method('set')
            ->with('user', 'my_token');

        $client = $this->getMockBuilder('MMoravac\\AppNexusClient\\AppNexusClient')
            ->setMethods(array('getNewToken'))
            ->setConstructorArgs(array('user', 'pass', 'http://example.com', $this->storage, $this->http))
            ->getMock();
        $client->expects($this->once())
            ->method('getNewToken')
            ->will($this->returnValue('my_token'));
        $response = (object) array('status' => 'OK', 'foo' => 'bar');
        $this->http->expects($this->once())
            ->method('call')
            ->with('PUT', 'http://example.com/my_path', array('my_foo' => 'my_bar'), array('Authorization: my_token'))
            ->will($this->returnValue($response));
        $this->assertEquals($response, $client->call('PUT', '/my_path', array('my_foo' => 'my_bar')));
    }

    public function testHotCacheExpiredToken()
    {
        $this->storage->expects($this->once())
            ->method('get')
            ->with('user')
            ->will($this->returnValue('old_token'));

        $this->storage->expects($this->once())
            ->method('set')
            ->with('user', 'new_token');

        $client = $this->getMockBuilder('MMoravac\\AppNexusClient\\AppNexusClient')
            ->setMethods(array('getNewToken'))
            ->setConstructorArgs(array('user', 'pass', 'http://example.com', $this->storage, $this->http))
            ->getMock();
        $client->expects($this->once())
            ->method('getNewToken')
            ->will($this->returnValue('new_token'));
        $response = (object) array('status' => 'OK', 'foo' => 'bar');
        $this->http->expects($this->at(0))
            ->method('call')
            ->with('PUT', 'http://example.com/my_path', array('my_foo' => 'my_bar'), array('Authorization: old_token'))
            ->will($this->throwException(new TokenExpiredException(new stdClass)));
        $this->http->expects($this->at(1))
            ->method('call')
            ->with('PUT', 'http://example.com/my_path', array('my_foo' => 'my_bar'), array('Authorization: new_token'))
            ->will($this->returnValue($response));

        $this->assertEquals($response, $client->call('PUT', '/my_path', array('my_foo' => 'my_bar')));
    }

    /**
     * @expectedException \MMoravac\AppNexusClient\TokenExpiredException
     */
    public function testFreshTokenExpired()
    {
        $this->storage->expects($this->once())
            ->method('get')
            ->with('user')
            ->will($this->returnValue('old_token'));

        $this->storage->expects($this->once())
            ->method('set')
            ->with('user', 'new_token');

        $client = $this->getMockBuilder('MMoravac\\AppNexusClient\\AppNexusClient')
            ->setMethods(array('getNewToken'))
            ->setConstructorArgs(array('user', 'pass', 'http://example.com', $this->storage, $this->http))
            ->getMock();
        $client->expects($this->once())
            ->method('getNewToken')
            ->will($this->returnValue('new_token'));
        $this->http->expects($this->at(0))
            ->method('call')
            ->with('PUT', 'http://example.com/my_path', array('my_foo' => 'my_bar'), array('Authorization: old_token'))
            ->will($this->throwException(new TokenExpiredException(new stdClass)));
        $this->http->expects($this->at(1))
            ->method('call')
            ->with('PUT', 'http://example.com/my_path', array('my_foo' => 'my_bar'), array('Authorization: new_token'))
            ->will($this->throwException(new TokenExpiredException(new stdClass)));

        $client->call('PUT', '/my_path', array('my_foo' => 'my_bar'));
    }
}
