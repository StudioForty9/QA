<?php

use Behat\MinkExtension\Context\MinkContext;

/**
 * Checkout context.
 */
class CheckoutContext extends MinkContext
{
    use AbstractContext, MagentoProjectContext;

    protected $_differentShippingAddress = false;
    protected $_payment = null;

    protected $_ajax = null;

    public function __construct($payment, $ajax = null)
    {
        $this->_payment = $payment;
        $this->_ajax = $ajax;
    }

    /**
     * @Given /^I go to the checkout$/
     */
    public function iGoToTheCheckout()
    {
        PHPUnit_Framework_Assert::assertNotNull($this->find('xpath', '//*[@id="shopping-cart-totals-table"]'));
        $this->getSession()->visit($this->getMinkParameter('base_url') . 'checkout/onepage/');
    }

    /**
     * @Given /^I choose Guest Checkout$/
     */
    public function iChooseGuestCheckout()
    {
        $this->getSession()->getDriver()->click('//*[@id="login:guest"]');
        $this->getSession()->getDriver()->click('//button[@onclick="checkout.setMethod();"]');
    }

    /**
     * @Given /^I choose Register$/
     */
    public function iChooseRegister()
    {
        $this->getSession()->getDriver()->click('//*[@id="login:register"]');
        $this->getSession()->getDriver()->click('//button[@onclick="checkout.setMethod();"]');
    }

    /**
     * @Given /^I login to my account$/
     */
    public function iLoginToMyAccount()
    {
        $date = date('Ymd');

        $context = $this;
        $context->fillField('login[username]', "behat-$date@sf9.ie");
        $context->fillField('login[password]', 'password');
        $context->pressButton('Login');
    }

    /**
     * @Given /^I fill in my Billing Address$/
     */
    public function iFillInMyBillingAddress()
    {
        PHPUnit_Framework_Assert::assertNotNull($this->find('xpath', '//button[@onclick="billing.save()"]'));

        $context = $this;
        if ($this->getSession()->getPage()->find('xpath', '//*[@id="billing:firstname"]')->isVisible()) {
            $context->fillField('billing[firstname]', 'John');
            $context->fillField('billing[lastname]', 'Smith');

            $timestamp = date('YmdHis');
            if ($this->getSession()->getPage()->find('xpath', '//*[@id="billing:email"]')) {
                $context->fillField('billing[email]', "behat$timestamp@sf9.ie");
            }

            $context->fillField('billing:street1', '20a Lower John Street');
            $context->fillField('billing[city]', 'Cork City');
            $context->selectOption('billing[country_id]', 'Ireland');
            $context->fillField('billing[telephone]', '0872251250');
        }
    }

    /**
     * @Given /^I fill in a Password$/
     */
    public function iFillInAPassword()
    {
        $context = $this;
        $context->fillField('billing[customer_password]', 'password123');
        $context->fillField('billing[confirm_password]', 'password123');
    }

    /**
     * @Given /^I use my Billing Address as my Shipping Address$/
     */
    public function iUseMyBillingAddressAsMyShippingAddress()
    {
        $this->getSession()->getDriver()->click('//*[@id="billing:use_for_shipping_yes"]');
        $this->getSession()->getDriver()->click('//button[@onclick="billing.save()"]');
    }

    /**
     * @Given /^I enter a different Shipping Address$/
     */
    public function iEnterADifferentShippingAddress()
    {
        $this->_differentShippingAddress = true;
        $this->getSession()->getDriver()->click('//*[@id="billing:use_for_shipping_no"]');
        $this->getSession()->getDriver()->click('//button[@onclick="billing.save()"]');

        PHPUnit_Framework_Assert::assertNotNull($this->find('xpath', '//button[@onclick="shipping.save()"]'));

        $context = $this;
        $context->fillField('shipping[firstname]', 'Jane');
        $context->fillField('shipping[lastname]', 'Doe');
        $context->fillField('shipping:street1', '19 Shop Street');
        $context->fillField('shipping[city]', 'Galway City');
        $context->selectOption('shipping[country_id]', 'Ireland');
        $context->fillField('shipping[telephone]', '0863016163');
    }

    /**
     * @Given /^I choose a Shipping Method$/
     */
    public function iChooseAShippingMethod()
    {
        if ($this->_differentShippingAddress) {
            $this->getSession()->getDriver()->click('//button[@onclick="shipping.save()"]');
        }

        PHPUnit_Framework_Assert::assertNotNull($this->find('xpath', '//button[@onclick="shippingMethod.save()"]'));
        $this->getSession()->getDriver()->click('//*[@name="shipping_method"]');
        $this->getSession()->getDriver()->click('//button[@onclick="shippingMethod.save()"]');
    }

    /**
     * @Given /^I choose Payment Method$/
     */
    public function iChoosePaymentMethod()
    {
        PHPUnit_Framework_Assert::assertNotNull($this->find('xpath', '//button[@onclick="payment.save()"]'));
        switch ($this->_payment['method']){
            case 'realex':
                $this->getSession()->getDriver()->click('//*[@id="p_method_realex"]');
                $context = $this;
                $context->selectOption('Credit Card Type', 'Visa');
                $context->fillField('Name as it appears on Credit Card', 'Alan Morkan');
                $context->fillField('Name as it appears on Credit Card', '4263971921001307');
                $context->selectOption('payment[cc_exp_month]', '10');
                $context->selectOption('payment[cc_exp_year]', '2020');
                $context->fillField('CVV', '333');
                break;
            case 'checkmo':
            default:
                $this->getSession()->getDriver()->click('//*[@id="p_method_checkmo"]');
                break;
        }
    }

    /**
     * @Given /^I save the Payment Method$/
     */
    public function iSaveThePaymentMethod()
    {
        $this->getSession()->getDriver()->click('//button[@onclick="payment.save()"]');
        PHPUnit_Framework_Assert::assertNotNull($this->find('xpath', '//button[@onclick="review.save();"]'));
    }

    /**
     * @Then /^I should be on the Success Page$/
     */
    public function iShouldBeOnTheSuccessPage()
    {
        PHPUnit_Framework_Assert::assertNotNull($this->find('xpath', '//h1[contains(., "Your order has been received.")]'));
    }

    /**
     * @AfterSuite
     */
    public static function cleanup($event)
    {
        Mage::register('isSecureArea', true);
        $orders = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('customer_email', array('like' => "behat%@sf9.ie"));
        foreach($orders as $order){
            $order->delete();
        }
        Mage::unregister('isSecureArea');
    }
}
