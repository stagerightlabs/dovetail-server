<?php

namespace Tests\Unit;

use App\Invitation;
use Tests\TestCase;

class InvitationCodeTest extends TestCase
{
    /** @test */
    public function invitation_codes_are_at_least_sixteen_characters_long()
    {
        $invitation = factory(Invitation::class)->create();

        $this->assertTrue(strlen($invitation->code) == 16);
    }

    /** @test */
    public function invitation_codes_can_only_contain_uppercase_letters()
    {
        $invitation = factory(Invitation::class)->create();

        $this->assertRegExp('/^[A-Z]+$/', $invitation->code);
    }

    /** @test */
    public function invitation_codes_for_the_same_ticket_id_are_the_same()
    {
        $code1 = hashid(1, 'invitation');
        $code2 = hashid(1, 'invitation');

        $this->assertEquals($code1, $code2);
    }

    /** @test */
    public function invitation_codes_for_different_ticket_ids_are_different()
    {
        $code1 = factory(Invitation::class)->create();
        $code2 = factory(Invitation::class)->create();

        $this->assertNotEquals($code1, $code2);
    }
}
