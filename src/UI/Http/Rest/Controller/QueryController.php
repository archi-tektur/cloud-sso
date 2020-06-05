<?php

declare(strict_types=1);

namespace App\UI\Http\Rest\Controller;

use App\Infrastructure\Share\Bus\Query\Collection;
use App\Infrastructure\Share\Bus\Query\Item;
use App\Infrastructure\Share\Bus\Query\QueryBus;
use App\Infrastructure\Share\Bus\Query\QueryInterface;
use App\UI\Http\Rest\Response\JsonApiFormatter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Throwable;

use function count;

abstract class QueryController
{
    private const CACHE_MAX_AGE = 31536000; // Year.

    private QueryBus $queryBus;

    private JsonApiFormatter $formatter;

    private UrlGeneratorInterface $router;

    public function __construct(QueryBus $queryBus, JsonApiFormatter $formatter, UrlGeneratorInterface $router)
    {
        $this->queryBus = $queryBus;
        $this->formatter = $formatter;
        $this->router = $router;
    }

    /**
     * @param QueryInterface $query
     * @return mixed
     * @throws Throwable
     */
    protected function ask(QueryInterface $query)
    {
        return $this->queryBus->handle($query);
    }

    protected function jsonCollection(Collection $collection, bool $isImmutable = false): JsonResponse
    {
        $response = new JsonResponse($this->formatter::collection($collection));

        $this->decorateWithCache($response, $collection, $isImmutable);

        return $response;
    }

    private function decorateWithCache(JsonResponse $response, Collection $collection, bool $isImmutable): void
    {
        if ($isImmutable && $collection->limit === count($collection->data)) {
            $response
                ->setMaxAge(self::CACHE_MAX_AGE)
                ->setSharedMaxAge(self::CACHE_MAX_AGE);
        }
    }

    protected function json(Item $resource): JsonResponse
    {
        return new JsonResponse($this->formatter->one($resource));
    }

    protected function route(string $name, array $params = []): string
    {
        return $this->router->generate($name, $params);
    }
}