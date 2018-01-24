Parser E-mail
===============================

This mini-library can be useful in different cases,
for example, to disperse applications and store them in the system,
or creating a ticket system, where the application should see the administrator or someone else

In order not to obscure for a long time, I will say that the whole class works on the library [IMAP](http://php.net/manual/ru/ref.imap.php),
and accordingly, some arguments are taken according to this documentation.

How to use
---------------------

We call an instance of the class, as an argument, we pass it a mailbox, to which we connect,
password from it and [descriptor](http://php.net/manual/ru/function.imap-open.php#refsect1-function.imap-open-parameters).

```php
// Descriptor is an example for yandex (third argument)
$imap = new IMAPParse ('YOUR EMAIL', 'YOUR PASSWORD', '{imap.yandex.ru:993/imap/ssl/novalidate-cert}INBOX');
```

Then everything depends on what you want.

**parseMails ($criteria = 'NEW', $download = false)**, the default parameters are specified in parentheses

If you need to spar mail from a specific sender, look at the example in example.php, there just, it's the most.
If you want to receive all the new messages, then as a criterion, send `NEW` or leave it blank.

If, however, you need something more complicated, look at the [documentation](http://php.net/manual/ru/function.imap-search.php) parameter `criteria`.

Saving attachment files and a good example, see the file [example.php](example.php)
