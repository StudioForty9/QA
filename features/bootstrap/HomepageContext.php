<?php

use Behat\Behat\Exception\PendingException;
use Behat\Behat\Context\BehatContext;

/**
 * Homepage context.
 */
class HomepageContext extends MagentoProjectContext
{
    /**
     * @Then /^I should see the logo$/
     */
    public function iShouldSeeTheLogo()
    {
        $this->getMainContext()->assertElementOnPage('.logo');
    }

    /**
     * @Given /^I should see a search box$/
     */
    public function iShouldSeeASearchBox()
    {
        $this->getMainContext()->assertElementOnPage('#search_mini_form');
    }

    /**
     * @Given /^I should see the navigation$/
     */
    public function iShouldSeeTheNavigation()
    {
        $this->getMainContext()->assertElementOnPage('#nav');
    }

    /**
     * @Given /^I should see the footer$/
     */
    public function iShouldSeeTheFooter()
    {
        $this->getMainContext()->assertElementOnPage('.footer');
    }
}
