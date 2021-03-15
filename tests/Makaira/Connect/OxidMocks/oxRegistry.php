<?php

class oxLang
{
    public function getLanguageArray()
    {
        $german       = new stdClass();
        $german->id   = 1;
        $german->abbr = 'de';

        $english       = new stdClass();
        $english->id   = 2;
        $english->abbr = 'en';

        return [$german, $english];
    }
}

class oxUtilsView
{
}

class oxBase
{
    public function getSqlActiveSnippet()
    {
        return 1;
    }
}

class oxSeoEncoder
{
}

class oxConfig
{
    const OXMODULE_MODULE_PREFIX = null;

    public function isMall()
    {
        return true;
    }

    public function getConfigParam()
    {
        return null;
    }

    public function getShopConfVar()
    {
        return null;
    }
}

class oxRegistry
{
    public static function getLang()
    {
        return new oxLang();
    }

    public static function get($key)
    {
        switch (strtolower($key)) {
            case 'oxutilsview':
                return new oxUtilsView();
            case 'oxarticle':
            case 'oxcategory':
            case 'oxmanufacturer':
                return new oxBase();
            case 'oxseoencoderarticle':
            case 'oxseoencodercategory':
            case 'oxseoencodermanufacturer':
                return new oxSeoEncoder();
        }

        return null;
    }

    public static function getConfig()
    {
        return new oxConfig();
    }
}
