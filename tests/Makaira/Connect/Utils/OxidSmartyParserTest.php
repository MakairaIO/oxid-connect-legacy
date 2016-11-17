<?php

namespace Makaira\Connect\Utils;


class OxidSmartyParserTest extends \PHPUnit_Framework_TestCase
{

    public function testLanguageSetting()
    {
        $langMock = $this->getMock(\oxLang::class, ['setTplLanguage']);
        $langMock
            ->expects($this->once())
            ->method('setTplLanguage')
            ->with(4);
        $utilsViewMock = $this->getMock(\oxUtilsView::class);
        $parser = new OxidSmartyParser($langMock, $utilsViewMock);
        $parser->setTplLang(4);
    }

    public function testParsing()
    {
        $langMock = $this->getMock(\oxLang::class);
        $utilsViewMock = $this->getMock(\oxUtilsView::class, ['parseThroughSmarty']);
        $utilsViewMock
            ->expects($this->once())
            ->method('parseThroughSmarty')
            ->with('foo')
            ->willReturn('bar');

        $parser = new OxidSmartyParser($langMock, $utilsViewMock);
        $this->assertEquals('bar', $parser->parseContent('foo'));
    }

}
