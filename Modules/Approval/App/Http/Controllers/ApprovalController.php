<?php

namespace Modules\Approval\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Approval\App\Models\ApprovalItem;

class ApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = ApprovalItem::all();

        return view('approval::index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('approval::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        $item = ApprovalItem::create($validated);

        return redirect()->route('approval.items.show', $item);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        $item = ApprovalItem::findOrFail($id);

        return view('approval::show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $item = ApprovalItem::findOrFail($id);

        return view('approval::edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $item = ApprovalItem::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $item->update($validated);

        return redirect()->route('approval.items.show', $item);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = ApprovalItem::findOrFail($id);
        $item->delete();

        return redirect()->route('approval.items.index');
    }
}
