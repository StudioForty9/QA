<?php

use Behat\MinkExtension\Context\MinkContext;

/**
 * Category context.
 */
class SecurityContext extends MinkContext
{
    use AbstractContext, MagentoProjectContext;

    /**
     * @Then /^an admin user with username "([^"]*)" should not exist$/
     */
    public function anAdminUserWithUsernameShouldNotExist($arg1)
    {
        $admins = Mage::getModel('admin/user')->getCollection()
            ->addFieldToFilter('username', $arg1);

        PHPUnit_Framework_Assert::assertEquals(0, $admins->count());
    }

    /**
     * @Then /^the site has a valid SSL certificate$/
     */
    public function theSiteHasAValidSslCertificate()
    {
        $url = str_replace('http://', 'https://', $this->getMinkParameter('base_url'));
        $this->visit($url);
    }

    /**
     * @Given /^Wordpress is installed$/
     */
    public function wordpressIsInstalled()
    {
        $this->visit($this->getMinkParameter('base_url') . 'wordpress');
        $this->assertResponseStatus(200);
    }

    /**
     * @Then /^Wordpress is using the latest version$/
     */
    public function wordpressIsUsingTheLatestVersion()
    {
        $this->visit($this->getMinkParameter('base_url') . 'wordpress/readme.html');
        $element = $this->find('xpath', '//*[@id="logo"]');

        $versionString = $element->getText();
        $version = str_replace('Version ', '', $versionString);

        $this->visit('https://wordpress.org/download/release-archive/');
        $element = $this->find('xpath', '//*[@id="pagebody"]/div/div[1]/table[1]/tbody/tr[1]/td[1]');
        $latestVersion = $element->getText();

        PHPUnit_Framework_Assert::assertEquals($version, $latestVersion, 'Latest Version of Wordpress (' . $latestVersion . ') is not installed');
    }
}
