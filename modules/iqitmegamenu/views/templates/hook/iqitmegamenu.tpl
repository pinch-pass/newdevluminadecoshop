{*
	* 2007-2014 PrestaShop
	*
	* NOTICE OF LICENSE
	*
	* This source file is subject to the Academic Free License (AFL 3.0)
	* that is bundled with this package in the file LICENSE.txt.
	* It is also available through the world-wide-web at this URL:
	* http://opensource.org/licenses/afl-3.0.php
	* If you did not receive a copy of the license and are unable to
	* obtain it through the world-wide-web, please send an email
	* to license@prestashop.com so we can send you a copy immediately.
	*
	* DISCLAIMER
	*
	* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
	* versions in the future. If you wish to customize PrestaShop for your
	* needs please refer to http://www.prestashop.com for more information.
	*
	*  @author    PrestaShop SA <contact@prestashop.com>
	*  @copyright 2007-2014 PrestaShop SA
	*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
	*  International Registered Trademark & Property of PrestaShop SA
	*}



	<div  class="iqitmegamenu-wrapper cbp-hor-width-{$menu_settings.hor_width}  clearfix">
		<div id="iqitmegamenu-horizontal{if isset($menu_settings_v) && ($menu_settings_v.ver_position == 6)}-sidebar{/if}" class="col-xs-12 iqitmegamenu {if $menu_settings.hor_s_transparent && $menu_settings.hor_sticky} cbp-sticky-transparent{/if}" role="navigation">
			<div class="container">
				
				{if isset($menu_settings_v) && ($menu_settings_v.ver_position==2 || $menu_settings_v.ver_position==3) }

					<div class="cbp-vertical-on-top {if $menu_settings_v.ver_position==2}cbp-homepage-expanded{/if}">
						{include file="./iqitmegamenu_vertical.tpl" ontop=1}  
					</div>
				{/if}

				{if isset($menu_settings_v) && ($menu_settings_v.ver_position == 6)}
					{include file="./iqitmegamenu_vertical.tpl" notitle=1} 
					{hook h='displayAfterIqitMegamenu'}
				{else} 
				{hook h='displayAfterIqitMegamenu'}
				<nav id="cbp-hrmenu" class="cbp-hrmenu cbp-horizontal {if $menu_settings.hor_width || $menu_settings.hor_sw_width}cbp-hrsub-narrow{/if} {if !$menu_settings.hor_sw_width}cbp-hrsub-wide{/if} {if $menu_settings.hor_animation==1}cbp-fade{/if} {if $menu_settings.hor_animation==2}cbp-fade-slide-bottom{/if} {if $menu_settings.hor_animation==3}cbp-fade-slide-top{/if} {if $menu_settings.hor_s_arrow}cbp-arrowed{/if} {if !$menu_settings.hor_arrow} cbp-submenu-notarrowed{/if} {if !$menu_settings.hor_arrow} cbp-submenu-notarrowed{/if} {if $menu_settings.hor_center} cbp-menu-centered{/if} ">
					<ul>
						<li class="li-logo"> <a href="/"> <img src="/img/logo_header.svg" alt="Dami Domo"> </a></li>
						{foreach $horizontal_menu as $tab}
						<li class="cbp-hrmenu-tab cbp-hrmenu-tab-{$tab.id_tab} {if $tab.active_label} cbp-onlyicon{/if}{if $tab.float} pull-right cbp-pulled-right{/if}">
	{if $tab.url_type == 2}<a role="button" class="cbp-empty-mlink">{else}<a href="{$tab.url}" {if $tab.new_window}target="_blank"{/if}>{/if}
								

								<span class="cbp-tab-title">{if $tab.icon_type && !empty($tab.icon_class)} <i class="{$tab.icon_class} cbp-mainlink-icon"></i>{/if}

								{*{if !$tab.icon_type && !empty($tab.icon)} <img src="{$tab.icon}" alt="{$tab.title}" class="cbp-mainlink-iicon" />{/if}*}
									{if !$tab.active_label}{$tab.title}{/if}{if $tab.submenu_type} <i class="icon-angle-down cbp-submenu-aindicator"></i>{/if}</span>
								{if !empty($tab.label)}<span class="label cbp-legend cbp-legend-main">{if !empty($tab.legend_icon)} <i class="{$tab.legend_icon} cbp-legend-icon"></i>{/if} {$tab.label}
								<span class="cbp-legend-arrow"></span></span>{/if}
							{if $tab.submenu_type && !empty($tab.submenu_content)}<span class="cbp-triangle-container"><span class="cbp-triangle-top"></span><span class="cbp-triangle-top-back"></span></span>{/if}
						</a>
							{if $tab.submenu_type && !empty($tab.submenu_content)}
							<div class="cbp-hrsub col-xs-{$tab.submenu_width}">
								<div class="cbp-hrsub-inner">
									{if $menu_settings.hor_s_width && !$menu_settings.hor_width && !$menu_settings.hor_sw_width}<div class="container">{/if}
									{if $tab.submenu_type==1}
									<div class="container-xs-height cbp-tabs-container">
									<div class="row row-xs-height">
									<div class="col-xs-2 col-xs-height">
										<ul class="cbp-hrsub-tabs-names cbp-tabs-names">
											{if isset($tab.submenu_content_tabs)}
											{foreach $tab.submenu_content_tabs as $innertab name=innertabsnames}
											<li class="innertab-{$innertab->id} {if $smarty.foreach.innertabsnames.first}active{/if}">
												<a href="#{$innertab->id}-innertab-{$tab.id_tab}" {if $innertab->url_type != 2} data-link="{$innertab->url}" {/if}>
												{if $innertab->icon_type && !empty($innertab->icon_class)} <i class="{$innertab->icon_class} cbp-mainlink-icon"></i>{/if}
												{if !$innertab->icon_type && !empty($innertab->icon)} <img src="{$innertab->icon}" alt="{$innertab->title}" class="cbp-mainlink-iicon" />{/if}
												{$innertab->title} 
												{if !empty($innertab->label)}<span class="label cbp-legend cbp-legend-inner">{if !empty($innertab->legend_icon)} <i class="{$innertab->legend_icon} cbp-legend-icon"></i>{/if} {$innertab->label}
												<span class="cbp-legend-arrow"></span></span>{/if}
											</a><i class="icon-angle-right cbp-submenu-it-indicator"></i><span class="cbp-inner-border-hider"></span></li>
											{/foreach}
											{/if}
										</ul>	
									</div>
								
												{if isset($tab.submenu_content_tabs)}
											{foreach $tab.submenu_content_tabs as $innertab name=innertabscontent}
											<div role="tabpanel" class="col-xs-10 col-xs-height tab-pane cbp-tab-pane {if $smarty.foreach.innertabscontent.first}active{/if} innertabcontent-{$innertab->id}"  id="{$innertab->id}-innertab-{$tab.id_tab}">

												{if !empty($innertab->submenu_content)}
												<div class="clearfix">
												{foreach $innertab->submenu_content as $element}
												{include file="./front_submenu_content.tpl" node=$element}               
												{/foreach}
												</div>
												{/if}

											</div>
											{/foreach}
											{/if}
									</div></div>
									{else}

										{if !empty($tab.submenu_content)}
											{foreach $tab.submenu_content as $element}
											{include file="./front_submenu_content.tpl" node=$element}               
											{/foreach}
										{/if}

									{/if}
									{if $menu_settings.hor_s_width && !$menu_settings.hor_width && !$menu_settings.hor_sw_width}</div>{/if}
								</div>
							</div>
							{/if}
						</li>
						{/foreach}
					</ul>
				</nav>
				{/if}

				
				

				
			</div>
			<div id="iqitmegamenu-mobile">
				{*<div id="iqitmegamenu-shower" class="clearfix">
					<div class="container">
						<div class="iqitmegamenu-icon"> <i class="icon-reorder"></i>
						</div>
						<span>{l s='Menu' mod='iqitmegamenu'}</span>
					</div>
				</div>*}
				<div class="cbp-mobilesubmenu">
					<div class="container">
						<div id="iqitmegamenu-accordion">
							<ul class="{if $mobile_menu_style}iqitmegamenu-accordion{else}cbp-spmenu cbp-spmenu-vertical cbp-spmenu-left{/if}">
								{if !$mobile_menu_style}
									<li id="cbp-close-mobile"> <i class="icon-chevron-left"></i>
										{l s='Hide' mod='iqitmegamenu'}
									</li>
								{/if}
								{$mobile_menu}
							</ul>
							<div class="hide-me"></div>
						</div>
					</div>
				</div>
				{if !$mobile_menu_style}
					<div id="cbp-spmenu-overlay" class="cbp-spmenu-overlay"></div>
				{/if}
			</div>
		</div>
	</div>
