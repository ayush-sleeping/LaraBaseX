<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * CODE STRUCTURE SUMMARY:
 * EnquiryController ( Handles enquiry management for the backend administration, Provides CRUD operations and data tables for enquiries. )
 * Display a listing of enquiries
 * Display the specified enquiry
 * Update remark for the specified enquiry
 * Remove the specified enquiry from storage
 * Get enquiry statistics
 */
class EnquiryController extends Controller
{
    /* Display a listing of enquiries :: */
    public function index(Request $request): Response
    {
        $query = Enquiry::query();

        // Apply remark filter if provided
        if ($request->filled('remark_status')) {
            if ($request->remark_status === 'with_remark') {
                $query->whereNotNull('remark')->where('remark', '!=', '');
            } elseif ($request->remark_status === 'without_remark') {
                $query->where(function ($q) {
                    $q->whereNull('remark')->orWhere('remark', '');
                });
            }
        }

        // Apply date range filter if provided
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $enquiries = $query->orderBy('created_at', 'desc')->get();

        return Inertia::render('backend/enquiries/index', [
            'enquiries' => $enquiries,
            'filters' => [
                'remark_status' => $request->remark_status,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
            ],
        ]);
    }

    /* Display the specified enquiry :: */
    public function show(Enquiry $enquiry): Response
    {
        $enquiry->load(['createdBy:id,first_name,last_name', 'updatedBy:id,first_name,last_name']);

        return Inertia::render('backend/enquiries/show', compact('enquiry'));
    }

    /* Update remark for the specified enquiry :: */
    public function updateRemark(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $request->validate([
            'remark' => 'required|string|max:1000',
        ], [
            'remark.required' => 'Remark is required',
            'remark.max' => 'Remark cannot exceed 1000 characters',
        ]);

        try {
            $enquiry->remark = $request->remark;
            $enquiry->save();

            return redirect()->back()->with('success', 'Remark updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update remark: '.$e->getMessage());
        }
    }

    /* Remove the specified enquiry from storage :: */
    public function destroy(Enquiry $enquiry): RedirectResponse
    {
        $enquiry->delete();

        return redirect()->route('admin.enquiries.index')->with('success', 'Enquiry deleted successfully.');
    }

    /* Get enquiry statistics :: */
    public function stats(): JsonResponse
    {
        $stats = [
            'total' => Enquiry::count(),
            'today' => Enquiry::whereDate('created_at', today())->count(),
            'this_week' => Enquiry::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Enquiry::whereMonth('created_at', now()->month)->count(),
        ];

        return response()->json([
            'status' => 'success',
            'stats' => $stats,
        ], 200);
    }
}
