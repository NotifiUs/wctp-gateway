<p align="center"><img src="https://wctp.io/assets/images/phones.svg" width="200"></p>

<div align="center">


[![GitHub issues](https://img.shields.io/github/issues/NotifiUs/wctp-gateway)](https://github.com/NotifiUs/wctp-gateway/issues)
[![GitHub license](https://img.shields.io/github/license/NotifiUs/wctp-gateway)](https://github.com/NotifiUs/wctp-gateway/blob/master/LICENSE)
![GitHub All Releases](https://img.shields.io/github/downloads/NotifiUs/wctp-gateway/total)

</div>

## About WCTP Gateway

WCTP Gateway is a web application that implements the Carrier Gateway WCTP Actor roles needed to interface with the [Amtelco](https://amtelco.com) WCTP Intelligent Series SMS Aggregator interface.

Ardently crafted for the Amtelco and [NAEO](https://www.naeo.org) community, you can use this application to run an Amtelco-compatible primary or backup SMS aggregator service. 
Send and recieve messages from your own [Twilio](https://twilio.com) and [ThinQ](https://thinq.com) accounts &mdash; just bring your API keys!

The modern WCTP engine brings the following features and more:

- Bring your own Twilio or ThinQ accounts
- Automatic carrier failover when using multiple carriers
- Sticky sending by recipient to favor specific carriers
- Create and manage Enterprise Hosts
- IP Whitelisting, MFA, and login notifications
- TLS/SSL required by default across the entire stack

## Status

**Not production ready**

## Requirements

We use features that require the following tools and technologies:

- PHP 7.2+
- nginx
- redis-server
- mysql
- supervisord
- Linux ( We generally recommend Ubuntu for most users)

## Technologies

WCTP Gateway is a [Laravel](https://laravel.com) web-application that implements a WCTP endpoint and administrative portal. 
This system cannot send SMS messages without a supported carrier (Telecom API provider) like Twilio or ThinQ. 

The web portal UI is built on [Bootstrap](https://getbootstrap.com) and uses [Font Awesome](https://fontawesome.com/) icons. 

You can setup and run Laravel in a wide-variety of environments, including Digital Ocean, Amazon, Azure, and just about any modern PHP web environment. 
Start from a single-server appliance setup and grow into a full blown load balancing and clustering setup.

You can also go *serverless*, and try out [Vapor](https://vapor.laravel.com/). 

## Contributing

Thank you for considering contributing to WCTP Gateway! The contribution guide is coming soon. In the meantime, please email [Patrick Labbett](mailto:patrick.labbett@notifi.us)

## Security Vulnerabilities

If you discover a security vulnerability within WCTP Gateway, please send an e-mail to [patrick.labbett@notifi.us](mailto:patrick.labbett@notifi.us). All security vulnerabilities will be promptly addressed.

## License

WCTP Gateway is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
