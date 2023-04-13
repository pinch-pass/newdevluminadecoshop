<!-- Created by michalz on 13.03.14 -->

{capture name=path}{l s='Pay with Przelewy24' mod='przelewy24'}{/capture}

<div class="box" style="overflow: auto;">
	<h2><a href="http://przelewy24.pl" target="_blank"><img src="{$modules_dir}przelewy24/img/logo.png" alt="{l s='Pay with Przelewy24' mod='przelewy24'}"/></a>&nbsp;{l s='Congratulation!' mod='przelewy24'}</h2>
	<p style="margin-bottom:1em;">{l s='Thank you for your purchase. Your payment was confirmed by Przelewy24. You can track your order in history of orders.' mod='przelewy24'}</p>
	<p class="cart_navigation">
		<a href="{$base_dir_ssl}index.php" class="{if $smarty.const._PS_VERSION_ >= 1.6 }button-exclusive btn btn-default{else}button_large{/if}">
			<span>
				<i class="icon-chevron-left"></i>
				{l s='Return to shop' mod='przelewy24'}
			</span>
		</a>
		<a class="{if $smarty.const._PS_VERSION_ >= 1.6 }button btn btn-default button-medium{else}exclusive_large{/if}" href="{$base_dir_ssl}index.php?controller=history">
            <span>
                {l s='Show order history' mod='przelewy24'}
            </span>
		</a>
	</p>
</div>

{$HOOK_ORDER_CONFIRMATION}
<script>
	window.setTimeout(function(){
		if (opener && opener.P24_Transaction) {
			opener.P24_Transaction.threeDSReturn(window);
			window.close();
		}
	},1000);
</script>

{if $ga_key}{include file="./_ga.tpl" ga_key=$ga_key ga_conversion=$ga_conversion}{/if}