<?php

class makaira_connect_econda extends oxUBase
{
    public function __construct()
    {
        parent::__construct();

//        setcookie(
//            "mak_econda_session",
//            '{"wid":null,"aid":"00000cec-d98025a8-912b-46a4-a57d-7a691ba7a376","type":"cs","start":0,"widgetdetails":true,"emvid":"AWaHUExriuACR1AdcsqClMH0_hFsPTOs","p.ec:productBasketAddList":"[]","p.ec:productBuyList":"[]","p.ec:productViewList":"[{\"t\":\"2018-11-01T08:02:01Z\",\"pid\":\"00000001316221\"},{\"t\":\"2018-11-01T08:02:01Z\",\"pid\":\"00000001316221\"},{\"t\":\"2018-11-01T08:01:58Z\",\"pid\":\"00000001276623\"},{\"t\":\"2018-11-01T08:01:58Z\",\"pid\":\"00000001276623\"},{\"t\":\"2018-11-01T08:01:52Z\",\"pid\":\"00000001327106\"},{\"t\":\"2018-11-01T08:01:52Z\",\"pid\":\"00000001327106\"}]","timestamp":"2018-11-01T08:02:13Z","excl":[],"pid":[]}',
//            time() + 86400
//        );

        $econdaAccountId = null;
//        $econdaAccountId = '00000cec-d98025a8-912b-46a4-a57d-7a691ba7a376';

        if (oxRegistry::getConfig()->getConfigParam('makaira_connect_use_econda')) {
            $econdaAccountId = oxRegistry::getConfig()->getConfigParam('sEcondaRecommendationsAID');
        }

        header('Content-type: text/html');
        header('Expires: Sat, 01 Jan 2000 03:00:00 GMT');
        header('Cache-Control: no-cache, must-revalidate');

        echo $econdaAccountId ? $econdaAccountId : '';
        exit();
    }
}
