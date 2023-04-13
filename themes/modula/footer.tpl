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
{if !$content_only}

</div>

<!-- #center_column -->

{if isset($right_column_size) && !empty($right_column_size)}
<div id="right_column" class="col-xs-12 col-sm-{$right_column_size|intval} column">{$HOOK_RIGHT_COLUMN}</div>
{/if}

</div>

<!-- .row -->

</div>

<!-- #columns -->

</div>

<!-- .columns-container -->

<!-- Footer -->

<div class="footer-container">

<footer id="footer"  class="container">
<div class="row">
{* {include file='./footer_column1.tpl'}
{include file='./footer_column2.tpl'}
{include file='./footer_column3.tpl'}
{include file='./footer_column4.tpl'} *}
<div class="catalog">
{include file='./footer_1.tpl'}
</div>
<div class="about-block">
{include file='./footer_2.tpl'}
</div>
<div class="help">
{include file='./footer_3.tpl'}
</div>
{*<div class="helpes">*}

{*</div>*}
<div class="adress-block">
{include file='./footer_4.tpl'}
</div>


</div>
{include file='./footer_5.tpl'}

{$HOOK_FOOTER}
</footer>

</div>
<div class="after-footer"><div class="container">{include file='./footer_6.tpl'}</div></div>
<div class="post-footer-container">

<div id="post-footer"  class="container">


<a href="/"><img class="ld_logo_bottom" src="/img/logo_header.svg" alt="{$shop_name|escape:'html':'UTF-8'}"></a>
<p class="rights">{l s='© 2008-2022 - Lumina Deco - Все права защищены'}</p>
	<p class="creator">
		Разработка сайта <a href="//www.blancy.ru"><img src="{$img_dir}Blancy.svg" alt="Blancy" /> Blancy</a>
	</p>

</div>

</div>

<!-- #footer -->

</div>
<div id="call-pop">  
<div id="mod-bg-call"> </div>
    <div class="product__modal" id="sew-by-call-modal">
        <button class="btn-close-modal" type="button" id="primaryButtones"></button>
        <div class="sew-by-order">
             <div class="owner-feedbacks__success visuallyhidden"style="margin: 0 auto;">
                 <p class="sew-by-order__heading">Ваша заявка принята!</p>
                 <p class="sew-by-order__desc">В ближайшее время с Вами свяжеться наш менеджер!</p>
                 <img src="{$modules_dir}pfspreorder/views/img/check-mark.svg"/>
                 <button class="sew-by-order__button" id="thirdButton" type="submit" >Продолжить покупки</button>
            </div>
            <form class="sew-by-order__form" id="form-3">
                <input class="visuallyhidden" name="formname" type="hidden" value="Обратный звонок">
                <p class="sew-by-order__heading">Обратный звонок</p>
                <p class="sew-by-order__desc"></p>
                
                <label class="sew-by-order__label sew-by-order__label--100"><span>Ваше имя<span>*</span></span>
                    <input class="sew-by-order__input success" type="text" name="name"
                           placeholder="Ваше имя" required>
                </label>
                <label class="sew-by-order__label sew-by-order__label--100"><span>Контактный телефон<span>*</span></span>
                    <input class="sew-by-order__input sew-by-order__input--100" type="text" name="phone" placeholder="Контактный телефон*" required>
                </label>

                <div class="modal-check">
                    <input type="checkbox" name="personal" value="value-1" onchange="document.getElementById('lowers').disabled = !this.checked;">
                    <p>Я ознакомился и принимаю условия <a href="/content/16-politika-konfidentsialnosti" target="_blank">“Политики конфиденциальности”</a> и <a href="/content/16-politika-konfidentsialnosti" target="_blank">“Пользовательского соглашения“</a> </p>
                </div>
                <button class="sew-by-order__button" type="submit" id="lowers">Отправить</button>
            </form>
        </div>
    </div>


</div>
<!-- #page -->

{/if}

