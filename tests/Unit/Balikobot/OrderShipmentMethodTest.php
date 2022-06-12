<?php

declare(strict_types=1);

namespace Inspirum\Balikobot\Tests\Unit\Balikobot;

use Inspirum\Balikobot\Model\Aggregates\OrderedPackageCollection;
use Inspirum\Balikobot\Model\Values\OrderedPackage;
use Inspirum\Balikobot\Services\Balikobot;

class OrderShipmentMethodTest extends AbstractBalikobotTestCase
{
    public function testMakeRequest(): void
    {
        $requester = $this->newRequesterWithMockedRequestMethod(200, [
            'status'       => 200,
            'order_id'     => '29',
            'file_url'     => 'http://csv.balikobot.cz/cp/eNoz0jUFXDABKFwwlQ..',
            'handover_url' => 'http://pdf.balikobot.cz/cp/eNoz0jW0BfwwAe5cMMo.',
            'labels_url'   => 'http://pdf.balikobot.cz/cp/eNoz0jW0XDBcMAHtXDDJ',
            'package_ids'  => ['1', '2'],
        ], [
            'https://apiv2.balikobot.cz/ppl/order',
            [
                'package_ids' => ['1', '2'],
            ],
        ]);

        $service = new Balikobot($requester);

        $packages = new OrderedPackageCollection();

        $packages->add(new OrderedPackage('1', 'ppl', '0001', '1234'));
        $packages->add(new OrderedPackage('2', 'ppl', '0001', '5678'));

        $service->orderShipment($packages);

        self::assertTrue(true);
    }

    public function testResponseData(): void
    {
        $service = $this->newMockedBalikobot(200, [
            'status'       => 200,
            'order_id'     => '29',
            'file_url'     => 'http://csv.balikobot.cz/cp/eNoz0jUFXDABKFwwlQ..',
            'handover_url' => 'http://pdf.balikobot.cz/cp/eNoz0jW0BfwwAe5cMMo.',
            'labels_url'   => 'http://pdf.balikobot.cz/cp/eNoz0jW0XDBcMAHtXDDJ',
            'package_ids'  => ['1', '2'],
        ]);

        $packages = new OrderedPackageCollection();

        $packages->add(new OrderedPackage('1', 'ppl', '0001', '1234'));
        $packages->add(new OrderedPackage('2', 'ppl', '0001', '5678'));

        $orderedShipment = $service->orderShipment($packages);

        self::assertEquals('ppl', $orderedShipment->getShipper());
        self::assertEquals(['1', '2'], $orderedShipment->getPackageIds());
        self::assertEquals('http://csv.balikobot.cz/cp/eNoz0jUFXDABKFwwlQ..', $orderedShipment->getFileUrl());
        self::assertEquals('http://pdf.balikobot.cz/cp/eNoz0jW0XDBcMAHtXDDJ', $orderedShipment->getLabelsUrl());
        self::assertEquals('http://pdf.balikobot.cz/cp/eNoz0jW0BfwwAe5cMMo.', $orderedShipment->getHandoverUrl());
        self::assertEquals('29', $orderedShipment->getOrderId());
    }
}
