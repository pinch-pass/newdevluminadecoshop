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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture name=path}{l s='Our stores'}{/capture}

<h1 class="page-heading">
    {l s='Our stores'}
</h1>

{if $simplifiedStoresDiplay}
    {if $stores|@count}
        <div class="stores">
            <h1>Магазины</h1>

            <div class="stores__nav">
                <p class="stores__nav__link list"><img src="{$img_dir}store-list.svg" alt=""><img class="act" src="{$img_dir}store-list-hov.svg" alt=""><span>Cписком</span></p>
                <p class="stores__nav__link stores__nav__link--active map"><img src="{$img_dir}map.svg" alt=""><img class="act" src="{$img_dir}map-hov.svg" alt=""><span>На карте</span></p>
            </div>
            <div class="stores__map" id="map">

            </div>
            <div class="stores__list" style="display: none;">
                {assign var="i" value=0 }
                {foreach $stores as $store}
                    {if $i > 4}
                        {$i = 1 }
                    {else}
                        <span style="display:none;">{$i++}</span>
                    {/if}

                    <div class="stores__list-item">
                        <div class="stores__slide owl-carousel">
                            {assign var="store_img_1" value="img/st/{$store.id_store}_1.jpg"}
                            {assign var="store_img_2" value="img/st/{$store.id_store}_2.jpg"}
                            {assign var="store_img_3" value="img/st/{$store.id_store}_3.jpg"}
                            {assign var="store_img_4" value="img/st/{$store.id_store}_4.jpg"}
                            {assign var="store_img_5" value="img/st/{$store.id_store}_5.jpg"}
                            {assign var="store_img_6" value="img/st/{$store.id_store}_6.jpg"}
                            {assign var="store_img_7" value="img/st/{$store.id_store}_7.jpg"}

                            {if $store_img_1|file_exists}
                                <img src="{$img_store_dir}{$store.id_store}_1.jpg" alt="">
                            {/if}
                            {if $store_img_2|file_exists}
                                <img src="{$img_store_dir}{$store.id_store}_2.jpg" alt="">
                            {/if}
                            {if $store_img_3|file_exists}
                                <img src="{$img_store_dir}{$store.id_store}_3.jpg" alt="">
                            {/if}
                            {if $store_img_4|file_exists}
                                <img src="{$img_store_dir}{$store.id_store}_4.jpg" alt="">
                            {/if}
                            {if $store_img_5|file_exists}
                                <img src="{$img_store_dir}{$store.id_store}_5.jpg" alt="">
                            {/if}
                            {if $store_img_6|file_exists}
                                <img src="{$img_store_dir}{$store.id_store}_6.jpg" alt="">
                            {/if}
                            {if $store_img_7|file_exists}
                                <img src="{$img_store_dir}{$store.id_store}_7.jpg" alt="">
                            {/if}


                        </div>

                        <div class="stores__info">
                            <p class="stores__info-name"> <img src="{$img_dir}store-metro-{$i}.svg" alt=""><span>{$store.name}</span></p>
                            <p class="stores__info-adress">{$store.address1}</p>
                            <p class="stores__info-telephone">Телефон:  <a href="tel:+{$store.phone|regex_replace:"/[^0-9]/":""}">  {$store.phone}</a></p>
                            <p class="stores__info-worktimme">
                                Время работы:   <span>  {$store.working_hours}</p>
                            <div class="stores__info-services">

                                {if $store.option_1==1}
                                    <div class="tooltip">
                                        <p class="tooltiptext"><span class="ul-arrow"></span>Шаговая доступность от метро</p>
                                        <img src="{$img_dir}1.svg"  >
                                    </div>
                                {/if}

                                {if $store.option_2==1}
                                    <div class="tooltip">
                                        <p class="tooltiptext"><span class="ul-arrow"></span>Бесплатная парковка у входа</p>
                                        <img src="{$img_dir}2.svg" >
                                    </div>
                                {/if}
                                {if $store.option_3==1}
                                    <div class="tooltip">
                                        <p class="tooltiptext"><span class="ul-arrow"></span>Пандус для инвалидов</p>
                                        <img src="{$img_dir}3.svg" >
                                    </div>
                                {/if}
                                {if $store.option_4==1}
                                    <div class="tooltip">
                                        <p class="tooltiptext"><span class="ul-arrow"></span>Специалист по работе с дизайнерами</p>
                                        <img src="{$img_dir}4.svg" >
                                    </div>
                                {/if}
                                {if $store.option_5==1}
                                    <div class="tooltip">
                                        <p class="tooltiptext"><span class="ul-arrow"></span>Комфортная зона</p>
                                        <img src="{$img_dir}5.svg" >
                                    </div>
                                {/if}
                                {if $store.option_6==1}
                                    <div class="tooltip">
                                        <p class="tooltiptext"><span class="ul-arrow"></span>Игровая зона</p>
                                        <img src="{$img_dir}6.svg" >
                                    </div>
                                {/if}
                                {if $store.option_7==1}
                                    <div class="tooltip">
                                        <p class="tooltiptext"><span class="ul-arrow"></span>Профессиональная консультация</p>
                                        <img src="{$img_dir}7.svg" >
                                    </div>
                                {/if}

                            </div>
                            <div class="stores__info-buttons">
                                <a href="{$store.city}" class="more">Подробнее<img src="{$img_dir}store-arrow.svg" alt=""></a>
                                <a href="{$store.address2}" class="route"><img src="{$img_dir}placeholder.svg" alt=""><span>Построить маршрут</span></a>
                            </div>
                        </div>
                    </div>
                {/foreach}
            </div>

        </div>

    {/if}
{else}
    <div id="map"></div>
    <p class="store-title">
        {l s='Enter a location (e.g. zip/postal code, address, city or country) in order to find the nearest stores.'}
    </p>
    <div class="store-content">
        <div class="address-input">
            <label for="addressInput">{l s='Your location:'}</label>
            <input class="form-control grey" type="text" name="location" id="addressInput" value="{l s='Address, zip / postal code, city, state or country'}" />
        </div>
        <div class="radius-input">
            <label for="radiusSelect">{l s='Radius:'}</label>
            <select name="radius" id="radiusSelect" class="form-control">
                <option value="15">15</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <img src="{$img_ps_dir}loader.gif" class="middle" alt="" id="stores_loader" />
        </div>
        <div>
            <button style="float:left; clear:both; margin:20px 0;" name="search_locations" class="button btn btn-default button-small">
            	<span>
            		<i class="icon-search left"></i> {l s='Search'}
            	</span>
            </button>
        </div>
    </div>
    <div class="store-content-select selector3">
        <select id="locationSelect" class="form-control">
            <option>-</option>
        </select>
    </div>

    <table id="stores-table" class="table table-bordered">
        <thead>
        <tr>
            <th class="num">#</th>
            <th>{l s='Store'}</th>
            <th>{l s='Address'}</th>
            <th>{l s='Distance'}</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    {strip}
        {addJsDef map=''}
        {addJsDef markers=array()}
        {addJsDef infoWindow=''}
        {addJsDef locationSelect=''}
        {addJsDef defaultLat=$defaultLat}
        {addJsDef defaultLong=$defaultLong}
        {addJsDef hasStoreIcon=$hasStoreIcon}
        {addJsDef distance_unit=$distance_unit}
        {addJsDef img_store_dir=$img_store_dir}
        {addJsDef img_ps_dir=$img_ps_dir}
        {addJsDef searchUrl=$searchUrl}
        {addJsDef logo_store=$logo_store}
        {addJsDefL name=translation_1}{l s='No stores were found. Please try selecting a wider radius.' js=1}{/addJsDefL}
        {addJsDefL name=translation_2}{l s='store found -- see details:' js=1}{/addJsDefL}
        {addJsDefL name=translation_3}{l s='stores found -- view all results:' js=1}{/addJsDefL}
        {addJsDefL name=translation_4}{l s='Phone:' js=1}{/addJsDefL}
        {addJsDefL name=translation_5}{l s='Get directions' js=1}{/addJsDefL}
        {addJsDefL name=translation_6}{l s='Not found' js=1}{/addJsDefL}
    {/strip}
{/if}
