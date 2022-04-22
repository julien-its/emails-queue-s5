<?php

namespace JulienIts\EmailsQueueBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class EmailsQueueExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // Load the bundle config service.yml from this bundle
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');

        // Define package parameters config when install the bundle
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        //$definition = $container->getDefinition('julien_its_emails_queue');
        //$definition->replaceArgument(0, $config['mode']);
    }
}