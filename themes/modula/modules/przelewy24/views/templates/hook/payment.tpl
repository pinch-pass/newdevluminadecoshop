{foreach $p24_channels_list as $item}
	<div class="row">
		<div class="{if !empty($p24_gate_class)}{$p24_gate_class}{else}col-xs-12 col-md-12{/if}">
			<p class="payment_module p24-payment-module">
				<a href="{$item.url}"
					{if $p24_gate_logo == 0}
					style="background-image: url({$item.logo}); background-position: 1em center; background-repeat: no-repeat; background-size: 64px auto;"
					{/if}
				>
					{if $p24_gate_logo == 1}
					<img src="{$item.logo}" width="64">
					{/if}
					{l s='Pay with' mod='przelewy24'}&nbsp;{$item.name}{if $item.desc}&nbsp;<span>{$item.desc}</span>{/if}
				</a>
			</p>
		</div>
	</div>
{/foreach}

{if $p24_gate_logo == 0}
	<style>
	.p24-payment-module a {
		min-height: 25px;
		padding-left: 95px;
	}
	</style>
{/if}

{if $p24_chevron_right == 1}
	<style>
	.p24-payment-module a:after {
		display: block;
		content: "\f054";
		position: absolute;
		right: 15px;
		margin-top: -11px;
		top: 50%;
		font-family: "FontAwesome";
		font-size: 25px;
		height: 22px;
		width: 14px;
		color: #777;
	}
	</style>
{/if}
