<?php

use Behat\Behat\Exception\PendingException;
use Behat\Behat\Context\BehatContext;

/**
 * Homepage context.
 */
class HomepageContext extends MagentoProjectContext
{
//    public function getSession($name = null)
//    {
//        return $this->getMainContext()->getSession($name);
//    }

    public function assertSession()
    {
        return $this->getMainContext()->assertSession();
    }

    /**
     * @Then /^I should see the logo$/
     */
    public function iShouldSeeTheLogo()
    {
        //FIXME
        //$this->assertSession()->elementExists('css', '.logo');
        $this->assertSession()->elementExists('css', '#logo');
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
        //$this->assertSession()->elementExists('css', '#nav');
        $this->assertSession()->elementExists('css', '#top-navigation');
    }

    /**
     * @Given /^I should see the footer$/
     */
    public function iShouldSeeTheFooter()
    {
        //$this->assertSession()->elementExists('css', '.footer');
        $this->assertSession()->elementExists('css', '#footer');
    }
}
