<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Mail\Mailables\Attachment;

class VehiclesCsvMail extends Mailable
{
    use Queueable, SerializesModels;

    public $filePath;

    /**
     * Create a new message instance.
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
        Log::info("VehiclesCsvMail instance created with filePath: {$filePath}");
    }
    
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        Log::info('VehiclesCsvMail envelope method called.');
        return new Envelope(
            subject: 'Vehicles Csv Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        Log::info('VehiclesCsvMail content method called.');
        return new Content(
            markdown: 'emails.vehicles_csv',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        Log::info("VehiclesCsvMail attachments method called. Attaching file: {$this->filePath}");
        
        return [
            Attachment::fromStorage($this->filePath)
                ->as('vehicles_result.csv')
                ->withMime('text/csv'),
        ];
    }
}
