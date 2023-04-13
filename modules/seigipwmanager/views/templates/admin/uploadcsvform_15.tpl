<fieldset style="margin-top: 10px">
	<legend>{l s="Dodawanie pliku .csv" mod="seigipwmanager"} :</legend>
	<form method="post" enctype="multipart/form-data">
		<input type="hidden" name="seigipwman_id" value="{$id}">
		<label></label>
		<table class="table">
			<tr>
				<td>
					<label>
						{l s="Wybierz plik .csv do wgrania na serwer." mod="seigipwmanager"}
						</br>
						{l s="Uwaga! Można przesłać tylko 1 plik .csv, każdy kolejny przesłany plik nadpisze poprzedni." mod="seigipwmanager"}
						</br>
						{l s="Poprawne separatory to: przecinek (,) - dla pól oraz cudzysłów (\") - dla tekstu" mod="seigipwmanager"}
					</label>
				</td>
				<td>
					<input id="seigipwman_upload_csv" type="file" name="seigipwman_csv" onchange="checkFileExt(this)" accept=".csv">
				</td>
			</tr>
		</table>
		<input class="btn btn-default button" type="submit" name="seigipwman_csv_upload" value="{l s="Zapisz" mod="seigipwmanager"}">
	</form>
</fieldset>
<script>
	function checkFileExt(file){
		var ext = '.csv';
		var fileExt = file.value;
		fileExt = fileExt.substring(fileExt.lastIndexOf('.'));
		if(ext != fileExt){
			alert({l s='Nieprawidłowy format pliku. Akceptujemy jedynie pliki .csv' mod="seigipwmanager"});
			return false;
		}else{
			return true;
		}
	}
</script>