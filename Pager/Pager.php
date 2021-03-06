<?php


namespace pingdecopong\PagerBundle\Pager;

use pingdecopong\PagerBundle\Pager\PagerColumn\PagerColumn;
use pingdecopong\PagerBundle\Pager\PagerSelector\PagerSelector;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormView;

class Pager {

    /**
     * @var \Symfony\Component\Form\FormFactory
     */
    private $formFactory;
    /**
     * @var \Symfony\Component\Validator\Validator
     */
    private $validator;

    private $pagerSelector;
    private $pagerColumn;

    private $allFormView;
    private $pagerFormView;

    private $linkRouteName;

    private $formName;

    /**
     * @param mixed $linkRouteName
     */
    public function setLinkRouteName($linkRouteName)
    {
        $this->linkRouteName = $linkRouteName;
    }

    /**
     * @return mixed
     */
    public function getLinkRouteName()
    {
        return $this->linkRouteName;
    }

    /**
     * @param mixed $allFormView
     */
    public function setAllFormView($allFormView)
    {
        $this->allFormView = $allFormView;
    }

    /**
     * @return mixed
     */
    public function getAllFormView()
    {
        return $this->allFormView;
    }

    /**
     * @param mixed $pagerFormView
     */
    public function setPagerFormView($pagerFormView)
    {
        $this->pagerFormView = $pagerFormView;
    }

    /**
     * @return mixed
     */
    public function getPagerFormView()
    {
        return $this->pagerFormView;
    }

    /**
     * @param mixed $maxSelector
     */
    public function setMaxSelector($maxSelector)
    {
        $this->pagerSelector->setMaxSelector($maxSelector);
    }

    /**
     * @return mixed
     */
    public function getMaxSelector()
    {
        return $this->pagerSelector->getMaxSelector();
    }

    public function getPageNo()
    {
        return $this->pagerSelector->getPageNo();
    }
    public function getPageSize()
    {
        return $this->pagerSelector->getPageSize();
    }
    public function getSortName()
    {
        return $this->pagerColumn->getSortName();
    }
    public function getSortType()
    {
        return $this->pagerColumn->getSortType();
    }


    public function __construct($formFactory, $validator, $formName = 'p')
    {
        $this->formFactory = $formFactory;
        $this->validator = $validator;
        $this->formName = $formName;

        $this->pagerSelector = new PagerSelector($formFactory);
        $this->pagerColumn = new PagerColumn($formFactory);

    }

    /**
     * @param int $allCount
     */
    public function setAllCount($allCount)
    {
        $this->pagerSelector->setAllCount($allCount);
    }

    /**
     * @return int
     */
    public function getAllCount()
    {
        return $this->pagerSelector->getAllCount();
    }

    public function addColumn($name, $option)
    {
        $this->pagerColumn->addColumn($name, $option);

        return $this;
    }

    public function getColumn($name)
    {
        return $this->pagerColumn->getColumn($name);
    }

    public function getFormName()
    {
        return $this->formName;
    }

    public function getMaxPageNum()
    {
        return $this->pagerSelector->getMaxPageNum();
    }

    public function isValid()
    {
        //pager column
        $columnErrors = $this->validator->validate($this->pagerColumn->getFormModel());
        if(count($columnErrors)>0){
            return false;
        }

        //pager selector
        $selectorErrors = $this->validator->validate($this->pagerSelector->getFormModel());
        if(count($selectorErrors)>0){
            return false;
        }

        return true;
    }

    public function getFormBuilder()
    {
        $pagerSelectorFormBuilder = $this->pagerSelector->getFormBuilder();
        $pagerColumnFormBuilder = $this->pagerColumn->getFormBuilder();

        $formBuilder = $this->formFactory->createNamedBuilder($this->formName, 'form', null, array('csrf_protection' => false))
            ->add($pagerSelectorFormBuilder)
            ->add($pagerColumnFormBuilder);

        return $formBuilder;
    }

