<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCourseRequest;
use App\Http\Requests\Admin\UpdateCourseRequest;
use App\Models\Course;
use App\Models\Direction;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        $courses = Course::query()->with('direction')->latest('updated_at')->paginate(15);

        return view('admin.courses.index', compact('courses'));
    }

    public function create(): View
    {
        $directions = Direction::query()->orderBy('name')->get();

        return view('admin.courses.create', compact('directions'));
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        Course::query()->create($request->validated());

        return redirect()->route('admin.courses.index')->with('success', 'Курс создан.');
    }

    public function edit(Course $course): View
    {
        $directions = Direction::query()->orderBy('name')->get();

        return view('admin.courses.edit', compact('course', 'directions'));
    }

    public function update(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $course->update($request->validated());

        return redirect()->route('admin.courses.index')->with('success', 'Сохранено.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'Удалено.');
    }
}
