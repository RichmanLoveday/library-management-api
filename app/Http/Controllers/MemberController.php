<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMembersRequest;
use App\Http\Resources\MemberResource;
use App\Models\Members;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Members::with(['activeBorrowings']);

        //? search functionality
        if ($request->has('search')) {
            $search = $request->search;

            //? search by name, email, or address
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });


            //? filter memeber by status
            if ($request->has('status')) {
                $query->where("status", $request->status);
            }
        }

        //? get paginated members
        $members = $query->paginate(10);
        return MemberResource::collection($members);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request): MemberResource
    {
        $members = Members::create($request->validated());
        $members->load(['activeBorrowings']);
        return new MemberResource($members);
    }

    /**
     * Display the specified resource.
     */
    public function show(string|int $id)
    {
        try {
            $member = Members::with(['activeBorrowings', 'borrowings'])->findOrFail($id);
            return new MemberResource($member);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Member not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMembersRequest $request, string|int $id)
    {
        try {
            $member = Members::with(['activeBorrowings'])->findOrFail($id);
            $member->update($request->validated());

            return new MemberResource($member);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Member not found'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|int $id)
    {
        try {
            $member = Members::findOrFail($id);

            //? check if member has active borrowings
            if ($member->activeBorrowings()->count() > 0) {
                return response()->json(['message' => 'Cannot delete member with active borrowings'], 400);
            }

            $member->delete();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Member deleted successfully'
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Member not found'
            ], 404);
        }
    }
}