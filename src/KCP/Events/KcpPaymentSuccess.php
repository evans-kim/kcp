<?php

namespace EvansKim\KCP\Events;

use EvansKim\KCP\KcpPayment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class KcpPaymentSuccess
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $payment;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(KcpPayment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
