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
* @author PrestaShop SA
<contact@prestashop.com>
    * @copyright 2007-2014 PrestaShop SA
    * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
    * International Registered Trademark & Property of PrestaShop SA
    *}
    <!DOCTYPE HTML>
    <!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7 " lang="{$lang_iso}">
<![endif]-->
    <!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8 ie7" lang="{$lang_iso}">
<![endif]-->
    <!--[if IE 8]>
<html class="no-js lt-ie9 ie8" lang="{$lang_iso}">
<![endif]-->
    <!--[if gt IE 8]>
<html class="no-js ie9" lang="{$lang_iso}">
<![endif]-->
    <html lang="{$lang_iso}">

    <head>
        {*
        <link rel="manifest" href="/site.webmanifest">
        <link rel="mask-icon" href="/logo_luminadeco.png" color="#7a06a5">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="theme-color" content="#ffffff">
        *}

        <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
        <link rel="android-touch-icon" sizes="32x32" href="/android-icon-192x192.png">
        <link rel="android-touch-icon" sizes="60x60" href="/android-icon-192x192.png">
        <link rel="android-touch-icon" sizes="76x76" href="/android-icon-192x192.png">
        <link rel="android-touch-icon" sizes="120x120" href="/android-icon-192x192.png">
        <link rel="android-touch-icon" sizes="152x152" href="/android-icon-192x192.png">
        <link rel="android-touch-icon" sizes="192x192" href="/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="192x192" href="/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="48x48" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/manifest.json">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
        <meta name="msapplication-square70x70logo" content="/ms-icon-144x144.png" />
        <meta name="msapplication-square150x150logo" content="/ms-icon-144x144.png" />
        <meta name="theme-color" content="#ffffff">

        <meta name="og:type" content="website" />
        <meta property="og:title" content="Интернет-магазин люстр и светильников - Lumina Deco" />
        <meta name="twitter:title" content="Интернет-магазин люстр и светильников - Lumina Deco" />
        <meta property="og:image" content="/logo_luminadeco.png" />
        <meta name="twitter:image" content="/logo_luminadeco.png" />

        <link rel="preconnect" href="https://mc.yandex.ru/" crossorigin>
        <link rel="preconnect" href="https://chat.chatra.io" crossorigin>
        <link rel="preconnect" href="https://call.chatra.io" crossorigin>
        <link rel="preconnect" href="https://www.facebook.com" crossorigin>
        {literal}
            <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=UA-134675870-1"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
    
                gtag('config', 'UA-134675870-1');
            </script>
        {/literal}
        <meta charset="utf-8" />
        {if !empty( $category ) && $category}
            {$section_meta = $category->metaForSection()}
        {/if}

        {if !empty($section_meta.title)}
            <title>{$section_meta.title|escape:'html':'UTF-8'}</title>
        {else}
            <title>{$meta_title|escape:'html':'UTF-8'}</title>
        {/if}


        {if isset($meta_description) AND $meta_description}
            {if !empty( $category ) && $category}
                {$titleP = $category->title|escape:'html':'UTF-8'}
                {$titleP = $category->titlePage($titleP)}
                {if !empty($section_meta.meta_description)}
                    <meta name="description" content="{$section_meta.meta_description}" />
                {else}
                    <meta name="description" content="{$category->replaceDesc($meta_description, $titleP, $name)}" />
                {/if}
            {else}
                <meta name="description" content="{$meta_description|escape:'html':'UTF-8'}" />
            {/if}
        {/if}

        {if !empty($section_meta.keywords)}
            <meta name="keywords" content="{$section_meta.keywords|escape:'html':'UTF-8'}" />
        {elseif isset($meta_keywords) AND $meta_keywords}
            <meta name="keywords" content="{$meta_keywords|escape:'html':'UTF-8'}" />
        {/if}

        <meta name="format-detection" content="telephone=no">



        {if $page_name == index}
            <link rel="canonical" href="{$base_dir_ssl}" />
        {elseif $page_name == product}
            <link rel="canonical" href="{$base_dir_ssl}{$request_uri|substr:1}" />
        {else}
            <link rel="canonical" href="{$base_dir_ssl}{$request_uri|substr:1|regex_replace:'/\/(.*)/':''|regex_replace:'/\?(.*)/':''}" />
        {/if}

        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="generator" content="PrestaShop" />
        <meta name="robots" content="{if isset($nobots)}no{/if}index,{if isset($nofollow) && $nofollow}no{/if}follow" />
        <meta name="viewport" content="width=device-width, minimum-scale=0.25, maximum-scale=1.6, initial-scale=1.0" />
        <meta name="apple-mobile-web-app-capable" content="yes" />

        <meta http-equiv="cache-control" content="no-cache">
        <meta http-equiv="expires" content="0">

        <link rel="icon" type="image/vnd.microsoft.icon" href="/favicon-32x32.png" />
        <link rel="shortcut icon" type="image/x-icon" href="{$favicon_url}?{$img_update_time}" />

        {*
        <link rel="manifest" href="{$img_dir}favicons/site.webmanifest">
        <link rel="mask-icon" href="{$img_dir}favicons/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">
        *}
        {*
        <script type="text/javascript" src="/fancybox/fancybox-1.3.4/jquery.easing-1.3.pack.js"></script>
        <script type="text/javascript" src="/fancybox/fancybox-1.3.4/jquery.mousewheel-3.0.4.pack.js"></script>
        <script type="text/javascript" src="/fancybox/fancybox-1.3.4/jquery.fancybox-1.3.4.js"></script>

        <link rel="stylesheet" href="/fancybox/jquery.fancybox-1.3.4.css" type="text/css" media="screen" /> *}
        <link rel="stylesheet" href="/themes/modula/css/custom_style.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="/themes/modula/css/modal.css" type="text/css" media="screen" />
        {if isset($css_files)}
            {foreach from=$css_files key=css_uri item=media}
                <link rel="stylesheet" href="{$css_uri|escape:'html':'UTF-8'}" type="text/css" media="{$media|escape:'html':'UTF-8'}" />
            {/foreach}
        {/if}
        {if isset($js_defer) && !$js_defer && isset($js_files) && isset($js_def)}
            {$js_def}
            {foreach from=$js_files item=js_uri}
                <script type="text/javascript" src="{$js_uri|escape:'html':'UTF-8'}"></script>
            {/foreach}
        {/if}
        <script type="text/javascript" src="/themes/modula/js/plugins.js"></script>
        <script type="text/javascript" src="/themes/modula/js/jquery.modal.js"></script>
        <script type="text/javascript" src="/themes/modula/js/OwlCarousel2-2.3.4/dist/owl.carousel.min.js"></script>
        <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=1265d557-2340-48ce-b41a-e6286461416e" type="text/javascript"></script>
        {if $page_name=="stores"}<script src="{$js_dir}icon_customImage.js" type="text/javascript"></script>{/if}
        {if ($page_name=="cms") and ($cms->id == 19)}<script src="{$js_dir}map-19.js" type="text/javascript"></script>{/if}
        {if ($page_name=="cms") and ($cms->id == 20)}<script src="{$js_dir}map-20.js" type="text/javascript"></script>{/if}
        {if ($page_name=="cms") and ($cms->id == 21)}<script src="{$js_dir}map-21.js" type="text/javascript"></script>{/if}
        {if ($page_name=="cms") and ($cms->id == 22)}<script src="{$js_dir}map-22.js" type="text/javascript"></script>{/if}
        {$HOOK_HEADER}

        <!--[if IE 8]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
        {literal}
            <!-- Google Tag Manager -->
            <script>
                (function(w, d, s, l, i) {
                    w[l] = w[l] || [];
                    w[l].push({
                        'gtm.start': new Date().getTime(),
                        event: 'gtm.js'
                    });
                    var f = d.getElementsByTagName(s)[0],
    
                        j = d.createElement(s),
                        dl = l != 'dataLayer' ? '&l=' + l : '';
                    j.async = true;
                    j.src =
                        'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                    f.parentNode.insertBefore(j, f);
                })(window, document, 'script', 'dataLayer', 'GTM-WMTW6TM');
            </script>
            <!-- End Google Tag Manager -->
        {/literal}
        <!-- Chatra {literal} -->
            <script>
                (function(d, w, c) {
                    w.ChatraID = 'p7eQ7cqnhAWyyWQZY';
                    var s = d.createElement('script');
                    w[c] = w[c] || function() {
                        (w[c].q = w[c].q || []).push(arguments);
                    };
                    s.async = true;
                    s.src = 'https://call.chatra.io/chatra.js';
                    if (d.head) d.head.appendChild(s);
                })(document, window, 'Chatra');
                window.ChatraSetup = {
                    colors: {
                        buttonText: '#f0f0f0',
                        /* chat button text color */
                        buttonBg: '#951bc4' /* chat button background color */
                    }
                };
            </script>
        <!-- /Chatra {/literal} -->
        {literal}
            <script type="text/javascript">
                ! function() {
                    var t = document.createElement("script");
                    t.type = "text/javascript", t.async = !0, t.src = "https://vk.com/js/api/openapi.js?162", t.onload = function() { VK.Retargeting.Init("VK-RTRG-409705-fwkb7"), VK.Retargeting.Hit() }, document.head.appendChild(t)
                }();
            </script>
            <noscript>
                <img src="https://vk.com/rtrg?p=VK-RTRG-409705-fwkb7" style="position:fixed; left:-999px;" alt="" />
            </noscript>
        {/literal}
        <meta name="mailru-domain" content="Yg1L3PyLSCILKpbH" />
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,700;0,900;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    </head>

    <body{if isset($page_name)} id="{$page_name|escape:'html':'UTF-8'}" {/if} class="{if isset($page_name)}{$page_name|escape:'html':'UTF-8'}{/if}{if isset($body_classes) && $body_classes|@count} {implode value=$body_classes separator=' '}{/if}{if $hide_left_column} hide-left-column{/if}{if $hide_right_column} hide-right-column{/if}{if isset($content_only) && $content_only} content_only{/if} lang_{$lang_iso}">
        {literal}
            <!-- Google Tag Manager (noscript) -->
            <noscript>
                <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WMTW6TM" height="0" width="0" style="display:none;visibility:hidden"></iframe>
            </noscript>
            <!-- End Google Tag Manager (noscript) -->
        {/literal}

        {if !isset($content_only) || !$content_only}
            {if isset($restricted_country_mode) && $restricted_country_mode}
                <div id="restricted-country">
                    <p>
                        {l s='You cannot place a new order from your country.'}
                        <span class="bold">{$geolocation_country|escape:'html':'UTF-8'}</span>
                    </p>
                </div>
            {/if}
            <div id="page">
                <div class="header-container">
                    <header id="header">
                        <div id="stickynav" class="nav">
                            <nav class="responsive-nav">
                                <div class="container header-nav">
                                    <div class="left-block">

                                        {* <a class="icon-bg left" href="#"><span class="marker-icon icon" >Санкт-Петербург</span></a> *}


                                        <a  class="icon-bg left" target="_blank" href="https://api.whatsapp.com/send?phone=79264755330"><span class="phone-icon icon" >8 (495) 120-18-27</span></a>



                                        <a href="tel:88005002123">8 (800) 500-21-23</a>
                                        <a href="#" class="call-back">Обратный звонок</a>
                                    </div>
                                    <div class="right-block">
                                        <div class="toogle-nav-links"><img class="manu-drop" src="{$img_dir}menu-drop.svg" alt="Меню" /><img class="manu-drop-active" src="{$img_dir}menu-drop-active.svg" alt="Меню" /></div>

                                        <ul>
                                            <span class="ul-arrow"></span>
                                            <li><a href="{$link->getCMSLink(9)}">Доставка</a></li>
                                            <li><a href="{$link->getCMSLink(8)}">Оплата</a></li>
                                            {*<li><a href="#">О компании</a></li>*}
                                            {*<li><a href="#">Блог</a></li>*}
                                            <li><a href="{$base_dir_ssl}kontakt">Помощь</a></li>
                                            <li><a href="{$base_dir_ssl}magaziny">Контакты</a></li>

                                        </ul>
                                    </div>


                                </div>

                            </nav>


                        </div>

                        {hook h="displayNav2"}

                        <div id="stickybar">

                            <div class="container">
                                <div class="row">
                                    <div id="iqitmegamenu-shower" class="menus-mobile">
                                        <div class="header-mobil__burger" id="open-mobmenu">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="14.583" viewBox="0 0 25 14.583">
                                                <path d="M28.458,12.375H5.542A1.089,1.089,0,0,1,4.5,11.25h0a1.089,1.089,0,0,1,1.042-1.125H28.458A1.089,1.089,0,0,1,29.5,11.25h0A1.089,1.089,0,0,1,28.458,12.375Z" transform="translate(-4.5 -10.125)" fill="#1d1d1d" class="header-mobil__path1"></path>
                                                <path d="M28.458,19.125H5.542A1.089,1.089,0,0,1,4.5,18h0a1.089,1.089,0,0,1,1.042-1.125H28.458A1.089,1.089,0,0,1,29.5,18h0A1.089,1.089,0,0,1,28.458,19.125Z" transform="translate(-4.5 -10.708)" fill="#1d1d1d" class="header-mobil__path2"></path>
                                                <path d="M28.458,25.875H5.542A1.089,1.089,0,0,1,4.5,24.75h0a1.089,1.089,0,0,1,1.042-1.125H28.458A1.089,1.089,0,0,1,29.5,24.75h0A1.089,1.089,0,0,1,28.458,25.875Z" transform="translate(-4.5 -11.292)" fill="#1d1d1d" class="header-mobil__path3"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div id="header_logo">
                                        <a href="{$base_dir}">
                                            <img class="logo img-responsive" src="/img/logo_header.svg" alt="{$shop_name|escape:'html':'UTF-8'}" {if isset($logo_image_width) && $logo_image_width} width="{$logo_image_width}" {/if}{if isset($logo_image_height) && $logo_image_height} height="{$logo_image_height}" {/if} />
                                        </a>
                                    </div>
                                    <div class="right-infos">
                                        {hook h="displayNav"}
                                    </div>
                                </div>
    
                            </div>
                            {if isset($HOOK_TOP)}{$HOOK_TOP}{/if}
                        </div>
    
                    </header>
    
                </div>
                {if $page_name !='index' && $page_name !='pagenotfound' && $page_name !='product' && $page_name !='module-blockwishlist-mywishlist'}
                    <div class="columns-container" style="border-bottom: 1px solid #E4E4E4;">
                        <div class="container">
                            <div class="page-heading-top breadcrumb-cat">
                                <div class="scrolim col-xs-12">{include file="$tpl_dir./breadcrumb.tpl"}</div>
                            </div>
                        </div>
                    </div>
                {/if}

                {if $page_name !='index' && $page_name !='pagenotfound' && $page_name !='product' && $page_name !='module-blockwishlist-mywishlist' && $page_name !='stores' && $page_name !='cms'}
                    <div class="post-header-container {if isset($category_class)}{$category_class}{/if}">
                        <div class="milky milky-t0">
                            <div class="container">
        
                                <div class="row">
                                    <div class="col-md-12 a-center">
        
                                        <div class="header-letters">
        
                                            <!-- Category -->
                                            {if !empty( $category ) && $category}
                                                <h1 class="category-name">
                                                    {$titleP = $category->title|escape:'html':'UTF-8'}
                                                    {$titleP = $category->titlePage($titleP)}
                                                    {if !empty($section_meta.h1)}
                                                        {$section_meta.h1}
                                                    {elseif !empty($titleP)}
                                                        {$titleP}
                                                    {else}
                                                        {$category->name|escape:'html':'UTF-8'}{if !empty( $product ) && $product} : {$product->name|escape:'html':'UTF-8'}{/if}
                                                    {/if}
                                                </h1>
                                                {*
                                                <div id="category_description_short" class="rte" style="display:none">
                                                    {$description_short}
                                                    <p class="lnk_more">{l s='More'}</p>
                                                </div>
                                                <div id="category_description_long" class="rte">{$category->description}</div>
                                                *}
                                            {/if}
                                            <!-- Category end -->
        
                                            <!-- manufacturer -->
                                            {if !empty( $manufacturer ) && $manufacturer}
                                                <span class="category-name">{$manufacturer->name|escape:'html':'UTF-8'}</span>
                                                {if $manufacturer->description}
                                                    <div class="header-desc">{$manufacturer->description|truncate:200:' ...'}</div>
                                                {/if}
                                            {/if}
                                            <!-- Manufacturer end -->
        
                                            <!-- manufacturer-list -->
                                            {if !empty( $manufacturers ) && $manufacturers}
                                                <span class="header-name">{l s='Brands'}</span>
                                                {strip}
                                                    <span class="header-desc">
                                                        {if $nbManufacturers == 0}{l s='There are no manufacturers.'}
                                                        {else}
                                                            {if $nbManufacturers == 1}
                                                                {l s='There is 1 brand'}
                                                            {else}
                                                                {l s='There are %d brands' sprintf=$nbManufacturers}
                                                            {/if}
                                                        {/if}
                                                    </span>
                                                {/strip}
            
                                            {/if}
                                            <!-- manufacturer-list end -->
        
                                            <!-- supplier -->
                                            {if !empty( $supplier ) && $supplier}
                                                <span class="category-name">{$supplier->name|escape:'html':'UTF-8'}</span>
                                                {if $supplier->description}
                                                    <div class="header-desc">{$supplier->description|truncate:200:' ...'}</div>
                                                {/if}
                                            {/if}
                                            <!-- supplier end -->
        
                                            <!-- supplier-list -->
                                            {if !empty( $suppliers_list ) && $suppliers_list}
                                                <span class="header-name">{l s='Suppliers'}</span>
                                                {strip}
                                                    <span class="header-desc">
                                                        {if $nbSuppliers == 0}{l s='There are no suppliers.'}
                                                        {else}
                                                            {if $nbSuppliers == 1}
                                                                {l s='There is 1 supplier'}
                                                            {else}
                                                                {l s='There are %d suppliers' sprintf=$nbSuppliers}
                                                            {/if}
                                                        {/if}
                                                    </span>
                                                {/strip}
            
                                            {/if}
                                            <!-- supplier-list end -->
        
                                            <!-- Blog -->
                                            {if !empty( $postcategory ) && $postcategory}
                                                <span class="header-name">{l s='All Blog News'} :</span>
                                            {/if}
        
                                            {if !empty( $title_category ) && $title_category}
                                                {if !empty( $postcategory ) && $postcategory}
                
                                                    {foreach from=$categoryinfo item=category}
                                                        <span class="header-name">{$title_category}</span>
                                                        <span class="header-desc">{$category.description}</span>
                                                    {/foreach}
                
                                                {/if}
                                            {/if}
        
                                            {if !empty( $post ) && $post}
                                                <span class="header-name">{$meta_title}</span>
                                                <span class="header-desc">{$content|truncate:100:' ...'}</span>
                                            {/if}
                                            <!-- Blog end -->
        
                                            <!-- Compare -->
                                            {if $page_name=="products-comparison"}
                                                <span class="header-name">{l s='Product Comparison'}</span>
                                            {/if}
                                            <!-- Compare end -->
        
                                            <!-- Contact -->
                                            {if $page_name=="contact"}
                                                <h1 class="header-name">
                                                    {if isset($customerThread) && $customerThread}{l s='Your reply'}{else}{l s='Contact Form'}{/if}
                                                </h1>
                                            {/if}
                                            <!-- Contact end -->
        
                                            <!-- My wishlist -->
                                            {if !empty( $wishlists ) && $wishlists}
                                                <span class="header-name">{l s='My wishlists'}</span>
                                            {/if}
                                            <!-- My wishlist end -->
        
                                            <!-- My account -->
                                            {if $page_name=="my-account"}
                                                <span class="header-name">{l s='My account'}</span>
                                                <span class="header-desc">
                                                    {l s='Welcome to your account. Here you can manage all of your personal information and orders.'}
                                                </span>
                                            {/if}
                                            <!-- My account end -->
        
                                            <!-- History -->
                                            {if $page_name=="history"}
                                                <span class="header-name">{l s='Order history'}</span>
                                                <span class="header-desc">
                                                    {l s='Here are the orders you\'ve placed since your account was created.'}
                                                </span>
                                            {/if}
                                            <!-- Hystory end -->
        
                                            <!-- Order follow -->
                                            {if $page_name=="order-follow"}
                                                <span class="header-name">{l s='Return Merchandise Authorization (RMA)'}</span>
                                            {/if}
                                            <!-- Order follow end -->
        
                                            <!-- Order slip -->
                                            {if $page_name=="order-slip"}
                                                <span class="header-name">{l s='Credit slips'}</span>
                                                <span class="header-desc">
                                                    {l s='Credit slips you have received after cancelled orders'}
                                                </span>
                                            {/if}
                                            <!-- Order slip end -->
        
                                            <!-- addresses -->
                                            {if $page_name=="addresses"}
                                                <span class="header-name">{l s='My addresses'}</span>
                                            {/if}
                                            <!-- addresses end -->
        
                                            <!-- identity -->
                                            {if $page_name=="identity"}
                                                <span class="header-name">{l s='Your personal information'}</span>
                                                <span class="header-desc">
                                                    {l s='Please be sure to update your personal information if it has changed.'}
                                                </span>
                                            {/if}
                                            <!-- identity end -->
        
                                            <!-- favoriteproducts -->
                                            {if !empty( $favoriteProducts ) && $favoriteProducts}
                                                <span class="header-name">{l s='My favorite products'}</span>
                                            {/if}
                                            <!-- favoriteproducts end -->
        
                                            <!-- prices-drop -->
                                            {if $page_name=="prices-drop"}
                                                <h1 class="header-name">{l s='Price drop'}</h1>
                                            {/if}
                                            <!-- prices-drop end -->
        
                                            <!-- new-products -->
                                            {if $page_name=="new-products"}
                                                <h1 class="header-name">{l s='New products'}</h1>
                                            {/if}
                                            <!-- new-products end -->
        
                                            <!-- best-sales -->
                                            {if $page_name=="best-sales"}
                                                <h1 class="header-name">{l s='Top sellers'}</h1>
                                            {/if}
                                            <!-- best-sales end -->
        
                                            <!-- stores -->
                                            {if $page_name=="stores"}
                                                <span class="header-name">{l s='Our stores'}</span>
                                                <span class="header-desc">
                                                    {l s='Here you can find our store locations. Please feel free to contact us:'}
                                                </span>
                                            {/if}
                                            <!-- stores end -->
        
                                            <!-- cms -->
                                            {if !empty( $cms ) && $cms}
                                                {$ptl = $cms->page_title}
                                                {if !empty($ptl)}
                                                    <h1 class="header-name">{$ptl}</h1>
                                                {else}
                                                    <h1 class="header-name">{$cms->meta_title}</h1>
                                                {/if}
            
                                            {/if}
                                            <!-- cms end -->
        
                                            <!-- sitemap -->
                                            {if $page_name=="sitemap"}
                                                <span class="header-name">{l s='Sitemap'}</span>
                                            {/if}
                                            <!-- sitemap end -->
        
                                            <!-- authentication -->
                                            {if $page_name=="authentication"}
                                                <span class="header-name">
                                                    {if isset($smarty.get.display_guest_checkout)}{l s="Dane Kontaktowe"}{elseif !isset($email_create)}{l s='Authentication'}{else}{l s='Create an account'}{/if}
                                                </span>
                                            {/if}
                                            <!-- authentication end -->
        
                                            <!-- address -->
                                            {if $page_name=="address"}
                                                <span class="header-name">{l s='Your addresses'}</span>
                                                <span class="header-desc">
                                                    {if isset($id_address) && (isset($smarty.post.alias) || isset($address->alias))}
                                                        {l s='Modify address'}
                                                        {if isset($smarty.post.alias)}
                                                            "{$smarty.post.alias}"
                                                        {else}
                                                            {if isset($address->alias)}"{$address->alias|escape:'html':'UTF-8'}"{/if}
                                                        {/if}
                                                    {else}
                                                        {l s='To add a new address, please fill out the form below.'}
                                                    {/if}
                                                </span>
                                            {/if}
                                            <!-- address end -->
        
                                            <!-- order-opc -->
                                            {if $page_name=="order-opc"}
                                                <span class="header-name">{l s='Оформление заказа'}</span>
                                                {* <span class="header-desc">
                                                    {l s='Your shopping cart contains :'} {$productNumber} {if $productNumber == 1}товар{elseif $productNumber == 2}товара{elseif $productNumber == 3}товара{elseif $productNumber == 4}товара{else}товаров{/if}
                                                </span> *}
                                            {/if}
                                            <!-- order-opc end -->
        
                                            <!-- order-opc -->
                                            {if $page_name=="order-confirmation"}
                                                <span class="header-name">{l s='Спасибо за заказ'} <i class="icon-heartt"></i></span>
                                                {* <span class="header-desc">
                                                    {l s='Your shopping cart contains :'} {$productNumber} {if $productNumber == 1}товар{elseif $productNumber == 2}товара{elseif $productNumber == 3}товара{elseif $productNumber == 4}товара{else}товаров{/if}
                                                </span> *}
                                            {/if}
                                            <!-- order-opc end -->
        
                                            <!-- search -->
                                            {if $page_name=="search"}
                                                <span class="header-name">
                                                    {if isset($search_query) && $search_query}{$search_query|escape:'html':'UTF-8'}{elseif $search_tag}{$search_tag|escape:'html':'UTF-8'}{elseif $ref}{$ref|escape:'html':'UTF-8'}{/if}
                                                </span>
                                                <span class="header-desc">
                                                    {if $nbProducts > 0}{/if} {if $nbProducts == 1}{l s='%d result has been found.' sprintf=$nbProducts|intval}{else}{l s='%d results have been found.' sprintf=$nbProducts|intval}{/if}
                                                </span>
                                            {/if}
                                            <!-- search end -->
        
                                            <!-- guest-tracking -->
                                            {if $page_name=="guest-tracking"}
                                                <span class="header-name">{l s='Guest Tracking'}</span>
                                            {/if}
                                            <!-- guest-tracking end -->
        
                                            <!-- Shopping Cart -->
                                            {if isset($smarty.get.step)}{$order_step = $smarty.get.step}{elseif isset($smarty.post.step)}{$order_step = $smarty.post.step}{/if}
                                            {if isset($order_step) && $page_name=="order"}
                                                <span class="header-name">
                                                    {if $order_step == 1}{l s='Adres Dostawy'}{elseif $order_step == 2}{l s='Sposób Dostawy'}{elseif $order_step == 3}{l s='Płatność'}{/if}
                                                </span>
                                            {elseif $page_name=="order"}
                                                <span class="header-name">{l s='Shopping-cart summary'}</span>
                                            {/if}
                                            <!-- Shopping Cart -->
        
                                            <!-- module-bankwire-payment -->
                                            {if $page_name=="module-bankwire-payment"}
                                                <span class="header-name">{l s='Order summary'}</span>
                                            {/if}
                                            <!-- module-bankwire-payment -->
        
                                            <!-- module-cheque-payment -->
                                            {if $page_name=="module-cheque-payment"}
                                                <span class="header-name">{l s='Order summary'}</span>
                                            {/if}
                                            <!-- module-cheque-payment -->
                                        </div>
        
                                        <!-- Breadcrumb -->
                                        {*
                                        <div class="page-heading-top">
                                            <span>{include file="$tpl_dir./breadcrumb.tpl"}</span>
                                        </div>
                                        *}
                                        <!-- Breadcrumb end -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}

                <div class="columns-container">
    
                    <div id="top-column-container">

                        {if $page_name=="index"} <div id="top_column" class="center_column container">{hook h="displayTopColumn"}</div>{/if}
    
                        {*<div class="banner">
                            <div class="container">
                                <div class="rte col-xs-12">{hook h="displayBanner"}</div>
                            </div>
                        </div>*}
                        <div id="columns" class="container">
                            {if isset($left_column_size) && !empty($left_column_size)}
                                <div id="left_column" class="column col-xs-12 col-sm-{$left_column_size|intval}">
                                    <div class="mob-open__filter mobile"> <a href="#" class="mob-open__filter-link">Показать фильтр</a></div>{$HOOK_LEFT_COLUMN}
                                </div>

                                <script>
                                    if (!$('#layered_block_left').length) {
                                        $('.mob-open__filter').hide();
                                    }
                                </script>

                            {/if}
    
                            {if isset($left_column_size) && isset($right_column_size)}{assign var='cols' value=(12 - $left_column_size - $right_column_size)}{else}{assign var='cols' value=12}{/if}
                            <div id="center_column" class="{$page_name} center_column col-xs-12 col-sm-{$cols|intval}">
                            {/if}