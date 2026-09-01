<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddChildrenRequest;
use Illuminate\Support\Facades\DB;

class ChildrenController extends Controller
{
    /**
     * POST /api/v1/children
     */
    public function store(AddChildrenRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();

        $children = DB::transaction(function () use ($data, $user) {

            // Replace previously saved child details
            $user->children()->delete();

            $children = [];

            foreach ($data['children'] as $child) {
                $children[] = $user->children()->create([
                    'name' => $child['name'],
                    'gender' => $child['gender'],
                    'dob' => $child['dob'],
                ]);
            }

            // Save TOTAL children counts
            $user->update([
                'total_children' => $data['total_children'],
                'total_daughters' => $data['total_daughters'],
                'total_sons' => $data['total_sons'],
                'total_transgender' => $data['total_transgender'],
            ]);

            return $children;
        });

        return response()->json([
            'success' => true,
            'message' => 'Children details saved successfully.',
            'summary' => [
                'total_children' => $data['total_children'],
                'total_daughters' => $data['total_daughters'],
                'total_sons' => $data['total_sons'],
                'total_transgender' => $data['total_transgender'],
                'details_available' => count($children),
                'details_missing' => max(
                    0,
                    $data['total_children'] - count($children)
                ),
            ],
            'children' => collect($children)->map(function ($child) {
                return [
                    'id' => $child->id,
                    'name' => $child->name,
                    'gender' => $child->gender,
                    'dob' => $child->dob->format('Y-m-d'),
                ];
            })->values(),
        ], 201);
    }

    /**
     * GET /api/v1/children
     */
    public function index()
    {
        $user = auth()->user();

        $children = $user->children()->get();

        $detailsAvailable = $children->count();
        $detailsMissing = max(
            0,
            $user->total_children - $detailsAvailable
        );

        return response()->json([
            'success' => true,
            'message' => 'Children details fetched successfully.',

            'summary' => [
                'total_children' => $user->total_children,
                'total_daughters' => $user->total_daughters,
                'total_sons' => $user->total_sons,
                'total_transgender' => $user->total_transgender,
                'details_available' => $detailsAvailable,
                'details_missing' => $detailsMissing,
            ],

            'children' => $children->map(function ($child) {
                return [
                    'id' => $child->id,
                    'name' => $child->name,
                    'gender' => $child->gender,
                    'dob' => $child->dob->format('Y-m-d'),
                ];
            })->values(),
        ]);
    }
}
