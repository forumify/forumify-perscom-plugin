<?php

declare(strict_types=1);

namespace Forumify\PerscomPlugin;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class PerscomPluginBundle extends AbstractBundle
{
    /** @inheritDoc */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $environment = $container->env();
        $configDir = $this->getConfigDir();

        $container->import($configDir . '/services.yaml');
        $container->import($configDir . '/{services}_' . $environment . '.yaml');
    }

    private function getConfigDir(): string
    {
        return $this->getPath() . '/config';
    }
}
