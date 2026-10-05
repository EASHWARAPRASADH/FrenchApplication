<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentDailyStatus;
use App\Models\StudentDailyTopic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentStatusController extends Controller
{
    public function index(Request $request)
    {
        // Use Canada/Eastern (Toronto) as the standard "Canada Time"
        $canadaTime = now()->setTimezone('America/Toronto');
        // Default to current month based on Canada time if no month param
        $date = $request->has('month') ? Carbon::parse($request->month) : $canadaTime->copy();

        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        // Get existing statuses for the month with topics
        $statuses = StudentDailyStatus::with('topics')
            ->where('user_id', Auth::id())
            ->whereBetween('log_date', [$startOfMonth, $endOfMonth])
            ->get()
            ->keyBy(function ($item) {
                return $item->log_date->format('Y-m-d');
            });

        // Generate calendar days
        $days = [];
        $current = $startOfMonth->copy();
        while ($current <= $endOfMonth) {
            $dateStr = $current->format('Y-m-d');
            $status = $statuses->get($dateStr);

            $days[] = [
                'date' => $current->copy(),
                'status' => $status,
                // Check if this specific day matches "Today" in Canada Time
                'is_today' => $current->format('Y-m-d') === $canadaTime->format('Y-m-d')
            ];
            $current->addDay();
        }

        return view('student.status.index', compact('days', 'date'));
    }

    public function storeTopic(Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        $canadaTime = now()->setTimezone('America/Toronto');
        $requestDate = Carbon::parse($request->date);

        if ($requestDate->format('Y-m-d') !== $canadaTime->format('Y-m-d')) {
            return response()->json(['success' => false, 'message' => 'You can only edit status for today (Canada Time).'], 403);
        }

        $status = StudentDailyStatus::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'log_date' => $request->date,
            ]
        );

        $topic = $status->topics()->create([
            'topic' => '',
            'starting_time' => '',
            'ending_time' => '',
            'duration' => ''
        ]);

        return response()->json([
            'success' => true,
            'topic' => $topic
        ]);
    }

    public function updateTopic(Request $request)
    {
        $request->validate([
            'topic_id' => 'required|exists:student_daily_topics,id',
            'field' => 'required|in:topic,starting_time,ending_time,duration',
            'value' => 'nullable|string'
        ]);

        $topic = StudentDailyTopic::with('status')->find($request->topic_id);

        if ($topic->status->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Validate Date
        $canadaTime = now()->setTimezone('America/Toronto');
        $logDate = is_string($topic->status->log_date) ? Carbon::parse($topic->status->log_date) : $topic->status->log_date;

        if ($logDate->format('Y-m-d') !== $canadaTime->format('Y-m-d')) {
            return response()->json(['success' => false, 'message' => 'You can only edit status for today (Canada Time).'], 403);
        }

        $topic->update([
            $request->field => $request->value
        ]);

        return response()->json(['success' => true]);
    }

    public function deleteTopic(Request $request)
    {
        $request->validate([
            'topic_id' => 'required|exists:student_daily_topics,id',
        ]);

        $topic = StudentDailyTopic::with('status')->find($request->topic_id);

        if ($topic->status->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Validate Date
        $canadaTime = now()->setTimezone('America/Toronto');
        $logDate = is_string($topic->status->log_date) ? Carbon::parse($topic->status->log_date) : $topic->status->log_date;

        if ($logDate->format('Y-m-d') !== $canadaTime->format('Y-m-d')) {
            return response()->json(['success' => false, 'message' => 'You can only edit status for today (Canada Time).'], 403);
        }

        $topic->delete();

        return response()->json(['success' => true]);
    }
}
