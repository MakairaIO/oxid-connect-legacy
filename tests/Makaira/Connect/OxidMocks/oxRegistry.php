<?php

class oxLang
{
}

class oxUtilsView
{
}

class oxConfig
{
    public function isMall()
    {
        return true;
    }

    public function getConfigParam()
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
        }

        return null;
    }

    public static function getConfig()
    {
        return new oxConfig();
    }
}
