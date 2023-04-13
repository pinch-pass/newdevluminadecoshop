{if $showbutton}
    <a href="#" class="btn1-calling" id="open-call-back">Обратный звонок</a>
{/if}
<div id="call-pop">
    <div id="mod-bg-call"> </div>
    <div class="product__modal" id="sew-by-call-modal">
        <button class="btn-close-modal" type="button" id="primaryButtones"></button>
        <div class="sew-by-order">
            <div class="owner-feedbacks__success visuallyhidden" style="margin: 0 auto;">
                <p class="sew-by-order__heading">Ваша заявка принята!</p>
                <p class="sew-by-order__desc">В ближайшее время с Вами свяжется наш менеджер!</p>
                <img src="{$modules_dir}pwbackcall/views/images/check-mark.svg"/>
                <button class="sew-by-order__button" id="closeModal" type="submit" >Продолжить покупки</button>
            </div>
            <form method="POST" action="{$pwbackcall.link}" class="sew-by-order__form" id="uipw-form_call_modal">
                <input type="hidden" name="action" value="call"/>
                <input type="hidden" name="URL" value=""/>
                <p class="sew-by-order__heading">Обратный звонок</p>
                <p class="sew-by-order__desc"></p>
                {if $showfieldname}
                <label class="sew-by-order__label sew-by-order__label--100"><span>Ваше имя<span>*</span></span>
                    <input class="sew-by-order__input" type="text" name="name" placeholder="Ваше имя" required>
                </label>
                {/if}
                {if $showfieldphone}
                <label class="sew-by-order__label sew-by-order__label--100"><span>Контактный телефон<span>*</span></span>
                    <input class="sew-by-order__input sew-by-order__input--100" type="text" name="phone" placeholder="Контактный телефон*" required>
                </label>
                {/if}
                <div class="modal-check">
                    <input type="checkbox" name="personal" value="1">
                    <p>Я ознакомился и принимаю условия <a href="{$link->getCMSLink(16)}">“Политики конфиденциальности”</a> и <a href="{$link->getCMSLink(17)}">“Пользовательского соглашения“</a> </p>
                </div>
                <button class="sew-by-order__button" type="submit" id="lowers" >Отправить</button>
            </form>
        </div>
    </div>
</div>