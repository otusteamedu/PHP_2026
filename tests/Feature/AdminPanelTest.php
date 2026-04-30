<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Direction;
use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        $role = Role::factory()->create(['slug' => 'admin']);
        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }

    #[Test]
    public function guest_redirected_from_admin(): void
    {
        $this->get(route('admin.pages.index'))->assertRedirect(route('login'));
    }

    #[Test]
    public function student_receives_forbidden_on_admin(): void
    {
        $student = User::factory()->create();
        $student->roles()->attach(Role::factory()->create(['slug' => 'student']));

        $this->actingAs($student)->get(route('admin.pages.index'))->assertForbidden();
    }

    #[Test]
    public function admin_can_open_pages_index_dashboard_and_create_page(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->get('/admin')->assertRedirect(route('admin.pages.index'));

        $this->actingAs($admin)->get(route('admin.pages.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.courses.index'))->assertOk();

        $response = $this->actingAs($admin)->post(route('admin.pages.store'), [
            'title' => 'Тестовая страница',
            'slug' => 'test-page',
            'body' => 'Текст',
            'is_published' => true,
        ]);

        $response->assertRedirect(route('admin.pages.index'));
        $this->assertDatabaseHas('pages', ['slug' => 'test-page']);
    }

    #[Test]
    public function admin_can_store_course(): void
    {
        $admin = $this->makeAdmin();
        $direction = Direction::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.courses.store'), [
            'direction_id' => $direction->id,
            'title' => 'Курс PHPUnit',
            'slug' => 'course-phpunit',
            'summary' => 'Тестирование',
            'duration_hours' => 10,
        ]);

        $response->assertRedirect(route('admin.courses.index'));
        $this->assertDatabaseHas('courses', ['slug' => 'course-phpunit']);
    }

    #[Test]
    public function admin_can_update_and_delete_page(): void
    {
        $admin = $this->makeAdmin();
        $page = Page::factory()->create(['slug' => 'to-edit', 'title' => 'Old']);

        $this->actingAs($admin)->put(route('admin.pages.update', $page), [
            'title' => 'New',
            'slug' => 'to-edit',
            'body' => 'B',
            'is_published' => true,
        ])->assertRedirect(route('admin.pages.index'));

        $page->refresh();
        $this->assertSame('New', $page->title);

        $this->actingAs($admin)->delete(route('admin.pages.destroy', $page))
            ->assertRedirect(route('admin.pages.index'));
        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    }

    #[Test]
    public function admin_can_update_and_delete_course(): void
    {
        $admin = $this->makeAdmin();
        $course = Course::factory()->create(['slug' => 'editable']);

        $directionId = $course->direction_id;

        $this->actingAs($admin)->put(route('admin.courses.update', $course), [
            'direction_id' => $directionId,
            'title' => 'Обновлённый',
            'slug' => 'editable',
            'summary' => null,
            'duration_hours' => 5,
        ])->assertRedirect(route('admin.courses.index'));

        $course->refresh();
        $this->assertSame('Обновлённый', $course->title);

        $this->actingAs($admin)->delete(route('admin.courses.destroy', $course))
            ->assertRedirect(route('admin.courses.index'));
        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }
}
