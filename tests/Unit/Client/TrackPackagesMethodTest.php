<?php

declare(strict_types=1);

namespace Inspirum\Balikobot\Tests\Unit\Client;

use Inspirum\Balikobot\Exceptions\BadRequestException;
use Inspirum\Balikobot\Services\Client;

class TrackPackagesMethodTest extends AbstractClientTestCase
{
    public function testThrowsExceptionOnError(): void
    {
        $this->expectException(BadRequestException::class);

        $client = $this->newMockedClient(400, [
            'status' => 200,
        ]);

        $client->trackPackage('cp', '1');
    }

    public function testRequestShouldHaveStatus(): void
    {
        $client = $this->newMockedClient(200, [
            'packages' => [
                0 => [
                    'carrier_id' => '1',
                    'status'     => 200,
                    'states'     => [
                        [
                            'date'           => '2018-11-07 14:15:01',
                            'name'           => 'Doručování zásilky',
                            'status_id'      => 2,
                            'status_id_v2'   => 2.2,
                            'type'           => 'event',
                            'name_balikobot' => 'Zásilka je v přepravě.',
                        ],
                        [
                            'date'           => '2018-11-08 18:00:00',
                            'name'           => 'Dodání zásilky. (77072 - Depo Olomouc 72)',
                            'status_id'      => 1,
                            'status_id_v2'   => 1.2,
                            'type'           => 'event',
                            'name_balikobot' => 'Zásilka byla doručena příjemci.',
                        ],
                    ],
                ],
            ],
        ]);

        $status = $client->trackPackage('cp', '1');

        self::assertNotEmpty($status);
    }

    public function testThrowsExceptionOnBadStatusCode(): void
    {
        $this->expectException(BadRequestException::class);

        $client = $this->newMockedClient(200, [
            'status' => 400,
        ]);

        $client->trackPackage('cp', '1');
    }

    public function testThrowsExceptionWhenNoDataReturn(): void
    {
        $this->expectException(BadRequestException::class);

        $client = $this->newMockedClient(200, [
            'status' => 200,
        ]);

        $client->trackPackage('cp', '1');
    }

    public function testMakeRequest(): void
    {
        $requester = $this->newRequesterWithMockedRequestMethod(200, [
            'status'   => 200,
            'packages' => [
                0 => [
                    'carrier_id' => '1',
                    'status'     => 200,
                    'states'     => [
                        [
                            'date'           => '2018-11-07 14:15:01',
                            'name'           => 'Doručování zásilky',
                            'status_id'      => 2,
                            'status_id_v2'   => 2.2,
                            'type'           => 'event',
                            'name_balikobot' => 'Zásilka je v přepravě.',
                        ],
                    ],
                ],
            ],
        ], [
            'https://apiv2.balikobot.cz/v2/cp/track',
            [
                'carrier_ids' => [
                    '1',
                ],
            ],
        ]);

        $client = new Client($requester);

        $client->trackPackage('cp', '1');

        self::assertTrue(true);
    }

    public function testDataAreReturnedInV3Format(): void
    {
        $client = $this->newMockedClient(200, [
            'status'   => 200,
            'packages' => [
                0 => [
                    'carrier_id' => '1',
                    'status'     => 200,
                    'states'     => [
                        [
                            'date'           => '2018-11-07 14:15:01',
                            'name'           => 'Doručování zásilky',
                            'status_id'      => 2,
                            'status_id_v2'   => 2.2,
                            'type'           => 'event',
                            'name_balikobot' => 'Zásilka je v přepravě.',
                        ],
                    ],
                ],
            ],
        ]);

        $status = $client->trackPackage('cp', '1');

        self::assertEquals(
            [
                [
                    'date'          => '2018-11-07 14:15:01',
                    'name'          => 'Doručování zásilky',
                    'status_id'     => 2.2,
                    'type'          => 'event',
                    'name_internal' => 'Zásilka je v přepravě.',
                ],
            ],
            $status
        );
    }

