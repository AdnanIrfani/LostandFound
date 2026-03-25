<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MatchVerifiedNotification extends Notification
{
    use Queueable;

    public $otherUser;
    public $item;
    public $role;

    public function __construct($otherUser, $item, $role)
    {
        $this->otherUser = $otherUser;
        $this->item = $item;
        $this->role = $role; // 'lost' or 'found'
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Your item match has been verified!',
            'other_user' => [
                'name'  => $this->otherUser->name,
                'email' => $this->otherUser->email,
                'phone' => $this->otherUser->phone ?? 'Not provided',
            ],
            'item' => [
                'title' => $this->item->title,
                'category' => $this->item->category,
            ],
            'your_role' => $this->role,
        ];
    }
}
