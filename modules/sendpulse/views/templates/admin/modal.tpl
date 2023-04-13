<div id="sendpulse_modal" class="modal fade">
	<div class="modal-dialog ">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">×</button>                        
				<h4 class="modal-title">
					{l s="SendPulse progress" mod='sendpulse'}
				</h4>
			</div>
			<div class="modal-body current_category">
				
			</div>
			<div class="logs">
			
			</div>
			<div class="modal-body" id="catalog_deactivate_all_progression">
				<div class="progress active progress-striped" style="display: block; width: 100%">
					<div class="progress-bar" role="progressbar" style="width: 0%">
						<span>0%</span>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="cancel_button btn btn-tertiary-outline btn-lg" data-dismiss="modal">{l s="Cancel"}</button>
				<button type="button" style="display: none;" class="close_modal btn btn-tertiary-outline btn-lg" data-dismiss="modal">{l s="Close"}</button>
			</div>
		</div>
	</div>
</div>

<script>
	var messages = {};
	messages['complete'] = "{l s="Complete" mod="sendpulse"}";
	messages['errors'] = "{l s="Errors" mod="sendpulse"}";	
	messages['records'] = "{l s="Records" mod="sendpulse"}";		
</script>




<style>
	.logs{
		position: relative;
		padding: 15px;
    }
</style>
