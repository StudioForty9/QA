<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

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
            'custom',
            'customer',
            'homepage',
            'product',
		);
		
		$customContexts = isset($parameters['custom_contexts']) ? $parameters['custom_contexts'] : array();
        $subcontexts = array_merge($subcontexts, $customContexts);

		foreach($subcontexts as $pageType){
			$className = uc_words($pageType) . 'Context';
			$subcontext = new $className();
			$this->useContext($pageType, $subcontext);
		}
    }

    /**
     * @Given /^I wait for "([^"]*)" Seconds$/
     */
    public function iWaitForSeconds($arg)
    {
        sleep($arg);
    }

    public function getParameters()
    {
        return $this->_params;
    }

    public function getParameter($key){
        return $this->_params[$key];
    }
}
