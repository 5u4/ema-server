<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
class Invitation extends Mailable
{
    use Queueable, SerializesModels;
    public $name, $friend, $item;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $friend, $item)
    {
        $this->name = $name;
        $this->friend = $friend;
        $this->item = $item;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.welcome');
    }
}
