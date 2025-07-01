<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $recentComplaints = $user->complaints()
            ->with(['service', 'service.category'])
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total' => $user->complaints()->count(),
            'pending' => $user->complaints()->whereIn('status', ['submitted', 'pending_verification'])->count(),
            'in_progress' => $user->complaints()->whereIn('status', ['approved', 'in_progress'])->count(),
            'completed' => $user->complaints()->where('status', 'completed')->count(),
        ];

        return view('dashboard', compact('recentComplaints', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
