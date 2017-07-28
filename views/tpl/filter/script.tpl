<ul class="makaira-filter__list">
    [{foreach from=$aggregation->values item="item" name="items"}]
    <li class="makaira-filter__item[{if $item->selected}] makaira-filter__item--active[{/if}]">
        <label class="makaira-filter__label">
            <input
                    type="checkbox"
                    name="makairaFilter[[{$aggregation->key}]]"
                    class="makaira-input makaira-input--checkbox"
                    value="[{$item->key}]"
                    [{if $item->selected}]checked="checked"[{/if}]
            />
            [{$item->key}] [{if $blShowDocCount}]([{$item->count}])[{/if}]
        </label>
    </li>
    [{/foreach}]
</ul>
