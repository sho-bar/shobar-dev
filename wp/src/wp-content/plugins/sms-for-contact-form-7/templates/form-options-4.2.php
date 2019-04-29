<div id="sms-sortables" class="meta-box-sortables ui-sortable">
<h3>SMS оповещение</h3>
<fieldset>
<legend>В следующих полях Вы можете использовать эти почтовые метки:<br />
	<?php $data['form']->suggest_mail_tags(); ?>
</legend> 
<table class="form-table">
	<tbody>
	<tr>
		<th scope="row">
		  	<label for="wpcf7-sms-recipient">Кому отправлять:</label>
		</th>
		<td>
		  	<input type="text" id="wpcf7-sms-recipient" name="wpcf7-sms[phone]" class="wide" size="70" value="<?php echo $data['phone']; ?>">
			<div class="config-error">
				Телефоны можно указывать через запятую.<br />
				Примеры: [your-phone] | +79880110011 | [your-phone],[your-phone2],... | [your-phone],+79880110011,... | +79880110011,+79880110022,...
			</div>
		</td>
	</tr>
	
	<tr>
		<th scope="row">
		  	<label for="wpcf7-sms-lat">Перевести сообщение в латинские символы?</label>
		</th>
		<td>
		  	<input type="checkbox" id="wpcf7-sms-lat" name="wpcf7-sms[lat]" value="1" <?php isset($data['lat']) ? checked( $data['lat'] ) : ''; ?>>
		</td>
	</tr>
	
	<tr>
		<th scope="row">
		  	<label for="wpcf7-sms-recipient">Отложить отправку сообщения:</label>
		</th>
		<td>
		  	<input type="number" id="wpcf7-sms-recipient" name="wpcf7-sms[time]" class="wide" size="70" value="<?php echo $data['time']; ?>">
			<div class="config-error">
				Откладывает отправку сообщения на указанное время.<br />
				Время, на которое нужно отложить отправку указывается в секундах!!! Пример 252000 = 7 часов
			</div>
		</td>
	</tr>

	<tr>
		 <th scope="row">
			<label for="wpcf7-mail-body">Текст сообщения:</label>
		</th>
		<td>
			<textarea id="wpcf7-mail-body" name="wpcf7-sms[message]" cols="100" rows="6" class="large-text code"><?php echo $data['message']; ?></textarea>
		</td>
	</tr>
	</tbody>
</table>
</fieldset>
