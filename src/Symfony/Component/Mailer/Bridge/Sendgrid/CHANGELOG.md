CHANGELOG
=========

4.4.0
-----

 * [BC BREAK] Renamed and moved `Symfony\Component\Mailer\Bridge\Sendgrid\Http\Api\SendgridTransport`
   to `Symfony\Component\Mailer\Bridge\Sendgrid\Transport\SendgridApiTransport`, `Symfony\Component\Mailer\Bridge\Sendgrid\Smtp\SendgridTransport`
   to `Symfony\Component\Mailer\Bridge\Sendgrid\Transport\SendgridSmtpTransport`.
 * The "mail_settings" property can be set when using the SendgridApiTransport

4.3.0
-----

 * Added the bridge
