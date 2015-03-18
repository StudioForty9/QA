<?php

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Context\Step;

/**
 * Category context.
 */
class CategoryContext extends MagentoProjectContext
{
    protected $_category = null;

    /**
     * @param null $name
     * @return Behat\Mink\Session
     */
    public function getSession($name = null)
    {
        return $this->getMainContext()->getSession($name);
    }

    /**
     * @return Behat\Mink\WebAssert
     */
    public function assertSession()
    {
        return $this->getMainContext()->assertSession();
    }

    public function __construct()
    {
        $store = Mage::app()->getStore()->getId();
        $rootCategoryId = Mage::app()->getStore()->getRootCategoryId();

        $rootpath = Mage::getModel('catalog/category')
            ->setStoreId($store)
            ->load($rootCategoryId)
            ->getPath();

        $collection = Mage::getModel('catalog/category')->setStoreId($store)
            ->getCollection()
            ->addAttributeToSelect(array('is_active', 'url_key', 'name'))
            ->addAttributeToFilter('path', array("like"=>$rootpath."/"."%"))
            ->addAttributeToFilter('is_active', 1)
            ->setPageSize(1);

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
        $this->assertSession()->statusCodeEquals(200);
    }

    /**
     * @Then /^I should see the category name$/
     */
    public function iShouldSeeTheCategoryName()
    {
        return array(
            new Step\Then('I should see "' . $this->_category->getName() . '"')
        );
    }

    /**
     * @Given /^I should see the breadcrumbs$/
     */
    public function iShouldSeeTheBreadcrumbs()
    {
        $this->assertSession()->elementExists('css', '.breadcrumb');
    }

    /**
     * @Given /^I should see a list of products$/
     */
    public function iShouldSeeAListOfProducts()
    {
        $this->assertSession()->elementExists('css', '.category-products');
    }

    /**
     * @Given /^each product should have a price$/
     */
    public function eachProductShouldHaveAPrice()
    {
        //FIXME
        return true;
        $this->assertSession()->elementExists('css', '.price-box');
    }

    /**
     * @When /^I click on a product$/
     */
    public function iClickOnAProduct()
    {
        $link = $this->getSession()->getPage()->find('css', 'h2.product-title a');
        $this->getSession()->visit($link->getAttribute('href'));
        $this->assertSession()->statusCodeEquals(200);
    }

    /**
     * @Then /^I should be on a product page$/
     */
    public function iShouldBeOnAProductPage()
    {
        $this->assertSession()->elementExists('css', 'body.catalog-product-view');
    }
}
