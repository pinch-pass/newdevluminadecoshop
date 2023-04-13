{$notIn=$notIn|default:array()}
{if !empty($bank_name) && !in_array($bank_id, $notIn)}
<li>
	<input type="radio" class="bank-box" data-id="{$bank_id}" id="paymethod-bank-id-{$bank_id}" name="paymethod-bank">
	<label for="paymethod-bank-id-{$bank_id}" style="font-weight:normal;position:relative; top:-3px;">
		{$bank_name}
	</label>
</li>
{/if}
