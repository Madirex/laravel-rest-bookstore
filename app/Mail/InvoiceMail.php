<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $invoice;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $order, $invoice)
    {
        $this->order = $order;
        $this->invoice = $invoice;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Factura de compra',
        );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $invoiceData = file_get_contents($this->invoice);

        return $this->markdown('emails.invoice')
            ->with('order', $this->order)
            ->attachData($invoiceData, 'invoice.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
