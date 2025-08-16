<?php

namespace App\Mail;

use App\Models\Factura;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FacturaGenerada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Factura $factura, public string $pdfBytes)
    {
        //
    }

    public function build()
    {
        $nombrePdf = "factura_{$this->factura->id}.pdf";

        return $this->subject("Factura #{$this->factura->id}")
            ->view('emails.factura') // una vista simple del cuerpo del email
            ->attachData($this->pdfBytes, $nombrePdf, [
                'mime' => 'application/pdf'
            ]);
    }
}
