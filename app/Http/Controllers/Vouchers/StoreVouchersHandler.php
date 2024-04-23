<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Resources\Vouchers\VoucherResource;
use App\Models\Voucher;
use App\Services\VoucherService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use SimpleXMLElement;

class StoreVouchersHandler
{
  public function __construct(private readonly VoucherService $voucherService)
  {
  }

  public function __invoke(Request $request): Response
  {
    try {
      $xmlFiles = $request->file('files');

      if (!is_array($xmlFiles)) {
        $xmlFiles = [$xmlFiles];
      }

      $xmlContents = [];
      foreach ($xmlFiles as $xmlFile) {
        $xmlContents[] = file_get_contents($xmlFile->getRealPath());
      }

      $user = auth()->user();
      $vouchers = $this->voucherService->storeVouchersFromXmlContents($xmlContents, $user);

      return response([
        'data' => VoucherResource::collection($vouchers),
      ], 201);
    } catch (Exception $exception) {
      return response([
        'message' => $exception->getMessage(),
      ], 400);
    }
  }


  public function update(Request $request, $voucherId)
  {
    $voucher = Voucher::findOrFail($voucherId);

    if ($voucher->xml_content) {
      $xml = new SimpleXMLElement($voucher->xml_content);
      $xml->registerXPathNamespace('cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');

      $seriesNodes = $xml->xpath('//cbc:ID');
      $invoiceSeries = $seriesNodes ? (string)$seriesNodes[0] : null;
      $invoiceNumber = isset($seriesNodes[1]) ? (string)$seriesNodes[1] : null;

      $typeNodes = $xml->xpath('//cbc:InvoiceTypeCode');
      $invoiceType = $typeNodes ? (string)$typeNodes[0] : null;

      $currencyNodes = $xml->xpath('//cbc:DocumentCurrencyCode');
      $currency = $currencyNodes ? (string)$currencyNodes[0] : null;

      $voucher->update([
        'invoice_series' => $invoiceSeries,
        'invoice_number' => $invoiceNumber,
        'invoice_type' => $invoiceType,
        'currency' => $currency,
      ]);

      return response()->json(['message' => 'Voucher actualizado correctamente.']);
    } else {
      return response()->json(['message' => 'No hay contenido XML para actualizar.'], 422);
    }
  }

  public function destroy($id)
  {
    try {
      $voucher = Voucher::findOrFail($id);
      $voucher->delete();
      return response()->json(['message' => 'Voucher eliminado correctamente.'], 200);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      return response()->json(['message' => 'Voucher no encontrado.'], 404);
    } catch (\Exception $e) {
      return response()->json(['message' => 'Error al eliminar el voucher.'], 500);
    }
  }

}
