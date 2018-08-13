<?php
$yearsObjects = $api->objects->getFullObjectsListByClass(33015, 89, "AND o.active ORDER BY o.sort");
?>
<input type="hidden" name="email-to" value="n.lisovoy@mercedes-benz.com.kz">
<input type="hidden" name="email-to-copy" value="v.kem@mercedes-benz.com.kz,p.druchinin@mercedes-benz.com.kz">
<div class="rows">
    <label class="f_lab">Перечислите необходимые запчасти или аксессуары<span>*</span></label>
    <textarea required="required" name="detali" class="f_text" value="" style="width: 233px" rows="4"></textarea>
</div>
<div class="rows">
    <label class="f_lab">Вложить файл (при необходимости)</label>
    <input type="file" name="attach">
</div>
<div class="rows" style="position:relative;">
    <label class="f_lab">Год выпуска<span>*</span></label>
    <select required="required" name="year" class="ajax-form-year">
        <?php foreach ($yearsObjects as $item) : ?>
            <option value="<?=$item['id'] ?>"><?=$item['Значение'] ?></option>
        <?php endforeach ; ?>
    </select>
    <!--<div class="modal modal-message-form" style="position: absolute; border: 0; border-radius: 0; width: 312px; transition: .3s; margin-left: 40px;
    top: 0; opacity: 0; background: #000; color: #fff;">
        <img src="/img/window-year.png" alt="" style="display: block; float: left;">
        <div class="text" style="text-align: right; font-size: 12px; padding: 12px 13px 8px 10px; margin-left: 156px;">
            <p style="padding: 0px;">Премиум Сервис для Вашего Mercedes-Benz.</p>
            <p style="padding: 0px;">Скидка:</p>
            <p style="padding: 0px;">&#183; 30% на обслуживание</p>
            <p style="padding: 0px;">&#183; до 30% на запчасти</p>
        </div>
    </div>-->
</div>
<div class="rows">
    <label class="f_lab">VIN номер или Гос. Номер автомобиля<span>*</span></label>
    <input required="required" type="text" name="vin" class="f_text" value=""/>
</div>
