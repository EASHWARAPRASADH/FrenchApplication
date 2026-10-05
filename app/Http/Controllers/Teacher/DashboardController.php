<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display teacher dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Check if teacher account is pending approval
        if ($user->status === 'pending') {
            return view('teacher.pending', compact('user'));
        }

        $courses = Course::where('teacher_id', $user->id)
            ->withCount(['enrollments', 'lessons'])
            ->get();

        $totalStudents = User::whereHas('enrollments.course', function ($q) use ($user) {
            $q->where('teacher_id', $user->id);
        })->distinct()->count();

        return view('teacher.dashboard', compact('user', 'courses', 'totalStudents'));
    }

    /**
     * Display courses taught by teacher
     */
    public function courses()
    {
        $user = Auth::user();

        if ($user->status === 'pending') {
            return redirect()->route('teacher.dashboard');
        }

        $courses = Course::where('teacher_id', $user->id)
            ->withCount(['enrollments', 'lessons'])
            ->paginate(10);

        return view('teacher.courses', compact('courses'));
    }

    /**
     * Display students enrolled in teacher courses
     */
    public function students()
    {
        $user = Auth::user();

        if ($user->status === 'pending') {
            return redirect()->route('teacher.dashboard');
        }

        $students = User::whereHas('enrollments.course', function ($q) use ($user) {
            $q->where('teacher_id', $user->id);
        })->with(['enrollments.course' => function ($q) use ($user) {
            $q->where('teacher_id', $user->id);
        }])->paginate(15);

        return view('teacher.students', compact('students'));
    }
}
