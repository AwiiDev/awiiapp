<?php

namespace App\Helpers\Rides;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use Carbon\Carbon;
use App\Base\Constants\Masters\UnitType;


trait EndRequestHelper
{

    /**
     * Calculate and charge Ride fare
     * @param Request $request_detail with generated bill
     * 
     */
    //
    protected function calculateDistanceAndDuration($distance,$request_detail)
    {

        $duration = $this->calculateDurationOfTrip($request_detail->trip_start_time);

        if($request_detail->requestEtaDetail()->exists()){
           
           $eta_duration =$request_detail->requestEtaDetail->total_time; 
            

            if($duration<$eta_duration){

                $duration = $eta_duration;

            }
            if($distance<=1){

                $distance = $request_detail->requestEtaDetail->total_distance;  
                
                goto distance_calculation_end;

            }

        }


        if(env('APP_FOR')!='demo'){

        if(get_settings('map_type')=='open_street'){

           $distance_and_duration = getDistanceMatrixByOpenstreetMap($request_detail->pick_lat, $request_detail->pick_lng, $request_detail->drop_lat, $request_detail->drop_lng);

            $distance_in_meters=$distance_and_duration['distance_in_meters'];
            $calculated_distance = $distance_in_meters / 1000;

            if ($calculated_distance > $distance) {
                $distance = $calculated_distance;
            }

        }else{


        $distance_matrix = get_distance_matrix($request_detail->pick_lat, $request_detail->pick_lng, $request_detail->drop_lat, $request_detail->drop_lng, true);

        if ($distance_matrix->status =="OK" && $distance_matrix->rows[0]->elements[0]->status != "ZERO_RESULTS") {
            $distance_in_meters = get_distance_value_from_distance_matrix($distance_matrix);
            $calculated_distance = ($distance_in_meters / 1000);

            if ($calculated_distance > $distance) {
                $distance = $calculated_distance;
            }
        }

        }

        if ($request_detail->unit==UnitType::MILES) {
            $distance = (kilometer_to_miles($distance));
        }

        }

        distance_calculation_end:

        return $distance_and_duration = ['distance'=>$distance,'duration'=>$duration];

    
    }


    /**
     * Calculate Duration
     * @return $totald_duration number in minutes
     */
    protected function calculateDurationOfTrip($start_time)
    {

        $current_time = date('Y-m-d H:i:s');

        $start_time = Carbon::parse($start_time);
        // Log::info($start_time);
        $end_time = Carbon::parse($current_time);
        // Log::info($end_time);
        $totald_duration = $end_time->diffInMinutes($start_time);
        // Log::info($totald_duration);

        return $totald_duration;
    }


    /**
     * Calculate Waiting Time for the completed ride
     * 
     * */
    protected function calculateWaitingTime($before_trip_start_waiting_time,$after_trip_start_waiting_time,$zone_type_price){

        $subtract_with_free_waiting_before_trip_start = ($before_trip_start_waiting_time - $zone_type_price->free_waiting_time_in_mins_before_trip_start);

        $subtract_with_free_waiting_after_trip_start = ($after_trip_start_waiting_time - $zone_type_price->free_waiting_time_in_mins_after_trip_start);

        $waiting_time = ($subtract_with_free_waiting_before_trip_start+$subtract_with_free_waiting_after_trip_start);

        if($waiting_time<0){
            $waiting_time = 0;
        }

        return $waiting_time;
    }



    
}
