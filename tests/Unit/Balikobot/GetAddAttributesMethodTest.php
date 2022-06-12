<?php

declare(strict_types=1);

namespace Inspirum\Balikobot\Tests\Unit\Balikobot;

use Inspirum\Balikobot\Services\Balikobot;

class GetAddAttributesMethodTest extends AbstractBalikobotTestCase
{
    public function testMakeRequest(): void
    {
        $requester = $this->newRequesterWithMockedRequestMethod(200, [
            'status'   => 200,
            'services' => [],
        ], [
            'https://api.balikobot.cz/ppl/addattributes',
            [],
        ]);

        $service = new Balikobot($requester);

        $service->getAddAttributes('ppl');

        self::assertTrue(true);
    }

    public function testResponseData(): void
    {
        $service = $this->newMockedBalikobot(200, [
            'status'     => 200,
            'attributes' => [
                '0' => [
                    'name'       => 'eid',
                    'data_type'  => 'string',
                    'max_length' => 40,
                ],
                '1' => [
                    'name'       => 'services',
                    'data_type'  => 'plus_separated_values',
                    'max_length' => null,
                ],
                '2' => [
                    'name'       => 'vs',
                    'data_type'  => 'int',
                    'max_length' => 10,
                ],
            ],
        ]);

        $units = $service->getAddAttributes('cp');

        self::assertEquals(
            [
                'eid'      => [
                    'name'       => 'eid',
                    'data_type'  => 'string',
                    'max_length' => 40,
                ],
                'services' => [
                    'name'       => 'services',
                    'data_type'  => 'plus_separated_values',
                    'max_length' => null,
                ],
                'vs'       => [
                    'name'       => 'vs',
                    'data_type'  => 'int',
                    'max_length' => 10,
                ],
            ],
            $units
        );
    }
}
