<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;

class OrderStatusUpdated extends Mailable
{
    public Order $order;
    public string $oldStatus;

    public function __construct(Order $order, string $oldStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
    }

    public function build()
    {
        return $this
            ->subject('Cập nhật trạng thái đơn hàng #' . $this->order->id)
            ->view('emails.order-status-updated');
    }
}
