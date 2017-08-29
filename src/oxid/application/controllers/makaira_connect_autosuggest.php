<?php

class makaira_connect_autosuggest extends oxUBase
{
    public function __construct()
    {
        parent::__construct();

        //$dic            = oxRegistry::get('yamm_dic');
        //$oAutosuggester = $dic['marm_oxsearch']['oxsearch_autosuggester'];

        // get search term
        //$sTerm = oxRegistry::getConfig()->getRequestParameter('term');

        //$jsonResult = $oAutosuggester->search($sTerm);
        $jsonResult = '{"count":10,"items":[{"label":"Tecnica Mach 1 130 MV            Skischuh Bright Orange \/ Black Herren","value":"Tecnica Mach 1 130 MV            Skischuh Bright Orange \/ Black Herren","link":"https:\/\/www.sport-conrad.com\/produkte\/tecnica\/mach-1-130-mv.html?listtype=search&searchparam=ski","image":"https:\/\/static.sport-conrad.com\/out\/pictures\/\/generated\/product\/1\/87_87_75\/74104120_741_041_20.jpg","thumbnail":"https:\/\/static.sport-conrad.com\/out\/pictures\/\/generated\/product\/1\/247_200_75\/74104120_741_041_20.jpg","price":{"brutto":"379,00","netto":"318,49"},"tprice":{"brutto":"499,95","netto":"420,13"},"type":"product","category":"Produkte"},{"label":"Scarpa Maestrale RS Tourenskischuh White\/Orange Herren","value":"Scarpa Maestrale RS Tourenskischuh White\/Orange Herren","link":"https:\/\/www.sport-conrad.com\/produkte\/scarpa\/maestrale-rs.html?listtype=search&searchparam=ski","image":"https:\/\/static.sport-conrad.com\/out\/pictures\/\/generated\/product\/1\/87_87_75\/74609412_746_094_12.jpg","thumbnail":"https:\/\/static.sport-conrad.com\/out\/pictures\/\/generated\/product\/1\/247_200_75\/74609412_746_094_12.jpg","price":{"brutto":"399,00","netto":"335,29"},"tprice":{"brutto":"549,00","netto":"461,34"},"type":"product","category":"Produkte"},{"label":"CEP Ski Merino  Skisocken Black \/ Azur Herren","value":"CEP Ski Merino  Skisocken Black \/ Azur Herren","link":"https:\/\/www.sport-conrad.com\/produkte\/cep\/ski-merino-sc.html?listtype=search&searchparam=ski","image":"https:\/\/static.sport-conrad.com\/out\/pictures\/\/generated\/product\/1\/87_87_75\/87600213_876_002_13.jpg","thumbnail":"https:\/\/static.sport-conrad.com\/out\/pictures\/\/generated\/product\/1\/247_200_75\/87600213_876_002_13.jpg","price":{"brutto":"49,90","netto":"41,93"},"tprice":{"brutto":0,"netto":0},"type":"product","category":"Produkte"},{"label":"Marker Baron EPF 13 inkl. Stopper Tourenskibindung","value":"Marker Baron EPF 13 inkl. Stopper Tourenskibindung","link":"https:\/\/www.sport-conrad.com\/produkte\/marker\/baron-epf-13-inkl-stopper.html?listtype=search&searchparam=ski","image":"https:\/\/static.sport-conrad.com\/out\/pictures\/\/generated\/product\/1\/87_87_75\/72603701_726_037_01.jpg","thumbnail":"https:\/\/static.sport-conrad.com\/out\/pictures\/\/generated\/product\/1\/247_200_75\/72603701_726_037_01.jpg","price":{"brutto":"229,00","netto":"192,44"},"tprice":{"brutto":"329,95","netto":"277,27"},"type":"product","category":"Produkte"},{"label":"Marker Tour EPF 12 inkl. Stopper Tourenskibindung","value":"Marker Tour EPF 12 inkl. Stopper Tourenskibindung","link":"https:\/\/www.sport-conrad.com\/produkte\/marker\/tour-epf-12-inkl-stopper.html?listtype=search&searchparam=ski","image":"https:\/\/static.sport-conrad.com\/out\/pictures\/\/generated\/product\/1\/87_87_75\/72603702_726_037_02.jpg","thumbnail":"https:\/\/static.sport-conrad.com\/out\/pictures\/\/generated\/product\/1\/247_200_75\/72603702_726_037_02.jpg","price":{"brutto":"249,00","netto":"209,24"},"tprice":{"brutto":"339,95","netto":"285,67"},"type":"product","category":"Produkte"},{"label":"Marker Tour F10  Tourenskibindung Black \/ White","value":"Marker Tour F10  Tourenskibindung Black \/ White","link":"https:\/\/www.sport-conrad.com\/produkte\/marker\/tour-f10.html?listtype=search&searchparam=ski","image":"https:\/\/static.sport-conrad.com\/out\/pictures\/\/generated\/product\/1\/87_87_75\/72603703_726_037_03.jpg","thumbnail":"https:\/\/static.sport-conrad.com\/out\/pictures\/\/generated\/product\/1\/247_200_75\/72603703_726_037_03.jpg","price":{"brutto":"199,00","netto":"167,23"},"tprice":{"brutto":"309,95","netto":"260,46"},"type":"product","category":"Produkte"},{"label":"CEP Ski Thermo  Skisocken Cranberry \/ Orange Damen","value":"CEP Ski Thermo  Skisocken Cranberry \/ Orange Damen","link":"https:\/\/www.sport-conrad.com\/produkte\/cep\/ski-thermo.html?listtype=search&searchparam=ski","image":"https:\/\/static.sport-conrad.com\/out\/pictures\/\/generated\/product\/1\/87_87_75\/87600214_876_002_14.jpg","thumbnail":"https:\/\/static.sport-conrad.com\/out\/pictures\/\/generated\/product\/1\/247_200_75\/87600214_876_002_14.jpg","price":{"brutto":"49,90","netto":"41,93"},"tprice":{"brutto":0,"netto":0},"type":"product","category":"Produkte"}],"productCount":3484}';

        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');

        echo $jsonResult;
        exit();
    }
}
