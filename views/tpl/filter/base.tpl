[{assign var=activeClass value=$oViewConf->getActiveClassname()}]
[{assign var=params value=$oViewConf->getFacetParams()}]

[{if $activeClass == "alist" }]
    [{assign var=baseLink value=$oView->getLinkWithCategory()}]
[{elseif $activeClass == "manufacturerlist" }]
    [{assign var="act_manf" value=$oView->getActManufacturer() }]
    [{assign var=baseLink value=$oView->getLinkWithCategory() }]
[{elseif $activeClass == "details" }]
    [{assign var="oActProduct" value=$oView->getProduct() }]
    [{assign var=baseLink value=$oActProduct->getLink()}]
[{elseif $activeClass == "search"}]
    [{assign var=baseLink value=$oViewConf->getSelfActionLink()|cat:"cl=search&searchparam="|cat:$oView->getSearchParamForHtml()}]
[{else}]
    [{assign var=baseLink value=$oView->getLink()}]
[{/if }]

[{assign var="topActiveClass" value=$oViewConf->getTopActiveClassName()}]
[{assign var="blShowDocCount" value=true}]
[{assign var="aggregations" value=$oView->getAggregations()}]


<aside class="makaira-filter">
    <form class="makaira-form" action="[{oxgetseourl ident=$baseLink}]" method="get">

        [{if $topActiveClass == "search" }]
            <input type="hidden" name="cl" value="[{$topActiveClass}]">
            [{$oViewConf->getNavFormParams()}]
        [{/if}]

        [{foreach from=$oViewConf->getParametersForFilterSeo() key="param" item="value"}]
            <input type="hidden" name="[{$param}]" value="[{$value}]" />
        [{/foreach}]

        [{foreach from=$aggregations key="key" item="aggregation"}]
            [{assign var="filterTitle" value=$aggregation->title|oxmultilangassign}]
            [{if $filterTitle|stristr:"TRANSLATION FOR"}]
                [{assign var="filterTitle" value=$aggregation->title}]
            [{/if}]

            <section class="makaira-filter__filter makaira-filter__filter--[{$aggregation->type}]">
                <header class="makaira-filter__filter-headline">[{$filterTitle}]</header>
                [{include file="makaira/filter/"|cat:$aggregation->type|cat:".tpl"}]
                [{if $oViewConf->isFilterSet($aggregation->key)}]
                    <a href="[{$oViewConf->getFilterSeoLink($baseLink,$aggregation->key)}]" class="marmFilterReset">[{oxmultilang ident="MAKAIRA_FILTER_RESET"}]</a>
                [{/if}]
            </section>
        [{/foreach}]

        [{if $oViewConf->isFilterActive()}]
            <a href="[{$baseLink}]">[{oxmultilang ident="MAKAIRA_FILTER_RESET_ALL"}]</a>
        [{/if}]
    </form>
</aside>
