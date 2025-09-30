<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\Outlet;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserOutletAssignAPIController extends AppBaseController
{

    public function index()
    {
        $role_data      = Role::with(['users'])->get();
        $user_data_by_role = [];
        foreach ($role_data as $role) {
            $user_data_by_role[$role->id] = $role->users;
        }

        $outlets = Outlet::where('status', 1)->get();
        $outlet_by_company = [];
        foreach($outlets as $outlet) {
            $outlet_by_company[$outlet->company_id][] = $outlet;
        }

        $return_data    = [
            'role_data' => $role_data,
            'role_users'    => $user_data_by_role,
            'company_outlet'    => $outlet_by_company,
        ];

        return $this->sendResponse($return_data, 'Data Retrieve Successfully');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'outlets' => 'required',
        ]);

        // return json_decode($request->get('outlets'));
        $user_id    = $request->get('user_id');
        // $outlet_ids  = explode(",", $request->get('outlets'));
        $outlet_ids = $request->get('outlets');

        $user = User::find($user_id);
        if(empty($user)) {
            return $this->sendError('User Do Not Find');
        }
        // return $outlet_ids;
        // $outlet_assign = $user->outlets()->sync($outlet_ids);
        $user->outlet_id = $outlet_ids;
        // dd( $user,  $outlet_ids);
        $outlet_assign = $user->update();


        if($outlet_assign) {
            return $this->sendSuccess('Outlet Assign Successfully Done');
        }
        return $this->sendError('Something went wrong, please try again');

    }
}
