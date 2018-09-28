<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Organization;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrganizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_org_slugs_are_automatically_generated()
    {
        $organization = Organization::create(['name' => 'Phylos Bioscience']);

        $this->assertNotEmpty($organization->slug);
        $this->assertEquals(strtolower($organization->slug), $organization->slug);
    }

    public function test_org_slugs_are_are_unique()
    {
        $organizationA = Organization::create(['name' => 'Phylos Bioscience']);
        $organizationB = Organization::create(['name' => 'Phylos Bioscience']);

        $this->assertEquals('phylos-bioscience', $organizationA->slug);
        $this->assertEquals('phylos-bioscience-1', $organizationB->slug);
    }
}
