[{assign var=oActCurrency value=$oView->getActCurrency()}]
[{assign var=sCurrencySign value=$oActCurrency->sign}]

<div class="makaira-filter__slider-container">
    [{assign var='fromname' value='makairaFilter['|cat:$aggregation->key|cat:"_from]"}]
    [{assign var='toname' value='makairaFilter['|cat:$aggregation->key|cat:"_to]"}]
    [{assign var='dataLeft' value=$aggregation->min}]
    [{assign var='dataRight' value=$aggregation->max}]
    [{if $aggregation->selectedValues}]
        [{assign var='dataLeft' value=$aggregation->selectedValues.from}]
        [{assign var='dataRight' value=$aggregation->selectedValues.to}]
    [{/if}]

    <input type="hidden" class="makaira-filter__input--min" name="[{$fromname}]" value="[{$aggregation->min}]" />
    <input type="hidden" class="makaira-filter__input--max" name="[{$toname}]" value="[{$aggregation->max}]" />
    [{* TODO Handle min max check in js and remove additional inputs *}]
    [{assign var='maxname' value='makairaFilter['|cat:$aggregation->key|cat:"_rangemax]"}]
    [{assign var='minname' value='makairaFilter['|cat:$aggregation->key|cat:"_rangemin]"}]
    <input type="hidden" name="[{$minname}]" value="[{$aggregation->min|floor}]" />
    <input type="hidden" name="[{$maxname}]" value="[{$aggregation->max|ceil}]" />

    <p class="makaira-filter__slider-values">
        <span class="makaira-filter__symbol--currency">[{$sCurrencySign}]</span>
        <span class="makaira-filter__value--min"></span>
        <span class="makaira-filter__symbol--until">-</span>
        <span class="makaira-filter__symbol--currency">[{$sCurrencySign}]</span>
        <span class="makaira-filter__value--max"></span>
    </p>
    <div
        class="makaira-filter__range-slider"
        data-min="[{$aggregation->min}]"
        data-max="[{$aggregation->max}]"
        data-left="[{$dataLeft}]"
        data-right="[{$dataRight}]"
    >
    </div>
</div>
