[{assign var="hasAdditionalValues" value=false}]

<ul class="makaira-filter__items">
[{foreach from=$aggregation->values item="item" name="items"}]
    [{if $smarty.foreach.items.iteration == 6}]
        [{assign var="hasAdditionalValues" value=true}]
    [{/if}]

    <li class="makaira-filter__item[{if $item->selected}] makaira-filter__item--active[{/if}]">
        <label>
            <input type="checkbox" name="makairaFilter[[{$aggregation->key}]]" value="[{$item->value}]" [{if  $item->selected}]checked="checked"[{/if}]/>
            [{$item->value}] [{if $blShowDocCount}]([{$item->count}])[{/if}]
        </label>
    </li>
[{/foreach}]
</ul>
[{if $hasAdditionalValues}]
[{include file="widget/filter/list_show_more.tpl"}]
[{/if}]


