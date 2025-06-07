<?php

namespace Modules\FundRequest\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\FundRequest\App\Models\FundRequest;
use Yajra\DataTables\Facades\DataTables;

class FundRequestController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = FundRequest::with('user')->select('fund_requests.*');
            return DataTables::of($data)
                ->addColumn('action', function($row){
                    $showUrl = route('admin.fund-request.show', $row->id);
                    $editUrl = route('admin.fund-request.edit', $row->id);
                    $deleteUrl = route('admin.fund-request.destroy', $row->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');
                    $btn = '<a href="'.$showUrl.'" class="btn btn-info btn-sm">Show</a> ';
                    $btn .= '<a href="'.$editUrl.'" class="btn btn-primary btn-sm">Edit</a> ';
                    $btn .= '<form action="'.$deleteUrl.'" method="POST" style="display:inline-block;">'.$csrf.$method.'<button type="submit" class="btn btn-danger btn-sm">Delete</button></form>';
                    return $btn;
                })
                ->editColumn('status', function($row){
                    return ucfirst(str_replace('_',' ', $row->status));
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('fundrequest::index');
    }

    public function create()
    {
        return view('fundrequest::create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric',
            'purpose' => 'required|string',
        ]);

        FundRequest::create([
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'purpose' => $request->purpose,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.fund-request.index')
            ->with('success', 'Fund request submitted successfully.');
    }

    public function show($id)
    {
        $fundRequest = FundRequest::findOrFail($id);
        return view('fundrequest::show', compact('fundRequest'));
    }

    public function edit($id)
    {
        $fundRequest = FundRequest::findOrFail($id);
        return view('fundrequest::edit', compact('fundRequest'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $fundRequest = FundRequest::findOrFail($id);
        $request->validate([
            'amount' => 'required|numeric',
            'purpose' => 'required|string',
        ]);
        $fundRequest->update($request->only('amount','purpose'));

        return redirect()->route('admin.fund-request.index')
            ->with('success', 'Fund request updated successfully');
    }

    public function destroy($id)
    {
        $fundRequest = FundRequest::findOrFail($id);
        $fundRequest->delete();
        return redirect()->route('admin.fund-request.index')
            ->with('success', 'Fund request deleted successfully');
    }

    public function approve(Request $request, $id): RedirectResponse
    {
        $fundRequest = FundRequest::findOrFail($id);
        if ($fundRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Request not in pending state');
        }
        $fundRequest->status = 'admin_approved';
        $fundRequest->admin_id = Auth::id();
        $fundRequest->save();
        return redirect()->back()->with('success', 'Request approved');
    }

    public function reject(Request $request, $id): RedirectResponse
    {
        $fundRequest = FundRequest::findOrFail($id);
        if ($fundRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Request not in pending state');
        }
        $fundRequest->status = 'admin_rejected';
        $fundRequest->admin_id = Auth::id();
        $fundRequest->rejection_reason = $request->input('reason');
        $fundRequest->save();
        return redirect()->back()->with('success', 'Request rejected');
    }

    public function finalApprove(Request $request, $id): RedirectResponse
    {
        $fundRequest = FundRequest::findOrFail($id);
        if ($fundRequest->status !== 'admin_approved') {
            return redirect()->back()->with('error', 'Request not approved by admin');
        }
        $fundRequest->status = 'final_approved';
        $fundRequest->super_admin_id = Auth::id();
        $fundRequest->save();
        return redirect()->back()->with('success', 'Final approval done');
    }

    public function finalReject(Request $request, $id): RedirectResponse
    {
        $fundRequest = FundRequest::findOrFail($id);
        if ($fundRequest->status !== 'admin_approved') {
            return redirect()->back()->with('error', 'Request not approved by admin');
        }
        $fundRequest->status = 'final_rejected';
        $fundRequest->super_admin_id = Auth::id();
        $fundRequest->rejection_reason = $request->input('reason');
        $fundRequest->save();
        return redirect()->back()->with('success', 'Final rejection done');
    }
}
