<?php

namespace App\View\Composers;

use Core\Employee\Domain\Employee;
use Core\Profile\Domain\Contracts\ModuleFactoryContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\Profile;
use Core\User\Domain\User;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class MenuComposer
{
    private ModuleFactoryContract $moduleFactory;
    private array $menuOptions;
    
    public function __construct(
        ModuleFactoryContract $moduleFactory, 
    ){
        $this->moduleFactory = $moduleFactory;
        $this->menuOptions = config('menu.options');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function compose(View $view): void
    {
        /**@var User $user*/
        $user = session()->get('user');
        /**@var Profile $profile*/
        $profile = session()->get('profile');
        
        /**@var Employee $employee*/
        $employee = session()->get('employee');

        $menu = $this->prepareMenu($profile->modules());
        
        $view->with('menu', $menu);
        $view->with('user', $user);
        $view->with('employee', $employee);
        $view->with('profile', $profile);
    }
    
    private function prepareMenu(Modules $modules): array
    {
        $menuWithChildren = [];
        $menuUnique = [];
        foreach ($this->menuOptions as $index => $item) {
            $item['id'] = 0;

            if (is_null($item['route'])) {
                $options = $modules->moduleElementsOfKey($index);

                if (count($options)) {
                    $item['key'] = $index;
                    $item['route'] = '';

                    $mainModule = $this->changeExpandedToModule($options, $this->getModuleMenu($item));

                    $menuWithChildren[] = $mainModule;
                }
            } else {
                $item['key'] = '';
                $menuUnique[] = $this->getModuleMenu($item);
            }
        }

        return array_merge($menuWithChildren, $menuUnique);
    }
    
    private function getModuleMenu(array $data): Module
    {
        return $this->moduleFactory->buildModule(
            $this->moduleFactory->buildModuleId($data['id']),
            $this->moduleFactory->buildModuleMenuKey($data['key']),
            $this->moduleFactory->buildModuleName($data['name']),
            $this->moduleFactory->buildModuleRoute($data['route']),
            $this->moduleFactory->buildModuleIcon($data['icon']),
        );
    }

    /**
     * @param array<Module> $modules
     * @param Module $mainModule
     * @return Module
     */
    private function changeExpandedToModule(array $modules, Module $mainModule): Module
    {
        $routeCurrent = Route::current()->uri();
        
        /**@var Module $item*/
        foreach ($modules as $item) {
            if ($item->route()->value() === $routeCurrent) {
                $item->setExpanded(true);
                $mainModule->setExpanded(true);
            }
        }
        $mainModule->setOptions($modules);
        return $mainModule;
    }
}