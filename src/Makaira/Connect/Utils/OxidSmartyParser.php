<?php

namespace Makaira\Connect\Utils;


class OxidSmartyParser implements ContentParserInterface
{
    /** @var  \oxLang */
    private $oxLang;

    /** @var  \oxUtilsView */
    private $oxUtilsView;

    /**
     * OxidSmartyParser constructor.
     * @param \oxLang $oxLang
     * @param \oxUtilsView $oxUtilsView
     */
    public function __construct(\oxLang $oxLang, \oxUtilsView $oxUtilsView)
    {
        $this->oxLang = $oxLang;
        $this->oxUtilsView = $oxUtilsView;
    }

    public function setTplLang($langId)
    {
        $this->oxLang->setTplLanguage($langId);
    }

    /**
     * Parse content through a templating engine
     * @param string $content
     * @return string
     */
    public function parseContent($content)
    {
        return $this->oxUtilsView->parseThroughSmarty($content);
    }
}
