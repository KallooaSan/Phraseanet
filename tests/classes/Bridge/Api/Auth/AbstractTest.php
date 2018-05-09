<?php

require_once __DIR__ . '/../../Bridge_datas.inc';

/**
 * @group functional
 * @group legacy
 */
class Bridge_Api_Auth_AbstractTest extends \PhraseanetTestCase
{

    public function testSet_settings()
    {
        $stub = $this->getMockForAbstractClass('Bridge_Api_Auth_Abstract');
        $setting = $this->createMock("Bridge_AccountSettings", [], [], '', false);
        $return = $stub->set_settings($setting);
        $this->assertEquals($stub, $return);
    }
}
