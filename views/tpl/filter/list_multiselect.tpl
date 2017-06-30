[{assign var="hasAdditionalValues" value=false}]

<ul class="makaira-filter__list">
    [{foreach from=$aggregation->values item="item" name="items"}]
        [{if $smarty.foreach.items.iteration == 6}]
            [{assign var="hasAdditionalValues" value=true}]
            <li class="makaira-filter__item">
                <button type="button" class="makaira-filter__button makaira-filter__button--expand">
                     [{oxmultilang ident="MAKAIRA_FILTER_SHOW_MORE"}]
                 </button>
            </li>
        [{/if}]

        <li class="makaira-filter__item[{if $item->selected}] makaira-filter__item--active[{/if}]">
            <label class="makaira-filter__label">
                <input 
                    type="checkbox" 
                    name="makairaFilter[[{$aggregation->key}]][]" 
                    class="makaira-input makaira-input--checkbox" 
                    value="[{$item->key}]" 
                    [{if $item->selected}]checked="checked"[{/if}]
                />
                [{$item->key}] [{if $blShowDocCount}]([{$item->count}])[{/if}]
            </label>
        </li>

        [{if $hasAdditionalValues && $smarty.foreach.items.last}]
            <li class="makaira-filter__item makaira-filter__item--hidden">
                <button type="button" class="makaira-filter__button makaira-filter__button--collapse">
                    [{oxmultilang ident="MAKAIRA_FILTER_SHOW_LESS"}]
                </button>
            </li>
        [{/if}]
    [{/foreach}]
</ul>



