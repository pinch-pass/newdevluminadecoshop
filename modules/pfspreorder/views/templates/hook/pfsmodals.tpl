<div id="mod">  
<div id="mod-bg"> </div>
    <div class="product__modal" id="sew-by-order-modal">
        <button class="btn-close-modal" type="button" id="primaryButton"></button>
        <div class="sew-by-order">
           
             <div class="owner-feedbacks__success visuallyhidden"style="margin: 0 auto;">
             <p class="sew-by-order__heading">Ваша заявка принята!</p>
            <p class="sew-by-order__desc">В ближайшее время с Вами свяжеться наш менеджер 
для подтверждения заказа!</p>
            <img src="{$modules_dir}pfspreorder/views/img/check-mark.svg"/>
            <button class="sew-by-order__button" id="secondaryButton"
                        type="submit" >Продолжить покупки</button>
            </div>
            <form class="sew-by-order__form" id="form-1">
                <input class="visuallyhidden" name="product" type="hidden" value="{$product->name}">
                <input class="visuallyhidden" name="formname" type="hidden" value="Нашли дешевле?">
                <p class="sew-by-order__heading">Нашли дешевле?</p>
                <p class="sew-by-order__desc">Если вы нашли в другом интернет-магазине дешевле товар, который представлен на нашем сайте, мы снизим цену. Заполните форму ниже и мы готовы предложить Вам 
лучшую цену!</p>
                
                <label class="sew-by-order__label sew-by-order__label--100"><span>Ваше имя<span>*</span></span>
                    <input class="sew-by-order__input success" type="text" name="name"
                           placeholder="Ваше имя" required>
                </label>
                <label class="sew-by-order__label sew-by-order__label--100"><span>Контактный телефон<span>*</span></span>
                    <input class="sew-by-order__input sew-by-order__input--100" id="phone_mobile" type="tel" name="phone"
                           placeholder="Контактный телефон*" required>
                </label>
                
                <label class="sew-by-order__label sew-by-order__label--100"><span>E-mail<span>*</span></span>
                    <input class="sew-by-order__input sew-by-order__input--100" type="email" name="email"
                           placeholder="E-mail" required>
                </label>
                <label class="sew-by-order__label sew-by-order__label--100"><span>Ссылка на сайт где дешевле<span>*</span></span>
                    <input class="sew-by-order__input sew-by-order__input--100" type="message_text" name="message_text"
                           placeholder="Ссылка на сайт где дешевле" required>
                </label>
               
                          <div class="modal-check">
                           
                              <input type="checkbox" value="value-1" onchange="document.getElementById('lower').disabled = !this.checked;">
                              <p>Я ознакомился и принимаю условия <a href="">“Политики конфиденциальности”</a> и <a href="">“Пользовательского соглашения“</a> </p>
                            
                          </div>
                <button class="sew-by-order__button"
                        type="submit" id="lower" disabled >Отправить</button>

            </form>
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