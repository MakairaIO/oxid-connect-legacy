<?php

namespace Makaira\Connect\Type\Common;


use Makaira\Connect\DatabaseInterface;
use Makaira\Connect\Utils\ContentParserInterface;

class LongDescriptionModifier extends Modifier
{
    /** @var  ContentParserInterface */
    private $contentParser;

    /**
     * LongDescriptionModifier constructor.
     * @param ContentParserInterface $contentParser
     */
    public function __construct(ContentParserInterface $contentParser)
    {
        $this->contentParser = $contentParser;
    }


    /**
     * Modify product and return modified product
     *
     * @param BaseProduct $product
     * @param DatabaseInterface $database
     * @return BaseProduct
     */
    public function apply(ChangeDatum $product, DatabaseInterface $database)
    {
        $product->OXLONGDESC = trim(strip_tags($this->contentParser->parseContent($product->OXLONGDESC)));
        return $product;
    }
}
