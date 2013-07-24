<?php


namespace pingdecopong\PagerBundle\Pager\PagerSelector;


class PagerSelectorNoView {
    private $rows;
    private $nextPageNo;
    private $nextPageStatus;
    private $nextPageQuery;
    private $prevPageNo;
    private $prevPageStatus;
    private $prevPageQuery;


    function __construct()
    {
        $this->rows = array();
    }

    /**
     * @param mixed $nextPageQuery
     */
    public function setNextPageQuery($nextPageQuery)
    {
        $this->nextPageQuery = $nextPageQuery;
    }

    /**
     * @return mixed
     */
    public function getNextPageQuery()
    {
        return $this->nextPageQuery;
    }

    /**
     * @param mixed $prevPageQuery
     */
    public function setPrevPageQuery($prevPageQuery)
    {
        $this->prevPageQuery = $prevPageQuery;
    }

    /**
     * @return mixed
     */
    public function getPrevPageQuery()
    {
        return $this->prevPageQuery;
    }

    /**
     * @param PagerSelectorNoRowView $rows
     */
    public function addRows(PagerSelectorNoRowView $rows)
    {
        $this->rows[] = $rows;
    }

    /**
     * @return mixed
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @param mixed $nextPageNo
     */
    public function setNextPageNo($nextPageNo)
    {
        $this->nextPageNo = $nextPageNo;
    }

    /**
     * @return mixed
     */
    public function getNextPageNo()
    {
        return $this->nextPageNo;
    }

    /**
     * @param mixed $nextPageStatus
     */
    public function setNextPageStatus($nextPageStatus)
    {
        $this->nextPageStatus = $nextPageStatus;
    }

    /**
     * @return mixed
     */
    public function getNextPageStatus()
    {
        return $this->nextPageStatus;
    }

    /**
     * @param mixed $prevPageNo
     */
    public function setPrevPageNo($prevPageNo)
    {
        $this->prevPageNo = $prevPageNo;
    }

    /**
     * @return mixed
     */
    public function getPrevPageNo()
    {
        return $this->prevPageNo;
    }

    /**
     * @param mixed $prevPageStatus
     */
    public function setPrevPageStatus($prevPageStatus)
    {
        $this->prevPageStatus = $prevPageStatus;
    }

    /**
     * @return mixed
     */
    public function getPrevPageStatus()
    {
        return $this->prevPageStatus;
    }

}