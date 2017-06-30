[{* object(Makaira\Aggregation)[596] *}]
  [{* public 'title' => string 'Preis' (length=5) *}]
  [{* public 'key' => string 'price' (length=5) *}]
  [{* public 'type' => string 'range_slider' (length=12) *}]
  [{* public 'values' => null *}]
  [{* public 'min' => int 589 *}]
  [{* public 'max' => int 1599 *}]

[{* [{assign var=fromname value='makairaFilter['|cat:$aggregation->key|cat:"_from]"}] *}]
[{* [{assign var=toname value='makairaFilter['|cat:$aggregation->key|cat:"_to]"}] *}]

[{* <input type="hidden" name="[{$fromname}]" value="[{$facetParams.$fromname}]" /> *}]
[{* <input type="hidden" name="[{$toname}]" value="[{$facetParams.$toname}]" /> *}]

[{assign var=oActCurrency value=$oView->getActCurrency()}]
[{assign var=sCurrencySign value=$oActCurrency->sign}]

<div class="makaira-filter__slider-container">
    [{* TODO: is there a oxid-way to initially set min/max values? *}]
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
    />
    </div>
</div>
