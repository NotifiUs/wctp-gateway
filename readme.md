<p align="center"><img src="https://wctp.io/assets/images/phones.svg" width="200"></p>

<div align="center">

[![GitHub license](https://img.shields.io/github/license/notifius/wctp-gateway?color=blue)](https://github.com/NotifiUs/wctp-gateway/blob/master/LICENSE)
[![GitHub Release](https://img.shields.io/github/v/tag/NotifiUs/wctp-gateway)](https://github.com/NotifiUs/wctp-gateway/releases)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/3781c4b9d7b64ea0a5d7cdf8652e0723)](https://www.codacy.com/gh/NotifiUs/wctp-gateway/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=NotifiUs/wctp-gateway&amp;utm_campaign=Badge_Grade)

</div>

## About WCTP Gateway

WCTP Gateway is a web application that implements the Carrier Gateway WCTP Actor roles needed to interface with the [Amtelco](https://amtelco.com) WCTP Intelligent Series SMS Aggregator interface.

Ardently crafted for the Amtelco and [NAEO](https://www.naeo.org) community, you can use this application to run an Amtelco-compatible primary or backup SMS aggregator service using your own [Twilio](https://twilio.com) and [ThinQ](https://thinq.com) accounts &mdash; just bring your API keys!

The modern WCTP engine brings the following features and more:

- Create and manage Enterprise Host credentials
- Bring your own [Twilio](https://twilio.com), [ThinQ/Commio](https://thinq.com), or [Sunwire](https://sunwire.ca) accounts
- Support for using Twilio Messaging Services or Phone Numbers
- Carrier priority
- TLS/SSL required by default
- Email login notifications
- Work's out of the box with Twilio's WhatsApp integration
- A generic webhook API provider

**Coming Soon**

- Improved fail-over between carrier
- More telecom API providers
- MergeComm scripting integration for Amtelco ecosystems

## Status

The WCTP Gateway is operational for interacting with WCTP Enterprise Hosts and Transient Clients of all ecosystems.

The latest tagged version is: 

[![GitHub Release (latest by date)](https://img.shields.io/github/v/tag/NotifiUs/wctp-gateway)](https://github.com/NotifiUs/wctp-gateway/releases)

> Commercial installation and support is available through [NotifiUs, LLC](http://notifi.us)

## Requirements

We use application, language, and OS features that require the following tools and technologies:

- **Ubuntu Server** x64 linux 18.04 LTS or higher
- **`php` 8.1+** for web application scripting
- **nginx with `php-fpm`** for web server
- **`redis-server`** for caching/queue
- **`mysql`** for storage
- **`supervisord`** to watch queue processes

We utilize linux specific methods of obtaining data about the hardware/server (such as memory, disk space, CPU, etc.)
Because of this, we require the use of a linux host. We generally recommend Ubuntu for most users. 

## Technologies

WCTP Gateway is a [Laravel](https://laravel.com) web-application that implements a WCTP endpoint and administrative portal. 
This system cannot send SMS messages without a supported carrier (Telecom API provider) like Twilio or ThinQ. 

### Front End

The web portal UI is built on [Bootstrap](https://getbootstrap.com) with some [TailwindCSS](https://tailwindcss.com) sprinkled in.
We use [Font Awesome](https://fontawesome.com/) free for icons throughout the application. 
For various splash pages (errors, etc), we use open source illustrations from [UnDraw](https://undraw.co/illustrations).

### Laravel Application

You can setup and run Laravel in a wide-variety of environments, including Digital Ocean, Amazon, Azure, and just about any modern PHP web environment. 
Start from a single-server appliance setup and grow into a full blown load balancing and clustering setup.

Laravel [Forge](https://forge.laravel.com) is an easy to use server management tool to try out!

## Contributing

Thank you for considering contributing to WCTP Gateway! Please email [Patrick Labbett](mailto:patrick.labbett@notifi.us) or submit a PR!

## Security Vulnerabilities

Please see [SECURITY.md](SECURITY.md)

## License

WCTP Gateway is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Tested compatability list

-  Amtelco WCTP 2-Way Messaging API
-  InfoRad Messaging Gateway
-  NotePage PageGate / PageGate Platinum

## Carrier Drivers

Please see our guide to [creating a driver](driver-guide.md) for more information on creating a driver. 

> I recognize this is not the cleanest way to use drivers - a refactor is planned for next major version.
