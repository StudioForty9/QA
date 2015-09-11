<?php

use Behat\Behat\Context\Step;

require_once Mage::getBaseDir() . '/vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

/**
 * Category context.
 */
class SecurityContext extends MagentoProjectContext
{
    /**
     * @Then /^an admin user with username "([^"]*)" should not exist$/
     */
    public function anAdminUserWithUsernameShouldNotExist($arg1)
    {
        $admins = Mage::getModel('admin/user')->getCollection()
            ->addFieldToFilter('username', $arg1);

        assertEquals(0, $admins->count());
    }

    /**
     * @Then /^the config value for "([^"]*)" is "([^"]*)"$/
     */
    public function theConfigValueForIs($arg1, $arg2)
    {
        assertEquals(Mage::getStoreConfig($arg1), $arg2);
    }

    /**
     * @Then /^the site has a valid SSL certificate$/
     */
    public function theSiteHasAValidSslCertificate()
    {
        $url = str_replace('http://', 'https://', $this->getMinkParameter('base_url'));
        $this->getMainContext()->visit($url);
    }

    /**
     * @Given /^Wordpress is installed$/
     */
    public function wordpressIsInstalled()
    {
        $this->getMainContext()->visit($this->getMinkParameter('base_url') . 'wordpress');
        $this->getMainContext()->assertResponseStatus(200);
    }

    /**
     * @Then /^Wordpress is using the latest version$/
     */
    public function wordpressIsUsingTheLatestVersion()
    {
        $this->getMainContext()->visit($this->getMinkParameter('base_url') . 'wordpress/readme.html');
        $element = $this->find('xpath', '//*[@id="logo"]');
        assertNotNull($element);

        $versionString = $element->getText();
        $version = str_replace('Version ', '', $versionString);

        $this->getMainContext()->visit('https://wordpress.org/download/release-archive/');
        $element = $this->find('xpath', '//*[@id="pagebody"]/div/div[1]/table[1]/tbody/tr[1]/td[1]');
        $latestVersion = $element->getText();

        assertEquals($version, $latestVersion, 'Latest Version of Wordpress (' . $latestVersion . ') is not installed');
    }
}
