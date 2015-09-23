<?php

use Behat\MinkExtension\Context\MinkContext;

/**
 * Custom context.
 */
class CustomContext extends MinkContext
{
    use MagentoProjectContext, AbstractContext;
}
