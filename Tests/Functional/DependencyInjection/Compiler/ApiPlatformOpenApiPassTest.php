<?php

namespace Gesdinet\JWTRefreshTokenBundle\Tests\Functional\DependencyInjection\Compiler;

use Gesdinet\JWTRefreshTokenBundle\DependencyInjection\Compiler\ApiPlatformOpenApiPass;
use Gesdinet\JWTRefreshTokenBundle\OpenApi\OpenApiFactory;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ApiPlatformOpenApiPassTest extends AbstractCompilerPassTestCase
{
    public function test_openapi_factory_args_configured(): void
    {
        $this->registerService(OpenApiFactory::class, OpenApiFactory::class);
        $this->setParameter('security.firewalls', ['api']);

        $jsonLoginAuthenticator = new ChildDefinition('security.authenticator.json_login');
        $this->setDefinition('security.authenticator.json_login.api', $jsonLoginAuthenticator);

        $refreshTokenAuthenticator = new ChildDefinition('gesdinet_jwt_refresh_token.security.refresh_token_authenticator');
        $refreshTokenAuthenticator->setArguments([['check_path' => '/api/token/refresh']]);
        $this->setDefinition('security.authenticator.refresh_jwt.api', $refreshTokenAuthenticator);

        $this->compile();

        $this->assertContainerBuilderHasService('security.authenticator.refresh_jwt.api');
        $this->assertContainerBuilderHasParameter('security.firewalls', ['api']);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            OpenApiFactory::class,
            '$checkPath',
            '/api/token/refresh',
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ApiPlatformOpenApiPass());
    }
}
