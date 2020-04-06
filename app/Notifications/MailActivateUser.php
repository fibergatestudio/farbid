<?php

    namespace App\Notifications;

    use App\User;
    use Illuminate\Bus\Queueable;
    use Illuminate\Notifications\Messages\MailMessage;
    use Illuminate\Notifications\Notification;

    class MailActivateUser extends Notification
    {
        use Queueable;

        public $user;
        public $subject;

        public function __construct(User $user)
        {
            $this->user = $user;
            $this->subject = 'Поздравляем Вы зарегистрировались на сайте';
        }

        public function via($notifiable)
        {
            return ['mail'];
        }

        public function toMail($notifiable)
        {
            return (new MailMessage)
                ->view('mail.activate_account', [
                    'subject'       => $this->subject,
                    'site_url'      => config('app.url'),
                    'site_name'     => config('os_seo.settings.ru.site_name'),
                    'activate_link' => _r('account.activate', ['code' => $this->user->api_token])
                ])
                ->subject($this->subject);
        }

    }