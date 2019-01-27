<?php

namespace App\Events;

use App\Faq;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class FaqCreated implements ShouldBroadcast
{
    use SerializesModels;

    protected $question;
    protected $answer;

    public function __construct(Faq $faq)
    {
        $this->question = $faq->question;
        $this->answer   = $faq->answer;
    }

    public function broadcastWith()
    {
        return [
            'question' => $this->question,
            'answer'   => $this->answer,
        ];
    }

    public function broadcastAs()
    {
        return 'faq.created';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn()
    {
        return new Channel('public');
    }
}
