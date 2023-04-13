{if strlen($error)}
	<div class='alert alert-danger'>
		{$error}
	</div>
{/if}

{if $product_list === false}<div class="alert alert-info"> Użyj wyszukiwarki aby parować produkty! </div> {/if}
{if $product_list === []}<div class="alert alert-warning"> Brak wyników</div> {/if}

{if !empty($product_list)}
{if $is15}
	<fieldset style="margin-top: 10px">
		<legend class="panel-heading">{l s="Wynik wyszukiwania" mod="seigixsell"}</legend>
		{else}
	<div class="panel">
		<div class="panel-heading">{l s="Wynik wyszukiwania" mod="seigixsell"}</div>
		{/if}
		<label>Zaznacz produkty, które mają być powiązane ze sobą</label>
		<form method="post">
			<table class="table">
				<thead>
					<th>
						<input type="checkbox" id="addaccessory_check_a"> {l s="A" mod="seigixsell"}
					</th>
					<th>
						<input type="checkbox" id="addaccessory_check_b"> {l s="B" mod="seigixsell"}
					</th>
					<th>
						{l s="ID" mod="seigixsell"}
					</th>
					<th>
						{l s="Ref" mod="seigixsell"}
					</th>
					<th>
						{l s="Nazwa produktu" mod="seigixsell"}
					</th>
				</thead>
				<tbody>
					{foreach $product_list as $product}
						<tr>
							<td>
								<input class="addaccessory_box_a" type="checkbox" name="products_to_associate[a][]" value='{$product['id_product']}'>
							</td>
							<td>
								<input class="addaccessory_box_b" type="checkbox" name="products_to_associate[b][]" value='{$product['id_product']}'>
							</td>
							<td>
								{$product['reference']}
							</td>
							<td>
								{$product['id_product']}
							</td>
							<td>
								{$product['thumbnail']}
								{$product['name']}
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
			<label>{l s="Wybierz grupę/typ relacji w ramach jakiej nastąpi powiązanie:" mod="seigixsell"}</label><br>
			{foreach $group_list as $grl}
				<label><input name="grouplist[]" type="checkbox" value="{$grl.id}"> {$grl.name}</label><br>
			{/foreach}
			<label>{l s="Wybierz metodę przypisywania produktów:" mod="seigixsell"}</label><br>
			<input class='button btn btn-default' type='submit' name='seigixsell_associate_both' value='{l s="Obustronnie" mod="seigixsell"}'>
			<input class='button btn btn-default' type='submit' name='seigixsell_associate_a_to_b' value='{l s="A do B" mod="seigixsell"}'>
			<input class='button btn btn-default' type='submit' name='seigixsell_associate_b_to_a' value='{l s="B do A" mod="seigixsell"}'>
			<br><small>{l s="Obustronnie: Wszystkie produkty z obu zbiorów zostaną przypisane wzajemnie do siebie" mod="seigixsell"}</small>
			<br><small>{l s="A do B: Produkty ze zbioru A zostaną przypisane do wszystkich produktów ze zbioru B jako akcesoria" mod="seigixsell"}</small>
			<br><small>{l s="B do A: Produkty ze zbioru B zostaną przypisane do wszystkich produktów ze zbioru A jako akcesoria" mod="seigixsell"}</small>
		</form>
	</div>
{/if}
{if !empty($info)}
		{if $is15}
			<fieldset style="margin-top: 10px">
			<legend>{l s="Wynik powiązania" mod="seigixsell"}</legend>
		{else}
	<div class='panel'>
		<div class='panel-heading'>{l s="Wynik powiązania" mod="seigixsell"}</div>
		{/if}
		<p>
			{foreach $info as $i}
				{$i}
			{/foreach}
		</p>
	{if $is15}
	</fieldset>
	{else}
	</div>
	{/if}
{/if}

<script>
	$("#addaccessory_check_a").click(function(){
		$('input:checkbox.addaccessory_box_a').not(this).prop('checked', this.checked);
	});
	$("#addaccessory_check_b").click(function(){
		$('input:checkbox.addaccessory_box_b').not(this).prop('checked', this.checked);
	});
</script>