<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AkunRantingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nama_pengurus;
    public $nama_organisasi;
    public $email_login;
    public $password_login;
    /**
     * Create a new message instance.
     */
    public function __construct($nama_pengurus, $nama_organisasi, $email_login, $password_login)
    {
        $this->nama_pengurus = $nama_pengurus;
        $this->nama_organisasi = $nama_organisasi;
        $this->email_login = $email_login;
        $this->password_login = $password_login;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Selamat! Akun SIM PAC Anda Telah Aktif',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.akun_ranting', // Kita akan buat file blade ini di Langkah 2
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
