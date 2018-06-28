#!/usr/bin/php
<?php

$task = isset($argv[1]) ? $argv[1]:null;

include('cms/public/api.php');

const XML_PATH	= 'parser/xml';

const XML_IMPORT_ACTIVE_PATH	= 'parser/xml/import/active';

const XML_IMPORT_ARCHIVE_PATH	= 'parser/xml/import/archive';

const XML_EXPORT_ACTIVE_PATH	= 'parser/xml/export/active';

const XML_EXPORT_ARCHIVE_PATH	= 'parser/xml/export/archive';

const ITERATION_LIMIT = 50;

const PARTS_HEAD_ID = 7720;

const PARTS_CLASS_ID = 84;

$customersTypes = array(
    0 => 'Розничный',
    1 => 'Розничный',
    2 => 'Оптовый',
    3 => 'Франчайзи',
);

$defaultRegionId = '000000001';

switch($task){

    default:
        $files = glob(XML_IMPORT_ACTIVE_PATH . '/*');
        sort($files, SORT_NATURAL );

        if(!count($files)){
            return false;
        }

        $path = $files[0];

        $partsAddCount = 0;
        $partsEditCount = 0;

        $logStr = PHP_EOL.date('Y-m-d H:i:s') . ' обрабатывается файл: '.$path.PHP_EOL;
        $api->lang = 'ru';
        if(file_exists($path)) {

            $xml = new \XMLReader();
            $xml->open($path);

            while($xml->read()) {

                if ($xml->nodeType == \XMLReader::ELEMENT && $xml->name == 'parts') {
                    $element = simplexml_load_string($xml->readOuterXML());

                    if(!empty($element->id)){
                        if ( !$obj = $api->objects->getObjectsListByClass(-1, PARTS_CLASS_ID, "AND c.field_263='".$api->db->prepare(trim($element->id))."' LIMIT 1")) {
                            $obj = array();
                            $method = 'createObjectAndFields';
                        }else{
                            $method = 'editObjectAndFields';
                        }

                        $obj['name'] = trim($element->name);
                        $obj['active'] = 1;
                        $obj['class_id'] = PARTS_CLASS_ID;
                        $obj['head'] = PARTS_HEAD_ID;

                        $sale = trim($element->sale);

                        $fields = array(
                            'Артикул' => trim($element->id),
                            'Название' => trim($element->name),
                            'Количество' => Getfloat($element->count),
                            'Цена' => Getfloat($element->prise),
                            'Скидка' => !empty($sale) ? 1 : 0,
                            'Дата обновления' => date('Y-m-d')
                        );

                        $api->lang = 'ru';
                        if($method == 'createObjectAndFields'){
                            $obj['id'] = $api->objects->$method( $obj, $fields);
                            $partsAddCount++;
                        }else{
                            $api->objects->$method( $obj, $fields);
                            $partsEditCount++;
                        }
                    }
                }
            }

            $xml->close();


            $directoryPath = XML_IMPORT_ARCHIVE_PATH . '/' . date('Y-m-d');

            if(!is_dir($directoryPath)){
                mkdir($directoryPath, 0777);
            }

            $fileName = basename($path);

            $archivePath = $directoryPath.'/'.$fileName;
            rename($path, $archivePath);
            chmod($archivePath, 0777);
        }

        $logStr .= 'Добавлено запчастей: ' . $partsAddCount . PHP_EOL;
        $logStr .= 'Обновлено запчастей: ' . $partsEditCount . PHP_EOL;

        file_put_contents(XML_PATH.'/import.log', $logStr, FILE_APPEND | LOCK_EX);
}

function Getfloat($str) {
    $str = preg_replace("/[^0-9\,]/", "", $str);
    if(strstr($str, ",")) {
        $str = str_replace(",", ".", $str);
    }
    return floatval($str); // take some last chances with floatval

}



