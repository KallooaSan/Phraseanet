<?php

require_once __DIR__ . '/../Bridge_datas.inc';

/**
 * @group functional
 * @group legacy
 */
class Bridge_Api_ElementCollectionTest extends \PhraseanetTestCase
{

    public function testAdd_element()
    {
        $elements = [];
        $collection = new Bridge_Api_ElementCollection();
        $i = 0;
        while ($i < 5) {
            $elements[] = $element = $this->createMock("Bridge_Api_ElementInterface");
            $collection->add_element(new $element);
            $i ++;
        }
        $this->assertEquals(5, sizeof($collection->get_elements()));
    }
}
