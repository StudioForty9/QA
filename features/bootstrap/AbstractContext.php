<?php

use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Abstract context.
 * @method Behat\MinkExtension\Context\MinkContext getMainContext()
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
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getBasicProductCollection()
    {
        $store = Mage::app()->getStore()->getId();
        $collection = Mage::getModel('catalog/product')->setStoreId($store)
            ->getCollection()
            ->addAttributeToSelect(array('status', 'name', 'visibility'))
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('visibility', array('in' => array(2,4)))
            ->addFieldToFilter('type_id', 'simple');

        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);

        return $collection;
    }

    public function getRandomProduct()
    {
        $collection = $this->getBasicProductCollection();
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

    /**
     * Try to execute $callback with session as argument
     * for $retries with $sleep until a valid result or
     * false value.
     *
     * Used mainly to wait for elements in page while it loads.
     *
     * @param Closure $callback - callback to evaluate
     * @param integer $retries - max number of retries
     * @param int $sleep - sleep between every retry
     *
     * @return bool
     */
    public function tryCallback(\Closure $callback, $retries = 10, $sleep = 1)
    {
        static $supportsVisibilityCheck;
        $result = false;
        if ($supportsVisibilityCheck === null) {
            $refl = new \ReflectionMethod($cl = get_class($this->getSession()->getDriver()), 'isVisible');
            $supportsVisibilityCheck = $refl->getDeclaringClass()->getName() === $cl;
        }

        do {
            $result = $callback($this->getSession(), $supportsVisibilityCheck);
        } while (!$result && --$retries && sleep($sleep) !== false);
        return $result;
    }

    /**
     * Find an element matching $cond of $type withing $retries
     *
     * @param string $type - 'css', 'xpath'
     * @param string $cond - condition based on $type, css or xpath expression
     * @param integer $retries - max number of retries
     * @param float $sleep - sleep between every retry
     *
     * @return false or the first result of $cond expression
     */
    public function find($type, $cond, TraversableElement $parent = null, $retries = 10, $sleep = 1) {
        return $this->tryCallback(function($s, $svc) use ($type, $cond, $parent) {
            $parent = $parent ?: $s->getPage();
            if ($el = $parent->find($type, $cond)) {
                if (!$svc || ($svc && $el->isVisible())) {
                    return $el;
                }
            }
            return null;
        }, $retries, $sleep);
    }

    /**
     * @AfterScenario
     */
    public function takeScreenshotForFailedScenario($event)
    {
        if ($event->getResult() === 4) {
            Mage::log($event->getScenario()->getTitle(), null, 'behat.log', true);
            //Mage::log($this->getSession()->getCurrentUrl(), null, 'behat.log', true);

            $driver = $this->getSession()->getDriver();
            if (get_class($driver) == 'Behat\\Mink\\Driver\\Selenium2Driver') {
                try{
                    $path = Mage::getBaseDir() . '/var/behat/screenshots';
                    $io = new Varien_Io_File();
                    $io->checkAndCreateFolder($path);
                    $date = date('Ymdhis');
                    $this->saveScreenshot($date . '.png', $path);
                }catch(Exception $e){
                    Mage::log($e->getMessage(), null, 'behat.log', true);
                }
            }
        }
    }
}
