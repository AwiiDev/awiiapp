<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Admin\ServiceLocation;
use App\Models\Admin\Incentive;
use Illuminate\Validation\ValidationException;
use Grimzy\LaravelMysqlSpatial\Types\MultiPolygon;
use Illuminate\Support\Facades\Log;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Controllers\Controller;

class IncentiveController extends Controller
{
   
    public function index()
    {
        $dailyIncentives = Incentive::where('mode', 'daily')->orderBy('ride_count','ASC')->get();
        $weeklyIncentives = Incentive::where('mode', 'weekly')->orderBy('ride_count','ASC')->get();
    
        return inertia('pages/incentive/index', ['dailyIncentives' => $dailyIncentives,
            'app_for'=>env("APP_FOR"),
            'weeklyIncentives' => $weeklyIncentives]);
    } 
    public function update(Request $request)
    {
    // Retrieve the 'Daily' and 'Weekly' data from the request
    $daily = $request->Daily;
    $weekly = $request->Weekly;
    Incentive::whereIn('mode', ['daily','weekly'])->delete();

    // Handle 'Daily' incentives
    if (is_array($daily) && !empty($daily)) {
        // Remove all existing 'Daily' incentives

        // Create new 'Daily' incentives
        foreach ($daily as $item) {
            Incentive::create([
                'mode' => 'daily',
                'ride_count' => $item['ride_count'],
                'amount' => $item['amount'],
            ]);
        }
    }

    // Handle 'Weekly' incentives
    if (is_array($weekly) && !empty($weekly)) {
        // Remove all existing 'Weekly' incentives
        // Incentive::where('mode', 'weekly')->delete();

        // Create new 'Weekly' incentives
        foreach ($weekly as $item) {
            Incentive::create([
                'mode' => 'weekly',
                'ride_count' => $item['ride_count'],
                'amount' => $item['amount'],
            ]);
        }
    }

    // Return a success message or redirect
    return back()->with('success', 'Incentives updated successfully.');

    } 
}


