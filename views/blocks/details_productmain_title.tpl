[{$smarty.block.parent}]
[{assign var="oDetailsProduct" value=$oView->getProduct()}]
[{include file="makaira/widget/tracking.tpl" event="detail_view" value=$oDetailsProduct->getId()}]
