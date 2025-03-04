<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Models\User;
use App\Models\Admin\Driver;
use App\Base\Constants\Auth\Role;
use App\Http\Controllers\ApiController;
use App\Transformers\User\UserTransformer;
use App\Transformers\Driver\DriverProfileTransformer;
use App\Transformers\Owner\OwnerProfileTransformer;
use App\Models\Chat;
use App\Models\Request\Request;
use App\Models\Request\RequestBill;
use App\Models\Admin\DriverLevelUp;
use App\Base\Constants\Masters\WalletRemarks;
use Carbon\Carbon;
use Log;


class AccountController extends ApiController
{
    /**
     * Get the current logged in user.
     * @group User-Management
     * @return \Illuminate\Http\JsonResponse
    * @responseFile responses/auth/authenticated_driver.json
    * @responseFile responses/auth/authenticated_user.json
     */
    public function me()
    {

        $user = auth()->user();

        if (auth()->user()->hasRole(Role::DRIVER)) {

            $driver_details = $user->driver;
            $includes = ['onTripRequest.userDetail','onTripRequest.requestBill','metaRequest.userDetail','driverVehicleType'];
            if ( get_settings('show_driver_level_feature') && !$driver_details->owner_id) {
                $includes [] = 'level';
                if(!$driver_details->loyaltyPoint()->exists()) {
                    $driver_details->loyaltyPoint()->create(['amount'=>0]);
                }
                if(get_settings('show_driver_level_feature') && !$driver_details->levelDetail){
                    $first_level = DriverLevelUp::orderBy('level', 'asc')->first();
                    if($first_level){
                        $driver_level = $driver_details->levelHistory()->create(['level' => $first_level->level, 'level_id' => $first_level->id]);
                        $driver_details->driver_level_up_id = $driver_level->id;
                        $driver_details->save();
                        $driver_details->refresh();
                        $driver_details->load('levelDetail');
                        if ($first_level->reward_type == 'reward-cash') {
                            $this->creditDriver($first_level->reward, $driver_details, WalletRemarks::DRIVER_LEVEL_UP);
                        } elseif ($first_level->reward_type == 'reward-point') {
                            $this->rewardDriver($first_level->reward, $driver_details, WalletRemarks::DRIVER_LEVEL_UP);
                        }
                        $this->fetchReward($first_level, $driver_details);
                    }
                }
            }
            if(get_settings('driver_register_module') !== 'commission' ) {
                $includes [] = 'subscription';
            }
            $user = fractal($driver_details, new DriverProfileTransformer)->parseIncludes($includes);

        } else if(auth()->user()->hasRole(Role::USER)) {

            $user = fractal($user, new UserTransformer)->parseIncludes(['onTripRequest.driverDetail','onTripRequest.requestBill','metaRequest.driverDetail','favouriteLocations','laterMetaRequest.driverDetail']);
        }else{

            $owner_details = $user->owner;

            $user = fractal($owner_details, new OwnerProfileTransformer);
        }

        if(auth()->user()->hasRole(Role::DISPATCHER)){

            $user = User::where('id',auth()->user()->id)->first();
            $user->chat_id = "";
            $get_chat_data = Chat::where('user_id',$user->id)->first();
            if($get_chat_data)
            {
                $user->chat_id = $get_chat_data->id;
            }  
        }
      
        return $this->respondOk($user);
    }

    /**
     * Calculate and allocate Rewards
     * @return \Illuminate\Http\JsonResponse
     * 
     */
    public function fetchReward($target_level,$driver) {

        $current_date = Carbon::today()->toDateTimeString();
        $driver_level = $driver->levelDetail;
        $bonus_ride_rewards = 0;
        $bonus_amount_rewards = 0;
        if(!$driver_level->ride_rewarded){
            if($target_level->is_min_ride_complete){
                $driver_completed_rides = Request::where('driver_id', $driver->id)->where('is_completed', true)->count();
                if($target_level->min_ride_count <= $driver_completed_rides){
                    $this->rewardDriver($target_level->ride_points,$driver,WalletRemarks::DRIVER_LEVEL_UP_BONUS);
                    $driver_level->ride_rewarded = true;
                    $driver_level->save();
                }
            }else{
                $driver_level->ride_rewarded = true;
                $driver_level->save();
            }
        }
        if(!$driver_level->amount_rewarded){
            if($target_level->is_min_ride_amount_complete){
                $driverSpentAmount = RequestBill::whereHas('requestDetail', function ($query) use ($driver, $current_date) {
                    $query->where('driver_id', $driver->id)
                          ->where('is_completed', 1);
                })->sum('driver_commision');
                if($target_level->min_ride_amount <= $driverSpentAmount){
                    $this->rewardDriver($target_level->amount_points,$driver,WalletRemarks::DRIVER_LEVEL_UP_BONUS);
                    $driver_level->amount_rewarded = true;
                    $driver_level->save();
                }
            }else{
                $driver_level->amount_rewarded = true;
                $driver_level->save();
            }
        }
        return [
            'rewards'=>$bonus_amount_rewards + $bonus_ride_rewards,
            'bonus_ride_rewards'=>$bonus_ride_rewards,
            'bonus_amount_rewards'=>$bonus_amount_rewards,
        ];
    }
    /**
     * Reward Point rewards
     * 
     */
    public function rewardDriver($reward,$driver,$remarks) {
        $driver_reward = $driver->loyaltyPoint;
        $driver_reward->points_added += $reward;
        $driver_reward->balance_reward_points += $reward;
        $driver_reward->save();

        // Add the history
        $driver->loyaltyHistory()->create([
            'reward_points'=>$reward,
            'remarks'=>$remarks,
            'is_credit'=>true,
        ]);
    }
    /**
     * Wallet Rewards
     * 
     */
    public function creditDriver($reward,$driver,$remarks) {
        $driver_wallet = $driver->driverWallet;
        
        $driver_wallet->amount_added += $reward;
        $driver_wallet->amount_balance += $reward;
        $driver_wallet->save();

        // Add the history
        $driver->driverWalletHistory()->create([
            'amount'=>$reward,
            'transaction_id'=>str_random(10),
            'remarks'=>$remarks,
            'is_credit'=>true,
        ]);
    }

}
