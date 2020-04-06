<?php

    namespace App\Providers;

    use DB;
    use Illuminate\Http\Request;
    use Illuminate\Support\ServiceProvider;

    class ConfigServiceProvider extends ServiceProvider
    {
        public function boot()
        {
        }

        public function register()
        {

//            if($settings_mail) {
//                $settings_mail = unserialize($settings_mail->value);
//                $this->app['config']->set('mail.driver', $settings_mail->mail_driver);
//                $this->app['config']->set('mail.host', $settings_mail->mail_host);
//                $this->app['config']->set('mail.port', $settings_mail->mail_port);
//                $this->app['config']->set('mail.encryption', $settings_mail->mail_encryption);
//                $this->app['config']->set('mail.from.name', $settings_mail->mail_from_name);
//                $this->app['config']->set('mail.from.address', $settings_mail->mail_from_address);
//                $this->app['config']->set('mail.support.address', $settings_mail->mail_to_address);
//                $this->app['config']->set('mail.cc', $settings_mail->mail_cc_address);
//                $this->app['config']->set('mail.username', $settings_mail->mail_user);
//                $this->app['config']->set('mail.password', $settings_mail->mail_password);
//            }
        }
    }
