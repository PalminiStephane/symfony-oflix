# composer

## mise à jour

[doc](https://getcomposer.org/download/)

```bash
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"

Installer verified

php composer-setup.php
All settings correct for using Composer
Downloading...

Composer (version 2.5.3) successfully installed to: /var/www/html/Nazca/Symfo/composer.phar
Use it: php composer.phar

php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer
```
