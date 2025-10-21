<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $req)
    {
        $year     = (int)($req->input('year', now()->year));
        $month    = $req->input('month');
        $day      = $req->input('day');
        $material = $req->input('material');
        $unit     = $req->input('unit');
        $soType   = $req->input('so_type');
        $finOnly  = (int)$req->input('fin_ok', 1);

        $defaults = [
            'realisasiYTD'=>0,'targetYTD'=>0,'ytdPct'=>0,
            'realisasiMTD'=>0,'targetMTD'=>0,'mtdPct'=>0,
            'top10'=>collect(), 'others'=>0,
            'lsdLabels'=>['Expired','0 Week','1 Week','2 Week','3 Week','4 Week','5 Week','6 Week','7 Week','8 Week','9 Week','10 Week','11 Week','12 Week'],
            'lsdValues'=>array_fill(0,14,0.0),
            'years'=>collect([$year]), 'materials'=>collect(), 'units'=>collect(), 'soTypes'=>collect(),
        ];

        try {
            $hasSO = DB::getSchemaBuilder()->hasTable('sales_orders');
            $hasTarget = DB::getSchemaBuilder()->hasTable('target_sales');

            if (!$hasSO) {
                return view('dashboard', array_merge(compact('year','month','day','material','unit','soType','finOnly'), $defaults));
            }

            $base = DB::table('sales_orders')
                ->when($year,   fn($q)=>$q->where('year',$year))
                ->when($month,  fn($q)=>$q->where('month',$month))
                ->when($day,    fn($q)=>$q->where('day',$day))
                ->when($material, function($q) use ($material){
                    $q->where(function($x) use ($material){
                        $x->where('material_code',$material)->orWhere('material_name',$material);
                    });
                })
                ->when($unit,   fn($q)=>$q->where('unit',$unit))
                ->when($soType, fn($q)=>$q->where('so_type',$soType))
                ->when($finOnly===1, fn($q)=>$q->where('fin_doc_ok',1));

            $today = Carbon::today();
            $startYear = Carbon::create($year,1,1);
            $realisasiYTD = (clone $base)->whereBetween('order_date',[$startYear,$today])->sum('ton');

            $targetYTD = $hasTarget ? (int)DB::table('target_sales')
                ->where('year',$year)->whereNull('month')->where('scope','YTD')->value('target_value') : 0;

            $ytdPct = $targetYTD ? round($realisasiYTD/max($targetYTD,0.0001)*100,2) : 0;

            $m = $month ?: min(9, now()->month);
            $startMonth = Carbon::create($year,$m,1)->startOfMonth();
            $endMonth   = Carbon::create($year,$m,1)->endOfMonth();
            $realisasiMTD = (clone $base)->whereBetween('order_date',[$startMonth,min($endMonth,$today)])->sum('ton');

            $targetMTD = $hasTarget ? (int)DB::table('target_sales')
                ->where('year',$year)->where('month',$m)->where('scope','MTD')->value('target_value') : 0;

            $mtdPct = $targetMTD ? round($realisasiMTD/max($targetMTD,0.0001)*100,2) : 0;

            $top10 = (clone $base)->select('company_name', DB::raw('SUM(ton) as total_ton'))
                ->groupBy('company_name')->orderByDesc('total_ton')->limit(10)->get();

            $top10Total = (clone $base)->sum('ton');
            $others = max($top10Total - $top10->sum('total_ton'), 0);

            $lsdLabels = $defaults['lsdLabels'];
            $lsdValues = [];
            foreach ($lsdLabels as $i=>$label) {
                if ($label==='Expired') {
                    $v = (clone $base)->whereRaw("TIMESTAMPDIFF(DAY, CURDATE(), delivery_date) < 0")->sum('ton');
                } else {
                    $w = $i-1;
                    $v = (clone $base)->whereRaw("FLOOR(TIMESTAMPDIFF(DAY, CURDATE(), delivery_date)/7) = ?", [$w])->sum('ton');
                }
                $lsdValues[] = (float)$v;
            }

            $years     = DB::table('sales_orders')->select('year')->distinct()->orderBy('year')->pluck('year');
            $materials = DB::table('sales_orders')->select('material_name')->distinct()->orderBy('material_name')->pluck('material_name');
            $units     = DB::table('sales_orders')->select('unit')->distinct()->orderBy('unit')->pluck('unit');
            $soTypes   = DB::table('sales_orders')->select('so_type')->distinct()->orderBy('so_type')->pluck('so_type');

            return view('dashboard', compact(
                'year','month','day','material','unit','soType','finOnly',
                'realisasiYTD','targetYTD','ytdPct','realisasiMTD','targetMTD','mtdPct',
                'top10','others','lsdLabels','lsdValues','years','materials','units','soTypes'
            ));
        } catch (\Throwable $e) {
            return view('dashboard', array_merge(compact('year','month','day','material','unit','soType','finOnly'), $defaults));
        }
    }
}
