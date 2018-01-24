<?php

namespace dastanaron\extension;

class IMAPParse
{
    /**
     * @var resource
     */
    public $stream;

    /**
     * IMAPParse constructor.
     * @param $email
     * @param $password
     * @param $descriptor
     */
    public function __construct($email, $password, $descriptor)
    {
        $this->stream = imap_open($descriptor, $email, $password);
    }

    /**
     * Определяем количество писем, их них новых и т.п.
     * @return object
     */
    public function check()
    {
        return imap_check($this->stream);
    }

    /**
     * Парсим письма по критерию и раскладываем в массив
     * @param string $criteria
     * @param bool $download
     * @return array|bool
     */
    public function parseMails($criteria = 'NEW', $download=false)
    {
        $mails = imap_search($this->stream, $criteria);

        $array = array();

        if($mails){

            foreach($mails as $num_mail){

                $array[$num_mail]['header'] = $this->getHeader($num_mail);

                $array[$num_mail]['sender'] = $this->getSender($array[$num_mail]['header']);

                $array[$num_mail]['subject'] = $this->getSubject($array[$num_mail]['header']);

                $array[$num_mail]['subject'] = $this->getSubject($array[$num_mail]['header']);

                $array[$num_mail]['body'] = $this->getBody($num_mail);

                $array[$num_mail]['attache'] = $this->getAttaches($num_mail, $download);


            }

            return $array;

        }
        else{

            return false;

        }
    }

    /**
     * Получает заголовки
     * @param $msgNumber
     * @return object
     */
    protected function getHeader($msgNumber)
    {
        return imap_header($this->stream, $msgNumber);
    }

    /**
     * Из заголовка получает отправителя
     * @param $header
     * @return string
     */
    protected function getSender($header)
    {
        return $header->sender[0]->mailbox . "@" . $header->sender[0]->host;
    }

    /**
     * Из заголовка получает тему письма и декодирует ее
     * @param $header
     * @return string
     */
    protected function getSubject($header)
    {
        return imap_utf8 ($header->subject);
    }

    /**
     * Получает тело письма, без вложений
     * @param $msgNumber
     * @return string
     */
    protected function getBody($msgNumber)
    {
        return imap_fetchbody($this->stream, $msgNumber, 1);
    }

    /**
     * Получение массива вложений по номеру письма
     * @param $msgNumber
     * @param bool $download_file
     * @param bool $show_file
     * @return array|bool
     */
    public function getAttaches($msgNumber, $download_file = false, $show_file = false)
    {
        $structure = imap_fetchstructure($this->stream, $msgNumber);

        $array = array();

        if(isset($structure->parts)) {
            $i = 0;
            foreach($structure->parts as $part) {
                if($this->isAttache($part)) {

                    $array[$i]['filename'] = $this->getAttacheFilename($part);
                    $array[$i]['section'] = $i+1;

                    if($show_file) $array[$i]['file'] = $this->getAttache($msgNumber, $i+1);

                    if($download_file) {
                        $this->saveAttache($array[$i]['filename'], $msgNumber, $i+1);
                    }

                }
                $i++;
            }
            return $array;

        }
        else {
            return false;
        }

    }

    /**
     * Получение самого вложения в base64 по номеру письма и номеру секции
     * @param $msgNumber
     * @param $section
     * @return string
     */
    public function getAttache($msgNumber, $section)
    {
        return imap_fetchbody($this->stream, $msgNumber, $section);
    }

    /**
     * Альтернатива вышестоящей функции, только для статического вызова
     * @param $stream
     * @param $msgNumber
     * @param $section
     * @return string
     */
    protected static function getAttachStatic($stream, $msgNumber, $section)
    {
        return imap_fetchbody($stream, $msgNumber, $section);
    }

    /**
     * Является ли вложением в письмо
     * @param $part
     * @return bool
     */
    private function isAttache($part) {

        if(!isset($part->dparameters)) {
            return false;
        }

        foreach($part->dparameters as $object)
        {
            if(strtolower($object->attribute) == 'filename')
            {
                return true;
            }
        }

        return false;

    }

    /**
     * Получение названия файла вложения
     * @param $part
     * @return string
     */
    private function getAttacheFilename($part) {

        foreach($part->parameters as $object)
        {
            return imap_utf8($object->value);
        }
    }

    /**
     * Сохранение вложения(тестовая)
     * @param $filename
     * @param $msgNumber
     * @param $section
     * @return bool|int
     */
    public function saveAttache($filename, $msgNumber, $section)
    {
        return file_put_contents($filename, base64_decode($this->getAttache($msgNumber, $section)));
    }

    /**
     * Статическое сохранение файла вложения.
     * Принимает открытое соединение, название файла или путь до него, номер сообщения и номер секции.
     * Последние два параметра можно получить из массива отданного методом parseMails()
     * @param $imap_stream
     * @param $filename
     * @param $msgNumber
     * @param $section
     * @return bool|int
     */
    public static function saveAttacheFile($imap_stream, $filename, $msgNumber, $section) {
        return file_put_contents($filename, base64_decode(self::getAttachStatic($imap_stream, $msgNumber, $section)));
    }

    /**
     * Деструктор для закрытия соединения
     * IMAPParse destructor.
     */
    public function __destruct()
    {
        imap_close($this->stream);
    }


}