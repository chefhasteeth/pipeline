<?php

namespace Chefhasteeth\Pipeline;

use Closure;
use Illuminate\Support\Facades\DB;
use Throwable;
use UnexpectedValueException;

class Pipeline
{
    protected bool $useTransaction = false;

    protected mixed $passable;

    public static function make(): static
    {
        return new static();
    }

    /**
     * Inform the pipeline that we'll be using database transactions.
     */
    public function withTransaction(): static
    {
        $this->useTransaction = true;

        return $this;
    }

    /**
     * Set the object being sent through the pipeline.
     */
    public function send(mixed $passable): static
    {
        $this->passable = $passable;

        return $this;
    }

    /**
     * Set the array of pipes.
     *
     * @param array|mixed $pipes
     */
    public function through($pipes): static
    {
        $this->pipes = is_array($pipes) ? $pipes : func_get_args();

        return $this;
    }

    /**
     * Run the pipeline with a final callback.
     */
    public function then(Closure $step): mixed
    {
        return $step($this->traversePipeline());
    }

    /**
     * Run the pipeline and return the result.
     */
    public function thenReturn(): mixed
    {
        return $this->then(fn ($passable) => $passable);
    }

    protected function pipes()
    {
        return [
            ...$this->pipes,
            function ($passable) {
                $this->commitTransaction();

                return $passable;
            },
        ];
    }

    protected function traversePipeline()
    {
        try {
            $this->startTransaction();

            return array_reduce($this->pipes(), $this->executePipe(...), $this->passable);
        } catch (Throwable $e) {
            $this->undoTransaction();

            throw $e;
        }
    }

    protected function executePipe($previousValue, $pipe)
    {
        $action = $pipe;

        if (is_string($pipe) && class_exists($pipe)) {
            $action = resolve($pipe);
        }

        if (is_callable($action)) {
            return $action($previousValue);
        }

        if (method_exists($action, 'handle')) {
            return $action->handle($previousValue);
        }

        throw new UnexpectedValueException(
            'Pipeline only accepts callables and class strings',
        );
    }

    protected function startTransaction(): void
    {
        if (! $this->useTransaction) {
            return;
        }

        DB::beginTransaction();
    }

    protected function commitTransaction(): void
    {
        if (! $this->useTransaction) {
            return;
        }

        DB::commit();
    }

    protected function undoTransaction(): void
    {
        if (! $this->useTransaction) {
            return;
        }

        DB::rollBack();
    }
}
