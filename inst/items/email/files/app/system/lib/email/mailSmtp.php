<?php

namespace electronic\lib\email;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use electronic\core\config\config;
use electronic\lib\email\mailLayout;

class mailSmtp
{

    use mailLayout;

    private $alternative;
    private $attachment;
    private $replyTo;
    private $body;
    private $subject;
    private $fromMail;
    private $fromName;
    private $user; 
    private $mailUser;     

    public function attachment(array $attachment)
    {
        $this->attachment = $attachment;
        return $this;
    }

    public function alternative(string $alternative)
    {
        $this->alternative = htmlspecialchars_decode($alternative);
        return $this;
    }

    public function reply(string $replyTo)
    {
        $this->replyTo = $replyTo;
        return $this;
    }

    public function subject(string $subject)
    {
        $this->subject = htmlspecialchars_decode($subject);
        return $this;
    }

    public function body(string $body)
    {
        $this->body = htmlspecialchars_decode($body);
        return $this;
    }

    public function fromName($name)
    {
        $this->fromName = htmlspecialchars_decode($name);
        return $this;
    }


    public function send($mailUser, $name, $subject = '', $body = '', $alternative = '', $attachment = null)
    {
        $this->mailUser = $mailUser;
        $name = htmlspecialchars_decode($name);
        $subject = htmlspecialchars_decode($name);

        ob_start();
        $mail = new PHPMailer();

        $mail->isSMTP();
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->Host = config::mail('host');
        $mail->Port = config::mail('port');
        $mail->SMTPAuth = true;
        $mail->Username = config::mail('userName');
        $mail->Password = config::mail('password');
        $mail->CharSet = "utf-8";

        if ($this->fromName) {
            $mail->setFrom(config::mail('fromEmail'), htmlspecialchars_decode($this->fromName)); // от кого (email и имя)
        } else {
            $mail->setFrom(config::mail('fromEmail'), config::globals('siteName')); // от кого (email и имя)
        }

        if ($this->replyTo) {
            $mail->addReplyTo($this->replyTo);
        }
        
        $mail->addAddress($mailUser, $name); // кому (email и имя)

        $mail->Subject = $this->subject ? $this->subject : $subject;
        $mail->msgHTML($this->body);
        $mail->AltBody = $this->alternative;

        if ($this->attachment) {
            foreach ($this->attachment as $i) {
                $mail->addAttachment($i);
            }
        }

        $mail->preSend();
        $log = date('Y-m-d H:i');
        // $log .= PHP_EOL . $mail->getSentMIMEMessage() . PHP_EOL;

        // Отправляем сообщение
        if (!$mail->send()) {
            $log .= ' | ERROR |' .  $mail->ErrorInfo ;
        } else {
            $log .= ' | INFO |' .  $this->subject ;
        }

        $log .= PHP_EOL;

        $content = ob_get_contents();
        ob_end_clean();
        // dump($content);
        $log .= $this->body . PHP_EOL;

        if (config::mail('logs') == '1') {
            createDir(APP . '/cache/logs');
            file_put_contents(APP . '/cache/logs/mail_smtp-' . date('Y-m') . '.log', $log, FILE_APPEND);
        }
    }
}
