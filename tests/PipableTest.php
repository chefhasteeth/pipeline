<?php

namespace Chefhasteeth\Pipeline\Tests;

use Chefhasteeth\Pipeline\Pipable;
use Illuminate\Support\Facades\DB;

class PipableTest extends TestCase
{
    protected function pipeline()
    {
        return new class ('one', 'two') {
            use Pipable;

            public function __construct(public string $one, public string $two)
            {
            }
        };
    }

    /** @test */
    public function trait_sends_self_through_pipeline()
    {
        $this->pipeline()
            ->pipeThrough(
                function ($data) {
                    $this->assertSame('one', $data->one);
                    $this->assertSame('two', $data->two);
                },
            )
            ->thenReturn();
    }

    /** @test */
    public function trait_sends_self_through_pipeline_with_transaction()
    {
        DB::spy();

        $this->pipeline()
            ->pipeThrough(
                function ($data) {
                    $this->assertSame('one', $data->one);
                    $this->assertSame('two', $data->two);
                },
                withTransaction: true,
            )
            ->thenReturn();

        DB::shouldHaveReceived('beginTransaction')->once();
    }
}
