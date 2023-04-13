<div id="product__callback" class="product__callback">
<p>Обратный звонок</p>
<div id="callback">

        <div class="sew-by-callback">
         
            <div class="owner-feedbacks__success visuallyhidden"style="margin: 0 auto;">
            Ваша заявка отправлена
            
            </div>
            <form class="sew-by-callback__form" id="form-2">
                <input class="visuallyhidden" name="product" type="hidden" value="{$product->name}">
                <input class="visuallyhidden" name="formname" type="hidden" value="Перезвоните мне">
              
                <label class="sew-by-callback__label sew-by-callback__label--100">
                    <input class="sew-by-callback__input sew-by-callback__input--100" type="tel" name="phone"
                           placeholder="Номер телефона" required>
                </label>
                
                <button id="sew-by-callback__button" class="sew-by-callback__button"
                        type="submit" disabled>Перезвонить</button>
                     
            </form>
                <div class="modal-check">
				    <input type="checkbox" value="value-1" onchange="document.getElementById('sew-by-callback__button').disabled = !this.checked;">
					<p>Я ознакомился и принимаю условия <a href="">“Политики конфиденциальности”</a> и <a href="">“Пользовательского соглашения“</a> </p>
				</div>
               
        </div>
    </div>
    <div class="product__modal" id="report-on-stock">
        <button class="btn-close-modal" type="button" id="primaryButtons"></button>
        <div class="sew-by-order">

            <div class="owner-feedbacks__success"style="margin: 0 auto;">
                <p class="sew-by-order__heading">Ваша заявка принята!</p>
                <p class="sew-by-order__desc">В ближайшее время с Вами свяжеться наш менеджер
                    для подтверждения заказа!</p>
                <img src="{$modules_dir}pfspreorder/views/img/check-mark.svg"/>
                <p class="sew-by-order__button" id="secondaryButtons"
                >Продолжить покупки</p>
            </div>
        </div>
    </div>
</div>
