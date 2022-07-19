<?php

declare(strict_types=1);

namespace Inspirum\Balikobot\Client\Response;

use Inspirum\Balikobot\Exception\BadRequestException;
use Inspirum\Balikobot\Exception\UnauthorizedException;
use function array_is_list;
use function count;
use function max;

final class Validator
{
    /**
     * Validate status code
     *
     * @param int                $statusCode
     * @param array<mixed,mixed> $response
     *
     * @throws \Inspirum\Balikobot\Exception\Exception
     */
    public function validateStatus(int $statusCode, array $response = []): void
    {
        // unauthorized
        if ($statusCode === 401 || $statusCode === 403) {
            throw new UnauthorizedException(null, $statusCode);
        }

        // request error
        if ($statusCode >= 400) {
            throw new BadRequestException($response, (int) max($statusCode, $response['status'] ?? 0));
        }
    }

    /**
     * Validate response item status
     *
     * @param array<mixed,mixed>      $responseItem
     * @param array<mixed,mixed>|null $response
     * @param bool                    $shouldHaveStatus
     *
     * @return void
     *
     * @throws \Inspirum\Balikobot\Exception\Exception
     */
    public function validateResponseStatus(
        array $responseItem,
        ?array $response = null,
        bool $shouldHaveStatus = true,
    ): void {
        if ($shouldHaveStatus === false && isset($responseItem['status']) === false) {
            return;
        }

        $this->validateStatus((int) ($responseItem['status'] ?? 500), $response ?? $responseItem);
    }

    /**
     * Validate that every response item has given attribute
     *
     * @param array<int,array<string,mixed>> $items
     * @param string                         $attribute
     * @param array<mixed,mixed>             $response
     *
     * @return void
     *
     * @throws \Inspirum\Balikobot\Exception\BadRequestException
     */
    public function validateResponseItemHasAttribute(array $items, string $attribute, array $response): void
    {
        foreach ($items as $item) {
            if (isset($item[$attribute]) === false) {
                throw new BadRequestException($response);
            }
        }
    }

    /**
     * Validate response array has correct indexes
     *
     * @param array<mixed,mixed> $response
     *
     * @throws \Inspirum\Balikobot\Exception\BadRequestException
     */
    public function validateIndexes(array $response, int $count): void
    {
        if (array_is_list($response) === false || count($response) !== $count) {
            throw new BadRequestException($response);
        }
    }
}
