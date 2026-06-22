<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function getAll(): Collection
    {
        return Customer::all(); // Aquí irán tus queries de Firebird en el futuro
    }

    public function create(array $data): Customer
    {
        return Customer::create($data);
    }
}
