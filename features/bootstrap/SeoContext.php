<?php

use Behat\Behat\Exception\PendingException;

require_once Mage::getBaseDir() . '/vendor/phpunit/phpunit/src/Framework/Assert/Functions.php';

/**
 * Category context.
 */
class SeoContext extends MagentoProjectContext
{
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
        $context = $this->getMainContext();
        $baseUrl = $this->getMinkParameter('base_url');
        $page = str_replace('www.', '', $baseUrl);
        $context->visit($page);
    }

    /**
     * @Then /^I should be redirected to the www version of the site$/
     */
    public function iShouldBeRedirectedToTheWwwVersionOfTheSite()
    {
        $this->getMainContext()->assertPageAddress($this->getMinkParameter('base_url'));
    }

    /**
     * @Then /^"([^"]*)" file should not contain "([^"]*)"$/
     */
    public function fileShouldNotContain($arg1, $arg2)
    {
        $url = $this->getMinkParameter('base_url') . $arg1;
        $file = new Varien_Io_File();
        $contents = @$file->read($url);
        $result = strstr($contents, $arg2);
        assertFalse($result);
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
            assertNotEquals($element->getHtml(), $value);
        } else {
            assertNotEquals($element->getAttribute('content'), $value);
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
        assertNotNull($element);
        assertNotNull($element->getAttribute('href'));
    }
}
