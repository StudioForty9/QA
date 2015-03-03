<?php

use Behat\Behat\Exception\PendingException;

/**
 * Homepage context.
 */
class HomepageContext extends \Behat\MinkExtension\Context\RawMinkContext
{
    /**
     * @Then /^I should see the logo$/
     */
    public function iShouldSeeTheLogo()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I should see a search box$/
     */
    public function iShouldSeeASearchBox()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I should see the navigation$/
     */
    public function iShouldSeeTheNavigation()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I should see the footer$/
     */
    public function iShouldSeeTheFooter()
    {
        throw new PendingException();
    }
}
