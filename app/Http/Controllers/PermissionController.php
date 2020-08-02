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

        return response()->json(['success' => 'Work place has been created'], ResponseStatus::HTTP_OK);
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
        } elseif ($item instanceof Worker) {
            $workPlace = $item->workPlace;
        }

        return (Gate::denies('edit', $workPlace));
    }
}
