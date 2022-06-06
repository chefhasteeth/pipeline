<?php

namespace Chefhasteeth\Pipeline;

trait Pipable
{
    public function pipeThrough($pipes, bool $withTransaction = false)
    {
        $pipeline = resolve(Pipeline::class);

        if ($withTransaction) {
            $pipeline->withTransaction();
        }

        return $pipeline->send($this)->through($pipes);
    }
}
