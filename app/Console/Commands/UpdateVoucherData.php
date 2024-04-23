<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Voucher;
use SimpleXMLElement;

class UpdateVoucherData extends Command
{
    protected $signature = 'vouchers:update-data';
    protected $description = 'Update vouchers data from XML content';

    public function handle()
    {
        $vouchers = Voucher::all();
    
        foreach ($vouchers as $voucher) {
            try {
                $xml = new SimpleXMLElement($voucher->xml_content);
                // Registra los namespaces si están presentes en el XML
                $xml->registerXPathNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
                
                // Asegúrate de que las rutas y namespaces en xpath() correspondan a tu estructura XML.
                $invoiceSeries = (string)$xml->xpath('//cac:Invoice/cbc:ID')[0] ?? null;
                $invoiceNumber = (string)$xml->xpath('//cac:Invoice/cbc:ID')[1] ?? null;
                $invoiceType = (string)$xml->xpath('//cac:Invoice/cbc:InvoiceTypeCode')[0] ?? null;
                $currency = (string)$xml->xpath('//cac:Invoice/cbc:DocumentCurrencyCode')[0] ?? null;                
                
                $voucher->update([
                    'invoice_series' => $invoiceSeries,
                    'invoice_number' => $invoiceNumber,
                    'invoice_type' => $invoiceType,
                    'currency' => $currency,
                ]);
            } catch (\Exception $e) {
                $this->error("Error al actualizar el voucher {$voucher->id}: {$e->getMessage()}");
                continue; // O manejar de otra manera
            }
        }
    
        $this->info('Los datos del voucher se actualizaron correctamente.');
    }
}
