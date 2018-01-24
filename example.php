<?php

//------------[Подключаем файл класса или с автолодера]--------------//
require 'IMAPParse.php';

use dastanaron\extension\IMAPParse;


//------------[Подключаемся к ящику]--------------//

//Дескриптор - пример для яндекса (третий аргумент)
$imap = new IMAPParse('ВАШ ЯЩИК', 'ВАШ ПАРОЛЬ', '{imap.yandex.ru:993/imap/ssl/novalidate-cert}INBOX');

//------------[Парсим по критерию]--------------//

//Массив собранных сообщений с ящика по критерию
$mails = $imap->parseMails('FROM "ЯЩИК_ОТ_КОГО"');

//------------[Сохраняем файлы]--------------//

//Выбираем папку для сохранения
$dir = __DIR__.'/attaches/';


//Создаем ее если не существует
if(!file_exists($dir)) {
    mkdir($dir, 0777, true);
}

//Перебираем массив полученных писем для получения данных и сохранения
foreach($mails as $msgNumber => $mail)
{
    //Проверяем есть ли вложения
    if($mail['attache'] !== false) {

        //Если есть раскладываем их и записываем, можно использовать file_put_contents, но тогда придется самим забирать вложения, по секции и номеру
        foreach($mail['attache'] as $attache) {

            IMAPParse::saveAttacheFile($imap->stream, $dir.$attache['filename'], $msgNumber, $attache['section']);

        }

    }


}

//Другой способ сохранить файлы, это вызвать parseMail со вторым аргументом true, но тогда он будет сохранен, в текущей папке выполнения скрипта
$mails = $imap->parseMails('FROM "ОТ_КОГО_ПИСЬМА"', true);

