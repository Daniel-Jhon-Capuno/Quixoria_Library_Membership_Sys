<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $staffUser;
    protected User $studentUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users with different roles
        $this->adminUser = User::factory()->create(['role' => 'admin', 'email' => 'admin@test.com']);
        $this->staffUser = User::factory()->create(['role' => 'staff', 'email' => 'staff@test.com']);
        $this->studentUser = User::factory()->create(['role' => 'student', 'email' => 'student@test.com']);
    }

    // ===== ADMIN AUTHORIZATION TESTS =====

    /** @test */
    public function admin_can_access_admin_routes()
    {
        $this->actingAs($this->adminUser)
            ->get('/admin/dashboard')
            ->assertStatus(200);

        $this->actingAs($this->adminUser)
            ->get('/admin/tiers')
            ->assertStatus(200);

        $this->actingAs($this->adminUser)
            ->get('/admin/users')
            ->assertStatus(200);

        $this->actingAs($this->adminUser)
            ->get('/admin/books')
            ->assertStatus(200);

        $this->actingAs($this->adminUser)
            ->get('/admin/reports')
            ->assertStatus(200);
    }

    /** @test */
    public function admin_cannot_access_staff_routes()
    {
        $this->actingAs($this->adminUser)
            ->get('/staff/dashboard')
            ->assertStatus(403);

        $this->actingAs($this->adminUser)
            ->get('/staff/borrow-requests')
            ->assertStatus(403);

        $this->actingAs($this->adminUser)
            ->get('/staff/deadline-dashboard')
            ->assertStatus(403);
    }

    /** @test */
    public function admin_cannot_access_student_routes()
    {
        $this->actingAs($this->adminUser)
            ->get('/student/dashboard')
            ->assertStatus(403);

        $this->actingAs($this->adminUser)
            ->get('/student/books')
            ->assertStatus(403);

        $this->actingAs($this->adminUser)
            ->get('/student/borrow-requests')
            ->assertStatus(403);

        $this->actingAs($this->adminUser)
            ->get('/student/subscription')
            ->assertStatus(403);
    }

    // ===== STAFF AUTHORIZATION TESTS =====

    /** @test */
    public function staff_can_access_staff_routes()
    {
        $this->actingAs($this->staffUser)
            ->get('/staff/dashboard')
            ->assertStatus(200);

        $this->actingAs($this->staffUser)
            ->get('/staff/borrow-requests')
            ->assertStatus(200);

        $this->actingAs($this->staffUser)
            ->get('/staff/deadline-dashboard')
            ->assertStatus(200);
    }

    /** @test */
    public function staff_cannot_access_admin_routes()
    {
        $this->actingAs($this->staffUser)
            ->get('/admin/dashboard')
            ->assertStatus(403);

        $this->actingAs($this->staffUser)
            ->get('/admin/tiers')
            ->assertStatus(403);

        $this->actingAs($this->staffUser)
            ->get('/admin/users')
            ->assertStatus(403);

        $this->actingAs($this->staffUser)
            ->get('/admin/books')
            ->assertStatus(403);

        $this->actingAs($this->staffUser)
            ->get('/admin/reports')
            ->assertStatus(403);
    }

    /** @test */
    public function staff_cannot_access_student_routes()
    {
        $this->actingAs($this->staffUser)
            ->get('/student/dashboard')
            ->assertStatus(403);

        $this->actingAs($this->staffUser)
            ->get('/student/books')
            ->assertStatus(403);

        $this->actingAs($this->staffUser)
            ->get('/student/borrow-requests')
            ->assertStatus(403);

        $this->actingAs($this->staffUser)
            ->get('/student/subscription')
            ->assertStatus(403);
    }

    // ===== STUDENT AUTHORIZATION TESTS =====

    /** @test */
    public function student_can_access_student_routes()
    {
        $this->actingAs($this->studentUser)
            ->get('/student/dashboard')
            ->assertStatus(200);

        $this->actingAs($this->studentUser)
            ->get('/student/books')
            ->assertStatus(200);

        $this->actingAs($this->studentUser)
            ->get('/student/borrow-requests')
            ->assertStatus(200);

        $this->actingAs($this->studentUser)
            ->get('/student/subscription')
            ->assertStatus(200);

        $this->actingAs($this->studentUser)
            ->get('/student/active-borrows')
            ->assertStatus(200);
    }

    /** @test */
    public function student_cannot_access_admin_routes()
    {
        $this->actingAs($this->studentUser)
            ->get('/admin/dashboard')
            ->assertStatus(403);

        $this->actingAs($this->studentUser)
            ->get('/admin/tiers')
            ->assertStatus(403);

        $this->actingAs($this->studentUser)
            ->get('/admin/users')
            ->assertStatus(403);

        $this->actingAs($this->studentUser)
            ->get('/admin/books')
            ->assertStatus(403);

        $this->actingAs($this->studentUser)
            ->get('/admin/reports')
            ->assertStatus(403);
    }

    /** @test */
    public function student_cannot_access_staff_routes()
    {
        $this->actingAs($this->studentUser)
            ->get('/staff/dashboard')
            ->assertStatus(403);

        $this->actingAs($this->studentUser)
            ->get('/staff/borrow-requests')
            ->assertStatus(403);

        $this->actingAs($this->studentUser)
            ->get('/staff/deadline-dashboard')
            ->assertStatus(403);
    }

    // ===== UNAUTHENTICATED USER TESTS =====

    /** @test */
    public function unauthenticated_users_are_redirected_to_login()
    {
        $this->get('/admin/dashboard')
            ->assertRedirectToRoute('login');

        $this->get('/staff/dashboard')
            ->assertRedirectToRoute('login');

        $this->get('/student/dashboard')
            ->assertRedirectToRoute('login');
    }
}
