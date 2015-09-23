<?php

use Behat\MinkExtension\Context\MinkContext;

/**
 * Homepage context.
 */
class HomepageContext extends MinkContext
{
    use AbstractContext, MagentoProjectContext;

    /**
     * @Then /^I should see the logo$/
     */
    public function iShouldSeeTheLogo()
    {
        $this->assertElementOnPage('.logo');
    }

    /**
     * @Given /^I should see a search box$/
     */
    public function iShouldSeeASearchBox()
    {
        $this->assertElementOnPage('#search_mini_form');
    }

    /**
     * @Given /^I should see the navigation$/
     */
    public function iShouldSeeTheNavigation()
    {
        $this->assertElementOnPage('#nav');
    }

    /**
     * @Given /^I should see the footer$/
     */
    public function iShouldSeeTheFooter()
    {
        $this->assertElementOnPage('.footer');
    }
}
