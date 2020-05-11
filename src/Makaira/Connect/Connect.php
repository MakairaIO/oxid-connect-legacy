<?php

/**
 * This file is part of a Makaira GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Martin Schnabel <ms@marmalade.group>
 * Author URI: https://www.makaira.io/
 */

namespace Makaira\Connect;

class Connect
{
    /**
     * @return \OxidEsales\EshopCommunity\Internal\Container\ContainerFactory
     */
    public static function getContainerFactory()
    {
        static $containerFactory = null;
        if ($containerFactory === null) {
            if (\class_exists('Makaira\ConnectCompat\ContainerCompat')) {
                $containerFactory = \Makaira\ConnectCompat\ContainerCompat::getInstance();
            } else {
                $containerFactory = \OxidEsales\EshopCommunity\Internal\Container\ContainerFactory::getInstance();
            }
        }

        return $containerFactory;
    }
}
