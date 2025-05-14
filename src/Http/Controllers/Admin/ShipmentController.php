<?php

namespace Wontonee\Shiprocket\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Wontonee\Shiprocket\DataGrids\ShipmentDataGrid;

class ShipmentController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Display a listing of shipments
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('shiprocket::admin.sales.shipments.index');
    }
    
    /**
     * Returns a listing of shipments.
     *
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
        return app(ShipmentDataGrid::class)->toJson();
    }
}
