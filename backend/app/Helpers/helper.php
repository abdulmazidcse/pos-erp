<?php
// Important Global Function
use App\Models\AccountGroup;
use App\Models\AccountLedger;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

if(!function_exists('last_query_start')){
    function last_query_start(){
        \DB::enableQueryLog();
    }
}

if(!function_exists('last_query_end')){

    function last_query_end(){
        $query = \DB::getQueryLog();
        dd(end($query));
    }
}

if(!function_exists('file_url')){

    function file_url($file,$path){
        return asset('uploads/'.$path.'/' . $file);
    }
}

if(!function_exists('setTimeZone')){

    function setTimeZone(){
        date_default_timezone_set('Asia/Dhaka');
    }
}
if(!function_exists('customDateFormat')){

    function customDateFormat($value){
        return date("Y-m-d", strtotime($value));
    }
}

function auth_api_user_permission($permission){
    $auth = \Auth::guard('api')->user();
    return $auth->can($permission) || ($auth->email == defaultUserEmail());
}

if(!function_exists('defaultUserEmail')){

    function defaultUserEmail(){
        return 'admin@admin.com';
    }
}

if(!function_exists('auth_api_user')) {
    function auth_api_user(){
        return auth()->guard('api')->user();
    }
}



// For Purchase Order ApproveStatus
if(!function_exists('purchaseOrderApproveStatusName')) {
    function purchaseOrderApproveStatusName($approve_status)
    {
        switch ($approve_status){
            case 0:
                $approve_status_name = '<span class="badge bg-warning">Pending</span>';
                break;
            case 1:
                $approve_status_name = '<span class="badge bg-success">Approved</span>';
                break;
            case 2:
                $approve_status_name = '<span class="badge bg-danger">Reject</span>';
                break;
            default:
                $approve_status_name = '<span class="badge bg-default">N/A</span>';
                break;

        }

        return $approve_status_name;
    }
}

//function get_browser_name($user_agent){
//    $t = strtolower($user_agent);
//    $t = " " . $t;
//    if     (strpos($t, 'opera'     ) || strpos($t, 'opr/')     ) return 'Opera'            ;
//    elseif (strpos($t, 'edge'      )                           ) return 'Edge'             ;
//    elseif (strpos($t, 'chrome'    )                           ) return 'Chrome'           ;
//    elseif (strpos($t, 'safari'    )                           ) return 'Safari'           ;
//    elseif (strpos($t, 'firefox'   )                           ) return 'Firefox'          ;
//    elseif (strpos($t, 'msie'      ) || strpos($t, 'trident/7')) return 'Internet Explorer';
//
//    return 'Unkown';
//}
//
//function current_guard(){
//    $allGuards = config('auth.guards');
//    $activityCauseBy    = "";
//    $guardName          = "";
//    foreach($allGuards as $guard => $guard_value){
//        $getGuard = explode("_", "api");
//
//        if (request()->is('api/*')) {
//            if(!is_null(auth($guard)->user())){
//                $activityCauseBy    = auth($guard)->user();
//                $guardName          = $guard;
//            }
//        }
//        elseif(auth()->guard($guard)->check()){
//            $activityCauseBy    = auth($guard)->user();
//            $guardName          = $guard;
//        }
//    }
//
//
//    return [
//        "activityCauseBy"    => $activityCauseBy,
//        "guardName"          => $guardName
//    ];
//}


if(!function_exists('uniqueCodeWithPrefix')){
    function uniqueCodeWithPrefix($length,$prefix,$table,$field, $company_id){
        $prefix_length = strlen($prefix);
        $max_id = DB::table($table)
            ->when(!empty($prefix), function($query) use($prefix, $field, $length, $company_id){
              return $query->where('company_id', $company_id)->where(\DB::raw('substr(`'.$field.'`, 1, '.strlen($prefix).')'), $prefix)
                  ->where(\DB::raw('LENGTH(`'.$field.'`)'), $length);
            })
            ->max($field);
        if(strlen($prefix) > 0){
            $max_id = substr($max_id, strlen($prefix), 1000);
        }
        $new = (int)($max_id);
        $new++;
        $number_of_zero = $length-$prefix_length-strlen($new);
        $zero = $number_of_zero > 0 ? str_repeat("0", $number_of_zero) : '';
        $made_id = $prefix.$zero.$new;
        return $made_id;
    }
}

if(!function_exists('uniqueCodeWithoutPrefix')){
    function uniqueCodeWithoutPrefix($length,$table,$field){
        $max = DB::table($table)->max($field);
        $new=(int)($max);
        $new++;
        $number_of_zero=$length-strlen($new);
        $zero = $number_of_zero > 0 ? str_repeat("0", $number_of_zero) : '';
        $made_id=$zero.$new;
        return $made_id;
    }

}

function uniqueGeneratedCodeWithPrefix($length,$prefix,$table,$field, $sl=0){
    $prefix_length = strlen($prefix);
    $max_id = DB::table($table)->count($field);
    $new = (int)($max_id) + $sl;
    $new++;
    $number_of_zero = $length-$prefix_length-strlen($new);
    $zero = str_repeat("0", $number_of_zero);
    $made_id = $prefix.$zero.$new;
    return $made_id;
}

function uniqueGeneratedCodeWithoutPrefix($length,$table,$field){
    $max = DB::table($table)->count($field);
    $new=(int)($max);
    $new++;
    $number_of_zero=$length-strlen($new);
    $zero=str_repeat("0", $number_of_zero);
    $made_id=$zero.$new;
    return $made_id;
}

function returnDecimalNumber($value) {
    $data = number_format((float) $value, 2, '.', '');

    return $data;
}
// Extra Helper
//include_once ("chartofaccounthelper.php");
include_once ("purchaseChartOfAccountsHelper.php");
include_once ("accountsHelper.php");


if(!function_exists('getLedgerAccountData'))
{
    function getLedgerAccountData($key, $value)
    {
        $ledger_data = AccountLedger::where($key, $value)->first();

        return $ledger_data;
    }
}
if(!function_exists('checkCompanyId'))
{
    function checkCompanyId($request)
    {
        $company_id = 0;
        $user = auth()->user();  
        $roles = $user ? $user->roles()->pluck('name')->toArray() : array(); 
        if (in_array('Super Admin', $roles)) { 
            $company_id  = $request->input('company_id');
        }else{
            $company_id  = $user->company_id;
        } 
        return $company_id;
    }
} 
if(!function_exists('getFiscalYear'))
{ 
    function getFiscalYear() { 
        $currentDate = new DateTime();
        $currentYear = $currentDate->format('Y');

        // Assuming fiscal year starts on July 1
        if ($currentDate->format('n') >= 7) {
            // If current month is July or later, fiscal year is current year to next year
            return [
                'start' => "{$currentYear}-07-01",
                'end' => ($currentYear + 1) . "-06-30",
                'label' => $currentYear.'-'.$currentYear + 1
            ];
        } else {
            // If current month is before July, fiscal year is last year to current year
            return [
                'start' => ($currentYear - 1) . "-07-01",
                'end' => "{$currentYear}-06-30",
                'label' => $currentYear.'-'.$currentYear + 1
            ];
        }
    } 
}
function pleaseSortMe($query, $order, $orderByQuery){
    return $query->when($order == 'ASC', function($query) use($orderByQuery){
                    return $query->orderBy($orderByQuery);
                })
                ->when($order == 'DESC', function($query) use($orderByQuery){
                    return $query->orderByDesc($orderByQuery);
                });
}