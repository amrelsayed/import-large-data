<?php

namespace App\Jobs;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use JsonStreamingParser\Listener\InMemoryListener;

class ImportUsersData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $listener = new InMemoryListener();

        $stream = fopen('challenge.json', 'r');

        $parser = new \JsonStreamingParser\Parser($stream, $listener);
        $parser->parse();
        
        $data = $listener->getJson();   
        
        $chunks = array_chunk($data, 1000); 

        DB::transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                $data = $this->prepareData($chunk);
                User::insert($data);
            }    
        });

        fclose($stream);
    }

    private function prepareData($chunk)
    {
        $new_chunk = [];

        foreach ($chunk as $row) {
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
                $new_chunk[] = $row;
            }
        }

        return $new_chunk;
    }
}
