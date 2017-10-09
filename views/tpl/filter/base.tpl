[{assign var=activeClass value=$oViewConf->getActiveClassname()}]

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
[{assign var="showResetAll" value=false}]


<aside class="makaira-filter">
    [{* show small header if we have other search results located above producs *}]
    [{if $topActiveClass == "search" }]
        [{if $category_result || $manufacturer_result || $links_result}]
            <p class="makaira-filter__header">Produkte</p>
        [{/if}]
    [{/if}]
    <form class="makaira-form" action="[{oxgetseourl ident=$baseLink}]" method="get">

        [{if $topActiveClass == "search" }]
            <input type="hidden" name="cl" value="[{$topActiveClass}]">
            [{$oViewConf->getNavFormParams()}]
        [{/if}]

        [{*
            FIXME Code from old module
            TODO Do we really need a getter for following params? ['ldtype', '_artperpage', 'listorder', 'listorderby']
            [{foreach from=$oViewConf->getParametersForFilterSeo() key="param" item="value"}]
                <input type="hidden" name="[{$param}]" value="[{$value}]" />
            [{/foreach}]
        *}]

        [{foreach from=$aggregations key="key" item="aggregation"}]
            [{assign var="filterTitle" value=$aggregation->title|oxmultilangassign}]
            [{if $filterTitle|stristr:"TRANSLATION FOR"}]
                [{assign var="filterTitle" value=$aggregation->title}]
            [{/if}]

            <section class="makaira-filter__filter makaira-filter__filter--[{$aggregation->type}]">
                <header class="makaira-filter__filter-headline">[{$filterTitle}]</header>
                [{include file="makaira/filter/"|cat:$aggregation->type|cat:".tpl"}]
                [{if !empty($aggregation->selectedValues)}]
                    [{assign var="showResetAll" value=true}]
                    [{*
                        FIXME Handle filter reset with javascript instead of links
                        <a href="[{$oViewConf->getFilterSeoLink($baseLink,$aggregation->key)}]" class="marmFilterReset">[{oxmultilang ident="MAKAIRA_FILTER_RESET"}]</a>
                    *}]
                [{/if}]
            </section>
        [{/foreach}]

        [{if $showResetAll}]
            <a href="[{$baseLink|oxaddparams:'fnc=resetMakairaFilter'}]">[{oxmultilang ident="MAKAIRA_FILTER_RESET_ALL"}]</a>
        [{/if}]
    </form>
</aside>
