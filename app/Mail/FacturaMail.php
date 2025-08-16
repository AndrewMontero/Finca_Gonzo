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
    public string $pdfBytes;

   
public function __construct(Factura $factura, string $pdfBinary) {
     $this->factura  = $factura->load('entrega.cliente');
        $this->pdfBytes = $pdfBinary;
}

    public function build()
    {
        $cliente = optional(optional($this->factura->entrega)->cliente);
        $subject = 'Factura #'.$this->factura->id.( $cliente->nombre ? ' - '.$cliente->nombre : '' );

        return $this->subject($subject)
            ->view('emails.factura')
            ->with(['factura' => $this->factura])
            ->attachData(
                $this->pdfBytes,
                'factura-'.$this->factura->id.'.pdf',
                ['mime' => 'application/pdf']
            );
    }
}
