<fieldset style="margin-top: 10px">
	<legend>{l s="Test nadpisywania" mod="seigipwmanager"} :</legend>
	<form method="post" enctype="multipart/form-data">
		<table class="table">
			<tr>
				<td>
					<label>
						{l s="Plik xml" mod="seigipwmanager"}
					</label>
				</td>
				<td>
					<select name="id_xml">
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
					<input type="text" name="product_id">
				</td>
			</tr>
		</table>
		<input class="btn btn-default button" type="submit" name="seigipwman_test" value="{l s="Testuj" mod="seigipwmanager"}">
	</form>
</fieldset>
{if !empty($test_result)}
<fieldset style="margin-top: 10px">
	<legend>{l s="Wynik testu" mod="seigipwmanager"} :</legend>
	<div>
		{$test_result}
	</div>
</fieldset>
{/if}