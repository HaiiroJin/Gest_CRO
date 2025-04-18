<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Fonctionnaire;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UserResourceTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $regularUser;

    public function setUp(): void
    {
        parent::setUp();
        
        // Create a super admin user
        $this->superAdmin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'status' => 'active'
        ]);
        $this->superAdmin->assignRole('super_admin');

        // Create a regular user
        $this->regularUser = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'status' => 'active'
        ]);
    }

    public function test_super_admin_can_view_users_list()
    {
        $response = $this->actingAs($this->superAdmin)
            ->get('/admin/users');

        $response->assertSuccessful();
    }

    public function test_regular_user_cannot_view_users_list()
    {
        $response = $this->actingAs($this->regularUser)
            ->get('/admin/users');

        $response->assertForbidden();
    }

    public function test_super_admin_can_create_user()
    {
        $fonctionnaire = Fonctionnaire::factory()->create();
        
        $userData = [
            'email' => 'newuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'fonctionnaire_id' => $fonctionnaire->id,
            'status' => 'active'
        ];

        $response = $this->actingAs($this->superAdmin)
            ->post('/admin/users', $userData);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'status' => 'active'
        ]);
    }

    public function test_super_admin_can_update_user()
    {
        $user = User::factory()->create();

        $updatedData = [
            'email' => 'updated@example.com',
            'status' => 'inactive'
        ];

        $response = $this->actingAs($this->superAdmin)
            ->patch("/admin/users/{$user->id}", $updatedData);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'updated@example.com',
            'status' => 'inactive'
        ]);
    }

    public function test_user_last_login_is_updated()
    {
        $user = User::factory()->create();
        
        $this->assertNull($user->last_login_at);
        
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $user->refresh();
        $this->assertNotNull($user->last_login_at);
    }

    public function test_inactive_user_cannot_login()
    {
        $inactiveUser = User::factory()->create([
            'status' => 'inactive'
        ]);

        $response = $this->post('/login', [
            'email' => $inactiveUser->email,
            'password' => 'password'
        ]);

        $response->assertSessionHasErrors();
    }
}
