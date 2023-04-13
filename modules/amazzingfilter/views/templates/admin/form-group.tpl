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

{if empty($group_class)}{$group_class = 'form-group'}{/if}
{if empty($label_class)}{$label_class = 'control-label col-lg-3'}{/if}
{if !empty($field.label_class)}{$label_class = $label_class|cat:' '|cat:$field.label_class}{/if}
{if empty($input_wrapper_class)}{$input_wrapper_class = 'col-lg-3'}{/if}

{if !empty($field.subtitle) && !isset($locked)}
    <div class="clearfix in-the-middle"></div>
    <h3 class="in-the-middle">{$field.subtitle|escape:'html':'UTF-8'}</h3>
{/if}

<div class="{$group_class|escape:'html':'UTF-8'}{if !empty($field.class)} {$field.class|escape:'html':'UTF-8'}{/if}{if !empty($field.blocked)} blocked{/if}{if !empty($field.related_options)} has-related-options" data-related="{$field.related_options|escape:'html':'UTF-8'}{/if}">
    <label class="{$label_class|escape:'html':'UTF-8'}{if $field.type == 'checkbox'} checkbox-label{/if}">
        {if $field.type == 'checkbox'}<input type="checkbox" name="{$name|escape:'html':'UTF-8'}" value="1"{if !empty($field.value)} checked{/if}>{/if}
        <span{if !empty($field.tooltip)} class="label-tooltip" data-toggle="tooltip" title="{$field.tooltip|escape:'html':'UTF-8'}"{/if}>
            {$field.display_name|escape:'html':'UTF-8'}
        </span>
    </label>
    <div class="{$input_wrapper_class|escape:'html':'UTF-8'}{if isset($locked)} {if !$locked}un{/if}locked{/if}">
        {if isset($locked)}
            <div class="lock-overlay"><span class="txt">{l s='Inherit' mod='amazzingfilter'}</span></div>
            <a href="#" class="lock-toggle">
                <input type="checkbox" name="unlocked_{$name|escape:'html':'UTF-8'}" value="1"class="hidden"{if !$locked} checked{/if}>
                <i class="icon-lock"></i><i class="icon-unlock-alt"></i>
            </a>
        {/if}
        {if empty($field.multilang)}
            {include file="./input.tpl" value = $field.value name = $name}
        {else}
            <div class="multilang-wrapper">
                {foreach $available_languages as $id_lang => $iso_code}
                    {$l_value = ''}{if (!is_array($field.value))}{$l_value = $field.value}
                    {else if isset($field.value.$id_lang)}{$l_value = $field.value.$id_lang}{/if}
                    <div class="multilang lang-{$id_lang|intval}{if $id_lang != $id_lang_current} hidden{/if}" data-lang="{$id_lang|intval}">
                        {$l_name = $name|cat:'['|cat:$id_lang|cat:']'}
                        {include file="./input.tpl" value = $l_value name = $l_name}
                    </div>
                {/foreach}
                <div class="multilang-selector">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        {foreach $available_languages as $id_lang => $iso_code}
                            <span class="multilang lang-{$id_lang|intval}{if $id_lang != $id_lang_current} hidden{/if}">{$iso_code|escape:'html':'UTF-8'}</span>
                        {/foreach}
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        {foreach $available_languages as $id_lang => $iso_code}
                            <li><a href="#" class="selectLanguage" data-lang="{$id_lang|intval}">{$iso_code|escape:'html':'UTF-8'}</a></li>
                        {/foreach}
                    </ul>
                </div>
            </div>
        {/if}
    </div>
    {if !empty($field.warning) && !isset($locked)}
        <span class="field-note"><i class="icon-exclamation-circle"></i> {$field.warning|escape:'html':'UTF-8'}</span>
    {/if}
    {if !empty($field.info) && !isset($locked)}
        <span class="field-info">{$field.info|escape:'html':'UTF-8'}</span>
    {/if}
</div>
{* since 3.0.0 *}
