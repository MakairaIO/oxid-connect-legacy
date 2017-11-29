<?php

namespace Makaira\Connect\Modifier\Common;

use Makaira\Connect\Modifier;
use Makaira\Connect\Type;
use Makaira\Connect\Utils\ContentParserInterface;

class LongDescriptionModifier extends Modifier
{
    /** @var  ContentParserInterface */
    private $contentParser;

    /** @var bool */
    private $parseThroughSmarty;

    /**
     * LongDescriptionModifier constructor.
     *
     * @param ContentParserInterface $contentParser
     */
    public function __construct(ContentParserInterface $contentParser, $parseThroughSmarty = false)
    {
        $this->contentParser      = $contentParser;
        $this->parseThroughSmarty = (bool) $parseThroughSmarty;
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
        $parsedContent       =
            $this->parseThroughSmarty ? $this->contentParser->parseContent($product->OXLONGDESC) : $product->OXLONGDESC;
        $product->OXLONGDESC = trim(strip_tags($parsedContent));

        return $product;
    }
}
