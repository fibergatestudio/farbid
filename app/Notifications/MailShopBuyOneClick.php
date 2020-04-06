<?php

    namespace App\Notifications;

    use App\Models\Desk;
    use App\Models\Money;
    use App\Models\ShopBuyOneClick;
    use App\User;
    use Illuminate\Bus\Queueable;
    use Illuminate\Notifications\Messages\MailMessage;
    use Illuminate\Notifications\Notification;

    class MailShopBuyOneClick extends Notification
    {
        use Queueable;

        public $order;
        public $subject;

        public function __construct(ShopBuyOneClick $order)
        {
            $this->order = $order;
            $this->subject = 'Заявка на покупку товара из формы "Купить в 1 клик"';
        }

        public function via($notifiable)
        {
            return ['mail'];
        }

        public function toMail($notifiable)
        {
            return (new MailMessage)
                ->view('mail.buy_one_click', [
                    'subject'   => $this->subject,
                    'site_url'  => config('app.url'),
                    'site_name' => config('os_seo.settings.ru.site_name'),
                    'item'      => $this->order
                ])
                ->subject($this->subject);
        }

    }