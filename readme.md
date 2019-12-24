<p align="center"><img src="https://wctp.io/assets/images/phones.svg" width="200"></p>

## About WCTP Gateway

WCTP Gateway is a web application that implements the Carrier Gateway WCTP Actor roles. Ardently crafted for the [Amtelco](https://amtelco.com) community, you can use this application to run an Amtelco-compatible primary or backup SMS aggregator service. Send and recieve messages from your own [Twilio](https://twilio.com) and [ThinQ](https://thinq.com) accounts &mdash; just bring your API keys!

The modern WCTP engine brings the following features and more:

- Bring your own Twilio or ThinQ accounts
- Automatic carrier failover when using multiple carriers
- Sticky sending by recipient to favor specific carriers
- Create and manage Enterprise Hosts
- IP Whitelisting, MFA, and login notifications
- TLS/SSL required by default across the entire stack

## Status

We're in beta testing. If you'd like to help test, please email [support+wctp+beta@notifi.us](mailto:support+wctp+beta@notifi.us)

## Privacy

Sending PHI, PCI, or other similar private information over SMS [**SHOULD NOT BE CONSIDERED COMPLIANT FOR ANY REGULATORY PURPOSES**](https://support.twilio.com/hc/en-us/articles/223182008-Are-there-special-rules-for-campaigns-involving-health-information-)

From a message storage standpoint, we only keep the content of messages available as long as needed to complete conversations. The content itself is stored in an automaticaly expiring in-memory database ([redis.io](https://redis.io)) and never persisted to disk.

> To do: Encrypt message content even while stored in Redis and retrieve by key

Meta-data, such as SMS routing information (recipient, sender, date/time, etc.) are persisted into a normal database for metrics and analytics. 

## Technologies

WCTP Gateway is a [Laravel](https://laravel.com) web-application that implements a WCTP endpoint and administrative portal. This system cannot send SMS messages without a supported carrier (Telecom API provider) like Twilio or ThinQ. 

The web portal UI is built on [Bootstrap](https://getbootstrap.com) and uses [Font Awesome](https://fontawesome.com/) icons. 

You can setup and run Laravel in a wide-variety of environments, including Digital Ocean, Amazon, Azure, and just about any modern PHP web environment. Start from a single-server appliance setup and grow into a full blown load balancing and clustering setup.

You can also go *serverless*, and try out [Vapor](https://vapor.laravel.com/). 

## Contributing

Thank you for considering contributing to WCTP Gateway! The contribution guide is coming soon. In the meantime, please email [Patrick Labbett](mailto:patrick.labbett@notifi.us)

## Security Vulnerabilities

If you discover a security vulnerability within WCTP Gateway, please send an e-mail to [patrick.labbett@notifi.us](mailto:patrick.labbett@notifi.us). All security vulnerabilities will be promptly addressed.

## License

WCTP Gateway is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
