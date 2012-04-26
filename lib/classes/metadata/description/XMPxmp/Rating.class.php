<?php

/*
 * This file is part of Phraseanet
 *
 * (c) 2005-2012 Alchemy
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package
 * @license     http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link        www.phraseanet.com
 */
class metadata_description_XMPxmp_Rating extends metadata_Abstract implements metadata_Interface
{

  const SOURCE = '/rdf:RDF/rdf:Description/XMP-xmp:Rating';
  const NAME_SPACE = 'XMP-xmp';
  const TAGNAME = 'Rating';
  const MAX_LENGTH = 0;
  const TYPE = self::TYPE_FLOAT;
  const MULTI = false;

}
