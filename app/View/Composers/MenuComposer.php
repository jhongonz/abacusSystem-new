<?php

namespace App\View\Composers;

use Core\Employee\Domain\Employee;
use Core\Profile\Domain\Contracts\ModuleFactoryContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\Profile;
use Core\User\Domain\User;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Session\Session;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MenuComposer
{
    private ModuleFactoryContract $moduleFactory;
    private Config $config;
    private Router $router;
    private Session $session;
    private UrlGenerator $urlGenerator;
    private string $imagePathFull;

    public function __construct(
        ModuleFactoryContract $moduleFactory,
        Config $config,
        Router $router,
        Session $session,
        UrlGenerator $urlGenerator
    ) {
        $this->moduleFactory = $moduleFactory;
        $this->config = $config;
        $this->router = $router;
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
        $this->imagePathFull = '/images/full/';
    }

    public function compose(View $view): void
    {
        /** @var User $user */
        $user = $this->session->get('user');

        /** @var Profile $profile */
        $profile = $this->session->get('profile');

        /** @var Employee $employee */
        $employee = $this->session->get('employee');

        $menu = $this->prepareMenu($profile->modules());
        $image = $this->urlGenerator->to($this->imagePathFull.$user->photo()->value().'?v='.Str::random(10));

        $view->with('menu', $menu);
        $view->with('user', $user);
        $view->with('employee', $employee);
        $view->with('profile', $profile);
        $view->with('image', $image);
    }

    private function prepareMenu(Modules $modules): array
    {
        $menuWithChildren = [];
        $menuUnique = [];
        foreach ($this->config->get('menu.options') as $index => $item) {
            $item['id'] = 0;

            if (empty($item['route'])) {
                $options = $modules->moduleElementsOfKey($index);

                if (count($options) > 0) {
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
        return $this->moduleFactory->buildModuleFromArray([Module::TYPE => $data]);
    }

    /**
     * @param  array<Module>  $modules
     */
    private function changeExpandedToModule(array $modules, Module $mainModule): Module
    {
        $routeCurrent = $this->router->current()->uri();

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
