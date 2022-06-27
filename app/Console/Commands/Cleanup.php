<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Cleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:cleanup';

    /**i
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dumpDate = \Carbon\Carbon::now()->subMonth(2)->startOfMonth();

        $tables = [
            'referrals_paid' => 'timestamp',
            //'conversions' => 'timestamp',
            //'clicks' => 'first_timestamp'
        ];


        foreach ($tables as $table => $dateField) {
            $maxDate  = $dumpDate->format('Y-m-d');
	      //dd($table, $dateField, $maxDate);
            
            
	    //$del = \DB::table($table)->where($dateField, '<', $maxDate)->count();
	    
	    
	    dd('this is a different database');
	    
	    
	    //this is a different DB
	    $del = \DB::table($table)->count();
	    
	    dd($del. " rows");
        
        
        
            
            $dumpName = "dump-table-{$table}-" . $dumpDate->subDay()->format('Y-m') . '.sql';

            if (!file_exists($dumpName)) {
                $pass    = str_replace('&', '\&', getenv('DB_PASSWORD'));
                
                $command = "mysqldump -u " . getenv('DB_USERNAME') . " -p{$pass}  --single-transaction --quick --lock-tables=false   trafficmasters {$table} --where=\"{$dateField}<'{$maxDate}'\"  > {$dumpName}";
                
                $out     = $ret = '';
                
                exec($command, $out, $ret);
                
                if ($ret) {
                    continue;
                }
                
                $this->info("Backed up table $table");

                $del = \DB::table($table)->where($dateField, '<', $maxDate)->delete();

                $this->info("Removed $del rows from $table till date $maxDate");
            }
        }
    }
}
