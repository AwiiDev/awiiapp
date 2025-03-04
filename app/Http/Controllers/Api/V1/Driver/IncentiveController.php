<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Admin\Incentive;
use App\Models\Payment\DriverIncentiveHistory;
use App\Models\Request\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * @group Driver Earnings
 * @authenticated
 * APIs for Driver's Earnings
 */
class IncentiveController extends BaseController
{
    /**
     * List Incentive
     * 
     * This function retrieves the daily incentive history for the authenticated driver
     * over the past 25 days (from the start date to the current date). It aggregates
     * earned incentives and provides details about upcoming incentives, including their
     * ride count requirements and completion status for each day.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the incentive history.
     *
    * @response
    * {
    *    "success": true,
    *    "message": "incentive_history_listed",
    *    "data": {
    *       "incentive_history": [
    *           {
    *               "month": "Nov",
    *               "date": "09",
    *               "total_incentive_earned": 100,
    *               "earn_upto": 200,
    *               "upcoming_incentives": [
    *                   {
    *                       "ride_count": 5,
    *                       "incentive_amount": 50,
    *                       "is_completed": true
    *                   },
    *                   {
    *                       "ride_count": 10,
    *                       "incentive_amount": 150,
    *                       "is_completed": false
    *                   }
    *               ]
    *           }
    *       ]
    *    }
    * }
    */
    public function index()
    {
        $driver = auth()->user()->driver;
        $current_date = Carbon::today();
        $start_date = $current_date->copy()->subDays(25);
    
        // Initialize an array to hold the incentive data for each day
        $incentive_data = [];
    
        // Loop through each day in the last 30 days
        for ($date = $start_date; $date->lessThanOrEqualTo($current_date); $date->addDay()) {
            $formatted_date = $date->toDateString();
    
            // Check if there are any incentives for this date
            $daily_incentive_history = DriverIncentiveHistory::where('driver_id', $driver->id)
                ->whereDate('date', $formatted_date)
                ->where('mode', 'daily')
                ->get();
    
            $total_incentive = $daily_incentive_history->sum('amount');
            
            // Fetch upcoming incentives for this date
            $upcoming_incentives = Incentive::where('mode', 'daily')->orderBy('ride_count', 'asc')->get();
    
            $earn_upto = $upcoming_incentives->sum('amount');
    
            // Prepare the data for the day and add it to the array
            $day_data = [
                'month' => $date->format('M'),
                'date' =>$formatted_date = $date->format('d'),
                'total_incentive_earned' => $total_incentive,
                'earn_upto' => $earn_upto,
                'upcoming_incentives' => [],
            ];
    
            foreach ($upcoming_incentives as $incentive) {
                $is_completed = $daily_incentive_history->contains('amount', $incentive->amount);
                $day_data['upcoming_incentives'][] = [
                    'ride_count' => $incentive->ride_count,
                    'incentive_amount' => $incentive->amount,
                    'is_completed' => $is_completed,
                ];
            }
    
            // Append the day's data to the incentive_data array
            $incentive_data[] = $day_data;
        }
    
        return response()->json([
            'success' => true,
            'message' => 'incentive_history_listed',
            'data' => [
                'incentive_history' => $incentive_data,
            ],
        ]);
    }
    
