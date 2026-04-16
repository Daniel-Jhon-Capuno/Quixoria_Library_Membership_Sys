<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipTier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MembershipTierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tiers = MembershipTier::orderBy('priority_level')->get();

        return view('admin.tiers.index', compact('tiers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.tiers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:membership_tiers,name'],
            'monthly_fee' => ['required', 'numeric', 'min:0'],
            'borrow_limit_per_week' => ['required', 'integer', 'min:0'],
            'borrow_duration_days' => ['required', 'integer', 'min:0'],
            'can_reserve' => ['boolean'],
            'renewal_limit' => ['required', 'integer', 'min:0'],
            'late_fee_per_day' => ['required', 'numeric', 'min:0'],
            'priority_level' => ['required', 'integer', 'min:0'],
        ]);

        $data['can_reserve'] = $request->boolean('can_reserve');

        MembershipTier::create($data);

        return redirect()->route('admin.tiers.index')->with('success', 'Membership tier created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MembershipTier $tier)
    {
        return redirect()->route('admin.tiers.edit', $tier);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MembershipTier $tier)
    {
        return view('admin.tiers.edit', compact('tier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MembershipTier $tier)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('membership_tiers', 'name')->ignore($tier->id)],
            'monthly_fee' => ['required', 'numeric', 'min:0'],
            'borrow_limit_per_week' => ['required', 'integer', 'min:0'],
            'borrow_duration_days' => ['required', 'integer', 'min:0'],
            'can_reserve' => ['boolean'],
            'renewal_limit' => ['required', 'integer', 'min:0'],
            'late_fee_per_day' => ['required', 'numeric', 'min:0'],
            'priority_level' => ['required', 'integer', 'min:0'],
        ]);

        $data['can_reserve'] = $request->boolean('can_reserve');

        $tier->update($data);

        return redirect()->route('admin.tiers.index')->with('success', 'Membership tier updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MembershipTier $tier)
    {
        $tier->delete();

        return redirect()->route('admin.tiers.index')->with('success', 'Membership tier deleted successfully.');
    }
}
