<?php

use Behat\MinkExtension\Context\MinkContext;

/**
 * Customer context.
 */
class CustomerContext extends MinkContext
{
    use AbstractContext, MagentoProjectContext;

    /**
     * @When /^I go to the customer login page$/
     */
    public function iGoToTheCustomerLoginPage()
    {
        $url = $this->getMinkParameter('base_url') . 'customer/account/login';
        $this->getSession()->visit($url);
    }

    /**
     * @Given /^I enter my username and password$/
     */
    public function iEnterMyUsernameAndPassword()
    {
        $date = date('Ymd');
        $this->fillField('Email Address', "behat-$date@sf9.ie");
        $this->fillField('Password', 'password');
    }

    /**
     * @Then /^I should by on the My Account page$/
     */
    public function iShouldByOnTheMyAccountPage()
    {
        PHPUnit_Framework_Assert::assertNotNull($this->find('xpath','//h1[contains(., "My Dashboard")]'));
        $this->assertPageAddress('/customer/account/');
    }

    /**
     * @Given /^I am logged in as a customer$/
     */
    public function iAmLoggedInAsACustomer()
    {
        $this->iGoToTheCustomerLoginPage();
        $date = date('Ymd');

        $this->fillField('Email Address', "behat-$date@sf9.ie");
        $this->fillField('Password', 'password');
        $this->pressButton('Login');
        PHPUnit_Framework_Assert::assertNotNull($this->find('xpath','//h1[contains(., "My Dashboard")]'));
    }

    /**
     * @When /^I click on "([^"]*)"$/
     */
    public function iClickOn($arg1)
    {
        $this->getSession()->getPage()->clickLink($arg1);
        PHPUnit_Framework_Assert::assertNotNull($this->find('xpath','//h1[contains(., "' . $arg1 . '")]'));
    }

    /**
     * @AfterSuite
     */
    public static function cleanup($event)
    {
        Mage::getModel('customer/customer')->getCollection()
            ->addFieldToFilter('email', array('like' => "behat%@sf9.ie"))
            ->delete();
    }
}
