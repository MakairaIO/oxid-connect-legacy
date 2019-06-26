<?php

class oxLang
{
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

class oxUBase
{
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
            case 'alist':
            case 'details':
            case 'manufacturerlist':
                return new oxUBase();
        }

        return null;
    }

    public static function getConfig()
    {
        return new oxConfig();
    }
}
