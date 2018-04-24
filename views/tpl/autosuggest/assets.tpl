[{assign var="cssFilePath" value=$oViewConf->getMakairaMainStylePath()}]
[{oxstyle include=$oViewConf->getModuleUrl('makaira/connect', $cssFilePath)}]

[{assign var="jsFilePath" value=$oViewConf->getMakairaMainScriptPath()}]
[{oxscript include=$oViewConf->getModuleUrl('makaira/connect', $jsFilePath) priority=10 }]
