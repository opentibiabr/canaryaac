<?php
/**
 * Validator class
 *
 * @package   CanaryAAC
 * @author    Lucas Giovanni <lucasgiovannidesigner@gmail.com>
 * @copyright 2022 CanaryAAC
 */

namespace App\Model\Functions;

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class FunMailer
{
    public static function connectMail()
    {
        $smtp = $_ENV['MAIL_SMTP'];
        $transport = Transport::fromDsn($smtp);
        $mailer = new Mailer($transport);
        return $mailer;
    }

    public static function envMailFuncWebsite($emailTo = null, $subject = null, $text = null, $html = null)
    {
        $mailer = self::connectMail();
        $emailFrom = $_ENV['MAIL_WEB'];

        $email = (new Email())
            ->from($emailFrom)
            ->to($emailTo)
            ->subject($subject)
            ->text($text)
            ->html($html);

        $mailer->send($email);
    }
}
