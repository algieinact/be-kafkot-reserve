<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\VariationGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VariationGroupController extends Controller
{
    /**
     * Get all variation groups with their options
     */
    public function index()
    {
        $groups = VariationGroup::with('options')->get();

        return response()->json([
            'success' => true,
            'data' => $groups,
        ]);
    }

    /**
     * Store a newly created variation group
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:single_choice,multiple_choice',
            'is_required' => 'nullable|boolean',
            'min_selections' => 'nullable|integer|min:0',
            'max_selections' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $group = VariationGroup::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Variation group created successfully',
            'data' => $group->load('options'),
        ], 201);
    }

    /**
     * Display the specified variation group
     */
    public function show($id)
    {
        $group = VariationGroup::with('options')->find($id);

        if (!$group) {
            return response()->json([
                'success' => false,
                'error' => 'Variation group not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $group,
        ]);
    }

    /**
     * Update the specified variation group
     */
    public function update(Request $request, $id)
    {
        $group = VariationGroup::find($id);

        if (!$group) {
            return response()->json([
                'success' => false,
                'error' => 'Variation group not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:single_choice,multiple_choice',
            'is_required' => 'nullable|boolean',
            'min_selections' => 'nullable|integer|min:0',
            'max_selections' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $group->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Variation group updated successfully',
            'data' => $group->load('options'),
        ]);
    }

    /**
     * Remove the specified variation group
     */
    public function destroy($id)
    {
        $group = VariationGroup::find($id);

        if (!$group) {
            return response()->json([
                'success' => false,
                'error' => 'Variation group not found',
            ], 404);
        }

        $group->delete();

        return response()->json([
            'success' => true,
            'message' => 'Variation group deleted successfully',
        ]);
    }
}
