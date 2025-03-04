<?php

namespace App\Http\Controllers\Web\Common;

use App\Models\Country;
use App\Http\Controllers\ApiController;
use App\Transformers\CountryTransformer;
use App\Models\TimeZone;
use App\Transformers\TimezoneTransformer;
use Inertia\Inertia;
use App\Base\Filters\Admin\UserFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;

/**
 * @group Web-Spa-Countries&Timezones
 *
 * APIs for User-Management
 */
class CountryController extends ApiController
{

     /**
     * Country View
     * 
     * */
    public function countryView()
    {
        return Inertia::render('pages/countries/index');

    }

    /**
     * Get all the countries.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(QueryFilterContract $queryFilter)
    {
        $query = Country::active();

        $results = $queryFilter->builder($query)->customFilter(new UserFilter)->paginate();

        return response()->json([
            'items' => $results->items(),
            'paginator' => $results,
        ]);

        // return $this->respondOk($countries);
    }

    /**
     * Get all the timezone.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function timezones()
    {
        $timezonesQuery = TimeZone::active();

        $timezones = filter($timezonesQuery, new TimezoneTransformer)->defaultSort('name')->get();

        return $this->respondOk($timezones);
    }


}
