<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Http\Requests\StoreDailyReportRequest;
use App\Http\Requests\UpdateDailyReportRequest;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Carbon\Carbon;
use DB;

class ReportController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDailyOrderReport()
    {
        $title    = 'Report';
        $reports  = OrderProduct::whereDate('created_at', '=', Carbon::yesterday())->select(
            "id",
            "product_name",
            "product_id",
            "created_at",
            DB::raw("(CASE 
            WHEN SUM(product_quantity_phav) > 0 THEN SUM(product_quantity_phav) ELSE  0 END )as product_quantity_phav"),

            DB::raw("(CASE
            WHEN SUM(product_quantity_half_kg) > 0 THEN SUM(product_quantity_half_kg) ELSE 0 END ) as product_quantity_half_kg"),

            DB::raw("(CASE 
            WHEN SUM(product_quantity_kg)      > 0  THEN SUM(product_quantity_kg) ELSE 0 END ) as product_quantity_kg"),

            // DB::raw("(product_quantity_phavs/4)+(product_quantity_half_kgs/2)+(product_quantity_kgs) as TOTAL_SUM"),
        )->groupBy("product_id")
            ->get();
        foreach ($reports as $key => $value) {
            $value->TOTAL_SUM = ($value->product_quantity_phav / 4) + ($value->product_quantity_half_kg / 2) + ($value->product_quantity_kg);
        }
        // dd($reports[0]->TOTAL_SUM);
        $data     = compact('title', 'reports');
        return view('admin.reports.daily-order-index', $data);
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexDailyPurchaseReport()
    {
        $title    = 'report';
        $reports  = DailyReport::get()->groupBy(function ($item) {
            return $item->created_at->format('Y-m-d');
        });
        $data     = compact('title', 'reports');
        return view('admin.reports.daily-index', $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function editDailyPurchaseReport($date)
    {
        $title    = 'Report';
        $reports  = DailyReport::whereDate('created_at', $date)->get();
        $data     = compact('title', 'reports', 'date');
        return view('admin.reports.daily-edit', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createDailyPurchaseReport()
    {
        $title     =  'report';
        $products  =  Product::all();
        $data      =  compact('products', 'title');
        return view('admin.reports.daly-create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreDailyReportRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function storeDailyPurchaseReport(StoreDailyReportRequest $request)
    {
        try {
            $check_daily_report = DailyReport::whereDate('created_at', Carbon::today())->count();
            if ($check_daily_report > 0) {
                return redirect()->back()->with('Failed', 'Your today report already submited.');
            }
            foreach ($request->product_id as $key => $value) {
                if ($request->product_buy_price[$value] != '') {
                    $daily_reports                           = new DailyReport();
                    $daily_reports->product_id               = $value;
                    $daily_reports->product_name             = $request->product_name[$value];
                    $daily_reports->product_buy_price        = $request->product_buy_price[$value];
                    $daily_reports->product_selling_price    = $request->product_selling_price[$value];
                    $daily_reports->save();
                }
            }

            return redirect()->route('admin.daily.purchase.reports.index')->with('success', 'Daily Price Save successfully');
        } catch (\Throwable $e) {
            \DB::rollback();
            return redirect()->back()->with('Failed', $e->getMessage() . ' on line ' . $e->getLine());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DailyReport  $dailyReport
     * @return \Illuminate\Http\Response
     */
    public function show(DailyReport $dailyReport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DailyReport  $dailyReport
     * @return \Illuminate\Http\Response
     */
    public function edit(DailyReport $dailyReport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDailyReportRequest  $request
     * @param  \App\Models\DailyReport  $dailyReport
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDailyReportRequest $request, DailyReport $dailyReport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DailyReport  $dailyReport
     * @return \Illuminate\Http\Response
     */
    public function destroy(DailyReport $dailyReport)
    {
        //
    }
}
