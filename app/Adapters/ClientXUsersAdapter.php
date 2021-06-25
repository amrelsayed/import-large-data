<?php 

namespace App\Adapters;

use App\Adapters\UsersAdapterInterface;
use Carbon\Carbon;

class ClientXUsersAdapter implements UsersAdapterInterface
{
	public function transform(array $users_data): array
	{
		$new_data = [];
        
        foreach ($users_data as $row) {
            $row['credit_card'] = json_encode($row['credit_card']);
            
            $date_of_birth = Carbon::now();

            try {
               $date_of_birth = Carbon::parse($row['date_of_birth']);
            } catch (\Exception $e) {
                $date_of_birth = Carbon::createFromFormat('d/m/Y', stripslashes($row['date_of_birth']));
            }

            $age = Carbon::now()->diffInYears($date_of_birth);

            if (($age >= 18 && $age <= 65)) {
                $row['date_of_birth'] = $date_of_birth;
                $new_data[] = $row;
            }
        }

        return $new_data;
	}
}