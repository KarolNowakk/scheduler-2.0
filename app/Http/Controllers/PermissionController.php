<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Worker;
use App\WorkPlace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class PermissionController extends Controller
{
    /**
     * Grant a permission
     *
     * @param Response $response
     * @return json
     */
    public function store(Request $request)
    {
        $data = $this->storeValidator($request->all())->validate();

        if ($this->userHasNoPermissions($request)) {
            return response()->json(['error' => 'Access denied.'], ResponseStatus::HTTP_FORBIDDEN);
        }
        
        $created = Permission::create($data);

        if (!$created) {
            return response()->json(['error' => 'An error occured.'], ResponseStatus::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['success' => 'Permission has been created'], ResponseStatus::HTTP_OK);
    }

    /**
     * Grant a permission
     *
     * @param Response $response
     * @param Permission $permission
     * @return json
     */
    public function destroy(Permission $permission)
    {
        if ($this->userHasNoPermissions($permission)) {
            return response()->json(['error' => 'Access denied.'], ResponseStatus::HTTP_FORBIDDEN);
        }
        
        if (! $permission->delete()) {
            return response()->json(['error' => 'An error occured.'], ResponseStatus::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['success' => 'Permission has been deleted'], ResponseStatus::HTTP_OK);
    }

    /**
     * Validate incoming data
     *
     * @param array $data
     * @return Validator
     */
    protected function storeValidator(array $data)
    {
        return Validator::make($data, [
            'user_id' => 'numeric|required',
            'work_place_id' => 'numeric|required',
            'type' => 'in:can_edit,can_create|required',
        ]);
    }

    /**
     * Check users permissions
     *
     * @param $item
     * @return boolean
     */
    protected function userHasNoPermissions($item)
    {
        if ($item instanceof Request) {
            $workPlace = WorkPlace::findOrFail($item->get('work_place_id'));
        } elseif ($item instanceof Permission) {
            $workPlace = $item->workPlace;
        }

        return (Gate::denies('grantPermission', $workPlace));
    }
}
