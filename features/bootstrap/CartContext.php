<?php

use Behat\MinkExtension\Context\MinkContext;

/**
 * Cart context.
 */
class CartContext extends MinkContext
{
    use AbstractContext, MagentoProjectContext;

    /**
     * @When /^I change the Qty to (\d+)$/
     */
    public function iChangeTheQtyTo($arg1)
    {
        $element = $this->getSession()->getPage()->find('css', '.qty');

        $context = $this;
        $context->fillField($element->getAttribute('name'), $arg1);
        $context->pressButton('Update');
    }

    /**
     * @Given /^a discount exists with code "([^"]*)"$/
     */
    public function aDiscountExistsWithCode($arg1)
    {
        $existing = Mage::getModel('salesrule/coupon')->getCollection()
            ->addFieldToFilter('code', $arg1);

        if($existing->getSize() > 0){
            return true;
        }

        $coupon = Mage::getModel('salesrule/coupon');

        $rule = Mage::getModel('salesrule/rule');
        $rule->setName('Behat Test Coupon')
            ->setDescription($rule->getName())
            ->setFromDate(date('Y-m-d'))
            ->setCustomerGroupIds(Mage::getModel('customer/group')->getCollection()->getAllIds())
            ->setIsActive(1)
            ->setSimpleAction(Mage_SalesRule_Model_Rule::BY_FIXED_ACTION)
            ->setDiscountAmount(1)
            ->setDiscountQty(1)
            ->setStopRulesProcessing(0)
            ->setIsRss(0)
            ->setWebsiteIds(array(1))
            ->setCouponType(Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC)
            ->save();

        $coupon->setId(null)
            ->setRuleId($rule->getRuleId())
            ->setCode($arg1)
            ->setUsageLimit(1)
            ->setIsPrimary(1)
            ->setCreatedAt(time())
            ->setType(Mage_SalesRule_Helper_Coupon::COUPON_FORMAT_ALPHABETICAL)
            ->save();
    }

    /**
     * @Given /^I apply the "([^"]*)" coupon code$/
     */
    public function iApplyTheCouponCode($arg1)
    {
        $context = $this;
        $context->fillField('Discount Codes', $arg1);
        $context->pressButton('Apply');
    }

    /**
     * @AfterSuite
     */
    public static function cleanup($event)
    {
        $coupons = Mage::getModel('salesrule/coupon')->getCollection()
            ->addFieldToFilter('code', 'Test Coupon');
        foreach($coupons as $coupon){
            $coupon->delete();
        }
    }
}
