# WARNING!

This is a test task, don't use in production.

# Requirements

- nginx >= 1.8.0
- php >=5.5.9
- mysql >= 5.0

# Install

Clone source code to your www/ directory

Set php fastcgi_pass to your php in `givemeurl.ru.conf` - `location ~ \.php$`

# Initialize

 Write db settings to `config.yml` file

 Execute `migrations/givemeurl.ru.sql` in your mysql client

 If application installed to your local machine, add to your `/etc/hosts` string `127.0.0.1       givemeurl.ru`