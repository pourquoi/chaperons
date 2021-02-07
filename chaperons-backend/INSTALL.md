Installation docker
---
```shell
APP_HOST=localhost APP_PORT=80 docker-compose up -d
```

```shell
docker exec -it cartochaperons_api bin/up.sh
docker exec -it cartochaperons_api bin/console app:create-user admin
```

```shell
curl -I http://127.0.0.1:8899/api/doc
```

Installation sur une debian 8 vierge
---

Créér un compte

```shell
adduser chaperons
usermod -G sudo chaperons
su chaperons
cd
```

(Optionnel) Créer une clé ssh et l'ajouter au serveur git

```shell
ssh-keygen
```

Ajouter les sources dotdeb dans ```/etc/apt/sources.list```. Remplacer *jessie* par la distribution debian. 
Le nom de la distribution se retrouve avec ```cat /etc/*-release```

```
deb http://packages.dotdeb.org jessie all
deb-src http://packages.dotdeb.org jessie all
```

Ajouter la clé pour les paquets dotdeb

```shell
gpg --keyserver keys.gnupg.net --recv-key 89DF5277
gpg -a --export 89DF5277 | sudo apt-key add -
sudo apt-get update
```

Télécharger le code

```shell
sudo apt-get install git
git clone git@gitlab.com:pourquoi/carto-chaperons-api.git
cd carto-chaperons-api
```

Créér la base de donnée

```shell
sudo apt-get install mysql-server
mysql -p
```

```sql
CREATE DATABASE `chaperons` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_bin */;
CREATE USER 'chaperons'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON chaperons.* TO 'chaperons'@'localhost';
exit
```

Créér les procedures

```shell
mysql -u chaperons -p
```

```sql
use chaperons
source src/App/update_close_nurseries.sql
exit
```

Installer [nodejs](https://nodejs.org/en/download/package-manager/#debian-and-ubuntu-based-linux-distributions) et [phantomjs](http://phantomjs.org/) pour le rendu des cartes

```shell
curl -sL https://deb.nodesource.com/setup_6.x | sudo -E bash -
sudo apt-get install -y nodejs
sudo npm install -g phantomjs
```

Installer php7 et nginx

```shell
sudo apt-get update
sudo apt-get install php7.0 php7.0-fpm php7.0-xml php7.0-mysql php7.0-curl php7.0-mbstring
sudo apt-get remove apache2
sudo apt-get install nginx-full
sudo apt-get install unzip
```

Installer [composer](https://getcomposer.org/download/)

```shell
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
sudo php composer-setup.php -- --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php
```

Installer les librairies php et configurer les paramètres de

```shell
composer install
```

Créér les tables ```bin/console doctrine:schema:create```

Créér le dossier des rendus de carte ```mkdir web/maps```

Configurer les permissions

```
sudo setfacl -R -m u:"www-data":rwX -m u:`whoami`:rwX var
sudo setfacl -dR -m u:"www-data":rwX -m u:`whoami`:rwX var
sudo setfacl -R -m u:"www-data":rwX -m u:`whoami`:rwX web/maps
sudo setfacl -dR -m u:"www-data":rwX -m u:`whoami`:rwX web/maps
```

Copier la config nginx et changer le server_name

```
sudo cp doc/template-nginx.conf /etc/nginx/sites-available/carto-chaperons-api
sudo ln -s /etc/nginx/sites-available/carto-chaperons-api /etc/nginx/sites-enabled/
sudo vim /etc/nginx/sites-available/carto-chaperons-api
sudo service nginx restart
```

```
curl -I http://[SERVER_NAME]:8000/api/doc
```