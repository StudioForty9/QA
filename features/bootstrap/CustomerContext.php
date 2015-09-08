<?php

use Behat\Behat\Exception\PendingException;
use Behat\Behat\Context\Step;
/**
 * Customer context.
 */
class CustomerContext extends MagentoProjectContext
{
    /**
     * @Given /^I am a registered customer$/
     */
    public function iAmARegisteredCustomer()
    {
        $websiteId = Mage::app()->getWebsite()->getId();
        $store = Mage::app()->getStore();
        $date = date('Ymd');

        $customer = Mage::getModel('customer/customer')->setWebsiteId($websiteId)->loadByEmail("behat-$date@sf9.ie");
        if(!$customer->getId()) {
            $customer->setStore($store)
                ->setFirstname('Behat')
                ->setLastname($date)
                ->setEmail("behat-$date@sf9.ie")
                ->setPassword('password');

            try {
                $customer->save();
            } catch (Exception $e) {
                Zend_Debug::dump($e->getMessage());
            }
        }

        return $customer;
    }

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
        $context = $this->getMainContext();
        $context->fillField('Email Address', "behat-$date@sf9.ie");
        $context->fillField('Password', 'password');
    }

    /**
     * @Then /^I should by on the My Account page$/
     */
    public function iShouldByOnTheMyAccountPage()
    {
        assertNotNull($this->find('xpath','//h1[contains(., "My Dashboard")]'));
        $this->getMainContext()->assertPageAddress('/customer/account/');
    }

    /**
     * @Given /^I am logged in as a customer$/
     */
    public function iAmLoggedInAsACustomer()
    {
        $this->iGoToTheCustomerLoginPage();
        $date = date('Ymd');

        /* @var $context Behat\MinkExtension\Context\MinkContext */
        $context = $this->getMainContext();
        $context->fillField('Email Address', "behat-$date@sf9.ie");
        $context->fillField('Password', 'password');
        $context->pressButton('Login');
        assertNotNull($this->find('xpath','//h1[contains(., "My Dashboard")]'));
    }

    /**
     * @When /^I click on "([^"]*)"$/
     */
    public function iClickOn($arg1)
    {
        $this->getSession()->getPage()->clickLink($arg1);
        assertNotNull($this->find('xpath','//h1[contains(., "' . $arg1 . '")]'));
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
