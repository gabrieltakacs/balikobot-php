<?php

declare(strict_types=1);

namespace Inspirum\Balikobot\Tests\Unit\Client;

use Inspirum\Balikobot\Exceptions\BadRequestException;
use Inspirum\Balikobot\Services\Client;

class GetAdrUnitsMethodTest extends AbstractClientTestCase
{
    public function testThrowsExceptionOnError(): void
    {
        $this->expectException(BadRequestException::class);

        $client = $this->newMockedClient(400, [
            'status' => 200,
        ]);

        $client->getAdrUnits('cp');
    }

    public function testRequestShouldHaveStatus(): void
    {
        $this->expectException(BadRequestException::class);

        $client = $this->newMockedClient(200, []);

        $client->getAdrUnits('cp');
    }

    public function testThrowsExceptionOnBadStatusCode(): void
    {
        $this->expectException(BadRequestException::class);

        $client = $this->newMockedClient(200, [
            'status' => 400,
        ]);

        $client->getAdrUnits('cp');
    }

    public function testMakeRequest(): void
    {
        $requester = $this->newRequesterWithMockedRequestMethod(200, [
            'status' => 200,
            'units'  => [],
        ], [
            'https://apiv2.balikobot.cz/cp/adrunits',
            [],
        ]);

        $client = new Client($requester);

        $client->getAdrUnits('cp');

        self::assertTrue(true);
    }

    public function testEmptyArrayIsReturnedIfUnitsMissing(): void
    {
        $client = $this->newMockedClient(200, [
            'status' => 200,
            'units'  => null,
        ]);

        $units = $client->getAdrUnits('cp');

        self::assertEquals([], $units);
    }

    public function testOnlyUnitsDataAreReturned(): void
    {
        $client = $this->newMockedClient(200, [
            'status' => 200,
            'units'  => [
                [
                    'code' => 1,
                    'name' => 'KM',
                    'attr' => 4,
                ],
                [
                    'code' => 876,
                    'name' => 'M',
                ],
            ],
        ]);

        $units = $client->getAdrUnits('cp');

        self::assertEquals(
            [
                1   => 'KM',
                876 => 'M',
            ],
            $units
        );
    }

    public function testFullDataAreReturned(): void
    {
        $client = $this->newMockedClient(200, [
            'status' => 200,
            'units'  => [
                [
                    'code' => 1,
                    'name' => 'KM',
                    'id'   => 26,
                ],
                [
                    'code' => 876,
                    'name' => 'M',
                    'id'   => 59,
                ],
            ],
        ]);

        $units = $client->getAdrUnits('cp', fullData: true);

        self::assertEquals(
            [
                1   => [
                    'code' => 1,
                    'name' => 'KM',
                    'id'   => 26,
                ],
                876 => [
                    'code' => 876,
                    'name' => 'M',
                    'id'   => 59,
                ],
            ],
            $units
        );
    }
}
