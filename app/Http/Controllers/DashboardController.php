<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Issues\Models\Article;
use Modules\Issues\Models\Issue;
use Modules\Journals\Models\Journal;
use Modules\Submissions\Models\ReviewerAssignment;
use Modules\Submissions\Models\Submission;

class DashboardController extends Controller
{
    /**
     * Display the comprehensive role-aware editorial dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Determine user roles & capabilities
        $isEditorOrAdmin = $user->hasRole(['administrator', 'journal-admin', 'editor', 'Super Admin'])
            || $user->can('submissions.decide')
            || $user->can('submissions.read');

        $isReviewer = $user->hasRole('reviewer')
            || $user->can('submissions.review')
            || ReviewerAssignment::where('reviewer_id', $user->id)->exists();

        // 1. Editorial Metrics (Admin & Editor)
        $editorialMetrics = [];
        $actionNeededSubmissions = collect();
        if ($isEditorOrAdmin) {
            $editorialMetrics = [
                'total_submissions'       => Submission::where('status', '!=', 'draft')->count(),
                'need_assignment'         => Submission::whereIn('status', ['submitted', 'under_editor_review'])->count(),
                'in_peer_review'          => Submission::where('status', 'under_review')->count(),
                'revision_pending'        => Submission::where('status', 'revision_required')->count(),
                'revision_received'       => Submission::where('status', 'resubmitted')->count(),
                'ready_to_publish'        => Submission::where('status', 'accepted')->count(),
                'published_articles'      => Article::count(),
                'total_journals'          => Journal::where('is_active', true)->count(),
                'total_issues'            => Issue::count(),
                'total_article_views'     => Article::sum('views_count'),
                'total_article_downloads' => Article::sum('downloads_count'),
            ];

            $actionNeededSubmissions = Submission::with(['journal', 'authors', 'editor'])
                ->whereIn('status', ['submitted', 'resubmitted', 'under_editor_review'])
                ->latest('updated_at')
                ->take(6)
                ->get();
        }

        // 2. Reviewer Metrics (Mitra Bestari)
        $reviewerMetrics = [];
        $myReviewAssignments = collect();
        if ($isReviewer) {
            $reviewerMetrics = [
                'pending_invitations' => ReviewerAssignment::where('reviewer_id', $user->id)->where('response_status', 'pending')->count(),
                'active_reviews'      => ReviewerAssignment::where('reviewer_id', $user->id)->where('response_status', 'accepted')->count(),
                'completed_reviews'   => ReviewerAssignment::where('reviewer_id', $user->id)->where('response_status', 'completed')->count(),
            ];

            $myReviewAssignments = ReviewerAssignment::with(['round.submission.journal'])
                ->where('reviewer_id', $user->id)
                ->whereIn('response_status', ['pending', 'accepted'])
                ->orderBy('due_date', 'asc')
                ->take(5)
                ->get();
        }

        // 3. Author Metrics (Penulis)
        $authorMetrics = [
            'total_manuscripts' => Submission::where('corresponding_author_id', $user->id)->count(),
            'drafts'            => Submission::where('corresponding_author_id', $user->id)->where('status', 'draft')->count(),
            'in_progress'       => Submission::where('corresponding_author_id', $user->id)->whereIn('status', ['submitted', 'under_editor_review', 'under_review', 'resubmitted'])->count(),
            'revision_needed'   => Submission::where('corresponding_author_id', $user->id)->where('status', 'revision_required')->count(),
            'accepted_or_pub'   => Submission::where('corresponding_author_id', $user->id)->whereIn('status', ['accepted', 'published'])->count(),
        ];

        $myRecentSubmissions = Submission::with('journal')
            ->where('corresponding_author_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // 4. Quick Recent Published Articles
        $recentPublishedArticles = Article::with(['issue.journal', 'submission.authors'])
            ->latest('published_at')
            ->take(4)
            ->get();

        return view('pages.index', compact(
            'user',
            'isEditorOrAdmin',
            'isReviewer',
            'editorialMetrics',
            'actionNeededSubmissions',
            'reviewerMetrics',
            'myReviewAssignments',
            'authorMetrics',
            'myRecentSubmissions',
            'recentPublishedArticles'
        ));
    }
}
