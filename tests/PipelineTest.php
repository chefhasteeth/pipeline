<?php

namespace Chefhasteeth\Pipeline\Tests;

use Chefhasteeth\Pipeline\Pipeline;
use Chefhasteeth\Pipeline\Tests\Fakes\PipeForTesting;
use Illuminate\Support\Facades\DB;
use UnexpectedValueException;

class PipelineTest extends TestCase
{
    /** @test */
    public function runs_through_an_entire_pipeline()
    {
        $result = Pipeline::make()
            ->send(0)
            ->through(
                fn ($data) => ++$data,
                fn ($data) => ++$data,
            )
            ->thenReturn();

        $this->assertSame(2, $result);
    }

    /** @test */
    public function throws_exception_from_pipeline()
    {
        $this->assertThrows(
            function () {
                Pipeline::make()
                    ->send('test')
                    ->through(fn () => throw new UnexpectedValueException())
                    ->thenReturn();
            },
            UnexpectedValueException::class,
        );
    }

    /** @test */
    public function throws_exception_with_invalid_pipe_type()
    {
        $this->assertThrows(
            function () {
                Pipeline::make()
                    ->send('test')
                    ->through('not a callable or class string')
                    ->thenReturn();
            },
            UnexpectedValueException::class,
        );
    }

    /** @test */
    public function accepts_class_strings_as_pipes()
    {
        $result = Pipeline::make()
            ->send('test data')
            ->through(PipeForTesting::class)
            ->thenReturn();

        $this->assertSame('test data', $result);
    }

    /** @test */
    public function successfully_completes_a_database_transaction()
    {
        $database = DB::spy();

        Pipeline::make()
            ->withTransaction()
            ->send('test')
            ->through(fn ($data) => $data)
            ->thenReturn();

        $database->shouldHaveReceived('beginTransaction')->once();
        $database->shouldHaveReceived('commit')->once();
    }

    /** @test */
    public function rolls_the_databsae_transaction_back_on_failure()
    {
        $database = DB::spy();

        rescue(
            fn () => Pipeline::make()
                ->withTransaction()
                ->send('test')
                ->through(fn () => throw new UnexpectedValueException())
                ->thenReturn(),
        );

        $database->shouldHaveReceived('beginTransaction')->once();
        $database->shouldHaveReceived('rollBack')->once();
    }
}
