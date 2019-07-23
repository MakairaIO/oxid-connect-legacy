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

        $oConfig = oxRegistry::getConfig();
        $econdaAccountId = null;
        if ($oConfig->getConfigParam('makaira_connect_use_econda')) {
            $econdaAccountId = $oConfig->getConfigParam('makaira_connect_econda_aid');
            if (!$econdaAccountId) {
                $oConfig->setEcondaThemeParam();
                $oViewConfig = $this->getViewConfig();

                $econdaAccountId = $oViewConfig->getViewThemeParam('sEcondaRecommendationsAID');

                if (!$econdaAccountId) {
                    $econdaAccountId = $oViewConfig->getViewThemeParam('sEcondaRecommendationsAID');

                    if (!$econdaAccountId && method_exists($oViewConfig, 'oePersonalizationGetAccountId')) {
                        $econdaAccountId = $oViewConfig->oePersonalizationGetAccountId();
                    }
                }
            }
        }

        header('Content-type: text/html');
        header('Expires: Mon, 04 Sep 2017 03:35:00 GMT');
        header('Cache-Control: no-cache, must-revalidate');

        echo $econdaAccountId ? $econdaAccountId : '';
        exit();
    }
}
