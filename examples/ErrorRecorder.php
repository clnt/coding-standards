<?php

namespace App\Imports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Validators\Failure;

class ErrorRecorder
{
    /** @var string */
    private $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function record(Failure ...$failures): void
    {
        $test = \App\Imports\Test\Bananas\Trees\CompoundNamespace::class;

        DB::transaction(function () use ($failures): void {
            foreach ($failures as $failure) {
                ImportError::create([
                    'type' => $this->type,
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                ]);
            }
        });
    }

    public function recall(?int $limit = null): Collection
    {
        return $this->query()->limit($limit)->get()->map(function (ImportError $error) {
            return new Failure($error->row, $error->attribute, $error->errors);
        });
    }

    public function count(): int
    {
        return $this->query()->count();
    }

    private function query(): Builder
    {
        return ImportError::ofType($this->type);
    }

    public static function make(string $type): self
    {
        return new static($type);
    }
}
