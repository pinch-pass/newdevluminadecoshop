<script>
JXC.config =  JSON.parse('{{$CONFIG}}');
JXC.resultLink = '{{$linkToGen}}';
</script>
{* {{debug}} *}
<div class="panel">
	<h3><i class="icon icon-credit-card"></i> {l s='Экспорт товаров в XML' mod='jsox_xmlexport'}</h3>

	<p>
		{include file="{{$tpl_dir}}form.tpl"}
		<label for="resultLink">Ссылка на генерацию XML</label><input placeholder="Добавьте или выберите пресет XML" id="resultLink" type="text" disabled value="">
		<p style="padding: 20px; display: flex; justify-content: space-between;">
		<button id="updateConfig" class="btn btn-danger">Обновить</button>
		<button id="removeConfig" style="display:none;" class="btn btn-warning">Удалить пресет (необратимо)</button>
		</p>
	</p>
</div>

