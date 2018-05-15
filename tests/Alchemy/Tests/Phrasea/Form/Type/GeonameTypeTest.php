<?php

namespace Alchemy\Tests\Phrasea\Form\Type;

use Alchemy\Phrasea\Form\Type\GeonameType;

/**
 * @group functional
 * @group legacy
 */
class GeonameTypeTest extends \PhraseanetTestCase
{
    public function testGetParent()
    {
        $geoname = new GeonameType();
        $this->assertEquals('Symfony\Component\Form\Extension\Core\Type\TextType', $geoname->getParent());
    }

    public function testGetName()
    {
        $geoname = new GeonameType();
        $this->assertEquals('geoname', $geoname->getName());
    }
}
