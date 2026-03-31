<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminReportController extends Controller
{
    public function index(): View
    {
        $reports = Report::with(['user', 'topic', 'reply', 'reply.topic', 'reply.user'])
            ->latest()
            ->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }

    public function destroy(Report $report): RedirectResponse
    {
        $report->delete();

        return back()->with('success', 'Signalement supprime.');
    }

    public function resolve(Report $report): RedirectResponse
    {
        $report->update([
            'status' => 'resolved',
        ]);

        return back()->with('success', 'Signalement marque comme traite.');
    }

    public function ignore(Report $report): RedirectResponse
    {
        $report->update([
            'status' => 'ignored',
        ]);

        return back()->with('success', 'Signalement ignore.');
    }

    public function destroyReportedTopic(Report $report): RedirectResponse
    {
        abort_unless($report->topic, 404);

        $topic = $report->topic;

        $report->update([
            'status' => 'resolved',
            'topic_id' => null,
        ]);

        $topic->delete();

        return back()->with('success', 'Sujet signale supprime et signalement traite.');
    }

    public function destroyReportedReply(Report $report): RedirectResponse
    {
        abort_unless($report->reply, 404);

        $reply = $report->reply;

        $report->update([
            'status' => 'resolved',
            'reply_id' => null,
        ]);

        $reply->delete();

        return back()->with('success', 'Reponse signalee supprimee et signalement traite.');
    }
}
