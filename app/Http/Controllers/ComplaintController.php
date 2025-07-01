<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Service;
use App\Models\ComplaintDocument;
use App\Models\ComplaintStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ComplaintRequest;
use App\Notifications\ComplaintStatusChanged;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $complaints = Auth::user()->complaints()
            ->with(['service', 'service.category'])
            ->latest()
            ->paginate(10);
            
        return view('complaints.index', compact('complaints'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Service $service)
    {
        $user = Auth::user();
        return view('complaints.create', compact('service', 'user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ComplaintRequest $request, Service $service)
    {
        DB::beginTransaction();
        
        try {
            $complaint = Complaint::create([
                'user_id' => Auth::id(),
                'service_id' => $service->id,
                'registration_number' => Complaint::generateRegistrationNumber(),
                'status' => 'submitted',
                'identity_data' => $request->only(['name', 'nik', 'phone', 'address', 'job', 'birth_date']),
                'submitted_at' => now(),
            ]);

            // Upload documents
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $type => $file) {
                    if ($file) {
                        $path = $file->store('complaints/' . $complaint->id, 'public');
                        
                        ComplaintDocument::create([
                            'complaint_id' => $complaint->id,
                            'document_type' => $type,
                            'file_path' => $path,
                            'file_name' => $file->getClientOriginalName(),
                            'file_size' => $file->getSize(),
                            'mime_type' => $file->getMimeType(),
                        ]);
                    }
                }
            }

            // Create initial status
            ComplaintStatus::create([
                'complaint_id' => $complaint->id,
                'status' => 'submitted',
                'notes' => 'Pengaduan berhasil diajukan dan menunggu verifikasi.',
            ]);

            DB::commit();

            // Send email notification
            $complaint->user->notify(new ComplaintStatusChanged($complaint));

            return redirect()->route('complaints.show', $complaint)
                ->with('success', 'Pengaduan berhasil diajukan dengan nomor registrasi: ' . $complaint->registration_number);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan pengaduan.']);
        }
    
    }

    /**
     * Display the specified resource.
     */
    public function show(Complaint $complaint)
    {
        $this->authorize('view', $complaint);
        
        $complaint->load(['service', 'service.category', 'documents', 'statuses.processor']);
        
        return view('complaints.show', compact('complaint'));
    }

    public function trackResult(Request $request)
    {
        $request->validate([
            'registration_number' => 'required|exists:complaints,registration_number',
            'nik' => 'required|exists:users,nik',
        ]);

        $complaint = Complaint::with(['service', 'user', 'statuses'])
            ->where('registration_number', $request->registration_number)
            ->whereHas('user', function($q) use ($request) {
                $q->where('nik', $request->nik);
            })
            ->first();

        if (!$complaint) {
            return back()->withErrors(['error' => 'Data tidak ditemukan.']);
        }

        return view('complaints.track-result', compact('complaint'));
    }

    public function downloadDocument(ComplaintDocument $document)
    {
        $this->authorize('download', $document);
        
        return Storage::disk('public')->download($document->file_path, $document->file_name);
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
