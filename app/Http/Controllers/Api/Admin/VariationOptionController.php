<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\VariationOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VariationOptionController extends Controller
{
    /**
     * Store a newly created variation option
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'variation_group_id' => 'required|exists:variation_groups,id',
            'name' => 'required|string|max:255',
            'price_adjustment' => 'nullable|numeric',
            'is_default' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $option = VariationOption::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Variation option created successfully',
            'data' => $option,
        ], 201);
    }

    /**
     * Update the specified variation option
     */
    public function update(Request $request, $id)
    {
        $option = VariationOption::find($id);

        if (!$option) {
            return response()->json([
                'success' => false,
                'error' => 'Variation option not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'price_adjustment' => 'nullable|numeric',
            'is_default' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $option->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Variation option updated successfully',
            'data' => $option,
        ]);
    }

    /**
     * Remove the specified variation option
     */
    public function destroy($id)
    {
        $option = VariationOption::find($id);

        if (!$option) {
            return response()->json([
                'success' => false,
                'error' => 'Variation option not found',
            ], 404);
        }

        $option->delete();

        return response()->json([
            'success' => true,
            'message' => 'Variation option deleted successfully',
        ]);
    }

    /**
     * Reorder variation options
     */
    public function reorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'options' => 'required|array',
            'options.*.id' => 'required|exists:variation_options,id',
            'options.*.order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        foreach ($request->options as $optionData) {
            VariationOption::where('id', $optionData['id'])
                ->update(['order' => $optionData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Options reordered successfully',
        ]);
    }
}
