<?php
interface makaira_autosuggester_adapter
{
    public function prepareResults($searchResult);
    public function translate($string);
}