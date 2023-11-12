<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ModuleCreate extends BaseCommand
{
    /**
     * The group the command is lumped under
     * when listing commands.
     *
     * @var string
     */
    protected $group       = 'Development';

    /**
     * The Command's name
     *
     * @var string
     */
    protected $name        = 'module:create';

    /**
     * the Command's short description
     *
     * @var string
     */
    protected $description = 'Create CodeIgniter HMVC Modules in app/Modules folder';

    /**
     * the Command's usage
     *
     * @var string
     */
    protected $usage        = 'module:create [ModuleName] [Options]';

    /**
     * the Command's Arguments
     *
     * @var array
     */
    protected $arguments    = ['ModuleName' => 'Module name to be created'];

    /**
     * the Command's Options
     *
     * @var array
     */
    protected $options      = [
        '-c' => 'Set controller file name',
        '-m' => 'Set model file name',
        '-v' => 'Set view file name',
    ];

    /**
     * Module Name to be Created
     */
    protected $moduleName;

    /**
     * Controller file name
     */
    protected $controllerName;

    /**
     * Model file name
     */
    protected $modelName;


    /**
     * View file name
     */
    protected $viewName;


    /**
     * Run route:update CLI
     */
    public function run(array $params)
    {
        helper('inflector');

        if (isset($params[0])) {
            $this->moduleName = $params[0];
        } else {
            $this->moduleName = CLI::prompt('Enter Module Name');
        }

        if (empty($this->moduleName)) {
            CLI::error('You must provide a model name');
            return;
        }

        $this->moduleName = ucfirst($this->moduleName);

        // Set Custom Controller Name
        $controllerName         = $params['-c'] ?? CLI::getOption('c');
        $this->controllerName   = ucfirst($controllerName ?? $this->moduleName . 'Controller');

        // Set Custom Model Name
        $modelName         = $params['-m'] ?? CLI::getOption('m');
        $this->modelName   = ucfirst($modelName ?? $this->moduleName);

        // Set Custom View Name
        $viewName         = $params['-v'] ?? CLI::getOption('v');
        $this->viewName   = strtolower($viewName ?? $this->moduleName);

        try {
            $this->createFolder();
            $this->createRoute();
            $this->createController();
            $this->createModel();
            $this->createView();

            CLI::write('Module created!');
        } catch (\Exception $e) {
            CLI::error($e);
        }
    }

    private function createFolder()
    {
        // Create Modules Folder
        if (!is_dir(APPPATH . '/Modules/')) {
            mkdir(APPPATH . '/Modules/');
        }

        // Create Module Folder
        if (!is_dir(APPPATH . '/Modules/' . $this->moduleName)) {
            mkdir(APPPATH . '/Modules/' . $this->moduleName);
        }

        // Create Config Folder
        if (!is_dir(APPPATH . '/Modules/' . $this->moduleName . '/Config/')) {
            mkdir(APPPATH . '/Modules/' . $this->moduleName . '/Config/');
        }

        // Create Controllers Folder
        if (!is_dir(APPPATH . '/Modules/' . $this->moduleName . '/Controllers/')) {
            mkdir(APPPATH . '/Modules/' . $this->moduleName . '/Controllers/');
        }

        // Create Models Folder
        if (!is_dir(APPPATH . '/Modules/' . $this->moduleName . '/Models/')) {
            mkdir(APPPATH . '/Modules/' . $this->moduleName . '/Models/');
        }

        // Create Views Folder
        if (!is_dir(APPPATH . '/Modules/' . $this->moduleName . '/Views/')) {
            mkdir(APPPATH . '/Modules/' . $this->moduleName . '/Views/');
        }
    }

    protected function createRoute()
    {
        $configPath = APPPATH . '/Modules/' . $this->moduleName . '/Config';

        if (!file_exists($configPath . '/Routes.php')) {
            $routeName = strtolower($this->moduleName);

            $template = "<?php

\$routes->group('{$routeName}', ['namespace' => 'App\Modules\\$this->moduleName\\Controllers'], static function(\$routes){
    /*** Route for $this->moduleName ***/
    \$routes->get('/', '$this->controllerName::index');
});";

            file_put_contents($configPath . '/Routes.php', $template);
        } else {
            CLI::error("Can't Create Routes Config! Old File Exists!");
        }
    }

    protected function createController()
    {
        $controllerPath = APPPATH . '/Modules/' . $this->moduleName . '/Controllers';

        if (!file_exists($controllerPath . '/' . $this->controllerName . '.php')) {
            $template = "<?php 
namespace App\Modules\\$this->moduleName\\Controllers;

use App\Controllers\BaseController;
use App\Modules\\$this->moduleName\\Models\\$this->modelName;

class $this->controllerName extends BaseController
{
    public function index()
	{
		\$data = [
		    'title' => '$this->moduleName',
            'view' => 'App\Modules\\$this->moduleName\\Views\\$this->viewName',
            'data' => $this->modelName::all()
        ];

		return view('template/layout', \$data);
	}

}
";
            file_put_contents($controllerPath . '/' . $this->controllerName . '.php', $template);
        } else {
            CLI::error("Can't Create Controller! Old File Exists!");
        }
    }

    /**
     * Create Models File
     */
    protected function createModel()
    {
        $modelPath = APPPATH . '/Modules/' . $this->moduleName . '/Models';

        if (!file_exists($modelPath . '/' . $this->modelName . '.php')) {
            $template = "<?php 
namespace App\Modules\\$this->moduleName\\Models;

use Illuminate\Database\Eloquent\Model;

class $this->modelName extends Model
{
    
}";

            file_put_contents($modelPath . '/' . $this->modelName . '.php', $template);
        } else {
            CLI::error("Can't Create Model! Old File Exists!");
        }
    }

    /**
     * Create View
     */
    protected function createView()
    {
        $view_path = APPPATH . '/Modules/' . $this->moduleName . '/Views';
        if (!file_exists($view_path . '/' . $this->viewName . '.php')) {
            $template = "This is $this->moduleName View File";
            file_put_contents($view_path . '/' . $this->viewName . '.php', $template);
        } else {
            CLI::error("Can't Create View! Old File Exists!");
        }
    }
}
