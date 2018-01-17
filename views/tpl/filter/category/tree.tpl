[{assign var="hasAdditionalValues" value=false}]
[{assign var="items" value=$aggregation->values}]
[{defun name="tree" items=$items isInnerTree=false loopName="items"}]

<ul class="makaira-filter__list">
    [{foreach from=$items item="item" name=$loopName}]
        [{if !$isInnerTree && $smarty.foreach.items.iteration == 6}]
            [{assign var="hasAdditionalValues" value=true}]
            <li class="makaira-filter__item">
                <button type="button" class="makaira-filter__button makaira-filter__button--expand">
                    [{oxmultilang ident="MAKAIRA_FILTER_SHOW_MORE"}]
                </button>
            </li>
        [{/if}]
        <li class="makaira-filter__item[{if $item->selected}] makaira-filter__item--active[{/if}]">
            [{if $item->count}]
                <label class="makaira-filter__label">
                    <input
                            type="checkbox"
                            name="makairaFilter[[{$aggregation->key}]][]"
                            class="makaira-input makaira-input--checkbox"
                            value="[{$item->key}]"
                            [{if $item->selected}]checked="checked"[{/if}]
                    />
                    [{$item->title}] [{if $blShowDocCount && $item->count}]([{$item->count}])[{/if}]
                </label>
            [{else}]
                <span>[{$item->title}]</span>
            [{/if}]
            [{if $item->subtree}]
                [{fun name="tree" items=$item->subtree isInnerTree=true loopName="innerItems"}]
            [{/if}]
        </li>

        [{if !$isInnerTree && $hasAdditionalValues && $smarty.foreach.items.last}]
            <li class="makaira-filter__item makaira-filter__item--hidden">
                <button type="button" class="makaira-filter__button makaira-filter__button--collapse">
                    [{oxmultilang ident="MAKAIRA_FILTER_SHOW_LESS"}]
                </button>
            </li>
        [{/if}]

    [{/foreach}]
</ul>
[{/defun}]

