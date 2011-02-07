<?php
class RouterTest extends PHPUnit_Framework_TestCase
{
    protected $router;

    protected function setUp()
    {
        $mapper = $this->getMockBuilder('BankAccountMapper')
                       ->disableOriginalConstructor()
                       ->getMock();

        $mapperFactory = $this->getMockBuilder('MapperFactory')
                              ->disableOriginalConstructor()
                              ->getMock();

        $mapperFactory->expects($this->any())
                      ->method('getMapper')
                      ->will($this->returnValue($mapper));

        $this->router = new Router(new ControllerFactory($mapperFactory));
        $this->router->set('bankaccount', 'BankAccountController');
    }

    /**
     * @covers Router::route
     */
    public function testCorrectControllerIsSelectedWithAction()
    {
        $request = new Request(
          array('REQUEST_URI' => '/bankaccount/show/id/1')
        );

        $this->assertInstanceOf(
          'BankAccountController', $this->router->route($request)
        );

        $this->assertEquals('show', $request->get('action'));
        $this->assertEquals(1, $request->get('id'));
    }

    /**
     * @covers Router::route
     */
    public function testCorrectControllerIsSelectedWithDefaultAction()
    {
        $request = new Request(
          array('REQUEST_URI' => '/bankaccount')
        );

        $this->assertInstanceOf(
          'BankAccountController', $this->router->route($request)
        );

        $this->assertEquals('default', $request->get('action'));
    }

    /**
     * @covers            Router::route
     * @expectedException RuntimeException
     */
    public function testExceptionWhenNoControllerCanBeSelected()
    {
        $request = new Request(array('REQUEST_URI' => '/is/not/configured'));
        $this->router->route($request);
    }

    /**
     * @covers            Router::route
     * @expectedException RuntimeException
     */
    public function testExceptionWhenSomethingIsWrongWithTheValues()
    {
        $request = new Request(array('REQUEST_URI' => '/bankaccount/show/id'));
        $this->router->route($request);
    }
}
