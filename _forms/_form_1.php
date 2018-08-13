<?php
$select_class = array();
$classes = $api->objects->getFullObjectsListByClass(27364, 7, "AND o.active ORDER BY o.sort");
$email = $api->objects->getFullObject(33071);
$emailCopy = $api->objects->getFullObject(33078);
?>
<input type="hidden" name="email-to" value="<?=$email['Значение']?>">
<input type="hidden" name="email-to-copy" value="<?=$emailCopy['Значение']?>">
<div class="rows">
    <label class="f_lab">Класс автомобиля<span>*</span></label>
    <select required="required" name="klass">
        <?php foreach ($classes as $class) : ?>
            <option value="<?php echo $class['Значение'] ?>"><?php echo $class['Значение'] ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="rows">
    <label class="f_lab">Задайте Ваш вопрос</label>
    <textarea required="required" name="question" class="f_text" style="width: 233px" rows="4" value="<?php echo @$_POST['question']?>"></textarea>
</div>
