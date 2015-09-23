<?php

use Behat\MinkExtension\Context\MinkContext;

/**
 * Config context.
 */
class ConfigContext extends MinkContext
{
    use AbstractContext, MagentoProjectContext;

    /**
     * @Then /^the Magento cronjob is running$/
     */
    public function theMagentoCronjobIsRunning()
    {
        $crons = Mage::getModel('cron/schedule')->getCollection()
            ->addFieldToFilter('created_at', array('date' => true, 'gt' => date('Y-m-d 00:00:00')));
        PHPUnit_Framework_Assert::assertGreaterThan(0, $crons->count());
    }

    /**
     * @Then /^caching is enabled$/
     */
    public function cachingIsEnabled()
    {
        $caches = Mage::getResourceModel('core/cache')->getAllOptions();
        $disabledCaches = array_search(0, $caches);
        PHPUnit_Framework_Assert::assertFalse($disabledCaches);
    }
}
