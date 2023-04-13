<section id="sendpulse_export" class="panel widget">
	<div class="panel-heading">
		<i class="icon-group"></i> {l s='All categories' mod='sendpulse'}
		<span class="panel-heading-action">
			<a class="list-toolbar-btn" href="#" onclick="exportAllToSendpulse($('#sendpulse-categories')); return false;" 
				title="{l s='Export all categories' mod='sendpulse'}">
				<i class="process-icon-export"></i>
			</a>
		</span>
	</div>
	{if $categories}
	<div class="table-responsive-row clearfix">
		<table id="sendpulse-categories" class="table">
			<thead>
				<tr class="nodrag nodrop">
					<th class="center">--</th>
					<th class="fixed-width-xs"><span class="title_box active">{l s="ID"}</span></th>
					<th><span class="title_box active">{l s="Name"}</span></th>
					<th class="center"><span class="title_box active">{l s="Count" mod='sendpulse'}</span></th>
				</tr>
			</thead>
			<tbody>
				{foreach $categories as $cat}
				<tr data-category-id="{$cat.id_group}" data-category-count="{$cat.count}" data-category-name="{$cat.name}">
					<td class="center">
						<input {if $cat.count==0}disabled{/if} class="category  {if $cat.count!=0}contacts_exist{/if}" type="checkbox" name="category[]" value="{$cat.id_group}">
					</td>
					<td>
						{$cat.id_group}
					</td>
					<td class="center1">{$cat.name}</td>
					<td class="center">{$cat.count}</td>
				
				</tr>
				{/foreach}
			</tbody>		
		</table>

		<div class="row">
			<div class="col-lg-6">
				<div class="btn-group bulk-actions dropup">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					{l s='Group actions' mod='sendpulse'} <span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<li>
							<a href="#" onclick="return checkAll($(this));">
							<i class="icon-check-sign"></i>&nbsp;{l s='Check All'}
							</a>
						</li>
						<li>
							<a href="#" onclick="return unCheckAll($(this));">
							<i class="icon-check-empty"></i>&nbsp;{l s='Uncheck All'}
							</a>
						</li>
						<li class="divider"></li>
						<li>
							<a href="#" onclick="return exportCheckedToSendpulse($(this))">
							<i class="icon-power-off text-success"></i>&nbsp;{l s='Export checked' mod='sendpulse'}
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>


	</div>
	{else}
		{l s='Categories not found' mod='sendpulse'}
	{/if}
</section>

{include file=$modal_path}




