<?php

declare(strict_types=1);

namespace Inspirum\Balikobot\Model\PackageData\Package;

use Inspirum\Balikobot\Definitions\Option;

trait ParcelData
{
    /**
     * @param float $width
     *
     * @return void
     */
    public function setWidth(float $width): void
    {
        $this->offsetSet(Option::WIDTH, $width);
    }

    /**
     * @param float $length
     *
     * @return void
     */
    public function setLength(float $length): void
    {
        $this->offsetSet(Option::LENGTH, $length);
    }

    /**
     * @param float $height
     *
     * @return void
     */
    public function setHeight(float $height): void
    {
        $this->offsetSet(Option::HEIGHT, $height);
    }

    /**
     * @param float $weight
     *
     * @return void
     */
    public function setWeight(float $weight): void
    {
        $this->offsetSet(Option::WEIGHT, $weight);
    }

    /**
     * @param float $price
     *
     * @return void
     */
    public function setPrice(float $price): void
    {
        $this->offsetSet(Option::PRICE, $price);
    }

    /**
     * @param float $volume
     *
     * @return void
     */
    public function setVolume(float $volume): void
    {
        $this->offsetSet(Option::VOLUME, $volume);
    }

    /**
     * @param bool $overDimension
     *
     * @return void
     */
    public function setOverDimension(bool $overDimension = true): void
    {
        $this->offsetSet(Option::OVER_DIMENSION, (int) $overDimension);
    }

    /**
     * @param string $currency
     *
     * @return void
     */
    public function setInsCurrency(string $currency): void
    {
        $this->offsetSet(Option::INS_CURRENCY, $currency);
    }
}
