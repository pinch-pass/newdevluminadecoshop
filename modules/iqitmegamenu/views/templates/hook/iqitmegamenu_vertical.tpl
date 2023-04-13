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

<nav id="cbp-hrmenu1" class="cbp-hrmenu  cbp-vertical  {if $menu_settings_v.ver_animation==1}cbp-fade{/if} {if $menu_settings_v.ver_s_arrow}cbp-arrowed{/if} {if !$menu_settings_v.ver_arrow} cbp-submenu-notarrowed{/if} {if !$menu_settings_v.ver_arrow} cbp-submenu-notarrowed{/if}  ">
	{if !isset($notitle)}<div class="cbp-vertical-title"><i class="icon icon-bars cbp-iconbars"></i>{l s='Navigation' mod='iqitmegamenu'}</div>{/if}
					<ul>
						{foreach $vertical_menu as $tab}
						<li class="cbp-hrmenu-tab cbp-hrmenu-tab-{$tab.id_tab} {if $tab.active_label} cbp-onlyicon{/if}{if $tab.float} pull-right cbp-pulled-right{/if}">
	{if $tab.url_type == 2}<a role="button" class="cbp-empty-mlink">{else}<a href="{$tab.url}" {if $tab.new_window}target="_blank"{/if}>{/if}
								{if $tab.icon_type && !empty($tab.icon_class)} <i class="{$tab.icon_class} cbp-mainlink-icon"></i>{/if}
								{if !$tab.icon_type && !empty($tab.icon)} <img src="{$tab.icon}" alt="{$tab.title}" class="cbp-mainlink-iicon" />{/if}
								{if !$tab.active_label}{$tab.title}{/if}{if $tab.submenu_type} <i class="icon-angle-right cbp-submenu-aindicator"></i>{/if}
								{if !empty($tab.label)}<span class="label cbp-legend cbp-legend-vertical cbp-legend-main">{if !empty($tab.legend_icon)} <i class="{$tab.legend_icon} cbp-legend-icon"></i>{/if} {$tab.label}
								<span class="cbp-legend-arrow"></span></span>{/if}
								{if $tab.submenu_type && !empty($tab.submenu_content)}<span class="cbp-triangle-container"><span class="cbp-triangle-left"></span><span class="cbp-triangle-left-back"></span></span>{/if}
						</a>
							{if $tab.submenu_type && !empty($tab.submenu_content)}
							<div class="cbp-hrsub-wrapper">
							<div class="cbp-hrsub col-xs-{$tab.submenu_width}">
								<div class="cbp-hrsub-inner">
							
									{if $tab.submenu_type==1}
									<div class="container-xs-height cbp-tabs-container">
									<div class="row row-xs-height">
									<div class="col-xs-2 col-xs-height">
										<ul class="cbp-hrsub-tabs-names cbp-tabs-names">
											{foreach $tab.submenu_content_tabs as $innertab name=innertabsnames}
											<li class="innertab-{$innertab->id} {if $smarty.foreach.innertabsnames.first}active{/if}"><a href="#{$innertab->id}-innertab-{$tab.id_tab}" {if $innertab->url_type != 2} data-link="{$innertab->url}" {/if}>
												{if $innertab->icon_type && !empty($innertab->icon_class)} <i class="{$innertab->icon_class} cbp-mainlink-icon"></i>{/if}
												{if !$innertab->icon_type && !empty($innertab->icon)} <img src="{$innertab->icon}" alt="{$innertab->title}" class="cbp-mainlink-iicon" />{/if}
												{$innertab->title} 
												{if !empty($innertab->label)}<span class="label cbp-legend cbp-legend-inner">{if !empty($innertab->legend_icon)} <i class="{$innertab->legend_icon} cbp-legend-icon"></i>{/if} {$innertab->label}
												<span class="cbp-legend-arrow"></span></span>{/if}
											</a><i class="icon-angle-right cbp-submenu-it-indicator"></i><span class="cbp-inner-border-hider"></span></li>
											{/foreach}
										</ul>	
									</div>
								

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
									
									</div></div>
									{else}

										{if !empty($tab.submenu_content)}
											{foreach $tab.submenu_content as $element}
											{include file="./front_submenu_content.tpl" node=$element}               
											{/foreach}
										{/if}

									{/if}
								
								</div>
							</div></div>
							{/if}
						</li>
						{/foreach}
					</ul>
				</nav>
