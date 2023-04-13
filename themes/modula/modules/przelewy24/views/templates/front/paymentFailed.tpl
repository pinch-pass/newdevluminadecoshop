<!-- Created by michalz on 13.03.14 -->

{capture name=path}{l s='Pay with Przelewy24' mod='przelewy24'}{/capture}

<div class="box" style="overflow: auto;">
	<h2><a href="http://przelewy24.pl" target="_blank"><img src="{$modules_dir}przelewy24/img/logo.png" alt="{l s='Pay with Przelewy24' mod='przelewy24'}"/></a>&nbsp;{l s='Payment failed!' mod='przelewy24'}</h2>
	<p style="margin-bottom:1em;">{l s='Your payment was not confirmed by Przelewy24. Contact with your seller for more information.' mod='przelewy24'}</p>
	<p class="cart_navigation">
		<a href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}"
			class="{if $smarty.const._PS_VERSION_ >= 1.6 }button-exclusive btn btn-default{else}button_large{/if}">
			<span>
				<i class="icon-chevron-left"></i>{l s='Return to shop' mod='przelewy24'}
			</span>
		</a>
		<a style="margin-left: 15px;" class="{if $smarty.const._PS_VERSION_ >= 1.6 }button btn btn-default button-medium{else}exclusive_large{/if}" href="{$p24_retryPaymentUrl}">
			<span>
				{l s='Retry' mod='przelewy24'}
			</span>
		</a>
		<a class="{if $smarty.const._PS_VERSION_ >= 1.6 }button btn btn-default button-medium{else}exclusive_large{/if}" href="{$url_history}">
            <span>
                {l s='Show order history' mod='przelewy24'}
            </span>
		</a>
	</p>
</div>

<script>
	window.setTimeout(function(){
		opener.P24_Transaction.threeDSReturn(window);
		window.close();
	},1000);
</script>

{if $ga_key}{include file="./_ga.tpl" ga_key=$ga_key}{/if}