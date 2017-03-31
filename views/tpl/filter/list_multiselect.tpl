[{assign var="hasAdditionalValues" value=false}]

<ul class="makaira-filter__items">
[{foreach from=$aggregation->values item="item" name="items"}]
    [{if $smarty.foreach.items.iteration == 6}]
        [{assign var="hasAdditionalValues" value=true}]
    [{/if}]

    [{assign var="checked" value="0"}]
    [{assign var="decodedparamvalue" value=$paramvalue|utf8_decode}]
    [{if $paramvalue==$item|@key || $decodedparamvalue==$item|@key}]
        [{assign var="checked" value="1"}]
    [{/if}]

    <li class="makaira-filter__item[{if $checked}] makaira-filter__item--active[{/if}]">
        <label>
            <input type="checkbox" name="[{$aggregation->key}][]" value="[{$item|@key}]" [{if $checked}]checked="checked"[{/if}]/>
            [{$item|@key}] [{if $blShowDocCount}]([{$item|@current}])[{/if}]
        </label>
    </li>
[{/foreach}]
</ul>
[{if $hasAdditionalValues}]
[{include file="widget/filter/list_show_more.tpl"}]
[{/if}]


