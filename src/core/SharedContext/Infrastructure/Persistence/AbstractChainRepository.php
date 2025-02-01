<?php

namespace Core\SharedContext\Infrastructure\Persistence;

abstract class AbstractChainRepository
{
    /** @var array<ChainPriority> */
    private array $repositories;
    protected bool $canPersist = true;

    abstract public function functionNamePersist(): string;

    protected function canPersist(): bool
    {
        return $this->canPersist;
    }

    public function addRepository(ChainPriority ...$repository): self
    {
        foreach ($repository as $item) {
            $this->repositories[] = $item;
        }

        $this->sortingPriority();

        return $this;
    }

    protected function write(string $function, mixed ...$source): mixed
    {
        $repository = end($this->repositories);

        do {
            $callable = [$repository, $function];
            if (!is_callable($callable)) {
                throw new \InvalidArgumentException(sprintf('The function %s is not a callable.', $function));
            }

            $result = call_user_func_array($callable, $source);
        } while (false !== $repository = prev($this->repositories));

        return $result;
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    protected function read(string $functionName, mixed ...$source): mixed
    {
        $result = $this->readFromRepositories($functionName, ...$source);

        if ($this->canPersist()) {
            $this->persistence($this->functionNamePersist(), $result);
        }

        return $result;
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    protected function readFromRepositories(string $function, mixed ...$source): mixed
    {
        $result = null;
        $lastThrowable = new SourceNotFoundException('Source not found');
        $repository = reset($this->repositories);

        do {
            try {
                $callable = [$repository, $function];
                if (!is_callable($callable)) {
                    throw new \InvalidArgumentException(sprintf('The function %s is not a callable.', $function));
                }

                $result = call_user_func_array($callable, $source);
            } catch (\Throwable $throwable) {
                $lastThrowable = $throwable;
            }

            $repository = next($this->repositories);
        } while (is_null($result) and $repository);

        if (is_null($result)) {
            throw $lastThrowable;
        }

        return $result;
    }

    protected function persistence(string $functionName, mixed ...$sources): void
    {
        while ($repository = prev($this->repositories)) {
            try {
                $callable = [$repository, $functionName];
                if (is_callable($callable)) {
                    call_user_func_array($callable, $sources);
                }
            } catch (\Exception $exception) {
            }
        }
    }

    private function sortingPriority(): void
    {
        usort($this->repositories, function (ChainPriority $current, ChainPriority $next) {
            return $next->priority() <=> $current->priority();
        });
    }
}
