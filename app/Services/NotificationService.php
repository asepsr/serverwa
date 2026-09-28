<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public function sendToCustomer(int $customerId, string $type, string $message): Notification
    {
        return Notification::create([
            'customer_id' => $customerId,
            'type' => $type,
            'message' => $message,
            'is_read' => false,
        ]);
    }

    public function markAsRead(int $notificationId): bool
    {
        $notification = Notification::findOrFail($notificationId);
        $notification->update(['is_read' => true]);
        return true;
    }
}
