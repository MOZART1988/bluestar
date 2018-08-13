<?php
$email = $api->objects->getFullObject(33075);
$emailCopy = $api->objects->getFullObject(33082);
?>
<input type="hidden" name="email-to" value="<?=$email['Значение']?>">
<input type="hidden" name="email-to-copy" value="<?=$emailCopy['Значение']?>">
<div class="rows">
    <label class="f_lab">Желаемая должность или вакансия<span>*</span></label>
    <textarea required="required" name="description" class="f_text" style="width: 233px" rows="4" value=""></textarea>
</div>
<div class="rows">
    <label class="f_lab">Вложить файл (при необходимости)</label>
    <input type="file" name="attach">
</div>