<?php

namespace App\Http\Controllers\Web\Admin;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Subscription;
use App\Models\Admin\Driver;
use App\Models\Admin\SubscriptionDetail;
use App\Models\Admin\SubscriptionDiscount;
use App\Models\Admin\Zone;
use App\Models\Admin\VehicleType;
use App\Base\Filters\Admin\SubscriptionFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Models\Request\Request as RequestRequest;
use DB;

class SubscriptionController extends Controller
{

    public function index(){

        $page = trans('pages_names.subscription');
        $main_menu = 'settings';
        $sub_menu = 'subscription';
        $vehicle_types = VehicleType::active()->get();
        return Inertia::render('pages/subscription/index',[
            'app_for'=>env("APP_FOR"),
            'types' => $vehicle_types,
            'enabled_module' => get_settings('driver_register_module'),
        ]);
    }
    public function fetch(QueryFilterContract $queryFilter) {
        $query = Subscription::active();
        $results = $queryFilter->builder($query)->customFilter(new SubscriptionFilter)->paginate();
         
        return response()->json([
            'results' => $results->items(),
            'paginator' => $results,
        ]);
    }
    public function create(Request $request,QueryFilterContract $queryFilter) {
        $page = trans('pages_names.subscription');
        $main_menu = 'settings';
        $sub_menu = 'create_subscription';
        $vehicle_types = VehicleType::active()->get();

        return Inertia::render('pages/subscription/create',[
            'types' => $vehicle_types
        ]);
    }
    

    public function store(Request $request) {

        $params = $request->all();
        $result = Subscription::create($params);
        $message = trans('succes_messages.subscription_added_succesfully');

        return response()->json([
            'results' => $result,
            'successMessage' => $message,
        ]);
    }
    public function getById(Subscription $plan) {
        $page = trans('pages_names.subscription');
        $main_menu = 'settings';
        $sub_menu = 'create_subscription';
        $item = $plan;
        $item->transport_type = $item->vehicleTypeDetail->is_taxi;
        $types = VehicleType::active()->where('is_taxi',$item->vehicleTypeDetail->is_taxi)->get();

        return Inertia::render('pages/subscription/create',[
            'types' => $types,
            'plan'=>$item
        ]);
    }
    public function update(Subscription $plan, Request $request) {
        $params = $request->all();
        $plan->update($params);

        $message = trans('succes_messages.subscription_updated_succesfully');

        return response()->json([
            'successMessage' => $message,
            'result' => $plan,
        ]);
    }

    public function toggleStatus(Request $request) {
        Subscription::where('id', $request->id)->update(['active'=> $request->status]);

        $message = trans('succes_messages.subscription_updated_succesfully');

        return response()->json([
            'successMessage' => $message,
            'result' => $plan,
        ]);
    }

    public function delete(Subscription $plan) {
        $plan->delete();

        $message = trans('succes_messages.subscription_deleted_succesfully');

        return response()->json([
            'successMessage' => $message,
        ]);
    }

    public function discountView(subscription $plan){
        $page = trans('pages_names.subscription');
        $main_menu = 'settings';
        $sub_menu = 'subscription';
        $item = $plan;

        return view('admin.subscription.plan_details',compact('page','main_menu','sub_menu','item'));
    }
}
