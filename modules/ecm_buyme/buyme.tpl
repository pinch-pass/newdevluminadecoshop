{nocache}
<!-- Б -->
<input id="module_dir" name="module_dir" type="hidden" value ="{$module_template_dir}"/>
<input id="lang" name="lang" type="hidden" value ="{$lang_iso}"/>
<script language="JavaScript" src="{$module_template_dir}js/buyme.js" type="text/javascript"></script>
<div class="b1c-name" style="display: none">
{foreach  item=item from=$product_b1c name=b1c}
{$item|escape:'htmlall':'UTF-8'}{/foreach}</div>
{if $product_available && $product_available>0}
<input type="button" class="b1c hj-hj" value="{l s='Buyme one click' mod='ecm_buyme'}">{/if}
