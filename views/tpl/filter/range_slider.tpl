[{assign var=oActCurrency value=$oView->getActCurrency()}]
[{assign var=sCurrencySign value=$oActCurrency->sign}]

<div class="makaira-filter__slider-container">
     [{assign var=fromname value='makairaFilter['|cat:$aggregation->key|cat:"_from]"}]
     [{assign var=toname value='makairaFilter['|cat:$aggregation->key|cat:"_to]"}]

     <input type="hidden" class="makaira-filter__input--min" name="[{$fromname}]" value="[{$aggregation->min}]" />
     <input type="hidden" class="makaira-filter__input--max" name="[{$toname}]" value="[{$aggregation->max}]" />

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
        data-left="[{$aggregation->values.from}]"
        data-right="[{$aggregation->values.to}]"
    />
    </div>
</div>
