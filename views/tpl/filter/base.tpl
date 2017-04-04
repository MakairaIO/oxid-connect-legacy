[{include file="widget/filter/base.tpl"}]
[{*
[{oxstyle include=$oViewConf->getModuleUrl('marm/oxsearch','out/src/css/filter.css')}]
[{oxstyle include=$oViewConf->getModuleUrl('marm/oxsearch','out/src/css/filter-flow.css')}]

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
[{assign var="aggregations" value=$oView->getFacet()}]


<aside class="makaira-filter">
    <form method="get" action="[{oxgetseourl ident=$baseLink}]">

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
                    <br clear="all"><a href="[{$oViewConf->getFilterSeoLink($baseLink,$aggregation->key)}]" class="marmFilterReset">[{oxmultilang ident="MARM_OXSEARCH_FILTER_RESET"}]</a>
                [{/if}]
            </section>
        [{/foreach}]
        [{if $oViewConf->isFilterActive()}]
            <br clear="all"><a href="[{$baseLink}]">[{oxmultilang ident="MARM_OXSEARCH_FILTER_RESET_ALL"}]</a>
        [{/if}]
    </form>
</aside>

[{capture}]
    <script>
        [{capture assign="makairaFilterScript"}]
            jQuery('.makaira-filter input[type=checkbox]').change(function () {
            if (jQuery(this).closest('.categorySidebar').find('[type=checkbox]:checked').length > 0) {
                jQuery('#filterForm').submit();
            }
            else {
                jQuery(this).closest('.categorySidebar').find('.marmFilterReset')[0].click();
            }
        });
        [{/capture}]
    </script>
[{/capture}]
[{oxscript add=$makairaFilterScript}]
*}]
