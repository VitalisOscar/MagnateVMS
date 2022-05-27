<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Http;

class ApiService{

    const ROUTE_API = 'https://magnatevms.herokuapp.com/api/';
    // const ROUTE_API = 'http://localhost:3000/api/';

    const ROUTE_GET_DAILY_SITE_STATS = 'get_daily_stats';

    const ROUTE_GET_USERS = 'get_users';
    const ROUTE_ADD_USER = 'add_user';
    const ROUTE_GET_SINGLE_USER = 'get_single_user';
    const ROUTE_UPDATE_USER = 'update_user';
    const ROUTE_GET_LOGINS = 'get_logins';

    const ROUTE_GET_SITES = 'get_sites';
    const ROUTE_ADD_SITE = 'add_site';
    const ROUTE_GET_SINGLE_SITE = 'get_single_site';
    const ROUTE_UPDATE_SITE = 'update_site';

    const ROUTE_ADD_COMPANY = 'add_company';
    const ROUTE_GET_SINGLE_COMPANY = 'single_company';
    const ROUTE_DELETE_COMPANY = 'delete_company';

    const ROUTE_ADD_STAFF = 'add_staff';
    const ROUTE_GET_SINGLE_STAFF = 'single_staff';
    const ROUTE_UPDATE_STAFF = 'update_staff';
    const ROUTE_DELETE_STAFF = 'delete_staff';

    const ROUTE_GET_COMPANY_VEHICLES = 'get_company_vehicles';
    const ROUTE_ADD_VEHICLE = 'add_vehicle';
    const ROUTE_GET_NON_COMPANY_VEHICLES = 'get_non_company_vehicles';

    const ROUTE_GET_DRIVERS = 'get_drivers';
    const ROUTE_ADD_DRIVER = 'add_driver';
    const ROUTE_GET_SINGLE_DRIVER = 'get_single_driver';
    const ROUTE_UPDATE_DRIVER = 'update_driver';

    const ROUTE_GET_VISITORS = 'visitors';
    const ROUTE_GET_ALL_VISITOR_ACTIVITY = 'all_visitor_activity';
    const ROUTE_GET_ALL_STAFF_ACTIVITY = 'all_staff_activity';
    const ROUTE_GET_ALL_VEHICLE_ACTIVITY = 'all_vehicle_activity';
    const ROUTE_GET_SINGLE_VEHICLE = 'get_single_vehicle';

    const ROUTE_GET_SINGLE_VISITOR_ACTIVITY = 'single_visitor_activity';

    function getRoute($key, $urlParams = [], $queryParams = []){
        $routes = [
            self::ROUTE_GET_DAILY_SITE_STATS => self::ROUTE_API.'stats',

            self::ROUTE_GET_USERS => self::ROUTE_API.'users',
            self::ROUTE_ADD_USER => self::ROUTE_API.'users/add',
            self::ROUTE_GET_SINGLE_USER => self::ROUTE_API.'users/{user_id}',
            self::ROUTE_UPDATE_USER => self::ROUTE_API.'users/{user_id}/update',
            self::ROUTE_GET_LOGINS => self::ROUTE_API.'users/logins',

            self::ROUTE_GET_SITES => self::ROUTE_API.'sites',
            self::ROUTE_ADD_SITE => self::ROUTE_API.'sites/add',
            self::ROUTE_GET_SINGLE_SITE => self::ROUTE_API.'sites/{site_id}',
            self::ROUTE_UPDATE_SITE => self::ROUTE_API.'sites/{site_id}/update',

            self::ROUTE_ADD_COMPANY => self::ROUTE_API.'sites/{site_id}/companies/add',
            self::ROUTE_GET_SINGLE_COMPANY => self::ROUTE_API.'sites/{site_id}/companies/{company_id}',
            self::ROUTE_DELETE_COMPANY => self::ROUTE_API.'sites/{site_id}/companies/{company_id}/delete',

            self::ROUTE_ADD_STAFF => self::ROUTE_API.'sites/{site_id}/companies/{company_id}/staff/add',
            self::ROUTE_GET_SINGLE_STAFF => self::ROUTE_API.'sites/{site_id}/companies/{company_id}/staff/{staff_id}',
            self::ROUTE_UPDATE_STAFF => self::ROUTE_API.'sites/{site_id}/companies/{company_id}/staff/{staff_id}/update',
            self::ROUTE_DELETE_STAFF => self::ROUTE_API.'sites/{site_id}/companies/{company_id}/staff/{staff_id}/delete',

            self::ROUTE_GET_DRIVERS => self::ROUTE_API.'data/drivers',
            self::ROUTE_ADD_DRIVER => self::ROUTE_API.'data/drivers/add',
            self::ROUTE_GET_SINGLE_DRIVER => self::ROUTE_API.'data/drivers/{driver_id}',
            self::ROUTE_UPDATE_DRIVER => self::ROUTE_API.'data/drivers/{driver_id}/update',

            self::ROUTE_GET_COMPANY_VEHICLES => self::ROUTE_API.'data/vehicles/company',
            self::ROUTE_ADD_VEHICLE => self::ROUTE_API.'data/vehicles/add',

            self::ROUTE_GET_NON_COMPANY_VEHICLES => self::ROUTE_API.'data/vehicles/other',

            self::ROUTE_GET_VISITORS => self::ROUTE_API.'activity/visitors/list',
            self::ROUTE_GET_ALL_VISITOR_ACTIVITY => self::ROUTE_API.'activity/visitors',
            self::ROUTE_GET_ALL_STAFF_ACTIVITY => self::ROUTE_API.'activity/staff',
            self::ROUTE_GET_ALL_VEHICLE_ACTIVITY => self::ROUTE_API.'activity/vehicles',
            self::ROUTE_GET_SINGLE_VEHICLE => self::ROUTE_API.'data/vehicles/{vehicle_id}',

            self::ROUTE_GET_SINGLE_VISITOR_ACTIVITY => self::ROUTE_API.'activity/visitors/{visitor_id}',
        ];

        $route = $routes[$key];

        // Replace params in route
        foreach($urlParams as $key => $value){
            $route = str_replace('{'.$key.'}', $value, $route);
        }

        // Add query params to url
        if(count($queryParams) > 0){
            $route .= '?'.http_build_query($queryParams);
        }

        return $route;
    }

    function get($route, $routeParams = [], $queryParams = []){
        return $this->request('GET', $this->getRoute($route, $routeParams, $queryParams));
    }

    function post($route, $routeParams = [], $queryParams = [], $data){
        return $this->request('POST', $this->getRoute($route, $routeParams, $queryParams), $data);
    }

    private function request($method, $route, $data = null){
        $method = strtoupper($method);

        if($method == 'GET'){
            return new ApiResponse(Http::acceptJson()->get($route)->json());
        }else if($method == 'POST'){
            return new ApiResponse(Http::acceptJson()->post($route, $data)->json());
        }

        return null;
    }
}
