<div class="panel">
	<div class="panel-heading">{l s="Test nadpisywania" mod="seigipwmanager"} :</div>
	<form method="post" enctype="multipart/form-data">
		<table class="table">
			<tr>
				<td>
					<label>
						{l s="Plik xml" mod="seigipwmanager"}
					</label>
				</td>
				<td>
					<select name="pwman_id_xml">
						{foreach $xml_list as $xml}
							<option value="{$xml['id_xml']}"> {$xml['nazwa']}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label>
						{l s="ID produktu" mod="seigipwmanager"}
					</label>
				</td>
				<td>
					<input type="text" name="pwman_product_id">
				</td>
			</tr>
		</table>
		<input class="btn btn-default button" type="submit" name="seigipwman_test" value="{l s="Testuj" mod="seigipwmanager"}">
	</form>
</div>
{if !empty($test_result)}
<div class="panel">
	<div class="panel-heading">{l s="Wynik testu" mod="seigipwmanager"} :</div>
	<div>
		{$test_result}
	</div>
</div>
{/if}