<?php

use Behat\Behat\Exception\PendingException;

require_once Mage::getBaseDir() . '/vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

/**
 * Category context.
 */
class ConfigContext extends MagentoProjectContext
{
    /**
     * @Then /^the Magento cronjob is running$/
     */
    public function theMagentoCronjobIsRunning()
    {
        $crons = Mage::getModel('cron/schedule')->getCollection()
            ->addFieldToFilter('created_at', array('date' => true, 'gt' => date('Y-m-d 00:00:00')));
        assertGreaterThan(0, $crons->count());
    }

    /**
     * @Then /^caching is enabled$/
     */
    public function cachingIsEnabled()
    {
        $caches = Mage::getResourceModel('core/cache')->getAllOptions();
        $disabledCaches = array_search(0, $caches);
        assertFalse($disabledCaches);
    }
}
