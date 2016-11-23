<?php

require_once __DIR__ . '/../../../../bootstrap.php';

/** @var \Marm\Yamm\DIC $dic */
$dic = oxRegistry::get('yamm_dic');
/** @var Makaira\Connect\Repository $repo */
$repo = $dic['makaira.connect.repository'];
$repo->cleanup();
