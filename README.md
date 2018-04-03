## OpenCart 3 payment module

### Installation

* Backup your webstore and database
* Upload the module file [opencart-3-aldrapay-payment-module.ocmod.zip](https://github.com/aldrapay/opencart-3-payment-module/raw/master/opencart-3-aldrapay-payment-module.ocmod.zip) via _Extensions_ -> _Extension Installer_
* Activate the module in payment extensions (_Extensions_ -> _Payments_)
* Configure the module settings:
  * Merchant Id
  * Pass Code (secret key)
  * Encryption Algorithm (usually default unless upgrade requested or instructed otherwise)
  * Gateway domain (usually _secure.aldrapay.com_)
  * Order statuses for successfuly processed payment and for failed one
  * Enable the module, by setting  the `Status` option to `Enabled`
  * And optionally setup sort order id if you want to move the payment option higher level in payment method list

### Notes

Tested and developed with OpenCart v.3.0.2.0

### Troubleshooting

If you hosting service doesn't provide a FTP access, most probably you will have to install [the extension](http://www.opencart.com/index.php?route=extension/extension/info&extension_id=18892) before installing the payment module.

Alternatively you can just upload the _upload_ directory content to your opencart
installation directory.

### Demo credentials

You are free to register a merchant test account at https://secure.aldrapay.com/backoffice/register.html

Use the test data to make a test payment from https://secure.aldrapay.com/backoffice/docs/api/testing.html#test-cards

### Contributing

Issue pull requests or send feature requests or open [a new issue]( https://github.com/aldrapay/opencart-3-payment-module/issues/new)

