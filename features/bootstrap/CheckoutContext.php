<?php

use Behat\Behat\Exception\PendingException;
use Behat\Behat\Context\Step;

/**
 * Checkout context.
 */
class CheckoutContext extends AbstractContext
{
    /**
     * @When /^I add a product to the cart$/
     */
    public function iAddAProductToTheCart()
    {
//        try {
            //$product = $this->getRandomProduct();
        $product = Mage::getModel('catalog/product')->load(68699);
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $quote->addProduct($product, 1);
            $quote->collectTotals()->save();
//        }catch(Exception $e){
//            die(var_dump($product->getId()));
//        }
    }

    /**
     * @Given /^I go to the checkout$/
     */
    public function iGoToTheCheckout()
    {
        $this->getSession()->visit(Mage::getBaseUrl() . 'checkout/onepage/');
    }

    /**
     * @Given /^I fill in my Billing Address$/
     */
    public function iFillInMyBillingAddress()
    {
        $this->getSession()->executeScript("jQuery('#login\\:guest').attr('checked', 'checked')");
        $this->getSession()->executeScript("jQuery('#onepage-guest-register-button').click()");

        $steps = array(
            new Step\When('I wait for "5" Seconds'),
            new Step\When('I fill in "firstname" with "John"'),
            new Step\When('I fill in "lastname" with "Smith"'),
            new Step\When('I fill in "email" with "behat@sf9.ie"'),
            new Step\When('I fill in "street1" with "20a Lower John Street"'),
            new Step\When('I fill in "city" with "Cork City"'),
            new Step\When('I select "Ireland" with "Country"'),
            new Step\When('I fill in "telephone" with "0872251250"')
        );

        $this->getSession()->getDriver()->click('//button[@onclick="billing.save()"]');
        $this->getMainContext()->iWaitForSeconds(5);
    }

    /**
     * @Given /^I chooose a Shipping Method$/
     */
    public function iChoooseAShippingMethod()
    {
        $this->getSession()->getDriver()->click('//button[@onclick="shippingMethod.save()"]');
        $this->getMainContext()->iWaitForSeconds(5);
    }

    /**
     * @Given /^I choose Payment Method$/
     */
    public function iChoosePaymentMethod()
    {
        $steps = array(
            new Step\When('I select "Visa" with "Credit Card Type"'),
            new Step\When('I fill in "Credit Card Number" with "4263971921001307"'),
            new Step\When('I select "10" with "Expiry Month"'),
            new Step\When('I select "2017" with "Expiry Year"'),
            new Step\When('I fill in "Card Verification Number" with "333"'),
        );
        $this->getSession()->getDriver()->click('//button[@onclick="payment.save()"]');
        $this->getMainContext()->iWaitForSeconds(5);
    }
}
