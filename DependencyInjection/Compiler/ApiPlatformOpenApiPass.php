<?php

namespace Gesdinet\JWTRefreshTokenBundle\DependencyInjection\Compiler;

use Gesdinet\JWTRefreshTokenBundle\OpenApi\OpenApiFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ApiPlatformOpenApiPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(OpenApiFactory::class) || !$container->hasParameter('security.firewalls')) {
            return;
        }

        $checkPath = null;
        $firewalls = $container->getParameter('security.firewalls');
        foreach ($firewalls as $firewallName) {
            if ($container->hasDefinition('security.authenticator.json_login.' . $firewallName)) {
                foreach ($container->getDefinition('security.authenticator.refresh_jwt.' . $firewallName)->getArguments() as $argument) {
                    if (is_array($argument) && isset($argument['check_path'])) {
                        $checkPath = $argument['check_path'];

                        break 2;
                    }
                }
            }
        }

        if ($checkPath) {
            $openApiFactoryDefinition = $container->getDefinition(OpenApiFactory::class);
            $openApiFactoryDefinition->setArgument('$checkPath', $checkPath);
        }
    }
}
