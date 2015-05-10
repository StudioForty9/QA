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
        $url = Mage::getBaseUrl() . 'customer/account/login';
        $this->getSession()->visit($url);
        //$this->assertSession()->statusCodeEquals(200);
    }

    /**
     * @Given /^I enter my username and password$/
     */
    public function iEnterMyUsernameAndPassword()
    {
        $date = date('Ymd');
        return array(
            new Step\When('I fill in "Email Address" with "behat-' . $date . '@sf9.ie"'),
            new Step\When('I fill in "Password" with "password"'),
        );
    }

    /**
     * @Then /^I should by on the My Account page$/
     */
    public function iShouldByOnTheMyAccountPage()
    {
        return array(
            new Step\Then('I should be on "/customer/account/"')
        );
    }

    /**
     * @Given /^I am logged in as a customer$/
     */
    public function iAmLoggedInAsACustomer()
    {
        $this->iGoToTheCustomerLoginPage();
        $date = date('Ymd');

        return array(
            new Step\When('I fill in "Email Address" with "behat-' . $date . '@sf9.ie"'),
            new Step\When('I fill in "Password" with "password"'),
            new Step\When('I press "Login"'),
            new Step\When('I wait for "5" Seconds')
        );
    }

    /**
     * @When /^I click on "([^"]*)"$/
     */
    public function iClickOn($arg1)
    {
        return array(
            new Step\When('I follow "' . $arg1 . '"')
        );
    }

    /**
     * @AfterScenario
     */
    public function takeScreenshotForFailedScenario($event)
    {
        if ($event->getResult() === 4) {
            Mage::log($event->getScenario()->getTitle(), null, 'behat.log', true);
            Mage::log($this->getSession()->getCurrentUrl(), null, 'behat.log', true);

            $driver = $this->getSession()->getDriver();
            if (get_class($driver) == 'Behat\\Mink\\Driver\\Selenium2Driver') {
                try{
                    //Mage::helper('sf9_core')->getSlug($event->getScenario()->getTitle()
                    $date = date('Ymdhis');
                    $this->saveScreenshot($date . '.png',
                        Mage::getBaseDir() . '/var/screenshots');
                }catch(Exception $e){
                    Mage::log($e->getMessage(), null, 'behat.log', true);
                }
            }
        }
    }
}
