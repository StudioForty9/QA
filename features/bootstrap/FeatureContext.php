<?php

/**
 * Features context.
 */
class FeatureContext extends \Behat\MinkExtension\Context\MinkContext
{
    private $_params = array();

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->_params = $parameters;

        $subcontexts = array(
            'admin',
            'cart',
            'category',
            'checkout',
            'config',
            'customer',
            'homepage',
            'product',
            'security',
            'seo'
        );

        $customContexts = isset($parameters['custom_contexts']) ? $parameters['custom_contexts'] : array();
        $subcontexts = array_merge($subcontexts, $customContexts);

        foreach ($subcontexts as $pageType) {
            $className = uc_words($pageType) . 'Context';
            $subcontext = new $className();
            $this->useContext($pageType, $subcontext);
        }
    }

    /**
     * @Then /^I should see a success message$/
     */
    public function iShouldSeeASuccessMessage()
    {
        $this->assertElementOnPage('.success-msg');
    }

    /**
     * @Then /^I should see an error message$/
     */
    public function iShouldSeeAnErrorMessage()
    {
        $this->assertElementOnPage('.error-msg');
    }

    public function getParameters()
    {
        return $this->_params;
    }

    public function getParameter($key)
    {
        return $this->_params[$key];
    }

    /**
     * @var $event Behat\Behat\Event\SuiteEvent
     * @AfterSuite
     */
    public static function sendEmailWithResults($event)
    {
        $params = $event->getContextParameters();
        /* @var $logger Behat\Behat\DataCollector\LoggerDataCollector */
        $logger = $event->getLogger();
        if ($logger->getFailedStepsEvents()) {
            $file = new Varien_Io_File();
            $html = @$file->read(Mage::getBaseDir('var') . '/behat/results.html');

            if ($html) {
                ini_set('SMTP', Mage::getStoreConfig('system/smtp/host'));
                ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port'));

                $mail = new Zend_Mail();
                $mail->setFrom(Mage::getStoreConfig('qa/email/from_email'), Mage::getStoreConfig('qa/email/from_name'));
                $mail->addTo(Mage::getStoreConfig('qa/email/to_email'), Mage::getStoreConfig('qa/email/to_name'));

                $mail->setSubject('Behat Results for ' . $params['base_url']);
                $mail->setBodyHtml($html);
                $mail->send();
            }
        }
    }
}
