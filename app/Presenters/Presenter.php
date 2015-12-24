<?php

namespace App\Presenters;

abstract class Presenter
{
    /**
     * [$entity description]
     * @var
     */
    protected $entity;

    /**
     * [__construct description]
     * @param [type] $entity [description]
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    /**
     * [__get description]
     * @param  [type] $propery [description]
     * @return [type]          [description]
     */
    public function __get($propery)
    {
        if (method_exists($this, $propery)) {
            return $this->{$propery}();
        }

        return $this->entity->{$propery};
    }
}
