{*
* 2007-2020 Amazzing
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
*
*  @author    Amazzing <mail@amazzing.ru>
*  @copyright 2007-2020 Amazzing
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
*}

{*** main layout is coming after functions ***}
{function renderBoxes type = 'checkbox' label_type = 'checkbox' is_color = false values = [] foldered = false root = true}
	{if $values|count}
	<ul class="{if !$root}child-categories{/if}">
	{foreach $values as $value}
		{$v = $value.id}
		{$id = $t|cat:'-'|cat:$v}
		{if !empty($filter.special)}{$id = $k}{/if}
		{$count = 0}
		{$children = []}
		{if !empty($count_data.$id)}{$count = $count_data.$id}{/if}
		{if $check_for_children && !empty($filter.values[$v])}{$children = $filter.values[$v]}{/if}
		{$is_customer_filter = !empty($applied_customer_filters[$k][$v])}
		<li class="item-{$id|escape:'html':'UTF-8'}{if isset($value.class)} {$value.class|escape:'html':'UTF-8'}{/if}{if !empty($value.selected)} active{/if}{if !empty($children)} af-parent-category{/if}{if !$count} no-matches{/if}{if $is_customer_filter} has-customer-filter{/if}">
			<label for="{$id|escape:'html':'UTF-8'}"{if $is_color} title="{$value.name|escape:'html':'UTF-8'}"{/if} class="{if $is_customer_filter}customer-filter-label" data-id="{$id|escape:'html':'UTF-8'}{else}af-{$label_type|escape:'html':'UTF-8'}-label{/if}"{if isset($value.style)} style="{$value.style|escape:'html':'UTF-8'}"{/if}>
				{if !$is_customer_filter}
					<input type="{$type|escape:'html':'UTF-8'}" id="{$id|escape:'html':'UTF-8'}" class="af {$type|escape:'html':'UTF-8'}" name="{$filter.submit_name|escape:'html':'UTF-8'}" value="{$v|escape:'html':'UTF-8'}" data-url="{$value.link|escape:'html':'UTF-8'}"{if !empty($value.selected)} checked{/if}>
				{else}
					<a href="#" class="{$af_classes['icon-lock']|escape:'html':'UTF-8'}"></a><input type="hidden" id="{$id|escape:'html':'UTF-8'}" class="af {$type|escape:'html':'UTF-8'} customer-filter" name="{$filter.submit_name|escape:'html':'UTF-8'}" value="{$v|escape:'html':'UTF-8'}" data-name="{$filter.submit_name|escape:'html':'UTF-8'}" data-url="{$value.link|escape:'html':'UTF-8'}">
				{/if}
				<span class="name">{$value.name|escape:'html':'UTF-8'}</span>
				{if $hidden_inputs.count_data}<span class="count">{$count|intval}</span>{/if}
				{if !empty($children)}<a href="#" class="af-toggle-child"><span class="hidden-on-open">+</span><span class="visible-on-open">-</span></a>{/if}
			</label>
			{if !empty($children)}
				{renderBoxes type = $type label_type = $label_type is_color = $is_color values = $children foldered = $foldered root = false}
			{/if}
		</li>
	{/foreach}
	</ul>
	{/if}
{/function}

{function renderOptions values = [] nesting_prefix = ''}
	{foreach $values as $value}
		{$v = $value.id}
		{$id = $t|cat:'-'|cat:$v}
		{$count = 0}
		{if !empty($count_data.$id)}{$count = $count_data.$id}{/if}
		{$is_customer_filter = !empty($applied_customer_filters[$k][$v])}
		{if $count || !empty($value.selected) || $is_customer_filter || !$hidden_inputs.hide_zero_matches}
			<option id="{$id|escape:'html':'UTF-8'}" value="{$v|escape:'html':'UTF-8'}" data-url="{$value.link|escape:'html':'UTF-8'}"  data-text="{$value.name|escape:'html':'UTF-8'}" class="{if $is_customer_filter}customer-filter {/if}{if !$count}no-matches{/if}"{if !empty($value.selected)} selected{/if}{if !$count && $hidden_inputs.dim_zero_matches} disabled{/if}>
				{if $nesting_prefix}{$nesting_prefix|escape:'html':'UTF-8'}{/if}
				{$value.name|escape:'html':'UTF-8'}
				{if $hidden_inputs.count_data && $count}({$count|intval}){/if}
			</option>
			{if $check_for_children && !empty($filter.values[$v])}
				{renderOptions values = $filter.values[$v] nesting_prefix = $nesting_prefix|cat:'-'}
			{/if}
		{/if}
	{/foreach}
{/function}

