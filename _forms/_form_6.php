<?php
$email = $api->objects->getFullObject(33076);
$emailCopy = $api->objects->getFullObject(33083);
?>
<input type="hidden" name="email-to" value="<?=$email['Значение']?>">
<input type="hidden" name="email-to-copy" value="<?=$emailCopy['Значение']?>">
<div class="rows">
    <label class="f_lab">Опишите Ваше предложение<span>*</span></label>
    <textarea required="required" name="description" style="width: 233px" rows="4" class="f_text" value=""></textarea>
</div>
<div class="rows">
    <label class="f_lab">Вложить файл (при необходимости)</label>
    <input type="file" name="attach">
</div>