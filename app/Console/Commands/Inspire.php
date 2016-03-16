<?php namespace Gis\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

/**
 * the default class of laravel framework
 */
class Inspire extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'inspire';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Display an inspiring quote';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$this->comment(PHP_EOL.Inspiring::quote().PHP_EOL);
	}

}
