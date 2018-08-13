<?php
$select_class = array();
$classes = $api->objects->getFullObjectsListByClass(27364, 7, "AND o.active ORDER BY o.sort");
$email = $api->objects->getFullObject(33072);
$emailCopy = $api->objects->getFullObject(33079);
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
    <label class="f_lab">Дата<span>*</span></label>
    <a href="#calend-opened" class="calend"><img src="/img/cal.jpg"/></a>
    <input required="required" type="text" name="date" style="width:214px!important" class="date-picker f_text f_date" />
</div>
<div class="rows">
    <label class="f_lab">Желаемое время<span>*</span></label>
    <select required="required" name="time">
        <option value="8:00">8:00</option>
        <option value="9:00">9:00</option>
        <option value="10:00">10:00</option>
        <option value="11:00">11:00</option>
        <option value="12:00">12:00</option>
        <option value="13:00">13:00</option>
        <option value="14:00">14:00</option>
        <option value="15:00">15:00</option>
        <option value="16:00">16:00</option>
        <option value="17:00">17:00</option>
        <option value="18:00">18:00</option>
    </select>
</div>