    public function testDataAreReturnedInV3FormatFromV2Response(): void
    {
        $client = $this->newMockedClient(200, [
            'status'   => 200,
            'packages' => [
                0 => [
                    'carrier_id' => '1',
                    'status'     => 200,
                    'states'     => [
                        [
                            'date'      => '2018-11-07 14:15:01',
                            'name'      => 'Doručování zásilky',
                            'status_id' => 2,
                        ],
                    ],
                ],
            ],
        ]);

        $status = $client->trackPackage('cp', '1');

        self::assertEquals(
            [
                [
                    'date'          => '2018-11-07 14:15:01',
                    'name'          => 'Doručování zásilky',
                    'status_id'     => 2,
                    'type'          => 'event',
                    'name_internal' => 'Doručování zásilky',
                ],
            ],
            $status
        );
    }

    public function testThrowsExceptionOnErrorWithMultiplePackages(): void
    {
        $this->expectException(BadRequestException::class);

        $client = $this->newMockedClient(400, [
            'status' => 200,
        ]);

        $client->trackPackages('cp', ['1', '2', '4']);
    }

    public function testRequestShouldNotHaveStatusWithMultiplePackages(): void
    {
        $client = $this->newMockedClient(200, [
            'packages' => [
                0 => [
                    'carrier_id' => '3',
                    'status'     => 200,
                    'states'     => [
                        [
                            'date'           => '2018-11-07 14:15:01',
                            'name'           => 'Doručování zásilky',
                            'status_id'      => 2,
                            'status_id_v2'   => 2.2,
                            'type'           => 'event',
                            'name_balikobot' => 'Zásilka je v přepravě.',
                        ],
                        [
                            'date'           => '2018-11-08 18:00:00',
                            'name'           => 'Dodání zásilky. (77072 - Depo Olomouc 72)',
                            'status_id'      => 1,
                            'status_id_v2'   => 1.2,
                            'type'           => 'event',
                            'name_balikobot' => 'Zásilka byla doručena příjemci.',
                        ],
                    ],
                ],
                1 => [
                    'carrier_id' => '4',
                    'status'     => 200,
                    'states'     => [
                        [
                            'date'           => '2018-11-07 14:15:01',
                            'name'           => 'Doručování zásilky',
                            'status_id'      => 2,
                            'status_id_v2'   => 2.2,
                            'type'           => 'event',
                            'name_balikobot' => 'Zásilka je v přepravě.',
                        ],
                    ],
                ],
                2 => [
                    'carrier_id' => '5',
                    'status'     => 200,
                    'states'     => [
                        [
                            'date'           => '2018-11-08 18:00:00',
                            'name'           => 'Dodání zásilky. (77072 - Depo Olomouc 72)',
                            'status_id'      => 1,
                            'status_id_v2'   => 1.2,
                            'type'           => 'event',
                            'name_balikobot' => 'Zásilka byla doručena příjemci.',
                        ],
                    ],
                ],
            ],
        ]);

        $status = $client->trackPackages('cp', ['3', '4', '5']);

        self::assertNotEmpty($status);
    }

    public function testThrowsExceptionOnBadPackageIndexes(): void
    {
        $this->expectException(BadRequestException::class);

        $client = $this->newMockedClient(200, [
            'packages' => [
                0 => [
                    'carrier_id' => '3',
                    'status'     => 200,
                    'states'     => [
                        [
                            'date'           => '2018-11-07 14:15:01',
                            'name'           => 'Doručování zásilky',
                            'status_id'      => 2,
                            'status_id_v2'   => 2.2,
                            'type'           => 'event',
                            'name_balikobot' => 'Zásilka je v přepravě.',
                        ],
                        [
                            'date'           => '2018-11-08 18:00:00',
                            'name'           => 'Dodání zásilky. (77072 - Depo Olomouc 72)',
                            'status_id'      => 1,
                            'status_id_v2'   => 1.2,
                            'type'           => 'event',
                            'name_balikobot' => 'Zásilka byla doručena příjemci.',
                        ],
                    ],
                ],
                2 => [
                    'carrier_id' => '4',
                    'status'     => 200,
                    'states'     => [
                        [
                            'date'           => '2018-11-07 14:15:01',
                            'name'           => 'Doručování zásilky',
                            'status_id'      => 2,
                            'status_id_v2'   => 2.2,
                            'type'           => 'event',
                            'name_balikobot' => 'Zásilka je v přepravě.',
                        ],
                    ],
                ],
                3 => [
                    'carrier_id' => '5',
                    'status'     => 200,
                    'states'     => [
                        [
                            'date'           => '2018-11-08 18:00:00',
                            'name'           => 'Dodání zásilky. (77072 - Depo Olomouc 72)',
                            'status_id'      => 1,
                            'status_id_v2'   => 1.2,
                            'type'           => 'event',
                            'name_balikobot' => 'Zásilka byla doručena příjemci.',
                        ],
                    ],
                ],
            ],
        ]);

        $client->trackPackages('cp', ['3', '4', '5']);
    }

