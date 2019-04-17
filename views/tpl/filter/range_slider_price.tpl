[{assign var=oActCurrency value=$oView->getActCurrency()}]
[{assign var=sCurrencySign value=$oActCurrency->sign}]

<div class="makaira-filter__slider-container">
    [{assign var='fromname' value='makairaFilter['|cat:$aggregation->key|cat:"_from_price]"}]
    [{assign var='toname' value='makairaFilter['|cat:$aggregation->key|cat:"_to_price]"}]
    [{assign var='dataLeft' value=$aggregation->min}]
    [{assign var='dataRight' value=$aggregation->max}]
    [{if $aggregation->selectedValues}]
        [{assign var='dataLeft' value=$aggregation->selectedValues.from}]
        [{assign var='dataRight' value=$aggregation->selectedValues.to}]
    [{/if}]
    [{assign var='dataLeft' value=$oViewConf->toCurrency($dataLeft)}]
    [{assign var='dataRight' value=$oViewConf->toCurrency($dataRight)}]

    <input type="hidden" class="makaira-filter__input--min" name="[{$fromname}]" value="[{$oViewConf->toCurrency($aggregation->min)}]" />
    <input type="hidden" class="makaira-filter__input--max" name="[{$toname}]" value="[{$oViewConf->toCurrency($aggregation->max)}]" />
    [{* TODO Handle min max check in js and remove additional inputs *}]
    [{assign var='maxname' value='makairaFilter['|cat:$aggregation->key|cat:"_rangemax]"}]
    [{assign var='minname' value='makairaFilter['|cat:$aggregation->key|cat:"_rangemin]"}]
    <input type="hidden" name="[{$minname}]" value="[{$oViewConf->toCurrency($aggregation->min)|floor}]" />
    <input type="hidden" name="[{$maxname}]" value="[{$oViewConf->toCurrency($aggregation->max)|ceil}]" />

    <p class="makaira-filter__slider-values">
        <span class="makaira-filter__symbol--currency">[{$sCurrencySign}]</span>
        <span class="makaira-filter__value--min"></span>
        <span class="makaira-filter__symbol--until">-</span>
        <span class="makaira-filter__symbol--currency">[{$sCurrencySign}]</span>
        <span class="makaira-filter__value--max"></span>
    </p>
    <div
        class="makaira-filter__range-slider"
        data-min="[{$oViewConf->toCurrency($aggregation->min)}]"
        data-max="[{$oViewConf->toCurrency($aggregation->max)}]"
        data-left="[{$dataLeft}]"
        data-right="[{$dataRight}]"
    >
    </div>
</div>
