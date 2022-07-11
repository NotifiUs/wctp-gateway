# Basic Installation Steps

**Provision Server**

- Install git
- Install nginx
- Install php/php-fpm
- Install mysql
- Install redis
- Install supervisor
- Install certbot
- Install SSL/TLS
- Push code
- Edit .env file
- Migrate database
- Setup horizon (queue)
- Setup cron job

**Web Application**

- Create initial user `php artisan make:admin`
- Login to web portal
- Add carrier
- Enable carrier
- Add enterprise host (IS WCTPWeb endpoint)
- Enable enterprise host
- Add available number
- Setup available number
- Enable available number

**WCTP Client (IS Server)**

- Add a new terminal
- Configure based on settings in Application
- Use WCTP terminal within a virtual terminal
- Send SMS using the virtual terminal
- Validate if TLS 1.2 works with IS outbound

**WCTP Client (IS WCTP Web)**

- Install IS WCTP Web
- Configure web.config to use provider name from terminal setup
- Start receiving messages/replies
