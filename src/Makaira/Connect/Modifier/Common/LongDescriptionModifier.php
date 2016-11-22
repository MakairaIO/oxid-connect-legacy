<?php

namespace Makaira\Connect\Modifier\Common;

use Makaira\Connect\Modifier;
use Makaira\Connect\Type;
use Makaira\Connect\Utils\ContentParserInterface;

class LongDescriptionModifier extends Modifier
{
    /** @var  ContentParserInterface */
    private $contentParser;

    /**
     * LongDescriptionModifier constructor.
     *
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
     *
     * @return BaseProduct
     */
    public function apply(Type $product)
    {
        $product->OXLONGDESC = trim(strip_tags($this->contentParser->parseContent($product->OXLONGDESC)));

        return $product;
    }
}
