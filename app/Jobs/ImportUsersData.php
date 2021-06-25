<?php

namespace App\Jobs;

use App\Adapters\ClientXUsersAdapter;
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

        $usersXAdapter = new ClientXUsersAdapter();

        DB::transaction(function () use ($chunks, $usersXAdapter) {
            foreach ($chunks as $chunk) {
                $data = $usersXAdapter->transform($chunk);
                User::insert($data);
            }    
        });

        fclose($stream);
    }
}
