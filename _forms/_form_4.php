<?php
$select_class = array();
$classes = $api->objects->getFullObjectsListByClass(27364, 7, "AND o.active ORDER BY o.sort");
$yearsObjects = $api->objects->getFullObjectsListByClass(33015, 89, "AND o.active ORDER BY o.sort");
?>
<input type="hidden" name="email-to" value="d.nam@mercedes-benz.com.kz, n.kisselev@mercedes-benz.com.kz">
<input type="hidden" name="email-to-copy" value="v.sokolov@mercedes-benz.com.kz">
<div class="rows">
    <label class="f_lab">Класс автомобиля<span>*</span></label>
    <select required="required" name="klass">
        <?php foreach ($classes as $class) : ?>
            <option value="<?php echo $class['Значение'] ?>"><?php echo $class['Значение'] ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="rows">
    <label class="f_lab">Опишите вид сервисных работ, необходимых Вам<span>*</span></label>
    <textarea required="required" name="service_work_description" class="f_text" style="width: 233px" rows="4" value=""></textarea>
</div>
<div class="rows" style="position: relative;">
    <label class="f_lab">Год выпуска<span>*</span></label>
    <select required="required" name="year" class="ajax-form-year">
        <?php foreach ($yearsObjects as $item) : ?>
            <option value="<?=$item['id'] ?>"><?=$item['Значение'] ?></option>
        <?php endforeach ; ?>
    </select>
    <div class="modal modal-message-form" style="position: absolute; border: 0; border-radius: 0; width: 312px; transition: .3s; margin-left: 40px;
    top: 0; opacity: 0; background: #000; color: #fff;">
        <img src="/img/window-year.png" alt="" style="display: block; float: left;">
        <div class="text" style="text-align: right; font-size: 12px; padding: 12px 13px 8px 10px; margin-left: 156px;">
            <p style="padding: 0px;">Премиум Сервис для Вашего Mercedes-Benz.</p>
            <p style="padding: 0px;">Скидка:</p>
            <p style="padding: 0px;">&#183; 30% на обслуживание</p>
            <p style="padding: 0px;">&#183; до 30% на запчасти</p>
        </div>
    </div>
</div>
<div class="rows">
    <label class="f_lab">VIN номер или Гос. Номер автомобиля<span>*</span></label>
    <input required="required" type="text" name="vin" class="f_text" value=""/>
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
