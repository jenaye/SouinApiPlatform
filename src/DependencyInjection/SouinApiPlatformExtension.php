<?php

/*
 * This file is not part of the API Platform project.
 * I won't copyright my work.
 */

declare(strict_types=1);

namespace Darkweak\SouinApiPlatformBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SouinApiPlatformExtension extends Extension
{
    private function loadKeys(ContainerBuilder $container, array $config, string $baseKey = 'souin_api_platform') {
        foreach ($config as $k => $v) {
            if (\is_array($v)) {
                $this->loadKeys($container, $v, \sprintf('%s.%s', $baseKey, $k));
            } else {
                $container->setParameter(\sprintf('%s.%s', $baseKey, $k), $v);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $this->loadKeys($container, $config);
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return 'souin_api_platform';
    }
}
