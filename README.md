aesop
=====

A web app to create multimedia stories

Pre ALPHA upload at moment - still busy building deployment strategy.

Have a look at the demo: http://story.openpoint.ie

Dependencies:
mediainfo libav-tools youtube-dl imagemagick postfix npm

mod_rewrite is required

Apache2 virtualhost config to include:
<Directory /var/www/html/yourproject>
    Options Indexes FollowSymLinks MultiViews
    AllowOverride All
    Order allow,deny
    allow from all
</Directory>
