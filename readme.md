
# WCTPd

An implementation of a basic WCTP carrier gateway for use with enterprise hosts.

## Localization

See `resources/lang/en.json` as an example. 

## Log out other sessions

    Auth::logoutOtherDevices($password);

## Features

### Web Interface
- Security
    - Minimum 8 characters
    - No Maximum
    - Support 2FA
        - TOTP (free)
    - Google Captcha
        - Site Key
        - Secret Key
    - Send emails whenever logging in from new IP
    - Send emails whenever password is changed
    - Send emails whenever account settings are changed
- Enter Twilio account information
- Enter ThinQ account information  
- Drag/Drop carrier to set priority

### Carriers
#### Twilio
- Account SID
- API token
- Enabled Features
   - Geo Match (local experience)
   - Scaler (distributed message sending)
   - Sticky Sender (same number for each recipient)
- Create a sub account for this
- Add numbers in to use them
- Add international numbers to support sending to that country 
- Setup webhook

#### ThinQ
- ThinQ username
- API token
- account_id
- Setup webhook

### WCTP Engine
#### Server (Sends to client)
- Message Replies - wctp-MessageReply
- Delivery Failures - wctp-StatusInfo
- Unsolicited Messages - wctp-SubmitRequest
- Server Capabilities - wctp-VersionQuery

Not implemented:

    wctp-LookupResponse
    wctp-DeviceLocationResponse
    

#### Client
- Message Replies - wctp-MessageReply
- Send message to multiple recipients - wctp-SendMsgMulti
- Unsolicited Messages - wctp-SubmitRequest
- Delivery Failures - wctp-StatusInfo
- Server Capabilities - wctp-VersionQuery

Not implemented:

    wctp-ReturnToSvc
    wctp-DeviceLocation
    wctp-LookupSubscriber

      


## License

This software is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
