<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Thomas Uhlig <uhlig@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

namespace Makaira\Connect;

class VersionHandler
{
    /*
     * @var string
     */
    private static $connectVersion = null;

    /**
     * @return string
     */
    public function getVersionNumber(): string
    {
        if (null === self::$connectVersion) {
            $pathToMetadata = __DIR__ . '/../../../metadata.php';
            self::$connectVersion  = '';

            if (file_exists($pathToMetadata)) {
                include $pathToMetadata;
                self::$connectVersion = $aModule['version'];
            }
        }

        return self::$connectVersion;
    }
}
