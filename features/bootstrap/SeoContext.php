<?php

use Behat\MinkExtension\Context\MinkContext;

/**
 * SEO context.
 */
class SeoContext extends MinkContext
{

    use AbstractContext, MagentoProjectContext;

    /**
     * @Then /^the Google Analytics tracking script should be on the page$/
     */
    public function theGoogleAnalyticsTrackingScriptShouldBeOnThePage()
    {
        try {
            $result = $this->getSession()->getDriver()->evaluateScript(
                "return ga;"
            );
        }catch(Exception $e){
            Mage::throwException('Google Analytics tracking script is not present on the page');
        }
    }

    /**
     * @When /^I go to the non-www version of the site$/
     */
    public function iGoToTheNonWwwVersionOfTheSite()
    {
        $baseUrl = $this->getMinkParameter('base_url');
        $page = str_replace('www.', '', $baseUrl);
        $this->visit($page);
    }

    /**
     * @Then /^I should be redirected to the www version of the site$/
     */
    public function iShouldBeRedirectedToTheWwwVersionOfTheSite()
    {
        $this->assertPageAddress($this->getMinkParameter('base_url'));
    }

    /**
     * @Then /^the meta "([^"]*)" should not be "([^"]*)"$/
     */
    public function theMetaShouldNotBe($type, $value)
    {
        if ($type == 'title') {
            $xpath = "//head/title";
        } else {
            $xpath = "//head/meta[@name='" . $type . "']";
        }
        $page = $this->getSession()->getPage();
        $element = $page->find("xpath", $xpath);

        if ($type == 'title') {
            PHPUnit_Framework_Assert::assertNotEquals($element->getHtml(), $value);
        } else {
            PHPUnit_Framework_Assert::assertNotEquals($element->getAttribute('content'), $value);
        }
    }

    /**
     * @Then /^the canonical URL should be set$/
     */
    public function theCanonicalUrlShouldBeSet()
    {
        $xpath = "//head/link[@rel='canonical']";

        $page = $this->getSession()->getPage();
        $element = $page->find("xpath", $xpath);
        PHPUnit_Framework_Assert::assertNotNull($element->getAttribute('href'));
    }
}
