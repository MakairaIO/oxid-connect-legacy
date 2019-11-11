<?php

use Makaira\Connect\Exception as ConnectException;

class makaira_connect_autosuggest extends oxUBase
{
    public function __construct()
    {
        parent::__construct();

        try {
            $dic = oxRegistry::get('yamm_dic');
            /** @var makaira_connect_autosuggester $suggester */
            $suggester = $dic['makaira.connect.suggester'];

            // get search term
            $searchPhrase = oxRegistry::getConfig()->getRequestParameter('term');

            $result = $suggester->search($searchPhrase);

            /** @var oxUtilsView $oxUtilsView */
            $oxUtilsView = oxRegistry::get('oxUtilsView');
            $smarty = $oxUtilsView->getSmarty();
            $smarty->assign('result', $result);
            $smarty->assign('searchPhrase', $searchPhrase);
            $oxViewConfig = oxNew('oxViewConfig');
            $smarty->assign('oViewConf', $oxViewConfig);

            $html = $smarty->fetch('makaira/autosuggest/autosuggest.tpl');
        } catch (ConnectException $e) {
            $oxException = new oxException($e->getMessage(), $e->getCode());
            $oxException->debugOut();
            $html = $e->getCode() . '  ' . $e->getMessage();
        } catch (Exception $e) {
            $html = '';
        }

        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: text/html');

        echo $html;
        exit();
    }
}
