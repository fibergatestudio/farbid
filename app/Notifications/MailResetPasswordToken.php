<?php

    namespace App\Notifications;

    use Illuminate\Bus\Queueable;
    use Illuminate\Notifications\Messages\MailMessage;
    use Illuminate\Notifications\Notification;

    class MailResetPasswordToken extends Notification
    {
        use Queueable;

        public $token;
        public $subject;

        public function __construct($token)
        {
            $this->token = $token;
            $this->subject = 'Сброс пароля на сайте';
        }

        public function via($notifiable)
        {
            return ['mail'];
        }

        public function toMail($notifiable)
        {
            return (new MailMessage)
                ->view('mail.reset_password_account', [
                    'subject'    => $this->subject,
                    'site_url'   => config('app.url'),
                    'site_name'  => config('os_seo.settings.ru.site_name'),
                    'reset_link' => _u("password/reset/{$this->token}")
                ])
                ->subject($this->subject);
        }
    }