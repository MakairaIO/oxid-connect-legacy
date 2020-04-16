<?php

use Makaira\Connect\Exception as ConnectException;
use Makaira\Connect\Core\Autosuggester;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;

class makaira_connect_autosuggest extends oxUBase
{
    public function __construct()
    {
        parent::__construct();

        try {
            $container = ContainerFactory::getInstance()->getContainer();
            /** @var Autosuggester $suggester */
            $suggester = $container->get(Autosuggester::class);

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
            $html = '';
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
