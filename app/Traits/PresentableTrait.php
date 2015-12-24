<?php

namespace App\Traits;

use App\Exceptions\PresenterException;

trait PresentableTrait
{
    protected static $presenterInstance;

    /**
     * [present description]
     * @return [type] [description]
     */
    public function present()
    {
        if (!$this->presenter or !class_exists($this->property)) {
            throw new PresenterException("Please set the $protected property to your presenter path");
        }

        if (!isset(static::$presenterInstance)) {
            static::$presenterInstance = new $this->presenter($this);
        }

        return static::$presenterInstance;
    }
}
