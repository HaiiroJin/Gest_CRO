<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AttestationTravail;
use App\Models\Fonctionnaire;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AttestationTravailResourceTest extends TestCase
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
        $this->fonctionnaire = Fonctionnaire::factory()->create();
        $this->regularUser = User::factory()->create([
            'fonctionnaire_id' => $this->fonctionnaire->id,
            'status' => 'active'
        ]);
    }

    public function test_user_can_create_attestation_request()
    {
        $attestationData = [
            'fonctionnaire_id' => $this->fonctionnaire->id,
            'date_demande' => now()->format('Y-m-d'),
            'langue' => 'fr',
            'status' => 'en cours'
        ];

        $response = $this->actingAs($this->regularUser)
            ->post('/admin/attestation-travails', $attestationData);

        $this->assertDatabaseHas('attestation_travails', [
            'fonctionnaire_id' => $this->fonctionnaire->id,
            'langue' => 'fr',
            'status' => 'en cours'
        ]);
    }

    public function test_super_admin_can_approve_attestation()
    {
        $attestation = AttestationTravail::factory()->create([
            'status' => 'en cours'
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->patch("/admin/attestation-travails/{$attestation->id}/approve");

        $this->assertDatabaseHas('attestation_travails', [
            'id' => $attestation->id,
            'status' => 'signé'
        ]);
    }

    public function test_super_admin_can_reject_attestation()
    {
        $attestation = AttestationTravail::factory()->create([
            'status' => 'en cours'
        ]);

        $rejectionData = [
            'rejection_reason' => 'Test rejection reason'
        ];

        $response = $this->actingAs($this->superAdmin)
            ->patch("/admin/attestation-travails/{$attestation->id}/reject", $rejectionData);

        $this->assertDatabaseHas('attestation_travails', [
            'id' => $attestation->id,
            'status' => 'rejeté',
            'raison_rejection' => 'Test rejection reason'
        ]);
    }

    public function test_regular_user_cannot_approve_attestation()
    {
        $attestation = AttestationTravail::factory()->create([
            'status' => 'en cours'
        ]);

        $response = $this->actingAs($this->regularUser)
            ->patch("/admin/attestation-travails/{$attestation->id}/approve");

        $response->assertForbidden();
        
        $this->assertDatabaseHas('attestation_travails', [
            'id' => $attestation->id,
            'status' => 'en cours'
        ]);
    }

    public function test_user_can_only_view_own_attestations()
    {
        $ownAttestation = AttestationTravail::factory()->create([
            'fonctionnaire_id' => $this->fonctionnaire->id
        ]);

        $otherFonctionnaire = Fonctionnaire::factory()->create();
        $otherAttestation = AttestationTravail::factory()->create([
            'fonctionnaire_id' => $otherFonctionnaire->id
        ]);

        $response = $this->actingAs($this->regularUser)
            ->get('/admin/attestation-travails');

        $response->assertSuccessful();
        $response->assertSee($ownAttestation->id);
        $response->assertDontSee($otherAttestation->id);
    }

    public function test_super_admin_can_view_all_attestations()
    {
        $attestation1 = AttestationTravail::factory()->create();
        $attestation2 = AttestationTravail::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->get('/admin/attestation-travails');

        $response->assertSuccessful();
        $response->assertSee($attestation1->id);
        $response->assertSee($attestation2->id);
    }

    public function test_user_can_download_own_attestation_request()
    {
        $attestation = AttestationTravail::factory()->create([
            'fonctionnaire_id' => $this->fonctionnaire->id,
            'status' => 'signé'
        ]);

        $response = $this->actingAs($this->regularUser)
            ->get("/attestation/demande/{$attestation->id}");

        $response->assertSuccessful();
    }

    public function test_super_admin_can_print_attestation()
    {
        $attestation = AttestationTravail::factory()->create([
            'status' => 'signé'
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->get("/attestation/print/{$attestation->id}");

        $response->assertSuccessful();
    }
}
