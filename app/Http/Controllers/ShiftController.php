<?php

namespace App\Http\Controllers;

use App\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as ResponseStatus;

class ShiftController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('can:edit')->except(['index', 'show']);
    // }

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

        $created = Shift::create($data);

        if (!$created) {
            return response()->json(['error' => 'An error occured.'], ResponseStatus::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['success' => 'Shift has been added to schedule'], ResponseStatus::HTTP_OK);
    }

    /**
     * Update existing data
     *
     * @param Response $response
     * @param Shift $shift
     * @return json
     */
    public function update(Request $request, Shift $shift)
    {
        $data = $this->validator($request->all(), 'update')->validate();
        $data['updated_by'] = Auth::id();

        if (!$shift->update($data)) {
            return response()->json(['error' => 'An error occured.'], ResponseStatus::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['success' => 'Shift has been updated'], ResponseStatus::HTTP_OK);
    }

    /**
     * Delete Worker
     *
     * @param Shift $shift
     * @return json
     */
    public function destroy(Shift $shift)
    {
        if (!$shift->delete()) {
            return response()->json(['error' => 'An error occured.'], ResponseStatus::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['success' => 'Shift has been deleted'], ResponseStatus::HTTP_OK);
    }

    /**
     * Validate incoming data
     *
     * @param array $data
     * @param string $reqestType
     * @return Validator
     */
    protected function validator(array $data, $reqestType = 'add')
    {
        if ($reqestType = 'update') {
            return Validator::make($data, [
                'worker_id' => 'numeric|min:0',
                'work_place_id' => 'numeric|min:0',
                'day' => 'date_format:Y-m-d',
                'shift_start' => 'date_format:H:i',
                'shift_end' => 'date_format:H:i',
            ]);
        }
        return Validator::make($data, [
            'worker_id' => 'numeric|min:0|required',
            'work_place_id' => 'numeric|min:0|required',
            'day' => 'date_format:Y-m-d|required',
            'shift_start' => 'date_format:H:i|required',
            'shift_end' => 'date_format:H:i|required',
        ]);
    }
}
