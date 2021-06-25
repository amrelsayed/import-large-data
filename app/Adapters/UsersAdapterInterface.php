<?php

namespace App\Adapters;

interface UsersAdapterInterface
{
	public function transform(array $users_data): array;
}