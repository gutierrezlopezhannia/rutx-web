<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;

interface CustomerRepositoryInterface
{
    public function getAll(): Collection;
    public function create(array $data): Customer;
}
