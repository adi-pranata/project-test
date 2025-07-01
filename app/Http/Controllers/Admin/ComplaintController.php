<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintDocument;
use App\Models\ComplaintStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Notifications\ComplaintStatusChanged;

class ComplaintController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        $request->validate([
            'status' => 'required|in:pending_verification,approved,needs_revision,in_progress,completed,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        
        try {
            $oldStatus = $complaint->status;
            
            $complaint->update([
                'status' => $request->status,
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            if ($request->status === 'completed') {
                $complaint->update(['completed_at' => now()]);
            }

            // Create status history
            ComplaintStatus::create([
                'complaint_id' => $complaint->id,
                'status' => $request->status,
                'notes' => $request->notes,
                'processed_by' => Auth::id(),
            ]);

            DB::commit();

            // Send notification to user
            $complaint->user->notify(new ComplaintStatusChanged($complaint));

            return back()->with('success', 'Status pengaduan berhasil diupdate.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat mengupdate status.']);
        }
    }

    public function uploadResultDocument(Request $request, Complaint $complaint)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
            'document_type' => 'required|string|max:100',
        ]);

        try {
            $file = $request->file('document');
            $path = $file->store('complaints/' . $complaint->id . '/results', 'public');
            
            ComplaintDocument::create([
                'complaint_id' => $complaint->id,
                'document_type' => $request->document_type,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'is_result_document' => true,
            ]);

            // Create status update
            ComplaintStatus::create([
                'complaint_id' => $complaint->id,
                'status' => $complaint->status,
                'notes' => 'Dokumen hasil layanan telah diupload: ' . $request->document_type,
                'processed_by' => Auth::id(),
            ]);

            // Send notification
            $complaint->user->notify(new ComplaintStatusChanged($complaint));

            return back()->with('success', 'Dokumen hasil berhasil diupload.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat mengupload dokumen.']);
        }
    }

    public function deleteResultDocument(ComplaintDocument $document)
    {
        if (!$document->is_result_document) {
            return back()->withErrors(['error' => 'Hanya dokumen hasil yang dapat dihapus.']);
        }

        try {
            // Delete file from storage
            Storage::disk('public')->delete($document->file_path);
            
            // Delete record
            $document->delete();

            return back()->with('success', 'Dokumen hasil berhasil dihapus.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus dokumen.']);
        }
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'complaint_ids' => 'required|array',
            'complaint_ids.*' => 'exists:complaints,id',
            'status' => 'required|in:pending_verification,approved,needs_revision,in_progress,completed,rejected',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        
        try {
            $complaints = Complaint::whereIn('id', $request->complaint_ids)->get();
            
            foreach ($complaints as $complaint) {
                $complaint->update([
                    'status' => $request->status,
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                ]);

                if ($request->status === 'completed') {
                    $complaint->update(['completed_at' => now()]);
                }

                // Create status history
                ComplaintStatus::create([
                    'complaint_id' => $complaint->id,
                    'status' => $request->status,
                    'notes' => $request->notes,
                    'processed_by' => Auth::id(),
                ]);

                // Send notification
                $complaint->user->notify(new ComplaintStatusChanged($complaint));
            }

            DB::commit();

            return back()->with('success', 'Status ' . count($request->complaint_ids) . ' pengaduan berhasil diupdate.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat mengupdate status.']);
        }
    }

    public function export(Request $request)
    {
        $query = Complaint::with(['user', 'service', 'service.category']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $complaints = $query->get();

        $filename = 'laporan_pengaduan_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($complaints) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, [
                'No Registrasi',
                'Nama Pemohon',
                'NIK',
                'Layanan',
                'Kategori',
                'Status',
                'Tanggal Pengajuan',
                'Tanggal Selesai'
            ]);

            // Data
            foreach ($complaints as $complaint) {
                fputcsv($file, [
                    $complaint->registration_number,
                    $complaint->user->name,
                    $complaint->user->nik,
                    $complaint->service->name,
                    $complaint->service->category->name,
                    $complaint->getStatusLabel(),
                    $complaint->created_at->format('d/m/Y H:i'),
                    $complaint->completed_at ? $complaint->completed_at->format('d/m/Y H:i') : '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

}