    public function testThrowsExceptionOnBadStatusCodeWithMultiplePackages(): void
    {
        $this->expectException(BadRequestException::class);

        $client = $this->newMockedClient(200, [
            'status' => 400,
        ]);

        $client->trackPackages('cp', ['4', '2']);
    }

    public function testThrowsExceptionWhenNoDataReturnWithMultiplePackages(): void
    {
        $this->expectException(BadRequestException::class);

        $client = $this->newMockedClient(200, [
            'status' => 200,
        ]);

        $client->trackPackages('cp', ['1', '3']);
    }

    public function testThrowsExceptionWhenNotAllDataReturnWithMultiplePackages(): void
    {
        $this->expectException(BadRequestException::class);

        $client = $this->newMockedClient(200, [
            'status'   => 200,
            'packages' => [
                1 => [
                    'carrier_id' => '3',
                    'status'     => 200,
                    'states'     => [
                        [
                            'date'           => '2018-11-07 14:15:01',
                            'name'           => 'Dodání zásilky. 10005 Depo Praha 701',
                            'status_id'      => 1,
                            'status_id_v2'   => 1.2,
                            'type'           => 'event',
                            'name_balikobot' => 'Zásilka byla doručena příjemci..',
                        ],
                    ],
                ],
            ],
        ]);

        $client->trackPackages('ppl', ['1', '3']);
    }

    public function testThrowsExceptionWhenBadResponseData(): void
    {
        $this->expectException(BadRequestException::class);

        $client = $this->newMockedClient(200, [
            'status'   => 200,
            'packages' => [
                0 => [
                    'carrier_id' => '1',
                    'status'     => 200,
                    'states'     => [
                        [
                            'date'           => '2018-11-07 14:15:01',
                            'name'           => 'Dodání zásilky. 10005 Depo Praha 701',
                            'status_id'      => 1,
                            'status_id_v2'   => 1.2,
                            'type'           => 'event',
                            'name_balikobot' => 'Zásilka byla doručena příjemci..',
                        ],
                    ],
                ],
                1 => [
                    'status' => 500,
                ],
            ],
        ]);

        $client->trackPackages('ppl', ['1', '3']);
    }

