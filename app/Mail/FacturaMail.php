<?php

namespace App\Mail;

use App\Models\Factura;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FacturaMail extends Mailable
{
    use Queueable, SerializesModels;

    public Factura $factura;
    public string $pdfBinary;

    public function __construct(Factura $factura, string $pdfBinary)
    {
        $this->factura   = $factura;
        $this->pdfBinary = $pdfBinary;
    }

    public function build(): self
    {
        $nombrePdf = 'factura-' . $this->factura->id . '.pdf';

        return $this->subject('Factura #' . $this->factura->id)
            ->view('facturas.email') // vista simple del cuerpo del correo
            ->attachData($this->pdfBinary, $nombrePdf, ['mime' => 'application/pdf']);
    }
}