    /**
     * Weekly Incentives
     *
     * This function calculates and returns the weekly incentive history for the authenticated driver
     * for the past 12 weeks, including the current week. It aggregates earned incentives, tracks the
     * overall total incentives, and provides details about upcoming incentives, their ride count 
     * requirements, and completion status for each week.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing weekly incentive history.
     *
     * @response
     * {
     *    "success": true,
     *    "message": "incentive_history_listed",
     *    "data": {
     *       "incentive_history": [
     *           {
     *               "month": "Oct",
     *               "date": "1-7",
     *               "total_incentive_earned": 200,
     *               "earn_upto": 500,
     *               "upcoming_incentives": [
     *                   {
     *                       "ride_count": 10,
     *                       "incentive_amount": 100,
     *                       "is_completed": true
     *                   },
     *                   {
     *                       "ride_count": 20,
     *                       "incentive_amount": 200,
     *                       "is_completed": false
     *                   }
     *               ]
     *           }
     *       ]
     *    }
     * }
     */
    public function weeklyIncentives()
    {
        $driver = auth()->user()->driver;
    
        // Get the current date
        $current_date = Carbon::today();
    
        // Calculate the start date as the beginning of the current week (Sunday)
        $start_date = $current_date->copy()->startOfWeek(Carbon::SUNDAY)->subWeeks(11); // Start from 11 weeks ago to include the current week
    
        // Calculate total incentives up to the current date
        $earn_upto = Incentive::where('mode', 'weekly')->orderBy('ride_count', 'asc')->sum('amount');
    
        // Initialize an array to hold the incentive data for each week
        $incentive_data = [];
        $overall_total_incentive = 0; // To track total incentives over 12 weeks
        $maximum_incentive_amount = Incentive::where('mode', 'weekly')->pluck('amount')->toArray();
        $maximum_incentive_amount = array_sum($maximum_incentive_amount);
    
        // Loop through each week for the past 12 weeks
        for ($week = 0; $week < 12; $week++) {
            // Calculate the week start and end dates
            $week_start_sunday = $start_date->copy()->addWeeks($week)->startOfWeek(Carbon::SUNDAY);
            $week_end_saturday = $week_start_sunday->copy()->endOfWeek(Carbon::SATURDAY);
    
            $total_incentive = 0;
            $upcoming_incentives = [];
    
            // Check if there are any incentives for this week
            $driver_incentive_history = DriverIncentiveHistory::where('driver_id', $driver->id)
                ->whereBetween('date', [$week_start_sunday, $week_end_saturday])
                ->where('mode', 'weekly')
                ->get();
    
            if ($driver_incentive_history->isNotEmpty()) {
                // Calculate the total incentive for the week
                $total_incentive = $driver_incentive_history->sum('amount');
    
                // Prepare the upcoming incentives
                foreach ($driver_incentive_history as $driver_incentive) {
                    $incentive = Incentive::where('amount', $driver_incentive->amount)
                        ->where('mode', 'weekly')
                        ->first();
    
                    if ($incentive) {
                        $upcoming_incentives[] = [
                            'ride_count' => $incentive->ride_count,
                            'incentive_amount' => $incentive->amount,
                            'is_completed' => true,
                        ];
    
                        // Fetch upcoming incentives
                        $upcoming_incentive = Incentive::where('ride_count', '>', $incentive->ride_count)
                            ->where('mode', 'weekly')
                            ->orderBy('ride_count', 'asc')
                            ->first();
    
                        if ($upcoming_incentive) {
                            $upcoming_incentive_history_exists = DriverIncentiveHistory::where('driver_id', $driver->id)
                                ->whereBetween('date', [$week_start_sunday, $week_end_saturday])
                                ->where('mode', 'weekly')
                                ->where('amount', $upcoming_incentive->amount)
                                ->exists();
    
                            if (!$upcoming_incentive_history_exists) {
                                $upcoming_incentives[] = [
                                    'ride_count' => $upcoming_incentive->ride_count,
                                    'incentive_amount' => $upcoming_incentive->amount,
                                    'is_completed' => false,
                                ];
                            }
                        }
                    }
                }
            } else {
                // If no history exists for the week, fetch all upcoming incentives
                $incentives = Incentive::where('mode', 'weekly')->orderBy('ride_count', 'asc')->get();
                foreach ($incentives as $incentive) {
                    $upcoming_incentives[] = [
                        'ride_count' => $incentive->ride_count,
                        'incentive_amount' => $incentive->amount,
                        'is_completed' => false,
                    ];
                }
            }
    
            // Collect the data for this week
            $incentive_data[] = [
                'month' => $week_start_sunday->format('M') === $week_end_saturday->format('M') 
                    ? $week_start_sunday->format('M') 
                    : $week_start_sunday->format('M') . '-' . $week_end_saturday->format('M'),
                'date' => $week_start_sunday->format('j') . '-' . $week_end_saturday->format('j'),
                'total_incentive_earned' => $total_incentive,
                'earn_upto' => $earn_upto,
                'upcoming_incentives' => $upcoming_incentives,
            ];
        }
    
        return response()->json([
            'success' => true,
            'message' => 'incentive_history_listed',
            'data' => [
                'incentive_history' => $incentive_data,
            ],
        ]);
    }
    /**
     * List incentive history.
     * The function calculates daily incentives earned, upcoming incentives, and total incentives earned for each day and week.
     * 
     * @return \Illuminate\Http\JsonResponse
     * 
     * @response
     * {
     *     "success": true,
     *     "message": "incentive_history_listed",
     *     "data": {
     *         "incentive_history": [
     *             {
     *                 "from_date": "10-Nov-24",
     *                 "to_date": "16-Nov-24",
     *                 "dates": [
     *                     {
     *                         "day": "Sun",
     *                         "date": "10-Nov-24",
     *                         "is_today": false,
     *                         "total_incentive_earned": 0,
     *                         "earn_upto": 1600,
     *                         "upcoming_incentives": [
     *                             {
     *                                 "ride_count": 1,
     *                                 "incentive_amount": 100,
     *                                 "is_completed": false
     *                             },
     *                             {
     *                                 "ride_count": 2,
     *                                 "incentive_amount": 500,
     *                                 "is_completed": false
     *                             },
     *                             {
     *                                 "ride_count": 3,
     *                                 "incentive_amount": 1000,
     *                                 "is_completed": false
     *                             }
     *                         ]
     *                     },
     *                 ]
     *             },
     *         ]
     *     }
     * }
     */
    public function newIncentive()
    {
        $driver = auth()->user()->driver;
        $current_date = Carbon::today();
    
        $start_date = Carbon::now()->startOfWeek(Carbon::SUNDAY)->toDateString();
    
        // Initialize an array to hold the incentive data for each day of the week
        $incentive_data = [];
    
        // Define week days
        $weekDaysString = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']; // Corrected to start with Sunday
    
        // Loop through the weeks for the last 3 months
        $i = 0;
        do {
            $startOfWeek = Carbon::now()->startOfWeek(Carbon::SUNDAY)->subWeeks($i);
            $endOfWeek = Carbon::now()->endOfWeek(Carbon::SATURDAY)->subWeeks($i);
    
            // Prepare week range
            $fromDate = $startOfWeek->format('d-M-y');
            $toDate = $endOfWeek->format('d-M-y');
    
            $weekDays = [];
    
            // Loop over each day of the week
            for ($j = 0; $j < 7; $j++) {
                $weekDate = Carbon::parse($startOfWeek)->addDay($j)->format('Y-m-d');
    
                // Calculate daily incentive for the current week
                $dailyIncentive = DriverIncentiveHistory::where('driver_id', $driver->id)
                    ->whereDate('date', $weekDate)
                    ->where('mode', 'daily')
                    ->sum('amount');
                $total_completed_rides_week_day = Request::where('driver_id', $driver->id)
                    ->whereDate('completed_at', $weekDate)
                    ->where('is_completed', true)
                    ->count();
                
    
                // Check upcoming incentives
                $upcomingIncentives = Incentive::where('mode', 'daily')->orderBy('ride_count', 'asc')->get();
    
                $earn_upto = $upcomingIncentives->sum('amount');
                $ride_upto = $upcomingIncentives->max('ride_count');
    
                // Prepare day's incentive data
                $dayData = [
                    'day' => $weekDaysString[$j],
                    'date' => Carbon::parse($weekDate)->format('d-M-y'),
                    'is_today' => Carbon::parse($weekDate)->isSameDay($current_date), // Check if it's today
                    'total_rides' => $ride_upto - $total_completed_rides_week_day, // Round to 2 decimal places
                    'total_incentive_earned' => round($dailyIncentive, 2), // Round to 2 decimal places
                    'earn_upto' => round($earn_upto -$dailyIncentive, 2), // Round to 2 decimal places
                    'upcoming_incentives' => [],
                ];
    
                foreach ($upcomingIncentives as $incentive) {
                    $is_completed = $dailyIncentive >= $incentive->amount;
    
                    $dayData['upcoming_incentives'][] = [
                        'ride_count' => $incentive->ride_count,
                        'incentive_amount' => $incentive->amount,
                        'is_completed' => $is_completed,
                    ];
                }
    
                // Store the day's data in the week array
                $weekDays[] = $dayData;
            }
    
            // Prepare the week's incentive data
            $weekData = [
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'dates' => $weekDays,
            ];
    
            // Add the week's data to the incentive data array
            $incentive_data[] = $weekData;
    
            $i++; // Move to the next week
    
        } while ($startOfWeek->greaterThanOrEqualTo($start_date)); // Stop when we reach 3 months ago
    
        // Reverse the order of the incentive_data
        $incentive_data = array_reverse($incentive_data);
    
        // Return response with incentive data
        return response()->json([
            'success' => true,
            'message' => 'incentive_history_listed',
            'data' => [
                'incentive_history' => $incentive_data,
            ],
        ]);
    }

