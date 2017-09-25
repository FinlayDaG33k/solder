<?php

/*
 * This file is part of Solder.
 *
 * (c) Kyle Klaus <kklaus@indemnity83.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Feature;

use App\User;
use App\Modpack;
use BuildFactory;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowModpackTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_a_modpack()
    {
        $user = factory(User::class)->create();
        $modpack = factory(Modpack::class)->create([
            'slug' => 'example-modpack',
        ]);

        $response = $this->actingAs($user)->get('/modpacks/example-modpack');

        $response->assertStatus(200);
        $response->assertViewIs('modpacks.show');
        $response->assertViewHas('modpack', function ($viewModpack) use ($modpack) {
            return $viewModpack->id == $modpack->id;
        });
    }

    /** @test */
    public function guest_cannot_view_modpack()
    {
        factory(Modpack::class)->create([
            'slug' => 'example-modpack',
        ]);

        $response = $this->get('/modpacks/example-modpack');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function a_user_cannot_view_a_non_existent_modpack()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/modpacks/fake-modpack');

        $response->assertStatus(404);
    }

    /** @test */
    public function modpack_includes_builds_in_reverse_order()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        $modpack = factory(Modpack::class)->create([
            'slug' => 'example-modpack',
        ]);
        $buildA = BuildFactory::createForModpack($modpack, ['version' => '1.0.0a']);
        $buildB = BuildFactory::createForModpack($modpack, ['version' => '1.0.0b']);
        $buildC = BuildFactory::createForModpack($modpack, ['version' => '10.5']);

        $response = $this->actingAs($user)->get('/modpacks/example-modpack');

        $response->data('modpack')->builds->assertEquals([
            $buildC,
            $buildB,
            $buildA
        ]);
    }
}
