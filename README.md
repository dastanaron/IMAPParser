Парсер E-mail - писем
===============================

English [here](README_en.md)

Данная мини-билиотечка может пригодиться в разных случаях, 
например для распарсивания заявок и складирования их в систему, 
или создание тикет систем, где заявку должен увидеть администратор или кто-то другой

Чтобы долго не обхяснять, скажу, что весь класс работает на библиотеке [IMAP](http://php.net/manual/ru/ref.imap.php),
и соответственно, некоторые аргументы принимаются согласно этой документации.

Как пользоваться
---------------------
Вызываем экземпляр класса, в качестве аргумента, передаем ему почтовый ящик, к которому подключаемся, 
пароль от него и [дескриптор](http://php.net/manual/ru/function.imap-open.php#refsect1-function.imap-open-parameters).

```php
//Дескриптор - пример для яндекса (третий аргумент)
$imap = new IMAPParse('ВАШ ЯЩИК', 'ВАШ ПАРОЛЬ', '{imap.yandex.ru:993/imap/ssl/novalidate-cert}INBOX');
```

Дальше все зависит от того, что вы хотите.

**parseMails($criteria = 'NEW', $download=false)**, в скобках указаны параметры по умолчанию

Если нужно спарсить письма от конкретного отправителя, посмотрите пример в example.php, там как раз, оно самое.
Если же нужно получить все новые сообщения, то в качестве критерия отправьте `NEW` или оставьте пустым.

Если же, нужно что-то посложнее, посмотрите [документацию](http://php.net/manual/ru/function.imap-search.php) параметр `criteria`.

Сохранение файлов вложений и хороший пример, смотрите в файле [example.php](example.php)