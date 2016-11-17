<?php

namespace Makaira\Connect\Type\Common;


use Makaira\Connect\Type\Common\BaseProduct;
use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Utils\ContentParserInterface;

class LongDescriptionModifierTest extends \PHPUnit_Framework_TestCase
{

    public function testShortText()
    {
        $dbMock = $this->getMock(DatabaseInterface::class, ['query'], [], '', false);
        $parserMock = $this->getMock(ContentParserInterface::class);
        $parserMock
            ->expects($this->once())
            ->method('parseContent')
            ->will($this->returnArgument(0));
        $modifier = new LongDescriptionModifier($parserMock);
        $product = new BaseProduct();
        $product->OXLONGDESC = 'This is a short text';
        $product = $modifier->apply($product, $dbMock);
        $this->assertEquals('This is a short text', $product->OXLONGDESC);
    }

    public function testShortTextWithHTML()
    {
        $dbMock = $this->getMock(DatabaseInterface::class, ['query'], [], '', false);
        $parserMock = $this->getMock(ContentParserInterface::class);
        $parserMock
            ->expects($this->once())
            ->method('parseContent')
            ->will($this->returnArgument(0));
        $modifier = new LongDescriptionModifier($parserMock);
        $product = new BaseProduct();
        $product->OXLONGDESC = 'This is a <del>short</del> text';
        $product = $modifier->apply($product, $dbMock);
        $this->assertEquals('This is a short text', $product->OXLONGDESC);
    }

    public function testTrimming()
    {
        $dbMock = $this->getMock(DatabaseInterface::class, ['query'], [], '', false);
        $parserMock = $this->getMock(ContentParserInterface::class);
        $parserMock
            ->expects($this->once())
            ->method('parseContent')
            ->will($this->returnArgument(0));
        $modifier = new LongDescriptionModifier($parserMock);
        $product = new BaseProduct();
        $product->OXLONGDESC = '   This is a short text   ' . PHP_EOL;
        $product = $modifier->apply($product, $dbMock);
        $this->assertEquals('This is a short text', $product->OXLONGDESC);
    }

}
