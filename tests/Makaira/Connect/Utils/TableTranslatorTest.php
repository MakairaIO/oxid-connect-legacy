<?php

namespace Makaira\Connect\Utils;


class TableTranslatorTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleTranslate()
    {
        $translator = new TableTranslator(['oxarticles',]);

        $sql = $translator->translate('SELECT * FROM oxarticles');
        $this->assertEquals('SELECT * FROM oxv_oxarticles_de', $sql);
    }

    public function testTranslateWithSetLanguage()
    {
        $translator = new TableTranslator(['oxarticles',]);
        $translator->setLanguage('kh');

        $sql = $translator->translate('SELECT * FROM oxarticles');
        $this->assertEquals('SELECT * FROM oxv_oxarticles_kh', $sql);
    }

    public function testTranslateWithView()
    {
        $translator = new TableTranslator(['oxarticles',]);

        $sql = $translator->translate('SELECT * FROM oxv_oxarticles_en');
        $this->assertEquals('SELECT * FROM oxv_oxarticles_en', $sql);
    }

    public function testMultiTranslate()
    {
        $translator = new TableTranslator(['oxarticles',]);

        $sql = $translator->translate('SELECT * FROM oxarticles WHERE oxarticles.OXACTIVE = 1');
        $this->assertEquals('SELECT * FROM oxv_oxarticles_de WHERE oxv_oxarticles_de.OXACTIVE = 1', $sql);
    }

    public function testTranslateWithMultipleTables()
    {
        $translator = new TableTranslator(['oxarticles', 'oxartextends']);

        $sql = $translator->translate('SELECT * FROM oxarticles LEFT JOIN oxartextends ON oxartextends.OXID = oxarticles.OXID');
        $this->assertEquals('SELECT * FROM oxv_oxarticles_de LEFT JOIN oxv_oxartextends_de ON oxv_oxartextends_de.OXID = oxv_oxarticles_de.OXID', $sql);
    }

    public function testTranslateWithPartialMatches()
    {
        $translator = new TableTranslator(['oxarticles',]);

        $sql = $translator->translate('SELECT * FROM oxarticles LEFT JOIN oxarticles2shop ON oxarticles2shop.OXMAPOBJECTID = oxarticles.OXMAPID');
        $this->assertEquals('SELECT * FROM oxv_oxarticles_de LEFT JOIN oxarticles2shop ON oxarticles2shop.OXMAPOBJECTID = oxv_oxarticles_de.OXMAPID', $sql);
    }
}