<script>
$("a.grouped_elements").attr("rel", "group1");
$("a.grouped_elements2").attr("rel", "group2");
$("a.grouped_elements").fancybox();
$("a.grouped_elements2").fancybox();
</script>

<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600|Roboto&amp;subset=cyrillic,cyrillic-ext&display=swap" rel="stylesheet"> 
<link href="https://fonts.googleapis.com/css?family=Lato:900|Open+Sans|Poppins:300,400,500,600,700|Roboto&amp;subset=latin-ext&display=swap" rel="stylesheet">

{include file="$tpl_dir./global.tpl"}
<!-- Cookies -->
    <!-- 
        Skrypt stworzonyi  zainstalowany przez Grzegorz Zawadzki <kontakt@seigi.eu>
    -->
    <div id="cookieinfo" style="visibility:hidden;display:none;">
        {l s='Wyrażam zgodę na przetwarzanie danych osobowych na zasadach określonych w'} <a href="{$link->getCMSLink(16)|escape:'html':'UTF-8'}?content_only=1" title="" class="iframe" rel="nofollow">{l s="polityce prywatności"}</a>{l s=', Jeśli nie wyrażasz zgody na wykorzystywanie cookies we wskazanych w niej celach, w tym do profilowania, prosimy o wyłącznie cookies w przeglądarce lub opuszczenie serwisu.'} <b><a href="#" style="color: #fff;" onclick="javascript: return closeCookieInfo();">[{l s='Akceptuję'}]</a>
        <noscript><br>{l s='Aby zamknąć to okno, musisz mieć włączoną obsługę javascript'}</noscript>
        </b>
    </div>
    
    <script type="text/javascript">
    // {literal}
        function closeCookieInfo(){var e=document.getElementById("cookieinfo");e.style.display="none";setCookie("cookieinfoaccepted","1",180);return false}function setCookie(e,t,n){var r=new Date;r.setDate(r.getDate()+n);var i=escape(t)+(n==null?"":"; expires="+r.toUTCString());document.cookie=e+"="+i+"; path=/"}function getCookie(e){var t=document.cookie;var n=t.indexOf(" "+e+"=");if(n==-1){n=t.indexOf(e+"=")}if(n==-1){t=null}else{n=t.indexOf("=",n)+1;var r=t.indexOf(";",n);if(r==-1){r=t.length}t=unescape(t.substring(n,r))}return t}if(getCookie("cookieinfoaccepted")=="1")closeCookieInfo()
    // {/literal}
    </script>
<!-- EOF: Cookies -->
<script>
if ($(window).width() > 767) {
	$(document).ready(function() {
		var stickyNavTop = $('#stickynav').offset().top;

		var stickyNav = function() {
			var scrollTop = $(window).scrollTop();

			if (scrollTop > stickyNavTop) {
				$('#page').addClass('sticky');
				$('body').addClass('sticky_fix');
			} else {
				$('#page').removeClass('sticky');
				$('body').removeClass('sticky_fix');
			}
		};

		stickyNav();
		$(window).scroll(function() {
			stickyNav();
		});
	});
}
</script>
<script>
if ($(window).width() < 767) {
		$('#category_description_short').css('display', 'block')
		$('#category_description_long').css('display', 'none')
	}

</script>
<script>
$(function() {
  if (window.self != window.top) {
    $(document.body).addClass("in-iframe");
  }
});
</script>
<script type="text/javascript">
	$(".product_img_link").hover(
	function () {
		$(this).find('.img_second').toggle();
		$(this).find('.img_first').toggle();
	},
	function () {
		$(this).find('.img_second').toggle();
		$(this).find('.img_first').toggle();
	}
	);

$(document).ready(function() {
	if (typeof mywishlist_count != 'undefined' && mywishlist_count > 0) {
		$('.link-img-wishlist span').text(mywishlist_count);
	} else {
		$('.link-img-wishlist span').text('0');
	}
	$('.btn1-calling').modal();
})
</script>
  
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>

</body>
</html>