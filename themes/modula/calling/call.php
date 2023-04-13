<?php

if (isset($_POST['str'])) {
    $data_array = array();
    parse_str($_POST['str'], $data_array);
    
    $call1name = $data_array['call1name'];
    $call1phone = $data_array['call1phone'];
    $call1time = $data_array['call1time'];
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html;charset=utf-8 \r\n";
    $headers .= 'From: Заявка обратный звонок <mail@luminadecoshop.ru>' . "\r\n";
    
    $message = "<p>Заявка обратный звонок</p>
				<p><strong>Имя:</strong> $call1name</p>
				<p><strong>Телефон:</strong> $call1phone</p>
				<p><strong>Удобное время:</strong> $call1time</p>";

    mail("order@luminadecoshop.ru", "Заявка обратный звонок", $message, $headers);
}

?>

<div class="form1-calling">
	<form>
		<h3>Закажите обратный звонок, и мы Вам перезвоним!</h3>
		<div class="call-line8">Имя</div>
		<input type="text" name="call1name" placeholder="Имя" required>
		<div class="call-line8">Телефон</div>
		<input type="text" name="call1phone" placeholder="Телефон" required>
		<div class="call-line8">Удобное время</div>
		<input type="datetime-local" name="call1time" placeholder="Удобное время" required>
		<div class="politika-konfic">Нажимая кнопку "Заказать" вы соглашаетесь с 
			<a href="/content/16-politika-konfidentsialnosti" target="_blank">политикой конфиденциальности</a>
		</div>
		<div class="call1footer">
		<input type="submit" value="Заказать">
		</div>
	</form>
</div>
<script>
$('.form1-calling form').submit(function(){
    var $form = $(this);
	var $posting = $.post( '/themes/modula/calling/call.php', { str: $form.serialize() } );
	$posting.done(function( data ) {
		$('.form1-calling .call1footer').html('Спасибо за заявку! Наши специалисты скоро свяжутся с вами');
		$('.form1-calling .call1footer').addClass('calling-ok');
	  });
    return false;
});
$(document).ready(function(){$('.form1-calling input[name=call1phone]').mask('+7(999)999-99-99');});
</script>