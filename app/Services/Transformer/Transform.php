<?php

namespace Videouri\Services\Transformer;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Serializer\DataArraySerializer;
use League\Fractal\TransformerAbstract;

/**
 * @package Services\Transformers
 */
class Transform
{
    /**
     * @param mixed $collection
     * @param TransformerAbstract $transformer
     *
     * @return array
     */
    public static function collection($collection, TransformerAbstract $transformer)
    {
        $resource = new Collection($collection, $transformer);
        return static::process($resource);
    }

    /**
     * @param mixed $item
     * @param TransformerAbstract $transformer
     *
     * @return array
     */
    public static function item($item, TransformerAbstract $transformer)
    {
        $resource = new Item($item, $transformer);
        return static::process($resource);
    }

    /**
     * @param ResourceInterface $resource
     *
     * @return array
     */
    public static function process(ResourceInterface $resource)
    {
        $fractalManager = new Manager();
        $fractalManager->setSerializer(new DataArraySerializer());

        $videos = $fractalManager->createData($resource)->toArray();

        return $videos['data'];
    }
}
