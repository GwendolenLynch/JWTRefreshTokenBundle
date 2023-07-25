<?php

namespace Gesdinet\JWTRefreshTokenBundle\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

final class OpenApiFactory implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decorated;
    private string $checkPath;
    private string $tokenParameterName;
    private string $operationId;
    private array $tags;

    public function __construct(OpenApiFactoryInterface $decorated, string $checkPath, string $tokenParameterName, string $operationId, array $tags)
    {
        $this->decorated = $decorated;
        $this->checkPath = $checkPath;
        $this->tokenParameterName = $tokenParameterName;
        $this->operationId = $operationId;
        $this->tags = $tags;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);

        $openApi
            ->getPaths()
            ->addPath($this->checkPath, (new PathItem())->withPost(
                (new Operation())
                ->withOperationId($this->operationId)
                ->withTags($this->tags)
                ->withResponses([
                    Response::HTTP_OK => [
                        'description' => 'User token refreshed',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'token' => [
                                            'readOnly' => true,
                                            'type' => 'string',
                                            'nullable' => false,
                                        ],
                                        $this->tokenParameterName => [
                                            'readOnly' => true,
                                            'type' => 'string',
                                            'nullable' => false,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ])
                ->withSummary('Refreshes a user token.')
                ->withDescription('Refreshes a user token.')
                ->withRequestBody(
                    (new RequestBody())
                    ->withDescription('The refresh token')
                    ->withContent(new \ArrayObject([
                        'application/json' => new \ArrayObject([
                            'schema' => new \ArrayObject([
                                'type' => 'object',
                                'properties' => [
                                    $this->tokenParameterName => [
                                        'type' => 'string',
                                        'nullable' => false,
                                    ],
                                ],
                                'required' => [$this->tokenParameterName],
                            ])
                        ]),
                    ]))
                    ->withRequired(true)
                )
            ));

        return $openApi;
    }
}
