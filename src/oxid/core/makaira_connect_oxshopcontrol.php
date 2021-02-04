<?php

class makaira_connect_oxshopcontrol extends makaira_connect_oxshopcontrol_parent
{
    protected function _runOnce()
    {
        parent::_runOnce();

        $oxidConfig     = oxRegistry::getConfig();
        $gcp = function ($varName) use ($oxidConfig) {
            return $oxidConfig->getShopConfVar(
                $varName,
                null,
                oxConfig::OXMODULE_MODULE_PREFIX . 'makaira/connect'
            );
        };

        $enableLocalSelection = $gcp('makaira_ab_testing_local_group_select');

        if (!$enableLocalSelection) {
            return;
        }

        $cookieExperiments = oxRegistry::get('oxUtilsServer')->getOxCookie('mak_experiments');
        if (!$cookieExperiments) {
            $group = (bool) random_int(0, 1);
            $experimentsCookie = json_encode(
                [
                    [
                        'experiment' => (int) $gcp('makaira_ab_testing_local_group_id'),
                        'variation'  => $group ? $gcp('makaira_ab_testing_local_group_variation') : 'original'
                    ]
                ]
            );

            oxRegistry::get('oxUtilsServer')->setOxCookie(
                'mak_experiments',
                $experimentsCookie,
                time() + 15552000 // 180 days
            );

            $_COOKIE['mak_experiments'] = $experimentsCookie;
        }
    }
}
