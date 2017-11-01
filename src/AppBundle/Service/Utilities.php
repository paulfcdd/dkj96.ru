<?php

namespace AppBundle\Service;


use Doctrine\ORM\EntityManager;

class Utilities
{
    /** @var EntityManager $em */
    protected $em;

    /** @var string $objectName */
    protected $objectName;

    /** @var array $criteria */
    protected $criteria = [];

    /** @var array $orderBy */
    protected $orderBy = [];

    /** @var  integer | null $limit */
    protected $limit;

    /** @var  integer | null $offset */
    protected $offset;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->limit = null;
        $this->offset = null;
    }

    /**
     * @return string
     */
    public function getObjectName()
    {
        return $this->objectName;
    }

    /**
     * @param string $objectName
     * @return $this
     */
    public function setObjectName(string $objectName)
    {
        $this->objectName = $objectName;

        return $this;
    }

    /**
     * @return array
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param array $criteria
     * @return $this
     */
    public function setCriteria(array $criteria)
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * @return array
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * @param array $orderBy
     * @return $this
     */
    public function setOrderBy(array $orderBy)
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int|null $limit
     * @return Utilities
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int|null $offset
     * @return Utilities
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    public function getPages() {

        $pages = 1;

        $allRecords = $this->getAllRecords();

        $recordsToShow = $this->getRepository()->findBy($this->getCriteria(), $this->getOrderBy(), $this->getLimit(), $this->getOffset());

        $countAllRecords = count($allRecords);

        $countRecordsToShow = count($recordsToShow);

        if ($countAllRecords > $countRecordsToShow) {

            $pages = $countAllRecords / $countRecordsToShow;

            $pages = round($pages);

            return $pages;
        }

        return $pages;

    }

    /**
     * @return array
     */
    public function paginationAction() {

        return $this->getRepository()->findBy($this->getCriteria(), $this->getOrderBy(), $this->getLimit(), $this->getOffset());

    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getRepository() {

        $repository = $this->em->getRepository($this->getObjectName());

        return $repository;
    }

    /**
     * @return array
     */
    private function getAllRecords() {

        $records = $this->getRepository()->findAll();

        return $records;

    }

}