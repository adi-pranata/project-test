<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function dashboard()
    {
        $stats = [
            'total_complaints' => Complaint::count(),
            'pending_verification' => Complaint::where('status', 'pending_verification')->count(),
            'in_progress' => Complaint::whereIn('status', ['approved', 'in_progress'])->count(),
            'completed_today' => Complaint::where('status', 'completed')
                ->whereDate('completed_at', today())->count(),
        ];

        $recentComplaints = Complaint::with(['user', 'service'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentComplaints'));
    }

    public function complaints()
    {
        $complaints = Complaint::with(['user', 'service', 'service.category'])
            ->when(request('status'), function($q) {
                $q->where('status', request('status'));
            })
            ->when(request('search'), function($q) {
                $q->where('registration_number', 'like', '%' . request('search') . '%')
                  ->orWhereHas('user', function($q) {
                      $q->where('name', 'like', '%' . request('search') . '%');
                  });
            })
            ->latest()
            ->paginate(15);

        return view('admin.complaints.index', compact('complaints'));
    }

    public function showComplaint(Complaint $complaint)
    {
        $complaint->load(['user', 'service', 'documents', 'statuses.processor']);
        return view('admin.complaints.show', compact('complaint'));
    }
}
