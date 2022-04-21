# Upgrade from 1.x to 2.0

The below guide will assist in upgrading from the 1.x versions to 2.0.

## Bundle Requirements

- Symfony 5.4 or 6.0+
- PHP 8.1 or later

## Removed Features

- Removed classes supporting authentication for Symfony 5.3 and earlier
- Removed the `AbstractRefreshToken` classes from the `Gesdinet\JWTRefreshTokenBundle\Document` and `Gesdinet\JWTRefreshTokenBundle\Entity` namespaces, use the `RefreshToken` class from the same namespace instead
- Removed `Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface::create()` and its implementations, a `Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface` implementation should be used instead