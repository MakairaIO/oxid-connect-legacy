<?php
/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 * Version:    1.0
 * Author:     Jens Richter <richter@marmalade.de>
 * Author URI: http://www.marmalade.de
 */

use Makaira\Connect\Analyse\EventDispatcher;

/**
 * Class makaira_connect_tracking
 */
class makaira_connect_tracking extends oxView
{
    const SESSION_KEY = 'makaira_tracking_log';

    public function init()
    {
        // Send response immediately and continue with processing
        $this->sendPixel();

        $session         = oxRegistry::getSession();

        try {
            $dic = oxRegistry::get('yamm_dic');
            /** @var EventDispatcher $eventDispatcher */
            $eventDispatcher = $dic['makaira.connect.analyse.event_dispatcher'];

            $params = oxRegistry::getConfig()->getRequestParameter('params');
            $decodedParams = base64_decode($params);

            $parameters = [];
            parse_str($decodedParams, $parameters);

            $sessionHash = $params;

            $trackingLog =
                $session->hasVariable(makaira_connect_tracking::SESSION_KEY) ?
                    $session->getVariable(makaira_connect_tracking::SESSION_KEY) : [];

            if (!isset($trackingLog[$sessionHash]) || (time() - $trackingLog[$sessionHash] > 300)) {
                $eventDispatcher->dispatch($parameters);
                $trackingLog[$sessionHash] = time();
                $session->setVariable(makaira_connect_tracking::SESSION_KEY, $trackingLog);
            }
        } catch (Exception $e) {
            // intentionally left empty
        }

        // terminate oxid request
        $session->freeze();
        oxRegistry::getUtils()->commitFileCache();
        exit();
    }

    /**
     * Close connection and send transparent pixel
     *
     * @see http://stackoverflow.com/a/3203394
     * @see http://stackoverflow.com/a/15273676
     */
    private function sendPixel()
    {
        ob_end_clean();
        ignore_user_abort(true);
        ob_start();

        header('Connection: close');
        header('Content-Encoding: none');
        header('Content-Type: image/png');
        echo base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII='
        );
        header('Content-Length: ' . ob_get_length());

        ob_end_flush();
        ob_flush();
        flush();
    }
}
