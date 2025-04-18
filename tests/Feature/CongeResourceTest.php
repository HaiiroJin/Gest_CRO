<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Conge;
use App\Models\Fonctionnaire;
use App\Models\JoursFeries;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Carbon\Carbon;

class CongeResourceTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected User $regularUser;
    protected Fonctionnaire $fonctionnaire;

    public function setUp(): void
    {
        parent::setUp();
        
        // Create a super admin user
        $this->superAdmin = User::factory()->create([
            'status' => 'active'
        ]);
        $this->superAdmin->assignRole('super_admin');

        // Create a regular user with fonctionnaire
        $this->fonctionnaire = Fonctionnaire::factory()->create([
            'solde_année_act' => 22,
            'solde_année_prec' => 0
        ]);
        
        $this->regularUser = User::factory()->create([
            'fonctionnaire_id' => $this->fonctionnaire->id,
            'status' => 'active'
        ]);
    }

    public function test_user_can_create_conge_request()
    {
        $congeData = [
            'fonctionnaire_id' => $this->fonctionnaire->id,
            'type' => 'annuel',
            'date_debut' => now()->addDays(5)->format('Y-m-d'),
            'nombre_jours' => 5,
            'status' => 'en_attente'
        ];

        $response = $this->actingAs($this->regularUser)
            ->post('/admin/conges', $congeData);

        $this->assertDatabaseHas('conges', [
            'fonctionnaire_id' => $this->fonctionnaire->id,
            'type' => 'annuel',
            'nombre_jours' => 5,
            'status' => 'en_attente'
        ]);
    }

    public function test_super_admin_can_approve_conge()
    {
        $conge = Conge::factory()->create([
            'fonctionnaire_id' => $this->fonctionnaire->id,
            'type' => 'annuel',
            'nombre_jours' => 5,
            'status' => 'en_attente'
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->patch("/admin/conges/{$conge->id}/approve");

        $this->assertDatabaseHas('conges', [
            'id' => $conge->id,
            'status' => 'signée'
        ]);

        // Check that balance is updated
        $this->fonctionnaire->refresh();
        $this->assertEquals(17, $this->fonctionnaire->solde_année_act);
    }

    public function test_regular_user_cannot_approve_conge()
    {
        $conge = Conge::factory()->create([
            'fonctionnaire_id' => $this->fonctionnaire->id,
            'status' => 'en_attente'
        ]);

        $response = $this->actingAs($this->regularUser)
            ->patch("/admin/conges/{$conge->id}/approve");

        $response->assertForbidden();
        
        $this->assertDatabaseHas('conges', [
            'id' => $conge->id,
            'status' => 'en_attente'
        ]);
    }

    public function test_calculate_return_date_with_weekends()
    {
        // Set start date to Friday
        $startDate = Carbon::parse('2025-04-11');
        $numberOfDays = 3;

        $returnDate = \App\Filament\Resources\CongeResource::calculateReturnDate($startDate, $numberOfDays);

        // Should return Wednesday (skipping Saturday and Sunday)
        $this->assertEquals('2025-04-16', $returnDate->format('Y-m-d'));
    }

    public function test_calculate_return_date_with_holidays()
    {
        $startDate = Carbon::parse('2025-04-14'); // Monday
        $numberOfDays = 3;

        // Create a holiday
        JoursFeries::create([
            'date_depart' => '2025-04-15',
            'nombre_jours' => 1,
            'description' => 'Test Holiday'
        ]);

        $returnDate = \App\Filament\Resources\CongeResource::calculateReturnDate($startDate, $numberOfDays);

        // Should skip the holiday and return Friday
        $this->assertEquals('2025-04-18', $returnDate->format('Y-m-d'));
    }

    public function test_user_can_only_view_own_conges()
    {
        $ownConge = Conge::factory()->create([
            'fonctionnaire_id' => $this->fonctionnaire->id
        ]);

        $otherFonctionnaire = Fonctionnaire::factory()->create();
        $otherConge = Conge::factory()->create([
            'fonctionnaire_id' => $otherFonctionnaire->id
        ]);

        $response = $this->actingAs($this->regularUser)
            ->get('/admin/conges');

        $response->assertSuccessful();
        $response->assertSee($ownConge->id);
        $response->assertDontSee($otherConge->id);
    }

    public function test_super_admin_can_view_all_conges()
    {
        $conge1 = Conge::factory()->create();
        $conge2 = Conge::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->get('/admin/conges');

        $response->assertSuccessful();
        $response->assertSee($conge1->id);
        $response->assertSee($conge2->id);
    }
}
