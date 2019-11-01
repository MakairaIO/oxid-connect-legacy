<?php

/**
 * This file is part of a marmalade GmbH project
 * It is not Open Source and may not be redistributed.
 * For contact information please visit http://www.marmalade.de
 *
 * @version    0.1
 * @author     Florian Ludwig <ludwig@marmalade.de>
 * @link       http://www.marmalade.de
 */

/**
 * This adds Odoscope tracking data to standard makaira tracking
 */
class TrackingWithOdoscopeData extends TrackingWithOdoscopeData_parent
{
    /**
     * Hook to add custom data to Piwik/Matomo tracking.
     *
     * @return array
     */
    protected function getCustomTrackingData()
    {
        global $odoscopeTracking;

        if ($odoscopeTracking) {
            return [['trackEvent', 'odoscope', $odoscopeTracking['group'], $odoscopeTracking['data']]];
        }

        return [];
    }
}
