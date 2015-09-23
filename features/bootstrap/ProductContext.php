<?php

use Behat\MinkExtension\Context\MinkContext;

/**
 * Category context.
 */
class ProductContext extends MinkContext
{
    use AbstractContext, MagentoProjectContext;

    protected $_product = null;
    protected $_image = null;
    protected $_theme = null;

    public function __construct($theme, $image)
    {
        $this->_product = $this->getRandomProduct();
        $this->_image = $image;
        $this->_theme = $theme;
    }

    /**
     * @Given /^I am on a product page$/
     */
    public function iAmOnAProductPage()
    {
        $url = $this->getMinkParameter('base_url') . 'catalog/product/view/id/' . $this->_product->getId();
        $this->getSession()->visit($url);
    }

    /**
     * @Then /^I should see the product name$/
     */
    public function iShouldSeeTheProductName()
    {
        $this->assertElementContainsText('h1', trim($this->_product->getName()));
    }

    /**
     * @Given /^I should see a valid product image$/
     */
    public function iShouldSeeAValidProductImage()
    {
        $selector = $this->_image['selector'];
        $image = $this->getSession()->getPage()->find('css', $selector);
        PHPUnit_Framework_Assert::assertNotContains('placeholder', $image->getAttribute('src'));
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
        PHPUnit_Framework_Assert::assertNotNull($this->find('xpath','//h1[contains(., "Shopping Cart")]'));
        $this->assertPageAddress('/checkout/cart/');
    }
}
