<?php

class makaira_connect_autosuggest extends oxUBase
{
    public function __construct()
    {
        parent::__construct();

        $dic = oxRegistry::get('yamm_dic');
        /** @var makaira_connect_autosuggester $suggester */
        $suggester = $dic['makaira.connect.suggester'];

        // get search term
        $searchPhrase = oxRegistry::getConfig()->getRequestParameter('term');

        $jsonResult = $suggester->search($searchPhrase);

        /** @var oxUtilsView $oxUtilsView */
        $oxUtilsView = oxRegistry::get('oxUtilsView');
        $smarty = $oxUtilsView->getSmarty();
        $smarty->assign('result', json_decode($jsonResult, true));

        $html = $smarty->fetch('makaira/autosuggest/autosuggest.tpl');


        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');

        echo $jsonResult;
        exit();
    }
}
