<?php

//use Behat\Behat\Context\BehatContext;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Cart context.
 */
class AbstractContext extends RawMinkContext
{
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

    public function getRandomProduct()
    {
        $store = Mage::app()->getStore()->getId();

        $collection = Mage::getModel('catalog/product')->setStoreId($store)
            ->getCollection()
            ->addAttributeToSelect(array('status', 'name'))
            ->addAttributeToFilter('status', 1)
            ->addFieldToFilter('type_id', 'simple')
            ->setPageSize(1);

        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);

        $collection->getSelect()->order(new Zend_Db_Expr('RAND()'));

        return $collection->getFirstItem();
    }

    public function getClassNameByTheme($key){
        $rwd = array(
            'breadcrumbs' => '.breadcrumbs',
            'productNameOnCategoryPage' => 'h2.product-name a'
        );

        $other = array(
            'breadcrumbs' => '.breadcrumb',
            'productNameOnCategoryPage' => 'h2.product-title a'
        );

        $theme = $this->getMainContext()->getParameter('theme') == 'rwd' ? $rwd : $other;
        return $theme[$key];
    }
}
