<?php

namespace App\Jobs;

use App\Mail\LogReportMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class SendOracleEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $logContent;
    protected string $type;

    public function __construct(string $logContent, string $type)
    {
        $this->logContent = $logContent;
        $this->type = $type;
    }

    public function handle(): void
    {
        Mail::to( env('MAIL_DESTINATARI'))->send(new LogReportMail($this->logContent,'Informe d\'Errors - Log de la Importació '. $this->type));
    }
}
