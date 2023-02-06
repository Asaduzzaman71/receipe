<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WithdrawRequestNotification extends Notification
{
    use Queueable;

    public  $user,$withdrawRequest;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user,$withdrawRequest)
    {
        $this->user=$user;
        $this->withdrawRequest=$withdrawRequest;


    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }



    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => "Balance Withdraw Request",
            'notification_data'=> $this->user,
            'notification_type'=>"WithdrawBalance",
            'url' =>route('pending_withdraw_requests')
        ];
    }
}
