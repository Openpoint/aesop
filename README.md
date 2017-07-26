aesop
=====

A web app to create multimedia stories

Pre ALPHA upload at moment - still busy building deployment strategy.

Have a look at the demo: http://story.openpoint.ie

Dependencies:

`sudo apt-get install mediainfo libav-tools python imagemagick postfix npm`

Also: postgresql >=9

Apache2 mod_rewrite is required

`sudo a2enmod rewrite`

Apache2 virtualhost config to include:

```html
DocumentRoot /var/www/html/aesop/app
<Directory /var/www/html/aesop/app>
    Options Indexes FollowSymLinks MultiViews
    AllowOverride All
    Order allow,deny
    allow from all
</Directory>
```
Clone the repo, cd into root directory and set permissions:

```bash
sudo chown -R www-data:www-data app/static/resources utils log
```
Install the dependecies:

```bash
cd app
npm install
```

Spin it up in a web browser and follow the install insructions
