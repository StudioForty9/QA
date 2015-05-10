<?php

use Behat\Behat\Exception\PendingException;
use Behat\Behat\Context\BehatContext;

/**
 * Homepage context.
 */
class HomepageContext extends MagentoProjectContext
{
    public function assertSession()
    {
        return $this->getMainContext()->assertSession();
    }

    /**
     * @Then /^I should see the logo$/
     */
    public function iShouldSeeTheLogo()
    {
        $this->assertSession()->elementExists('css', '.logo');
    }

    /**
     * @Given /^I should see a search box$/
     */
    public function iShouldSeeASearchBox()
    {
        $this->assertSession()->elementExists('css', '#search_mini_form');
    }

    /**
     * @Given /^I should see the navigation$/
     */
    public function iShouldSeeTheNavigation()
    {
        $this->assertSession()->elementExists('css', '#nav');
    }

    /**
     * @Given /^I should see the footer$/
     */
    public function iShouldSeeTheFooter()
    {
        $this->assertSession()->elementExists('css', '.footer');
    }
}
