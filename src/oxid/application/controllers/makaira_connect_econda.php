<?php

/**
 * Class makaira_connect_econda
 */
class makaira_connect_econda extends oxUBase
{
    /**
     * makaira_connect_econda constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $econdaAccountId = null;
        if (oxRegistry::getConfig()->getConfigParam('makaira_connect_use_econda')) {
            $econdaAccountId = oxRegistry::getConfig()->getConfigParam('sEcondaRecommendationsAID');
            if (!$econdaAccountId && method_exists($this->getViewConfig(), 'oePersonalizationGetAccountId')) {
                $econdaAccountId = $this->getViewConfig()->oePersonalizationGetAccountId();
            }
        }

        header('Content-type: text/html');
        header('Expires: Sat, 01 Jan 2000 03:00:00 GMT');
        header('Cache-Control: no-cache, must-revalidate');

        echo $econdaAccountId ? $econdaAccountId : '';
        exit();
    }
}
