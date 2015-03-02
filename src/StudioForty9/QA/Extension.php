<?php

namespace StudioForty9\QA;

use Behat\Behat\Extension\Extension as BaseExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Loader\XmlFileLoader,
    Symfony\Component\Config\FileLocator;

class Extension extends BaseExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $container->setParameter('magento.base_url', \Mage::getStoreConfig('web/unsecure/base_url'));
    }
}
