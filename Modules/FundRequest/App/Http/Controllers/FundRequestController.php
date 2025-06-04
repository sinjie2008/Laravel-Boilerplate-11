<?php

namespace Modules\FundRequest\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\FundRequest\App\Models\FundRequest;

class FundRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view fund requests', ['only' => ['index', 'show']]);
        $this->middleware('permission:create fund requests', ['only' => ['create','store']]);
        $this->middleware('permission:update fund requests', ['only' => ['edit','update']]);
        $this->middleware('permission:delete fund requests', ['only' => ['destroy']]);
    }

    public function index()
    {
        $fundRequests = FundRequest::latest()->get();
        return view('fundrequest::index', compact('fundRequests'));
    }

    public function create()
    {
        return view('fundrequest::create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $fundRequest = FundRequest::create([
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        $fundRequest->submit();

        return redirect()->route('fundrequest.fundrequests.index')
            ->with('success', 'Fund request submitted successfully.');
    }

    public function show(FundRequest $fundrequest)
    {
        return view('fundrequest::show', compact('fundrequest'));
    }

    public function edit(FundRequest $fundrequest)
    {
        return view('fundrequest::edit', compact('fundrequest'));
    }

    public function update(Request $request, FundRequest $fundrequest): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $fundrequest->update($request->only('amount', 'description'));

        return redirect()->route('fundrequest.fundrequests.index')
            ->with('success', 'Fund request updated successfully.');
    }

    public function destroy(FundRequest $fundrequest): RedirectResponse
    {
        $fundrequest->delete();

        return redirect()->route('fundrequest.fundrequests.index')
            ->with('success', 'Fund request deleted successfully.');
    }
}
