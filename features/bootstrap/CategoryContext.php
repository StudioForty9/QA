<?php

use Behat\MinkExtension\Context\MinkContext;

/**
 * Category context.
 */
class CategoryContext extends MinkContext
{
    use AbstractContext, MagentoProjectContext;

    protected $_category = null;
    protected $_theme = null;
    protected $_productLinkCSS = null;

    public function __construct($theme, $product_link_css = null)
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
            ->addAttributeToFilter('path', array("like" => $rootpath . "/" . "%"))
            ->addAttributeToFilter('is_active', 1)
            ->setPageSize(1);

        if (!$collection->getSize()) {
            $this->_category = $this->generateDummyCategory();
            $this->generateDummyProduct($this->_category);
            /* @var $indexCollection Mage_Index_Model_Resource_Process_Collection */
            $indexCollection = Mage::getModel('index/process')->getCollection();
            foreach ($indexCollection as $index) {
                /* @var $index Mage_Index_Model_Process */
                $index->reindexAll();
            }
            return;
        }

        $products->addCountToCategories($collection);

        $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));

        $this->_category = $collection->getFirstItem();
        $this->_theme = $theme;
        $this->_productLinkCSS = $product_link_css;
    }


    /**
     * @Given /^I am on a category page$/
     */
    public function iAmOnACategoryPage()
    {
        $url = $this->getMinkParameter('base_url') . 'catalog/category/view/id/' . $this->_category->getId();
        $this->getSession()->visit($url);
    }

    /**
     * @Then /^I should see the category name$/
     */
    public function iShouldSeeTheCategoryName()
    {
        $this->assertElementContainsText('h1', $this->_category->getName());
    }

    /**
     * @Given /^I should see a list of products$/
     */
    public function iShouldSeeAListOfProducts()
    {
        $this->assertElementOnPage('.category-products');
    }

    /**
     * @Given /^each product should have a price$/
     */
    public function eachProductShouldHaveAPrice()
    {
        $this->assertElementOnPage('.price-box');
    }

    /**
     * @When /^I click on a product$/
     */
    public function iClickOnAProduct()
    {

        $className = $this->_productLinkCSS ?  $this->_productLinkCSS :
            $this->getClassNameByTheme($this->_theme, 'productNameOnCategoryPage');
        $link = $this->getSession()->getPage()->find('css', $className);
        PHPUnit_Framework_Assert::assertNotNull($link);
        $this->getSession()->visit($link->getAttribute('href'));
    }

    /**
     * @Then /^I should be on a product page$/
     */
    public function iShouldBeOnAProductPage()
    {
        $this->assertElementOnPage('body.catalog-product-view');
    }

    /**
     * @When /^I click on the add to cart button of a product$/
     */
    public function iClickOnTheAddToCartButtonOfAProduct()
    {

        $context = $this;
        $context->pressButton('Add to Cart');
    }

    /**
     * @Then /^I can change what attribute to sort by$/
     */
    public function iCanChangeWhatAttributeToSortBy()
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

        $context = $this;
        $context->clickLink("Set Descending Direction");
        $this->waitForAjax();
    }

    /**
     * @Given /^I can paginate forwards through the list of results$/
     */
    public function iCanPaginateForwardsThroughTheListOfResults()
    {
        $element = $this->getSession()->getPage()->find('css', '.pagination .next');
        $element->click();
        assertNotNull($this->find('css', '.pagination .previous'));
    }

    /**
     * @Given /^I can paginate backwards through the list of results$/
     */
    public function iCanPaginateBackwardsThroughTheListOfResults()
    {
        $element = $this->getSession()->getPage()->find('css', '.pagination .previous');
        $element->click();
        assertNotNull($this->find('css', '.pagination .next'));
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

        $context = $this;
        $context->clickLink("Grid");
        $this->waitForAjax();
    }

    /**
     * @Given /^I can change to a list view$/
     */
    public function iCanChangeToAListView()
    {

        $context = $this;
        $context->clickLink("List");
        $this->waitForAjax();
    }

    public function waitForAjax()
    {
        $this->getSession()->wait(10, '(0 === Ajax.activeRequestCount)');
        sleep(1);
    }

    public function generateDummyCategory()
    {
        $category = Mage::getModel('catalog/category');
        $category->setData(array(
            'name' => 'Dummy Category',
            'url_key' => 'dummy-category',
            'display_mode' => 'PRODUCTS',
            'is_active' => 1,
            'is_anchor' => 1,
            'store_id' => Mage::app()->getStore()->getId()
        ));

        $category->setPath('1/2');

        $category->save();
        return $category;
    }

    public function generateDummyProduct($category)
    {
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $product = Mage::getModel('catalog/product');
        try {
            $product->setWebsiteIds(array(1))
                ->setAttributeSetId(4)
                ->setTypeId('simple')
                ->setCreatedAt(strtotime('now'))
                ->setSku('dummy-product-' . rand(0, 1000))
                ->setName('Dummy Product')
                ->setWeight(4.0000)
                ->setStatus(1)
                ->setTaxClassId(0)
                ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                ->setPrice(10.00)
                ->setDescription('This is a long description')
                ->setShortDescription('This is a short description')
                ->setStockData(array(
                        'use_config_manage_stock' => 0,
                        'manage_stock' => 1,
                        'min_sale_qty' => 1,
                        'max_sale_qty' => 2,
                        'is_in_stock' => 1,
                        'qty' => 999
                    )
                )
                ->setCategoryIds(array($category->getId()))
                ->save();
        } catch (Exception $e) {
            Mage::log($e->getMessage());
        }
    }

    /**
     * @AfterSuite
     */
    public static function cleanup($event)
    {
        $categories = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('name', 'Dummy Category')
            ->delete();

        $products = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('name', 'Dummy Product')
            ->delete();
    }
}
