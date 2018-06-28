<?php
$select_class = array();
$classes = $api->objects->getFullObjectsListByClass(27364, 7, "AND o.active ORDER BY o.sort");
?>
<input type="hidden" name="email-to" value="opbs@mercedes-benz.com.kz,walvorin@gmail.com">
<input type="hidden" name="email-to-copy" value="">
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
