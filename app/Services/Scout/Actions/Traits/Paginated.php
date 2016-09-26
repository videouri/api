<?php

namespace Videouri\Services\Scout\Actions\Traits;

/**
 * @package Videouri\Services\Scout\Actions\Traits
 */
trait Paginated
{
    /**
     * @var integer
     */
    public $page;

    /**
     * @var integer
     */
    public $maxResults = 5;

    /**
     * @return integer
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param integer $page
     *
     * @return $this
     * @throws \Exception
     */
    public function setPage($page)
    {
        if (!is_numeric($page)) {
            throw new \Exception('Page number must be numeric.');
        }

        $this->page = $page;
        return $this;
    }

    /**
     * @return integer
     */
    public function getMaxResults()
    {
        return $this->maxResults;
    }

    /**
     * @param integer $maxResults
     *
     * @return $this
     * @throws \Exception
     */
    public function setMaxResults($maxResults)
    {
        if (!is_numeric($maxResults)) {
            throw new \Exception('Max results must be numeric.');
        }

        $this->maxResults = $maxResults;
        return $this;
    }
}