{function renderAvailableOptions values = [] nesting_prefix = ''}
	{foreach $values as $value}
		{$v = $value.id}
		{$id = $t|cat:'-'|cat:$v}
		<span class="{if !empty($applied_customer_filters[$k][$v])}customer-filter{/if}" data-value="{$v|escape:'html':'UTF-8'}" data-url="{$value.link|escape:'html':'UTF-8'}" data-text="{if $nesting_prefix}{$nesting_prefix|escape:'html':'UTF-8'} {/if}{$value.name|escape:'html':'UTF-8'}" data-id="{$id|escape:'html':'UTF-8'}"></span>
		{if $check_for_children && !empty($filter.values[$v])}
			{renderAvailableOptions values = $filter.values[$v] nesting_prefix = $nesting_prefix|cat:'-'}
		{/if}
	{/foreach}
{/function}

<div id="amazzing_filter" class="af block {$af_layout_type|escape:'html':'UTF-8'}-layout {$hook_name|escape:'html':'UTF-8'}{if !$hidden_inputs.count_data} hide-counters{/if}{if $hidden_inputs.hide_zero_matches} hide-zero-matches{/if}{if $hidden_inputs.dim_zero_matches} dim-zero-matches{/if}{if $hidden_inputs.compact_offset == 1} compact-offset-left{/if}"{if !$filters} style="display:none"{/if}>
	{if $hook_name != 'displayHome'}
		<h2 class="title_block">
			{if $current_controller == 'index'}{l s='Instant filter' mod='amazzingfilter'}{else}{l s='Filter by' mod='amazzingfilter'}{/if}
		</h2>
	{/if}
	<div class="block_content">
		{if $hook_name != 'displayHome'}
		<div class="selectedFilters clearfix hidden{if $af_layout_type == 'horizontal'} inline{/if}">
			{if $hidden_inputs.sf_position == 1}<span class="selected-filters-label">{l s='Filters:' mod='amazzingfilter'}</span>{/if}
			<div class="clearAll">
				<a href="#" class="all">
					<span class="txt">{l s='Clear' mod='amazzingfilter'}</span>
					<i class="{$af_classes['icon-eraser']|escape:'html':'UTF-8'}"></i>
				</a>
			</div>
		</div>
		{/if}
		<form action="#" id="af_form">
			<span class="hidden_inputs">
				{foreach $hidden_inputs as $name => $value}
					<input type="hidden" id="af_{$name|escape:'html':'UTF-8'}" name="{$name|escape:'html':'UTF-8'}" value="{$value|escape:'html':'UTF-8'}">
				{/foreach}
			</span>
			{foreach $filters as $k => $filter}
			{if !empty($filter.values)}
			{$t = $k|truncate:1:'':true}
			{$check_for_children = !empty($filter.id_parent) && !empty($filter.values[$filter.id_parent])}
			<div class="af_filter {$k|escape:'html':'UTF-8'} clearfix{if $t == 'p' || $t == 'w'} range-filter{/if}{if $filter.type == 4} has-slider{else} type-{$filter.type|intval}{/if}{if !empty($filter.is_color_group)} color-group{else if !empty($filter.textbox)} tb{/if}{if !empty($filter.special)} special{/if}{if isset($filter.foldered)} folderable{if $filter.foldered} foldered{/if}{/if}{if !empty($filter.class)} {$filter.class|escape:'html':'UTF-8'}{/if}{if !empty($filter.minimized)} closed{/if}" data-trigger="{$k|escape:'html':'UTF-8'}" data-url="{$filter.link|escape:'html':'UTF-8'}">
				<div class="af_subtitle_heading{if !empty($filter.special)} hidden{/if}">
					<h5 class="af_subtitle">{$filter.name|escape:'html':'UTF-8'}</h5>
				</div>
				<div class="af_filter_content">
				{if !empty($filter.quick_search)}
					<div class="af-quick-search">
						<input type="text" class="qsInput" data-notrigger="1" {if $check_for_children} data-tree="1"{/if}>
						<div class="alert-warning qs-no-matches hidden">{l s='no matches' mod='amazzingfilter'}</div>
					</div>
				{/if}
				{if $filter.type == 1 || $filter.type == 2}
					{$values = $filter.values}
					{if $check_for_children}{$values = $filter.values[$filter.id_parent]}{/if}
					{$type = 'checkbox'}{if $filter.type == 2}{$type = 'radio'}{/if}
					{$is_color = !empty($filter.is_color_group)}
					{$label_type = $type}{if $is_color}{$label_type='color'}{else if !empty($filter.textbox)}{$label_type='textbox'}{/if}
					{renderBoxes type = $type label_type = $label_type is_color = $is_color values = $values foldered = !empty($filter.foldered)}
				{else if $filter.type == 3}
					{$values = $filter.values}
					{if $check_for_children}{$values = $filter.values[$filter.id_parent]}{/if}
					{if !empty($applied_customer_filters[$k])}
						{$customer_filter_id = current(array_keys($applied_customer_filters[$k]))}
						{$customer_filter_name = current($applied_customer_filters[$k])}
						<label class="customer-filter-label for-select" data-id="{$t|escape:'html':'UTF-8'}-{$customer_filter_id|escape:'html':'UTF-8'}">
							<a href="#" class="{$af_classes['icon-lock']|escape:'html':'UTF-8'}"></a>
							<span class="name">{$customer_filter_name|escape:'html':'UTF-8'}</span>
						</label>
						<div class="selector-with-customer-filter hidden">
					{/if}
					<select id="selector-{$k|escape:'html':'UTF-8'}" class="af-select form-control form-control-select" name="{$filter.submit_name|escape:'html':'UTF-8'}">
						<option value="0" class="first">{if !empty($filter.first_option)}{$filter.first_option|escape:'html':'UTF-8'}{else}--{/if}</option>
						{renderOptions values = $values}
					</select>
					<div class="dynamic-select-options hidden">
						{renderAvailableOptions values = $values}
					</div>
					{if !empty($applied_customer_filters[$k])}</div>{/if}
				{else if $filter.type == 4}
					<div class="{$k|escape:'html':'UTF-8'}_slider af_slider slider" data-url="{$filter.link|escape:'html':'UTF-8'}" data-type="{$k|escape:'html':'UTF-8'}">
						<div class="slider-bar">
							<input type="hidden" id="{$k|escape:'html':'UTF-8'}_slider" value="{$filter.values.from|floatval},{$filter.values.to|floatval}">
						</div>
						<div class="slider-values">
							<span class="from_display slider_value">
								<span class="prefix">{$filter.prefix|escape:'html':'UTF-8'}</span><span class="value">
								{$filter.values.from|floatval}</span><span class="suffix">{$filter.suffix|escape:'html':'UTF-8'}</span>
								<input type="text" id="{$k|escape:'html':'UTF-8'}_from" class="input-text" name="sliders[{$k|escape:'html':'UTF-8'}][from]" value="{$filter.values.from|floatval}" >
								<input type="hidden" id="{$k|escape:'html':'UTF-8'}_min" name="sliders[{$k|escape:'html':'UTF-8'}][min]" value="{$filter.values.min|floatval}" >
							</span>
							<span class="to_display slider_value">
								<span class="prefix">{$filter.prefix|escape:'html':'UTF-8'}</span><span class="value">
								{$filter.values.to|floatval}</span><span class="suffix">{$filter.suffix|escape:'html':'UTF-8'}</span>
								<input type="text" id="{$k|escape:'html':'UTF-8'}_to" class="input-text" name="sliders[{$k|escape:'html':'UTF-8'}][to]" value="{$filter.values.to|floatval}">
								<input type="hidden" id="{$k|escape:'html':'UTF-8'}_max" name="sliders[{$k|escape:'html':'UTF-8'}][max]" value="{$filter.values.max|floatval}">
							</span>
						</div>
					</div>
					{if !empty($numeric_slider_values[$k])}
						<input type="hidden" name="numeric_slider_values[{$k|escape:'html':'UTF-8'}]" value="{$numeric_slider_values[$k]|implode:','|escape:'html':'UTF-8'}">
					{/if}
				{/if}
				{if !empty($available_options[$k])}
					<input type="hidden" name="available_options[{$k|escape:'html':'UTF-8'}]" value="{$available_options[$k]|implode:','|escape:'html':'UTF-8'}">
				{/if}
				</div>
				{if $filter.type == 1 || $filter.type == 2}
					<a href="#" class="toggle-cut-off">
						<span class="more">{l s='more...' mod='amazzingfilter'}</span>
						<span class="less">{l s='less' mod='amazzingfilter'}</span>
					</a>
				{/if}
			</div>
			{/if}
			{/foreach}
		</form>
		{if $hook_name == 'displayHome'}
			<div class="btn-holder"><a href="#" class="submitFilter btn btn-default full-width">{l s='OK' mod='amazzingfilter'}</a></div>
		{/if}
		<div class="btn-holder">
			<a href="#" class="btn btn-primary full-width viewFilteredProducts{if $hidden_inputs.reload_action != 2} hidden{/if}">
				{l s='View products' mod='amazzingfilter'} <span class="af-total-count">{$total_products|intval}</span>
			</a>
			{if !empty($my_filters_link)}
				<a href="{$my_filters_link|escape:'html':'UTF-8'}" class="btn btn-default manage-permanent-filters full-width" target="_blank">
					{l s='Manage permanent filters' mod='amazzingfilter'}
				</a>
			{/if}
		</div>
	</div>
	<a href="#" class="btn-primary compact-toggle type-{$hidden_inputs.compact_btn|intval}">
		<span class="{$af_classes['icon-filter']|escape:'html':'UTF-8'} compact-toggle-icon"></span>
		<span class="compact-toggle-text">{l s='Filter' mod='amazzingfilter'}</span>
	</a>
</div>
<div class="af-compact-overlay"></div>
{* since 3.0.3 *}
