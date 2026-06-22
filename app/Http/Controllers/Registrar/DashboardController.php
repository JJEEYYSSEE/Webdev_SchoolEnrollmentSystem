<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\SchoolYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Registrar home — enrollment stats, active school year, recent enrollments.
    public function showDashboard(Request $request)
    {
        $schoolYear = SchoolYear::active();

        $base = Enrollment::query()
            ->when($schoolYear, fn ($q) => $q->whereHas('section', fn ($s) => $s->where('school_year_id', $schoolYear->id)));

        $pendingCount  = (clone $base)->where('status', 'pending')->count();
        $approvedCount = (clone $base)->where('status', 'approved')->count();
        $rejectedCount = (clone $base)->where('status', 'invalid')->count();

        // Approved enrollments grouped by strand
        $perStrand = DB::table('enrollments')
            ->join('sections', 'sections.id', '=', 'enrollments.section_id')
            ->join('strands', 'strands.id', '=', 'sections.strand_id')
            ->where('enrollments.status', 'approved')
            ->when($schoolYear, fn ($q) => $q->where('sections.school_year_id', $schoolYear->id))
            ->groupBy('strands.strand_code')
            ->selectRaw('strands.strand_code as strand, count(*) as total')
            ->pluck('total', 'strand');

        $recentEnrollments = (clone $base)
            ->with(['student', 'section.strand'])
            ->latest('submitted_at')
            ->take(5)
            ->get();

        return view('registrar.dashboard', compact(
            'schoolYear', 'pendingCount', 'approvedCount', 'rejectedCount', 'perStrand', 'recentEnrollments'
        ));
    }
}
