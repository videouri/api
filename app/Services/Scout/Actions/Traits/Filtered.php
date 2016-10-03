<?php

namespace Videouri\Services\Scout\Actions\Traits;

use Session;

/**
 * @package Videouri\Services\Scout\Actions\Traits
 */
trait Filtered
{
    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    protected $sort = 'newest';

    /**
     * Sort period
     *
     * @var string
     */
    protected $period = 'ever';

    /**
     * If the country has not been manually set,
     * then return the user's country from the session token
     *
     * @return string
     */
    public function getCountry()
    {
        if ($this->country === null) {
            return Session::get('country');
        }

        return $this->country;
    }

    /**
     * @param string $country
     *
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param string $sort
     *
     * @return $this
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * @return string
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @param string $period
     *
     * @return $this
     */
    public function setPeriod($period)
    {
        $this->period = $period;
        return $this;
    }
}
