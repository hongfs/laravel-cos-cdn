<?php

namespace App\Console\Commands;

use App\Models\Option;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class InstallCdn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cdn:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CDN Install';

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
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            return $this->error('无法连接到数据库');
        }
        
        $this->callSilent('migrate');
        
        $this->callSilent('db:seed');

        $name = $this->ask('请输入站点名称：');

        Option::_set('site-name', $name ?? 'CDN');

        cache()->forget('option');

        return $this->info('安装完成');
    }
}
