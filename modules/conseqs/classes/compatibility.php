<?php
/**
 * Copyright (C) 2017-2019 Petr Hucik <petr@getdatakick.com>
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the DataKick Regular License version 1.0
 * For more information see LICENSE.txt file
 *
 * @author    Petr Hucik <petr@getdatakick.com>
 * @copyright 2017-2019 Petr Hucik
 * @license   Licensed under the DataKick Regular License version 1.0
 */

namespace Conseqs;

class Compatibility
{
    const NON_COMPATIBLE = 'non_compatible';

    /** @var string $minPrestaShopVersion */
    private $minPrestaShopVersion;

    /** @var string $minThirtybeesVersion */
    private $minThirtybeesVersion;

    /**
     * Compatibility constructor.
     * @param string $minPrestaShopVersion
     * @param string $minThirtybeesVersion
     */
    public function __construct($minPrestaShopVersion, $minThirtybeesVersion=null)
    {
        $this->minPrestaShopVersion = $minPrestaShopVersion;
        if ($minThirtybeesVersion) {
            $this->minThirtybeesVersion = $minThirtybeesVersion;
        } else {
            if (version_compare($minPrestaShopVersion, '1.6', '>=')) {
                $this->minThirtybeesVersion = '1.0.0';
            } else {
                $this->minThirtybeesVersion = static::NON_COMPATIBLE;
            }
        }
    }

    /**
     * Compatible with all platforms
     *
     * @return Compatibility
     */
    public static function all()
    {
        return new static('1.6', '1.0.0');
    }

    /**
     * Not compatible with any platforms
     *
     * @return Compatibility
     */
    public static function incompatible()
    {
        return new static(self::NON_COMPATIBLE, self::NON_COMPATIBLE);
    }

    /**
     * Compatible with thirtybees only
     *
     * @return Compatibility
     */
    public static function thirtybeesOnly()
    {
        return new static(self::NON_COMPATIBLE, '1.0.0');
    }

    /**
     * Compatible with ps17 only
     *
     * @return Compatibility
     */
    public static function ps17Only()
    {
        return new static('1.7', self::NON_COMPATIBLE);
    }

    /**
     * @return bool
     */
    public function isCompatible()
    {
        return defined('_TB_VERSION_')
            ? $this->checkVersion(_TB_VERSION_, $this->minThirtybeesVersion)
            : $this->checkVersion(_PS_VERSION_, $this->minPrestaShopVersion);
    }

    /**
     * @param string $version
     * @param string $minVersion
     * @return bool
     */
    private function checkVersion($version, $minVersion)
    {
        if ($minVersion === static::NON_COMPATIBLE) {
            return false;
        }
        return version_compare($version, $minVersion, '>=');
    }

}