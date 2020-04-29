# Social-Network-v2 #

### Database Info ###

---

### .htaccess ###
```
Options All -Indexes
RewriteEngine On

RewriteRule ^([a-zA-Z0-9_-]+)$ profile.php?profile_username=$1
RewriteRule ^([a-zA-Z0-9_-]+)/$ profile.php?profile_username=$1

RewriteBase /

RewriteRule ^index\$ – [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
```
