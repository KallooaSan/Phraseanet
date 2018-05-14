<?php

require_once __DIR__ . '/../Bridge_datas.inc';

/**
 * @group functional
 * @group legacy
 */
class Bridge_Api_ContainerCollectionTest extends \PhraseanetTestCase
{

    public function testAdd_element()
    {
        $collection = new Bridge_Api_ContainerCollection();
        $i = 0;
        while ($i < 5) {
            $container = $this->createMock("Bridge_Api_ContainerInterface");
            $collection->add_element(new $container);
            $i ++;
        }
        $this->assertEquals(5, sizeof($collection->get_elements()));
    }
}
