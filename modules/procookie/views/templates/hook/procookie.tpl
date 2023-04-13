
<style type="text/css">
	{$occ_css|escape:'htmlall'}
</style>

<script type="text/javascript">
{literal}

setTimeout(function() {
    $(function(){
        var auto_time = {/literal}{$occ_bar_auto_close_time}{literal} * 1000,
            test_mode = {/literal}{$occ_test_mode}{literal};

        if(test_mode == 1 && $.cookie('procookie')) {
            $.cookie('procookie', null);
        }

        function cookieanimate() {
            var position = {/literal}{$occ_bar_position}{literal};

            if($.cookie('procookie') == 1) {
                return false;
            }

            if($(".procookie").is(":visible") ) {
                if(position == 1) {
                    $('.procookie').hide();
                } else {
                    $('.procookie').hide();
                }
            } else if (!$(".procookie").hasClass("closed")) {
                if(position == 1) {
                    $('.procookie').show();
                } else {
                    $('.procookie').show();
                }
            }
        }

        if ($.cookie('procookie') != 1) {
            cookieanimate();
        }

        $(".procookie-close").click(function(){
            cookieanimate();
            if(test_mode == 0) {
                $.cookie('procookie', '1', { expires: {/literal}{$occ_timeout}{literal}, path: '/'});
            }
            $(".procookie").addClass("closed");
        });

        if(auto_time > 0) {
            setTimeout(cookieanimate, auto_time);
        }
    });
}, 3001);

{/literal}
</script>

<div style="display: none" class="procookie">
    <img src="/themes/modula/img/cookie.png" alt="">
    <span>Мы используем файлы <a href="/content/16-politika-konfidentsialnosti" target="_blank">cookies</a>, чтобы улучшить сайт для Вас</span>
    {if $occ_bar_url != ""}<a class="procookie-more" href="{$occ_bar_url}">{$occ_bar_read_more_text|escape:'htmlall'}</a>{/if}
    <a class="procookie-close">{$occ_bar_close_text|escape:'htmlall'}</a>
</div>
