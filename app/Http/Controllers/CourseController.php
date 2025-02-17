<?php
namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index() {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return response()->json(Course::all());
        }

        if ($user->role === 'student') {
            $subscribedCourses = $user->subscriptions()->with('course')->get()->pluck('course');
            return response()->json($subscribedCourses);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }

    public function store(Request $request) {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
        ]);

        $course = Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'instructor_id' => auth()->id()
        ]);

        return response()->json(['message' => 'Course created successfully', 'course' => $course]);
    }
}
