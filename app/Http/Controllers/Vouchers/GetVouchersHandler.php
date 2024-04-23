<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Requests\Vouchers\GetVouchersRequest;
use App\Http\Resources\Vouchers\VoucherResource;
use App\Models\Voucher;
use App\Services\VoucherService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class GetVouchersHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {
    }

    public function __invoke(GetVouchersRequest $request): Response
    {
        $vouchers = $this->voucherService->getVouchers(
            $request->query('page'),
            $request->query('paginate'),
        );

        return response([
            'data' => VoucherResource::collection($vouchers),
        ], 200);
    }

    public function filter(Request $request)
    {
        $serie = $request->query('serie');
        $numero = $request->query('numero');
        $fechaInicio = $request->query('fecha_inicio');
        $fechaFin = $request->query('fecha_fin');
        
        $query = Voucher::query();
        
        if ($serie) {
            $query->where('invoice_series', $serie);
        }
        
        if ($numero) {
            $query->where('invoice_number', $numero);
        }
        
        if ($fechaInicio && $fechaFin) {
          $query->whereBetween('created_at', [
              Carbon::parse($fechaInicio)->startOfDay(), 
              Carbon::parse($fechaFin)->endOfDay()
          ]);
      }
      
        
        $vouchers = $query->get();
        
        return response()->json($vouchers);
    }

    public function getTotalAmounts()
    {
        $totalSoles = Voucher::where('currency', 'PEN')->sum('total_amount');
        $totalDollars = Voucher::where('currency', 'USD')->sum('total_amount');

        return response()->json([
            'total_soles' => $totalSoles,
            'total_dollars' => $totalDollars,
        ]);
    }

}
