<?php

namespace Modules\FundRequest\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\FundRequest\Models\FundRequest;
use Modules\Role\App\Models\User; // Assuming this is the User model

class FundRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Show requests created by the user, or all if admin/approver (simplified for now)
        $user = Auth::user();
        if ($user->hasRole(['Manager', 'Director'])) {
            $fundRequests = FundRequest::with('user')->latest()->paginate(10);
        } else {
            $fundRequests = FundRequest::where('user_id', $user->id)->with('user')->latest()->paginate(10);
        }
        return view('fundrequest::index', compact('fundRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('fundrequest::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:1000',
        ]);

        $fundRequest = FundRequest::create([
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'description' => $request->description,
            'status' => 'pending_level1_approval', // Initial status from model default or explicit
        ]);

        // Automatically start the approval process
        // The Processable trait handles setting the first step's status if not already set
        // $fundRequest->startApproval(); // This might be redundant if status is set on create

        return redirect()->route('admin.fundrequests.index')->with('success', 'Fund request created successfully.');
    }

    /**
     * Show the specified resource.
     */
    public function show(FundRequest $fundRequest)
    {
        // $this->authorize('view', $fundRequest); // Temporarily commented out for debugging view/button visibility
        return view('fundrequest::show', compact('fundRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FundRequest $fundRequest)
    {
        // Only owner can edit, and only if it's in the initial pending state
        if (Auth::id() !== $fundRequest->user_id || $fundRequest->status !== 'pending_level1_approval') {
            // A more robust check would be to see if any approval actions exist for this request.
            // For now, checking initial status is a simplification.
            return redirect()->route('admin.fundrequests.index')->with('error', 'You cannot edit this fund request.');
        }
        return view('fundrequest::edit', compact('fundRequest'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FundRequest $fundRequest)
    {
        if (Auth::id() !== $fundRequest->user_id || $fundRequest->status !== 'pending_level1_approval') {
            return redirect()->route('admin.fundrequests.index')->with('error', 'You cannot update this fund request.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:1000',
        ]);

        $fundRequest->update($request->only(['amount', 'description']));

        return redirect()->route('admin.fundrequests.index')->with('success', 'Fund request updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FundRequest $fundRequest)
    {
        if (Auth::id() !== $fundRequest->user_id || $fundRequest->status !== 'pending_level1_approval') {
            return redirect()->route('admin.fundrequests.index')->with('error', 'You cannot delete this fund request.');
        }

        $fundRequest->delete();

        return redirect()->route('admin.fundrequests.index')->with('success', 'Fund request deleted successfully.');
    }

    /**
     * Approve the current step of the fund request.
     */
    public function approve(Request $request, FundRequest $fundRequest)
    {
        $user = Auth::user();
        $comments = $request->input('comments', 'Approved');

        try {
            if (!$fundRequest->canBeApprovedBy($user)) {
                return redirect()->back()->with('error', 'You are not authorized to approve this request at its current step.');
            }
            $fundRequest->approve($comments, $user);
            return redirect()->route('admin.fundrequests.show', $fundRequest)->with('success', 'Fund request approved.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error approving request: ' . $e->getMessage());
        }
    }

    /**
     * Reject the current step of the fund request.
     */
    public function reject(Request $request, FundRequest $fundRequest)
    {
        $user = Auth::user();
        $request->validate(['comments' => 'required|string|max:500']);
        $comments = $request->input('comments');

        try {
            if (!$fundRequest->canBeApprovedBy($user)) { // canBeApprovedBy also implies canRejectBy
                return redirect()->back()->with('error', 'You are not authorized to reject this request at its current step.');
            }
            $fundRequest->reject($comments, $user);
            return redirect()->route('admin.fundrequests.show', $fundRequest)->with('success', 'Fund request rejected.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error rejecting request: ' . $e->getMessage());
        }
    }
}
