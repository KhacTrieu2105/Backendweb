<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactReplyMail extends Mailable
{
    use SerializesModels;

    public $contact;
    public $replyContent;

    public function __construct($contact, $replyContent)
    {
        $this->contact = $contact;
        $this->replyContent = $replyContent;
    }

    public function build()
{
    return $this->subject('Phản hồi liên hệ từ ' . config('app.name', 'Luxury Store'))
                ->view('contact-reply')  // ← Dùng view thay vì html()
                ->with([
                    'contact' => $this->contact,
                    'replyContent' => $this->replyContent,
                ]);
}
}
