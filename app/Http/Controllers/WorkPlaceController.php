<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;
use App\Http\Resources\WorkPlaceResource;
use App\WorkPlace;
use Illuminate\Support\Facades\Gate;

class WorkPlaceController extends Controller
{
    /**
     * Show single work placed
     *
     * @param WorkPlace $workPlace
     * @return WorkPlaceResource
     */
    public function show(WorkPlace $workPlace)
    {
        return new WorkPlaceResource($workPlace);
    }

    /**
     * Show multiple work places
     *
     * @return WorkPlaceResource
     */
    public function index()
    {
        return WorkPlaceResource::collection(WorkPlace::all());
    }
    
    /**
     * Store newly created data
     *
     * @param Response $response
     * @return json
     */
    public function store(Request $request)
    {
        $data = $this->validator($request->all())->validate();
        $data['created_by'] = Auth::id();

        $created = WorkPlace::create($data);

        if (!$created) {
            return response()->json(['error' => 'An error occured.'], ResponseStatus::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['success' => 'Work place has been created'], ResponseStatus::HTTP_OK);
    }

    /**
     * Update existing data
     *
     * @param Response $response
     * @param WorkPlace $workPlace
     * @return json
     */
    public function update(Request $request, WorkPlace $workPlace)
    {
        if (Gate::denies('edit', $workPlace)) {
            return response()->json(['error' => 'Access denied.'], ResponseStatus::HTTP_FORBIDDEN);
        }

        $data = $this->validator($request->all())->validate();
        $data['updated_by'] = Auth::id();

        if (!$workPlace->update($data)) {
            return response()->json(['error' => 'An error occured.'], ResponseStatus::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['success' => 'Work place has been updated'], ResponseStatus::HTTP_OK);
    }

    /**
     * Delete Work Place
     *
     * @param WorkPlace $workPlace
     * @return json
     */
    public function destroy(WorkPlace $workPlace)
    {
        if (Gate::denies('delete', $workPlace)) {
            abort(403, 'Nope.');
        }

        if (!$workPlace->delete()) {
            return response()->json(['error' => 'An error occured.'], ResponseStatus::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['success' => 'Work place has been deleted'], ResponseStatus::HTTP_OK);
    }

    /**
     * Validate incoming data
     *
     * @param array $request
     * @return Validator
     */
    public function validator($request)
    {
        return Validator::make($request, [
            'name' => 'string|max:50|required',
            'logo_path' => 'string|max:1000',
            'address' => 'string|max:255'
        ]);
    }
}
