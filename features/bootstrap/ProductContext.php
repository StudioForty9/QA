<?php

use Behat\Behat\Context\Step;

/**
 * Category context.
 */
class ProductContext extends MagentoProjectContext
{
    protected $_product = null;

    public function __construct()
    {
        $this->_product = $this->getRandomProduct();
    }

    /**
     * @Given /^I am on a product page$/
     */
    public function iAmOnAProductPage()
    {
        $url = Mage::getBaseUrl() . 'catalog/product/view/id/' . $this->_product->getId();
        $this->getSession()->visit($url);
        $this->assertSession()->statusCodeEquals(200);
    }

    /**
     * @Then /^I should see the product name$/
     */
    public function iShouldSeeTheProductName()
    {
        return array(
            new Step\Then('I should see "' . $this->_product->getName() . '"')
        );
    }

    /**
     * @Given /^I should see a valid product image$/
     */
    public function iShouldSeeAValidProductImage()
    {
        //FIXME
        return true;
    }

    /**
     * @When /^I click the add to cart button$/
     */
    public function iClickTheAddToCartButton()
    {
        $this->getSession()->getDriver()->click('//button[@onclick="productAddToCartForm.submit(this)"]');
    }

    /**
     * @Then /^I should be on the cart page$/
     */
    public function iShouldBeOnTheCartPage()
    {
        $this->assertSession()->addressEquals(Mage::getBaseUrl() . 'checkout/cart/');
    }

    /**
     * @Given /^I should see a success message$/
     */
    public function iShouldSeeASuccessMessage()
    {
        $this->assertSession()->elementExists('css', '.success-msg');
    }
}
