<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCourseRequest;
use App\Http\Requests\Admin\UpdateCourseRequest;
use App\Models\Course;
use App\Services\DirectionsListCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function __construct(
        private readonly DirectionsListCache $directionsListCache
    ) {}

    public function index(): View
    {
        $courses = Course::query()->with('direction')->latest('updated_at')->paginate(15);

        return view('admin.courses.index', compact('courses'));
    }

    public function create(): View
    {
        $directions = $this->directionsListCache->allOrderedByName();

        return view('admin.courses.create', compact('directions'));
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        Course::query()->create($request->validated());

        return redirect()->route('admin.courses.index')->with('ok', 'Курс создан.');
    }

    public function edit(string $locale, Course $course): View
    {
        $directions = $this->directionsListCache->allOrderedByName();

        return view('admin.courses.edit', compact('course', 'directions'));
    }

    public function update(string $locale, Course $course, UpdateCourseRequest $request): RedirectResponse
    {
        $course->update($request->validated());

        return redirect()->route('admin.courses.index')->with('ok', 'Сохранено.');
    }

    public function destroy(string $locale, Course $course): RedirectResponse
    {
        $course->delete();

        return redirect()->route('admin.courses.index')->with('ok', 'Удалено.');
    }
}
