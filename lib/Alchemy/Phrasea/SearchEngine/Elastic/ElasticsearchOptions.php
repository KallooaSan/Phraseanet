<?php
/*
 * This file is part of Phraseanet
 *
 * (c) 2005-2016 Alchemy
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Alchemy\Phrasea\SearchEngine\Elastic;

class ElasticsearchOptions
{
    /** @var string */
    private $host;
    /** @var int */
    private $port;
    /** @var string */
    private $indexName;
    /** @var int */
    private $shards;
    /** @var int */
    private $replicas;
    /** @var int */
    private $minScore;
    /** @var  bool */
    private $highlight;

    /** @var  int[] */
    private $_customValues;
    private $activeTab;

    /**
     * Factory method to hydrate an instance from serialized options
     *
     * @param array $options
     * @return self
     */
    public static function fromArray(array $options)
    {
        $defaultOptions = [
            'host' => '127.0.0.1',
            'port' => 9200,
            'index' => '',
            'shards' => 3,
            'replicas' => 0,
            'minScore' => 4,
            'highlight' => true,
            'activeTab' => null,
        ];

        foreach(self::getAggregableTechnicalFields() as $k => $f) {
            $defaultOptions[$k.'_limit'] = 0;
        }
        $options = array_replace($defaultOptions, $options);


        $self = new self();
        $self->setHost($options['host']);
        $self->setPort($options['port']);
        $self->setIndexName($options['index']);
        $self->setShards($options['shards']);
        $self->setReplicas($options['replicas']);
        $self->setMinScore($options['minScore']);
        $self->setHighlight($options['highlight']);
        $self->setActiveTab($options['activeTab']);
        foreach(self::getAggregableTechnicalFields() as $k => $f) {
            $self->setAggregableFieldLimit($k, $options[$k.'_limit']);
        }


        return $self;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $ret = [
            'host' => $this->host,
            'port' => $this->port,
            'index' => $this->indexName,
            'shards' => $this->shards,
            'replicas' => $this->replicas,
            'minScore' => $this->minScore,
            'highlight' => $this->highlight,
            'activeTab' => $this->activeTab
        ];
        foreach(self::getAggregableTechnicalFields() as $k => $f) {
            $ret[$k.'_limit'] = $this->getAggregableFieldLimit($k);
        }

        return $ret;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = (int)$port;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $minScore
     */
    public function setMinScore($minScore)
    {
        $this->minScore = (int)$minScore;
    }

    /**
     * @return int
     */
    public function getMinScore()
    {
        return $this->minScore;
    }

    /**
     * @param string $indexName
     */
    public function setIndexName($indexName)
    {
        $this->indexName = $indexName;
    }

    /**
     * @return string
     */
    public function getIndexName()
    {
        return $this->indexName;
    }

    /**
     * @param int $shards
     */
    public function setShards($shards)
    {
        $this->shards = (int)$shards;
    }

    /**
     * @return int
     */
    public function getShards()
    {
        return $this->shards;
    }

    /**
     * @param int $replicas
     */
    public function setReplicas($replicas)
    {
        $this->replicas = (int)$replicas;
    }

    /**
     * @return int
     */
    public function getReplicas()
    {
        return $this->replicas;
    }

    /**
     * @return bool
     */
    public function getHighlight()
    {
        return $this->highlight;
    }

    /**
     * @param bool $highlight
     */
    public function setHighlight($highlight)
    {
        $this->highlight = $highlight;
    }

    public function setAggregableFieldLimit($key, $value)
    {
        $this->_customValues[$key.'_limit'] = $value;
    }

    public function getAggregableFieldLimit($key)
    {
        return $this->_customValues[$key.'_limit'];
    }

    public function getActiveTab()
    {
        return $this->activeTab;
    }
    public function setActiveTab($activeTab)
    {
        $this->activeTab = $activeTab;
    }

    public function __get($key)
    {
        if(!array_key_exists($key, $this->_customValues)) {
            $this->_customValues[$key] = 0;
        }
        return $this->_customValues[$key];
    }

    public function __set($key, $value)
    {
        $this->_customValues[$key] = $value;
    }

    public static function getAggregableTechnicalFields()
    {
        return [
            'base_aggregate' => [
                'label' => 'prod::facet:base_label',
                'field' => 'databox_name',
                'query' => 'database:%s',
            ],
            'collection_aggregate' => [
                'label' => 'prod::facet:collection_label',
                'field' => 'collection_name',
                'query' => 'collection:%s',
            ],
            'doctype_aggregate' => [
                'label' => 'prod::facet:doctype_label',
                'field' => 'type',
                'query' => 'type:%s',
            ],
            'camera_model_aggregate' => [
                'label' => 'Camera Model',
                'field' => 'metadata_tags.CameraModel.raw',
                'query' => 'meta.CameraModel:%s',
            ],
            'iso_aggregate' => [
                'label' => 'ISO',
                'field' => 'metadata_tags.ISO',
                'query' => 'meta.ISO=%s',
            ],
            'aperture_aggregate' => [
                'label' => 'Aperture',
                'field' => 'metadata_tags.Aperture',
                'query' => 'meta.Aperture=%s',
            ],
            'shutterspeed_aggregate' => [
                'label' => 'Shutter speed',
                'field' => 'metadata_tags.ShutterSpeed',
                'query' => 'meta.ShutterSpeed=%s',
            ],
            'flashfired_aggregate' => [
                'label' => 'FlashFired',
                'field' => 'metadata_tags.FlashFired',
                'query' => 'meta.FlashFired=%s',
                'choices' => [
                    "aggregated (2 values: fired = 0 or 1)" => -1,
                ],
            ],
            'framerate_aggregate' => [
                'label' => 'FrameRate',
                'field' => 'metadata_tags.FrameRate',
                'query' => 'meta.FrameRate=%s',
            ],
            'audiosamplerate_aggregate' => [
                'label' => 'Audio Samplerate',
                'field' => 'metadata_tags.AudioSamplerate',
                'query' => 'meta.AudioSamplerate=%s',
            ],
            'videocodec_aggregate' => [
                'label' => 'Video codec',
                'field' => 'metadata_tags.VideoCodec',
                'query' => 'meta.VideoCodec:%s',
            ],
            'audiocodec_aggregate' => [
                'label' => 'Audio codec',
                'field' => 'metadata_tags.AudioCodec',
                'query' => 'meta.AudioCodec:%s',
            ],
            'orientation_aggregate' => [
                'label' => 'Orientation',
                'field' => 'metadata_tags.Orientation',
                'query' => 'meta.Orientation=%s',
            ],
            'colorspace_aggregate' => [
                'label' => 'Colorspace',
                'field' => 'metadata_tags.ColorSpace',
                'query' => 'meta.ColorSpace:%s',
            ],
            'mimetype_aggregate' => [
                'label' => 'MimeType',
                'field' => 'metadata_tags.MimeType',
                'query' => 'meta.MimeType:%s',
            ],
        ];
    }

}
