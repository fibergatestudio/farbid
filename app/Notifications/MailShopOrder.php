<?php

    namespace App\Notifications;

    use App\Models\Desk;
    use App\Models\Money;
    use App\Models\ShopBuyOneClick;
    use App\Models\ShopOrder;
    use App\User;
    use Illuminate\Bus\Queueable;
    use Illuminate\Notifications\Messages\MailMessage;
    use Illuminate\Notifications\Notification;

    class MailShopOrder extends Notification
    {
        use Queueable;

        public $order;
        public $subject;

        public function __construct(ShopOrder $order)
        {
            $this->order = $order;
            $this->subject = 'Оформлен заказа на сайте';
        }

        public function via($notifiable)
        {
            return ['mail'];
        }

        public function toMail($notifiable)
        {
            return (new MailMessage)
                ->view('mail.order', [
                    'subject'   => $this->subject,
                    'site_url'  => config('app.url'),
                    'site_name' => config('os_seo.settings.ru.site_name'),
                    'item'      => $this->order
                ])
                ->subject($this->subject);
        }

    }