    public function testMakeRequestWithMultiplePackages(): void
    {
        $requester = $this->newRequesterWithMockedRequestMethod(200, [
            'status'   => 200,
            'packages' => [
                0 => [
                    'carrier_id' => '1',
                    'status'     => 200,
                    'states'     => [
                        [
                            'date'           => '2018-11-07 14:15:01',
                            'name'           => 'Dodání zásilky. 10005 Depo Praha 701',
                            'status_id'      => 1,
                            'status_id_v2'   => 1.2,
                            'type'           => 'event',
                            'name_balikobot' => 'Zásilka byla doručena příjemci..',
                        ],
                    ],
                ],
                1 => [
                    'carrier_id' => '33',
                    'status'     => 200,
                    'states'     => [
                        [
                            'date'           => '2018-11-07 14:15:01',
                            'name'           => 'Dodání zásilky. 10005 Depo Praha 701',
                            'status_id'      => 1,
                            'status_id_v2'   => 1.2,
                            'type'           => 'event',
                            'name_balikobot' => 'Zásilka byla doručena příjemci..',
                        ],
                    ],
                ],
                2 => [
                    'carrier_id' => '4',
                    'status'     => 200,
                    'states'     => [
                        [
                            'date'           => '2018-11-07 14:15:01',
                            'name'           => 'Dodání zásilky. 10005 Depo Praha 701',
                            'status_id'      => 1,
                            'status_id_v2'   => 1.2,
                            'type'           => 'event',
                            'name_balikobot' => 'Zásilka byla doručena příjemci..',
                        ],
                    ],
                ],
            ],
        ], [
            'https://apiv2.balikobot.cz/v2/cp/track',
            [
                'carrier_ids' => [
                    '1',
                    '33',
                    '4',
                ],
            ],
        ]);

        $client = new Client($requester);

        $client->trackPackages('cp', ['1', '33', '4']);

        self::assertTrue(true);
    }

    public function testGlsOnlyReturnsLastPackageStatusesWithMultiplePackages(): void
    {
        $this->expectException(BadRequestException::class);

        $client = $this->newMockedClient(200, [
            'status'   => 200,
            'packages' => [
                1 => [
                    'carrier_id' => '3',
                    'status'     => 200,
                    'states'     => [
                        [
                            'date'           => '2018-11-07 14:15:01',
                            'name'           => 'Doručování zásilky',
                            'status_id'      => 2,
                            'status_id_v2'   => 2.2,
                            'type'           => 'event',
                            'name_balikobot' => 'Zásilka je v přepravě.',
                        ],
                        [
                            'date'           => '2018-11-08 18:00:00',
                            'name'           => 'Dodání zásilky. (77072 - Depo Olomouc 72)',
                            'status_id'      => 1,
                            'status_id_v2'   => 1.2,
                            'type'           => 'event',
                            'name_balikobot' => 'Zásilka byla doručena příjemci.',
                        ],
                    ],
                ],
            ],
        ]);

        $statuses = $client->trackPackages('gls', ['1', '3']);

        self::assertEquals([], $statuses[0]);
        self::assertEquals(
            [
                [
                    'date'          => '2018-11-07 14:15:01',
                    'name'          => 'Doručování zásilky',
                    'status_id'     => 2.2,
                    'type'          => 'event',
                    'name_internal' => 'Zásilka je v přepravě.',
                ],
                [
                    'date'          => '2018-11-08 18:00:00',
                    'name'          => 'Dodání zásilky. (77072 - Depo Olomouc 72)',
                    'status_id'     => 1.2,
                    'type'          => 'event',
                    'name_internal' => 'Zásilka byla doručena příjemci.',
                ],
            ],
            $statuses[1]
        );
    }

    public function testDataAreReturnedAsString(): void
    {
        $client = $this->newMockedClient(200, [
            'status'   => 200,
            'packages' => [
                0 => [
                    'carrier_id' => '1',
                    'status'     => 200,
                    'states'     => [
                        [
                            '404',
                        ],
                    ],
                ],
                1 => [
                    'carrier_id' => '2',
                    'status'     => 200,
                    'states'     => [
                        [
                            '404',
                        ],
                        [
                            'date'           => '2018-11-07 14:15:01',
                            'name'           => 'Doručování zásilky',
                            'status_id'      => 2,
                            'status_id_v2'   => 2.2,
                            'type'           => 'event',
                            'name_balikobot' => 'Zásilka je v přepravě.',
                        ],
                    ],
                ],
            ],
        ]);

        $status = $client->trackPackages('cp', ['1', '2']);

        self::assertEquals(
            [
                0 => [],
                1 => [
                    [
                        'date'          => '2018-11-07 14:15:01',
                        'name'          => 'Doručování zásilky',
                        'status_id'     => 2.2,
                        'type'          => 'event',
                        'name_internal' => 'Zásilka je v přepravě.',
                    ],
                ],
            ],
            $status
        );
    }
}
