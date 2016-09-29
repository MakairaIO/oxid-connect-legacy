<?php
/**
 * This file is part of a marmalade GmbH project
 *
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 *
 * Version:    3.4
 * Author URI: http://www.marmalade.de
 */

/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id'          => 'makaira/connect',
    'title'       => 'Makaira :: Connect',
    'description' => 'Connector to Makaira',
    'thumbnail'   => 'marmalade.jpg',
    'version'     => '1.0.0',
    'author'      => 'marmalade GmbH',
    'url'         => 'http://www.marmalade.de',
    'email'       => 'support@marmalade.de',
    'extend'      => array(
    ),
    'files'       => array(
        'makaira_connect_endpoint' => 'makaira/connect/src/oxid/application/controllers/makaira_connect_endpoint.php',
    ),
    'templates'   => array(),
    'blocks'      => array(),
    'events'      => array(),
    'settings'    => array(),
);
