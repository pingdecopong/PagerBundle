<?php


namespace pingdecopong\PagerBundle\Pager\PagerSelector;

use pingdecopong\PagerBundle\Pager\PagerSelector\Form\PagerSelectorFormModel;
use pingdecopong\PagerBundle\Pager\PagerSelector\Form\PagerSelectorFormType;
use Symfony\Component\Form\FormFactory;

class PagerSelector {


    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    private $formFactory;
    private $formModel;
    private $formType;
    private $form;
    private $formCreatedFlug;

    private $allCount;
//    private $pageNo;
//    private $pageSize;

    private $maxSelector;

    function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
        $this->formCreatedFlug = false;
        $this->maxSelector = 10;

        $this->formModel = new PagerSelectorFormModel();
        $this->formType = new PagerSelectorFormType();
    }

    public function getFormBuilder()
    {
        //基礎フォーム生成
        $formBuilder = $this->formFactory->createBuilder($this->formType, $this->formModel, array('csrf_protection' => false));
        return $formBuilder;
    }

    /**
     * @return mixed
     */
    public function getForm()
    {
        if(!$this->formCreatedFlug){
            $this->buildForm();
        }
        return $this->form;
    }

    /**
     * @param mixed $allCount
     */
    public function setAllCount($allCount)
    {
        $this->allCount = $allCount;
    }

    /**
     * @return mixed
     */
    public function getAllCount()
    {
        return $this->allCount;
    }

    /**
     * @return \pingdecopong\PagerBundle\Pager\PagerSelector\Form\PagerSelectorFormModel
     */
    public function getFormModel()
    {
        return $this->formModel;
    }

    /**
     * @param mixed $pageNo
     */
    public function setPageNo($pageNo)
    {
//        $this->pageNo = $pageNo;
        $this->formModel->setPageNo($pageNo);
    }

    /**
     * @return mixed
     */
    public function getPageNo()
    {
//        return $this->pageNo;
        return $this->formModel->getPageNo();
    }

    /**
     * @param mixed $pageSize
     */
    public function setPageSize($pageSize)
    {
//        $this->pageSize = $pageSize;
        $this->formModel->setPageSize($pageSize);
    }

    /**
     * @return mixed
     */
    public function getPageSize()
    {
//        return $this->pageSize;
        return $this->formModel->getPageSize();
    }

    /**
     * @param mixed $maxSelector
     */
    public function setMaxSelector($maxSelector)
    {
        $this->maxSelector = $maxSelector;
    }

    /**
     * @return mixed
     */
    public function getMaxSelector()
    {
        return $this->maxSelector;
    }

    public function setPageSizeList($pageSizeList)
    {
        $this->formType->setSizeList($pageSizeList);
    }
    public function getPageSizeList()
    {
        return $this->formType->getSizeList();
    }

    public function getMaxPageNum()
    {
        $totalNum = (int)($this->allCount/$this->formModel->getPageSize());
        if($this->allCount % $this->formModel->getPageSize() != 0){
            $totalNum++;
        }

        if($totalNum == 0){
            $totalNum = 1;
        }
        return $totalNum;
    }

    private function buildForm()
    {
        //基礎フォーム生成
        $formBuilder = $this->formFactory->createBuilder($this->formType, $this->formModel, array('csrf_protection' => false));
        $this->form = $formBuilder->getForm();

        $this->formCreatedFlug = true;
    }

    private function createPagerNoView()
    {
        $pageNoListView = new PagerSelectorNoView();

        $totalNum = (int)($this->allCount/$this->formModel->getPageSize());
        if($this->allCount % $this->formModel->getPageSize() != 0){
            $totalNum++;
        }

        //max column count
        $nowPageNo = $this->formModel->getPageNo() -1;
        $startPosition = $nowPageNo - (int)($this->maxSelector / 2);
        if($startPosition < 0){
            $startPosition = 0;
        }
        $endPosition = $startPosition + $this->maxSelector;
        if($endPosition > $totalNum){
            $gap = $endPosition - $totalNum;
            $startPosition -= $gap;
            if($startPosition < 0){
                $startPosition = 0;
            }

            $endPosition = $totalNum;
        }

        for($i=$startPosition; $i<$endPosition; $i++)
        {
            $row = new PagerSelectorNoRowView();

            //表示
            $row->setLabel($i + 1);

            //ページ番号
            $row->setPageNo($i + 1);

            //選択状態
            if($this->formModel->getPageNo() == $i+1){
                $row->setSelect(true);
            }else{
                $row->setSelect(false);
            }

            $pageNoListView->addRows($row);
        }

        //next
        $nexPageNo = $this->formModel->getPageNo() + 1;
        $nexPageStatus = true;
        if($nexPageNo > $totalNum){
            $nexPageNo = $totalNum;
            $nexPageStatus = false;
        }
        $pageNoListView->setNextPageNo($nexPageNo);
        $pageNoListView->setNextPageStatus($nexPageStatus);

        //previous
        $prevPageNo = $this->formModel->getPageNo() -1;
        $prevPageStatus = true;
        if($prevPageNo <= 0){
            $prevPageNo = 0;
            $prevPageStatus = false;
        }
        $pageNoListView->setPrevPageNo($prevPageNo);
        $pageNoListView->setPrevPageStatus($prevPageStatus);

        return $pageNoListView;
    }

    private function createPagerSizeView()
    {
        $pageSizeView = new PagerSelectorSizeView();


        return $pageSizeView;
    }

    public function createView()
    {
        $basicPagerView = new PagerSelectorView();

        //pageno
        $pageNoView = $this->createPagerNoView();
        $basicPagerView->setPageNo($pageNoView);

        //pagesize
        $pageSizeView = $this->createPagerSizeView();
        $basicPagerView->setPageSize($pageSizeView);

        //
//        $basicPagerView->setNextPageNo()

        return $basicPagerView;
    }

}