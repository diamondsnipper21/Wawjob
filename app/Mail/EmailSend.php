<?php

namespace iJobDesk\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailSend extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $subject;
    public $content;

    public $from_email;
    public $from_name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $content, $from_email = null, $from_name = null)
    {
        //
        $this->subject = $subject;
        $this->content = $content;

        $this->from_email = $from_email;
        $this->from_name = $from_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        if ($this->from_email && $this->from_name)
            return $this->from($this->from_email, $this->from_name)
                        ->subject($this->subject)
                        ->view('emails.send', ['content' => $this->content]);
        else
            return $this->subject($this->subject)
                        ->view('emails.send', ['content' => $this->content]);
    }
}
