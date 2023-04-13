{if !$breadonly}

<div id="idTabRichSnippets">
	{if !isset($product) OR !$product}<p>Missing product object. Set nerw object mode from the back office Configuration</p>{/if}
	<div itemscope itemtype="http://schema.org/Product">
		<ul>
			<li>{l s='Product Name' mod='psrichsnippets'}:<span itemprop="name">{$product->name|escape:'htmlall':'UTF-8'}</span></li>

			{if $product->reference}
				<li>{l s='Product Reference' mod='psrichsnippets'}: <span itemprop="sku">{$product->reference|escape:'htmlall':'UTF-8'}</span></li>
			{/if}
			{if $product->manufacturer_name}
				<li>{l s="By"}<span itemprop="manufacturer" itemscope itemtype="http://schema.org/Organization"> <span itemprop="name">{$product->manufacturer_name}</span></span></li>
			{/if}
			

			<li>
				{l s='Price' mod='psrichsnippets'}:
				
				{if !$priceDisplay || $priceDisplay == 2}
					{assign var='productPrice' value=$product->getPrice(true, $smarty.const.NULL, $priceDisplayPrecision)}
					{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(false, $smarty.const.NULL)}
				{elseif $priceDisplay == 1}
					{assign var='productPrice' value=$product->getPrice(false, $smarty.const.NULL, $priceDisplayPrecision)}
					{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(true, $smarty.const.NULL)}
				{/if}
				{$rightPrice = {convertPrice price=$productPrice}}
				<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
					{if $priceDisplay >= 0 && $priceDisplay <= 2}
						    {if $currencyFormat == 1 || $currencyFormat == 3}
					        <meta itemprop="priceCurrency" content="{$currency->iso_code}" /> {$currencySign} <span itemprop="price">{$rightPrice|regex_replace:"/[\\{$currencySign}]/":""}</span>
					    {else}
					        <span itemprop="price">{$rightPrice|regex_replace:"/[\\{$currencySign}]/":""}</span> <meta itemprop="priceCurrency" content="{$currency->iso_code}" /> {$currencySign}
					    {/if}   
					    <!--{if $tax_enabled  && ((isset($display_tax_label) && $display_tax_label == 1) OR !isset($display_tax_label))}
					        {if $priceDisplay == 1}{l s='tax excl.'}{else}{l s='tax incl.'}{/if}
					    {/if}-->
					{/if}
				</span>
			</li>


			{if $product->width}
				<li>{l s='Product Width' mod='psrichsnippets'}: <span itemprop="width">{$product->width|number_format:2} {$ps_dimension_unit}</span></li>
			{/if}
			{if $product->height}
				<li>{l s='Product Height' mod='psrichsnippets'}: <span itemprop="height">{$product->height|number_format:2} {$ps_dimension_unit}</span></li>
			{/if}
			{if $product->depth}
				<li>{l s='Product Depth' mod='psrichsnippets'}: <span itemprop="depth">{$product->depth|number_format:2} {$ps_dimension_unit}</span></li>
			{/if}
			{if $product->weight}
				<li>{l s='Product Weight' mod='psrichsnippets'}: <span itemprop="weight">{$product->weight|number_format:2} {$ps_weight_unit}</span></li>
			{/if}
			{if isset($averageTotal) && $averageTotal}
				<li>
					<span itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
						{l s='Rating' mod='psrichsnippets'}: <span itemprop="ratingValue">{$averageTotal}</span>/<span itemprop="bestRating">5</span> {l s='based on' mod='psrichsnippets'} <span itemprop="ratingCount">{$nbComments}</span> {l s='user ratings' mod='psrichsnippets'}
					</span>
				</li>
			{/if}
			<!-- Stock -->
			{*if $product->quantity}
				<li>{l s='Availability' mod='psrichsnippets'}: <link itemprop="availability" href="http://schema.org/InStock" />{l s='In stock' mod='psrichsnippets'}</li>
			{/if*}
  
			<li>{l s='Description' mod='psrichsnippets'}: <span itemprop="description">{$product->description_short|strip_tags}</span></li>


				
		</ul>

	</div>
	

</div>
{/if}
	<!-- breadcrumbs -->
	{if isset($psrichsnippets_path) AND $psrichsnippets_path}
		<div style="display:none">
			{l s='Path' mod='psrichsnippets'}:
		
		    <span itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="{$base_dir}" itemprop="url" title="{l s='Return to Home'}"><span itemprop="title">{l s='Home'}</span></a></span>
		    
	        <span class="navigation-pipe" {if isset($category) && isset($category->id_category) && $category->id_category == 1}style="display:none;"{/if}>{$navigationPipe|escape:html:'UTF-8'}</span>
	        {if !$psrichsnippets_path|strpos:'span'}
	            <span itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
	                <span itemprop="title">{$psrichsnippets_path}</span>
	            </span>
	        {else}
	            {$psrichsnippets_path}
	        {/if}


		</div>
    {/if}
<div class="clear"></div>