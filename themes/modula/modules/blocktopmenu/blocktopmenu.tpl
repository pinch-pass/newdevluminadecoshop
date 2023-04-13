{if $MENU != ''}
	<!-- Menu -->
	<div id="block_top_menu" class="sf-contener clearfix col-lg-12">
		<div class="cat-title">{l s='Categories' mod='blocktopmenu'}</div>
		<ul class="sf-menu clearfix menu-content">
        
			{$MENU}
			<li class="menu-client-page"><a href="{$link->getCMSLink(6)}">{l s='STREFA KLIENTA' mod='blocktopmenu'}</a>
				<ul class="submenu-container clearfix first-in-line-xs">
					<li style="width: 21%;"><a href="{$link->getPageLink('contact')|escape:'html':'UTF-8'}">{l s='Formularz kontaktowy' mod='blocktopmenu'}</a></li>
					<li style="width: 18%;"><a href="{$link->getCMSLink(8)}">{l s='Sposoby płatności' mod='blocktopmenu'}</a></li>
					<li style="width: 10%;"><a href="{$link->getCMSLink(9)}">{l s='Dostawa' mod='blocktopmenu'}</a></li>
					<li style="width: 16%;"><a href="{$link->getCMSLink(10)}">{l s='Odbiór osobisty' mod='blocktopmenu'}</a></li>
					<li style="width: 17%;"><a href="{$link->getCMSLink(11)}">{l s='Formularz zwrotu' mod='blocktopmenu'}</a></li>
					<li style="width: 10%;"><a href="{$link->getCMSLink(12)}">{l s='Reklamacje' mod='blocktopmenu'}</a></li>
				</ul>
			</li>
			<li class="menu-bestsellers"><a href="{$link->getCategoryLink(21)|escape:'html':'UTF-8'}" style="color:#941114;">{l s='BESTSELLERY' mod='blocktopmenu'}</a></li>
			<li class="menu-news"><a href="{$link->getCategoryLink(22)|escape:'html':'UTF-8'}" style="color:#941114;">{l s='Nowości' mod='blocktopmenu'}</a></li>
			{if $MENU_SEARCH}
				<li class="sf-search noBack" style="float:right">
					<form id="searchbox" action="{$link->getPageLink('search')|escape:'html':'UTF-8'}" method="get">
						<p>
							<input type="hidden" name="controller" value="search" />
							<input type="hidden" value="position" name="orderby"/>
							<input type="hidden" value="desc" name="orderway"/>
							<input type="text" name="search_query" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query|escape:'html':'UTF-8'}{/if}" />
						</p>
					</form>
				</li>
			{/if}
		</ul>
	</div>
	<!--/ Menu -->
{/if}