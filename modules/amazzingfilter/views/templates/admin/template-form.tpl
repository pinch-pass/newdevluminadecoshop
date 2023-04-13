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

{$full = isset($template_filters) && isset($template_controller_settings)}

<div class="af_template{if isset($template_filters)} open{/if}" data-id="{$t.id_template|intval}" data-controller="{$t.template_controller|escape:'html':'UTF-8'}">
	<form class="template-form form-horizontal">
	<div class="template_header clearfix">
		<div class="template-name">
			<h4 class="list-view inline-block">{$t.template_name|escape:'html':'UTF-8'}</h4>
			<div class="open-view">
				<input type="text" name="template_name" value="{$t.template_name|escape:'html':'UTF-8'}">
			</div>
		</div>
		<div class="template-controller hidden"> {* temporarily hidden. May be used in next versions *}
			<div class="inline-block open-view">
				<label class="inline-block">{l s='Displayed on' mod='amazzingfilter'}</label>
				<div class="inline-block">
					<select name="template_controller">
					{foreach $controller_options as $value => $display_name}
						<option value="{$value|escape:'html':'UTF-8'}"{if $value == $t.template_controller} selected{/if}>{$display_name|escape:'html':'UTF-8'}</option>
					{/foreach}
					</select>
				</div>
			</div>
		</div>
		<div class="template-actions pull-right">
			<a class="activateTemplate list-action-enable action-{if $t.active == 1}enabled{else}disabled{/if}" href="#" title="{l s='Activate' mod='amazzingfilter'}">
				<i class="icon-check"></i>
				<i class="icon-remove"></i>
				<input type="hidden" name="active" value="{$t.active|intval}">
			</a>
			<div class="btn-group pull-right">
				<a href="#" title="{l s='Edit' mod='amazzingfilter'}" class="editTemplate btn btn-default">
					<i class="icon icon-pencil"></i> {l s='Edit' mod='amazzingfilter'}
				</a>
				<a href="#" title="{l s='Scroll Up' mod='amazzingfilter'}" class="scrollUp btn btn-default">
					<i class="icon icon-minus"></i> {l s='Scroll Up' mod='amazzingfilter'}
				</a>
				{if $additional_actions}
				<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					<i class="icon-caret-down"></i>
				</button>
				<ul class="dropdown-menu">
					<li><a href="#" class="template-action" data-action="Duplicate">
						<i class="icon icon-copy"></i> {l s='Dduplicate' mod='amazzingfilter'}
					</a></li>
					<li><a chref="#" class="template-action" data-action="Delete">
						<i class="icon icon-trash"></i> {l s='Delete' mod='amazzingfilter'}
					</a></li>
				</ul>
				{/if}
			</div>
		</div>
	</div>
	<div class="template_settings clearfix" style="display:none;">
		{if $full}
		<div class="controller-settings clearfix">
			{foreach $template_controller_settings as $name => $field}
				{include file="./form-group.tpl"
					group_class = 'basic-item'
					label_class = 'basic-label'
					input_wrapper_class = 'basic-input'
				}
			{/foreach}
		</div>

		<a href="#filters" class="template-tab-option first active">
		{l s='Filters' mod='amazzingfilter'}</a><a href="#additionalsettings" class="template-tab-option last">
		{l s='Additional settings' mod='amazzingfilter'} <span class="additional-settings-count">{$additional_settings|count}</span></a>

		<div id="filters" class="template-tab-content clearfix first active">
			<div class="f-list sortable">
				{foreach $template_filters as $key => $filter}
					{if !empty($filter)}{include file="./filter-form.tpl"}{/if}
				{/foreach}
			</div>
			<a href="#" class="addNewFilter" data-toggle="modal" data-target="#dynamic-popup">
				<i class="icon-plus"></i> {l s='add new' mod='amazzingfilter'}
			</a>
		</div>
		<div id="additionalsettings" class="template-tab-content clearfix">
			{foreach $general_settings_fields as $name => $field}
				{$locked = !isset($additional_settings[$name])}
				{if !$locked}{$field['value'] = $additional_settings[$name]}{/if}
				{include file="./form-group.tpl"
					name = 'additional_settings['|cat:$name|cat:']'
					group_class = 'settings-item'
					label_class = 'settings-label'
					input_wrapper_class = 'settings-input'
					locked = $locked
				}
			{/foreach}
		</div>
		<div class="tempate-footer clear-both">
			<input type="hidden" name="id_template" value="{$t.id_template|intval}">
			<button type="button" name="saveTemplate" class="saveTemplate btn btn-default">
				<i class="process-icon-save"></i>
				{l s='Save template' mod='amazzingfilter'}
			</button>
		</div>
		{/if}
	</div>
	</form>
</div>
{* since 2.8.2 *}