    /**
     * 
     * List weekly incentive history.
     * @response
     * {
     *     "success": true,
     *     "message": "incentive_history_listed",
     *     "data": {
     *         "incentive_history": [
     *             {
     *                 "from_date": "29-Sep-24",
     *                 "to_date": "16-Nov-24",
     *                 "dates": [
     *                     {
     *                         "day": "Sep",
     *                         "date": "29-Sep - 05-Oct",
     *                         "is_current_week": false,
     *                         "total_incentive_earned": 0,
     *                         "earn_upto": 150,
     *                         "upcoming_incentives": [
     *                             {
     *                                 "ride_count": 2,
     *                                 "incentive_amount": 100,
     *                                 "is_completed": false
     *                             },
     *                             {
     *                                 "ride_count": 3,
     *                                 "incentive_amount": 50,
     *                                 "is_completed": false
     *                             }
     *                         ]
     *                     }
     *                 ]
     *             }
     *         ]
     *     }
     * }
     * 
     */
    public function weekIncentives()
    {
        $driver = auth()->user()->driver;
    
        // Define the current week's Sunday as the start date
        $current_week_start = Carbon::today()->startOfWeek(Carbon::SUNDAY);
        
        // Define the date for the last Sunday of the previous week (for the 'to_date')
        $end_date = $current_week_start->copy()->subDay();
    
        // Define the start of the last 8 weeks (going back 7 weeks from the current week)
        $start_date = $current_week_start->copy()->subWeeks(7);
    
        $earn_upto = Incentive::where('mode', 'weekly')->sum('amount');
    
        $incentive_history = [];
    
        // Loop over the last 8 weeks to gather data (including the current week)
        for ($week = 0; $week < 8; $week++) {
            $week_start = $start_date->copy()->addWeeks($week);
            $week_end = $week_start->copy()->endOfWeek(Carbon::SATURDAY);
    
            // Check if it's the current week
            $is_current_week = $week_start->isSameDay($current_week_start);
    
            // Gather upcoming incentives for the week
            $upcoming_incentives = Incentive::where('mode', 'weekly')
                ->orderBy('ride_count', 'asc')
                ->get()
                ->map(function ($incentive) use ($driver, $week_start, $week_end) {
                    $ride_count_completed = DriverIncentiveHistory::where('driver_id', $driver->id)
                        ->where('mode', 'weekly')
                        ->whereBetween('date', [$week_start, $week_end])
                        ->max('ride_count');
    
                    return [
                        'ride_count' => $incentive->ride_count,
                        'incentive_amount' => $incentive->amount,
                        'is_completed' => $ride_count_completed >= $incentive->ride_count,
                    ];
                });
    
            // Calculate total incentives earned for the week
            $total_incentive_earned = DriverIncentiveHistory::where('driver_id', $driver->id)
                ->where('mode', 'weekly')
                ->whereBetween('date', [$week_start, $week_end])
                ->sum('amount');

            $total_incentive_rides = Incentive::where('mode', 'weekly')->max('ride_count');

            $total_completed_rides_week_day = Request::where('driver_id', $driver->id)
                    ->whereBetween('completed_at', [$week_start, $week_end])
                    ->where('is_completed', true)
                    ->count();

    
            // Add week data to the incentive history
            $incentive_history[] = [
                'day' => $week_start->format('M'),
                'date' => $week_start->format('d-M') . ' - ' . $week_end->format('d-M'),
                'is_current_week' => $is_current_week,
                'total_rides' => $total_incentive_rides - $total_completed_rides_week_day,
                'total_incentive_earned' => round($total_incentive_earned, 2), // Round to 2 decimal places
                'earn_upto' => round($earn_upto -$total_incentive_earned, 2), // Round to 2 decimal places
                'upcoming_incentives' => $upcoming_incentives,
            ];
        }
    
        // Response structure
        return response()->json([
            'success' => true,
            'message' => 'incentive_history_listed',
            'data' => [
                'incentive_history' => [
                    [
                        'from_date' => $start_date->format('d-M-y'),
                        'to_date' => $end_date->format('d-M-y'),
                        'dates' => $incentive_history,
                    ],
                ],
            ],
        ]);
    }
    

    
    
    
}
