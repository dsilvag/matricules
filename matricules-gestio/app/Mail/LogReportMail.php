<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LogReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $logContent;
    public string $header;

    public function __construct(string $logContent, string $header)
    {
        $this->logContent = $logContent;
        $this->header = $header;
    }

    public function build()
    {
        \Log::info('Log content:', ['logContent' => $this->logContent]);

        return $this->subject($this->header)
                    ->markdown('emails.log_report')
                    ->with([
                        'logContent' => $this->logContent,
                    ]);
    }

}
