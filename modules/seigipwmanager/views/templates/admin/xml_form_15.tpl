<fieldset style="margin-top: 10px">
	<legend>{l s="Przypisywanie pliku csv do xml" mod="seigipwmanager"} :</legend>
	<form method="post" enctype="multipart/form-data">
		<table class="table">
			<tr>
				<td>
					<label>
						{l s="Pliki xml" mod="seigipwmanager"}
					</label>
				</td>
				<td>
					{foreach $xml_list as $xml}
						<input type="checkbox" name="xml[]" value="{$xml['id_xml']}" {if in_array($xml['id_xml'], $xmls)} checked {/if}> {$xml['nazwa']}</br>
					{/foreach}
				</td>
			</tr>
		</table>
		<input class="btn btn-default button" type="submit" name="seigipwman_xml_save" value="{l s="Zapisz" mod="seigipwmanager"}">
	</form>
</fieldset>