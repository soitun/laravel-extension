<?php namespace LaravelPlus\Extension;

use Symfony\Component\Finder\Finder;
use Illuminate\Filesystem\Filesystem;
use LaravelPlus\Extension\Addons\Addon;
use LaravelPlus\Extension\Addons\AddonDirectory;
use LaravelPlus\Extension\Addons\AddonClassLoader;
use LaravelPlus\Extension\Repository;

class ServiceProvider extends \Illuminate\Support\ServiceProvider {

	/**
	 * @var array
	 */
	private static $commands = [
// app:
		['name' => 'command+.app.container.list', 'class' => 'LaravelPlus\Extension\Commands\AppContainerListCommand'],
		['name' => 'command+.app.serve', 'class' => 'LaravelPlus\Extension\Commands\ServeCommand'],
// addon:
		['name' => 'command+.addon.setup', 'class' => 'LaravelPlus\Extension\Commands\AddonSetupCommand'],
		['name' => 'command+.addon.make', 'class' => 'LaravelPlus\Extension\Commands\AddonMakeCommand'],
		['name' => 'command+.addon.check', 'class' => 'LaravelPlus\Extension\Commands\AddonCheckCommand'],
//		['name' => 'command+.addon.make.class', 'class' => 'LaravelPlus\Extension\Commands\AddonMakeClassCommand'],
// +migrate:
//		['name' => 'command.addon.migrate.run', 'class' => 'LaravelPlus\Extension\Commands\AddonMigrateRunCommand'],
// publish
// hash
		['name' => 'command+.hash.make', 'class' => 'LaravelPlus\Extension\Commands\HashMakeCommand'],
		['name' => 'command+.hash.check', 'class' => 'LaravelPlus\Extension\Commands\HashCheckCommand'],
// dump-autoload
	];

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * @var array
	 */
	private $addons;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['specs'] = $this->app->share(function($app) {
			$loader = new Repository\FileLoader($this->app['files'], $app['path'].'/specs');
			return new Repository\Repository($loader);
		});

		// MEMO 現在はクラスファイルの解決を動的に行うモードのみ実装している。
//		$this->loadAutoloadFiles(AddonDirectory::path());

		AddonClassLoader::register(Application::getAddons());
		AliasResolver::register(Application::getAddons(), $this->app['config']->get('app.aliases'));

		// register all addons
		$this->registerAddons();
	}

	/**
	 * setup & boot addons.
	 *
	 * @return void
	 */
	function registerAddons()
	{
		foreach (Application::getAddons() as $addon) {
			// register addon
			$addon->register($this->app);
		}
	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
//		$this->package('laravel-plus/extension', 'laravel-extension', __DIR__);

		// Add package commands
		$this->setupCommands(static::$commands);

		// setup all addons
		$this->bootAddons();
	}

	/**
	 * setup package's commands.
	 *
	 * @param  $command array
	 * @return void
	 */
	function setupCommands($commands)
	{
		$names = [];

		foreach ($commands as $command) {
			$this->app[$command['name']] = $this->app->share(function($app) use($command) {
				return new $command['class']($app);
			});

			$names[] = $command['name'];
		}

		// Now register all the commands
		$this->commands($names);
	}

	/**
	 * setup & boot addons.
	 *
	 * @return void
	 */
	function bootAddons()
	{
		foreach (Application::getAddons() as $addon) {
			// register package
			$this->registerPackage($addon->name, $addon);

			// boot addon
			$addon->boot($this->app);
		}
	}

	/**
	 * Register the package's component namespaces.
	 *
	 * @param  string  $namespace
	 * @param  string  $path
	 * @return void
	 */
	function registerPackage($namespace, $addon)
	{
		// TODO merge all config.
#		$config = $addon->path.'/config';
#		if (is_dir($config)) {
#			$this->app['config']->package($package, $config, $namespace);
#		}

		$lang = $addon->path.'/'.$addon->config('paths.lang', 'lang');
		if (is_dir($lang)) {
			$this->app['translator']->addNamespace($namespace, $lang);
		}

		$view = $addon->path.'/'.$addon->config('paths.views', 'views');
		if (is_dir($view)) {
			$this->app['view']->addNamespace($namespace, $view);
		}

		$spec = $addon->path.'/specs';
		if (is_dir($spec)) {
			$this->app['specs']->addNamespace($namespace, $spec);
		}
	}

	/**
	 * load 'autoload.php' files.
	 *
	 * @param  $path string
	 * @return void
	 */
	function loadAutoloadFiles($path)
	{
		// We will use the finder to locate all "autoload.php" files in the workbench
		// directory, then we will include them each so that they are able to load
		// the appropriate classes and file used by the given workbench package.
		$files = $this->app['files'];

		$autoloads = Finder::create()->in($path)->files()->name('autoload.php')->depth('<= 3')->followLinks();

		foreach ($autoloads as $file)
		{
			$files->requireOnce($file->getRealPath());
		}
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array_pluck(static::$commands, 'name');
	}

}
