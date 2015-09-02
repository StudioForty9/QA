<?php

use Behat\Behat\Context\Step;

/**
 * Category context.
 */
class CategoryContext extends MagentoProjectContext
{
    protected $_category = null;

    public function __construct()
    {
        $store = Mage::app()->getStore()->getId();
        $rootCategoryId = Mage::app()->getStore()->getRootCategoryId();

        $rootpath = Mage::getModel('catalog/category')
            ->setStoreId($store)
            ->load($rootCategoryId)
            ->getPath();

        $products = Mage::getModel('catalog/product')->getCollection();

        $collection = Mage::getModel('catalog/category')->setStoreId($store)
            ->getCollection()
            ->addAttributeToSelect(array('is_active', 'url_key', 'name'))
            ->addAttributeToFilter('path', array("like"=>$rootpath."/"."%"))
            ->addAttributeToFilter('is_active', 1)
            ->setPageSize(1);

        $products->addCountToCategories($collection);

        $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));

        $this->_category = $collection->getFirstItem();
    }


    /**
     * @Given /^I am on a category page$/
     */
    public function iAmOnACategoryPage()
    {
        $url = Mage::getBaseUrl() . 'catalog/category/view/id/' . $this->_category->getId();
        $this->getSession()->visit($url);
    }

    /**
     * @Then /^I should see the category name$/
     */
    public function iShouldSeeTheCategoryName()
    {
        $this->getMainContext()->assertElementContainsText('h1', $this->_category->getName());
    }

    /**
     * @Given /^I should see the breadcrumbs$/
     */
    public function iShouldSeeTheBreadcrumbs()
    {
        $className = $this->getClassNameByTheme('breadcrumbs');
        $this->getMainContext()->assertElementOnPage($className);
    }

    /**
     * @Given /^I should see a list of products$/
     */
    public function iShouldSeeAListOfProducts()
    {
        $this->getMainContext()->assertElementOnPage('.category-products');
    }

    /**
     * @Given /^each product should have a price$/
     */
    public function eachProductShouldHaveAPrice()
    {
        $this->getMainContext()->assertElementOnPage('.price-box');
    }

    /**
     * @When /^I click on a product$/
     */
    public function iClickOnAProduct()
    {
        $className = $this->getClassNameByTheme('productNameOnCategoryPage');
        $link = $this->getSession()->getPage()->find('css', $className);
        $this->getSession()->visit($link->getAttribute('href'));
    }

    /**
     * @Then /^I should be on a product page$/
     */
    public function iShouldBeOnAProductPage()
    {
        $this->getMainContext()->assertElementOnPage('body.catalog-product-view');
    }

    /**
     * @When /^I click on the add to cart button of a product$/
     */
    public function iClickOnTheAddToCartButtonOfAProduct()
    {
        
        $context = $this->getMainContext();
        $context->pressButton('Add to Cart');
    }

    /**
     * @Then /^I can change what attribue to sort by$/
     */
    public function iCanChangeWhatAttribueToSortBy()
    {
        $select = $this->getSession()->getPage()->find("css", ".sort-by select");
        $option = $this->getSession()->getPage()->find("css", ".sort-by option[text=\"Name\"]");
        $select->selectOption($option, false);
        $this->waitForAjax();
    }

    /**
     * @Given /^I can change the sort direction$/
     */
    public function iCanChangeTheSortDirection()
    {
        
        $context = $this->getMainContext();
        $context->clickLink("Set Descending Direction");
        $this->waitForAjax();
    }

    /**
     * @Given /^I can paginate forwards through the list of results$/
     */
    public function iCanPaginateForwardsThroughTheListOfResults()
    {
        //@TODO
        return true;
    }

    /**
     * @Given /^I can paginate backwards through the list of results$/
     */
    public function iCanPaginateBackwardsThroughTheListOfResults()
    {
        //@TODO
        return true;
    }

    /**
     * @Given /^I can change the number of products to show per page$/
     */
    public function iCanChangeTheNumberOfProductsToShowPerPage()
    {
        $select = $this->getSession()->getPage()->find("css", ".limiter select");
        $option = $this->getSession()->getPage()->find("css", ".limiter option[text=\"24\"]");
        $select->selectOption($option, false);
        $this->waitForAjax();
    }

    /**
     * @Given /^I can change to a grid view$/
     */
    public function iCanChangeToAGridView()
    {
        
        $context = $this->getMainContext();
        $context->clickLink("Grid");
        $this->waitForAjax();
    }

    /**
     * @Given /^I can change to a list view$/
     */
    public function iCanChangeToAListView()
    {
        
        $context = $this->getMainContext();
        $context->clickLink("List");
        $this->waitForAjax();
    }

    public function waitForAjax(){
        $this->getSession()->wait(10, '(0 === Ajax.activeRequestCount)');
        sleep(1);
    }
}