    public function createView()
    {
        $pagerView = new PagerView();

        //pagerColumn
        $pagerColumnView = $this->pagerColumn->createView();
        $pagerView->setPagerColumn($pagerColumnView);

        //pagerSelector
        $pagerSelectorView = $this->pagerSelector->createView();
        $pagerView->setPagerSelector($pagerSelectorView);

        //form view
//        $pagerView->setFormView($this->getPagerFormView());
        //all form view
        $pagerView->setAllFormView($this->allFormView);
        //pager form view
        $pagerView->setPagerFormView($this->pagerFormView);

        //all count
        $pagerView->setAllCount($this->pagerSelector->getAllCount());
        
        //ページ番号クリック時のリンクパラメータ作成
        $queryAllData = $this->getAllFormQueryStrings();
        $queryPagerData = $this->getPagerFormQueryKeyStrings();
        foreach($pagerView->getPagerSelector()->getPageNo()->getRows() as $value)
        {
            /* @var $value \pingdecopong\PagerBundle\Pager\PagerSelector\PagerSelectorNoRowView */
            $temp = $queryAllData;
            $temp[$queryPagerData['pageNo']] = $value->getPageNo();

            $value->setQuery($temp);
        }

        //カラム名クリック時のリンクパラメータ作成
        foreach($pagerView->getPagerColumn()->getRows() as $value)
        {
            /* @var $value \pingdecopong\PagerBundle\Pager\PagerColumn\PagerColumnRowView */
            $temp = $queryAllData;
            $temp[$queryPagerData['pageNo']] = 1;
            $temp[$queryPagerData['sortName']] = $value->getKeyName();
            if($value->getSortSelected() && $value->getSortType() == 'asc')
            {
                $temp[$queryPagerData['sortType']] = 'desc';
            }else
            {
                $temp[$queryPagerData['sortType']] = 'asc';
            }

            $value->setQuery($temp);
        }

        //ページサイズ変更時のリンクパラメータ作成
        foreach($this->pagerSelector->getPageSizeList() as $key => $value)
        {
            /* @var $value \pingdecopong\PagerBundle\Pager\PagerColumn\PagerColumnRowView */
            $temp = $queryAllData;
            $temp[$queryPagerData['pageNo']] = 1;
            $temp[$queryPagerData['pageSize']] = $value;

            $pagerView->addPageSizeParamList($key, $temp);
        }

        //prev クリック時のリンクパラメータ作成
        $prevPageNo = $pagerView->getPagerSelector()->getPageNo()->getPrevPageNo();
        /* @var $value \pingdecopong\PagerBundle\Pager\PagerSelector\PagerSelectorNoRowView */
        $temp = $queryAllData;
        $temp[$queryPagerData['pageNo']] = $prevPageNo;
        $pagerView->getPagerSelector()->getPageNo()->setPrevPageQuery($temp);

        //next クリック時のリンクパラメータ作成
        $nextPageNo = $pagerView->getPagerSelector()->getPageNo()->getNextPageNo();
        /* @var $value \pingdecopong\PagerBundle\Pager\PagerSelector\PagerSelectorNoRowView */
        $temp = $queryAllData;
        $temp[$queryPagerData['pageNo']] = $nextPageNo;
        $pagerView->getPagerSelector()->getPageNo()->setNextPageQuery($temp);

        //route name
        $pagerView->setLinkRouteName($this->linkRouteName);

        return $pagerView;
    }

    /**
     * GETパラメータ用配列取得（全フォーム）
     * @param bool $validCheck
     * @return array
     */
    public function getAllFormQueryStrings($validCheck = true)
    {
        $queryAllData = array();
        $this->generateQueryArray($this->allFormView, $queryAllData, $validCheck);
        return $queryAllData;
    }

    /**
     * GETパラメータのKEY用配列取得（ページャー用フォームのみ）
     * @return array
     */
    public function getPagerFormQueryKeyStrings()
    {
        $queryPagerData = array();
        $this->getPagerFormQueryNames($this->pagerFormView ,$queryPagerData);
        return $queryPagerData;
    }

    private function generateQueryArray(FormView $formView, &$queryArray, $validCheck = true, $choiceFlag = false, $expanded = false, $multiple = false)
    {
        if(count($formView) == 0)
        {
            if($validCheck && $formView->vars['valid'] == false){
                $queryArray[$formView->vars['full_name']] = '';
            }else{

                if($choiceFlag){

                    if($expanded == true && $multiple == true){

                        if($formView->vars['checked'] == true){
                            $key = $replace = substr( $formView->vars['full_name'] , 0 , strlen($formView->vars['full_name'])-2 );

                            if(!isset($queryArray[$key])){
                                $queryArray[$key] = array();
                            }
                            $queryArray[$key][] = $formView->vars['value'];
                        }

                    }else if($expanded == true && $multiple == false){

                        if($formView->vars['checked'] == true){
                            $queryArray[$formView->vars['full_name']] = $formView->vars['value'];
                        }

                    }else if($expanded == false && $multiple == true){

                        $key = $replace = substr( $formView->vars['full_name'] , 0 , strlen($formView->vars['full_name'])-2 );
                        $queryArray[$key] = $formView->vars['value'];

                    }else if($expanded == false && $multiple == false){

                        $queryArray[$formView->vars['full_name']] = $formView->vars['value'];

                    }

                }else{
                    $queryArray[$formView->vars['full_name']] = $formView->vars['value'];
                }

            }
        }else
        {
            foreach($formView as $value)
            {
                //add start
                if($choiceFlag == false &&
                    isset($value->vars['expanded']) && $value->vars['expanded'] == true &&
                    isset($value->vars['multiple']) && $value->vars['multiple'] == true
                ){

                    $this->generateQueryArray($value, $queryArray, true, true, true, true);

                }else if($choiceFlag == false &&
                    isset($value->vars['expanded']) && $value->vars['expanded'] == true &&
                    isset($value->vars['multiple']) && $value->vars['multiple'] == false
                ){

                    $this->generateQueryArray($value, $queryArray, true, true, true, false);

                }else if($choiceFlag == false &&
                    isset($value->vars['expanded']) && $value->vars['expanded'] == false &&
                    isset($value->vars['multiple']) && $value->vars['multiple'] == true
                ){

                    $this->generateQueryArray($value, $queryArray, true, true, false, true);

                }else if($choiceFlag == false &&
                    isset($value->vars['expanded']) && $value->vars['expanded'] == false &&
                    isset($value->vars['multiple']) && $value->vars['multiple'] == false
                ){

                    $this->generateQueryArray($value, $queryArray, true, true, false, false);

                }else{
                    $this->generateQueryArray($value, $queryArray, true, $choiceFlag, $expanded, $multiple);
                }

            }
        }
        return $queryArray;
    }
    private  function getPagerFormQueryNames(FormView $pagerFormView, &$queryArray)
    {
        if(count($pagerFormView) == 0)
        {
            $queryArray[$pagerFormView->vars['name']] = $pagerFormView->vars['full_name'];
        }else
        {
            foreach($pagerFormView as $value)
            {
                $this->getPagerFormQueryNames($value, $queryArray);
            }
        }
        return $queryArray;
    }

}