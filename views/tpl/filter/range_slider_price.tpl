[{assign var=oActCurrency value=$oView->getActCurrency()}]
[{assign var=sCurrencySign value=$oActCurrency->sign}]

<div class="makaira-filter__slider-container">
    [{assign var='fromname' value='makairaFilter['|cat:$aggregation->key|cat:"_from_price]"}]
    [{assign var='toname' value='makairaFilter['|cat:$aggregation->key|cat:"_to_price]"}]
    [{assign var='dataMin' value=$oViewConf->toCurrency($aggregation->min)}]
    [{assign var='dataMax' value=$oViewConf->toCurrency($aggregation->max)}]
    [{if $aggregation->selectedValues}]
        [{assign var='dataLeft' value=$oViewConf->toCurrency($aggregation->selectedValues.from)}]
        [{assign var='dataRight' value=$oViewConf->toCurrency($aggregation->selectedValues.to)}]
    [{else}]
        [{assign var='dataLeft' value=$dataMin}]
        [{assign var='dataRight' value=$dataMax}]
    [{/if}]

    <input type="hidden" class="makaira-filter__input--min" name="[{$fromname}]" value="[{$dataMin}]" />
    <input type="hidden" class="makaira-filter__input--max" name="[{$toname}]" value="[{$dataMax}]" />
    [{* TODO Handle min max check in js and remove additional inputs *}]
    [{assign var='maxname' value='makairaFilter['|cat:$aggregation->key|cat:"_rangemax]"}]
    [{assign var='minname' value='makairaFilter['|cat:$aggregation->key|cat:"_rangemin]"}]
    <input type="hidden" name="[{$minname}]" value="[{$dataMin|floor}]" />
    <input type="hidden" name="[{$maxname}]" value="[{$dataMax|ceil}]" />

    <p class="makaira-filter__slider-values">
        <span class="makaira-filter__symbol--currency">[{$sCurrencySign}]</span>
        <span class="makaira-filter__value--min"></span>
        <span class="makaira-filter__symbol--until">-</span>
        <span class="makaira-filter__symbol--currency">[{$sCurrencySign}]</span>
        <span class="makaira-filter__value--max"></span>
    </p>
    <div
        class="makaira-filter__range-slider"
        data-min="[{$dataMin}]"
        data-max="[{$dataMax}]"
        data-left="[{$dataLeft}]"
        data-right="[{$dataRight}]"
    >
    </div>
</div>
