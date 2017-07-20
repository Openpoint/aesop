aesop
=====

A web app to create multimedia stories

Pre ALPHA upload at moment - still busy building deployment strategy.

Have a look at the demo: http://story.openpoint.ie

Dependencies:

`sudo apt-get install mediainfo libav-tools youtube-dl imagemagick postfix npm`

Also: postgresql >=9

Apache2 mod_rewrite is required 

`sudo a2enmod rewrite`

Apache2 virtualhost config to include:

```html
<Directory /var/www/html/yourproject>
    Options Indexes FollowSymLinks MultiViews
    AllowOverride All
    Order allow,deny
    allow from all
</Directory>
```
Clone, cd into app directory and set permissions:
```bash
sudo chown -R www-data:www-data utils
sudo chown -R www-data:www-data static/resources
```

Spin it up in a web browser and follow the install insructions
