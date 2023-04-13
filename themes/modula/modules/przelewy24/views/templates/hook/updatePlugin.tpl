{if $p24_message}
<div style="display: none" id="{$p24_message_id}">{$p24_message}</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('#content').prepend($("#{$p24_message_id}").show());
	});
</script>
{/if}