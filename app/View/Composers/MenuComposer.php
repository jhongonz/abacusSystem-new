<?php

namespace App\View\Composers;

use App\Traits\UtilsDateTimeTrait;
use Core\Employee\Domain\Employee;
use Core\Profile\Domain\Contracts\ModuleFactoryContract;
use Core\Profile\Domain\Module;
use Core\Profile\Domain\Modules;
use Core\Profile\Domain\Profile;
use Core\SharedContext\Model\ValueObjectStatus;
use Core\User\Domain\User;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Session\Session;
use Illuminate\Routing\Route;
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
        UrlGenerator $urlGenerator,
    ) {
        $this->moduleFactory = $moduleFactory;
        $this->config = $config;
        $this->router = $router;
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
        $this->imagePathFull = '/images/full/';
    }

    /**
     * @throws \Exception
     */
    public function compose(View $view): void
    {
        /** @var User $user */
        $user = $this->session->get('user');

        /** @var Profile $profile */
        $profile = $this->session->get('profile');

        /** @var Employee $employee */
        $employee = $this->session->get('employee');

        $menu = $this->prepareMenu($profile->modules());
        $image = $this->urlGenerator->to(sprintf('%s%s?v=%s', $this->imagePathFull, $user->photo()->value(), Str::random()));

        $view->with('menu', $menu);
        $view->with('user', $user);
        $view->with('employee', $employee);
        $view->with('profile', $profile);
        $view->with('image', $image);
    }

    /**
     * @return array<int, mixed>
     * @throws \Exception
     */
    private function prepareMenu(Modules $modules): array
    {
        $menuWithChildren = [];
        $menuUnique = [];

        /** @var array<string> $menuOptions */
        $menuOptions = $this->config->get('menu.options');

        /**
         * @var array<string, mixed> $item
         */
        foreach ($menuOptions as $item) {

            $module = $this->getModuleMenu($item);
            $module->id()->setValue(0);
            $module->state()->setValue(ValueObjectStatus::STATE_ACTIVE);

            if ($module->isParent()) {
                $options = $modules->moduleElementsOfKey($module->menuKey()->value());

                if (count($options) > 0) {
                    $module->menuKey()->setValue($module->menuKey()->value());
                    $module->route()->setValue('');

                    $module = $this->changeExpandedToModule($options, $module);
                    $menuWithChildren[] = $module;
                }
            } else {
                $module->menuKey()->setValue('');
                $menuUnique[] = $module;
            }
        }

        return array_merge($menuWithChildren, $menuUnique);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function getModuleMenu(array $data): Module
    {
        return $this->moduleFactory->buildModuleFromArray([Module::TYPE => $data]);
    }

    /**
     * @param array<Module> $modules
     */
    private function changeExpandedToModule(array $modules, Module $mainModule): Module
    {
        /** @var Route $routeCurrent */
        $routeCurrent = $this->router->current();
        $uriCurrent = $routeCurrent->uri();

        foreach ($modules as $item) {
            if ($item->route()->value() === $uriCurrent) {
                $item->setExpanded(true);
                $mainModule->setExpanded(true);
            }
        }
        $mainModule->setOptions($modules);

        return $mainModule;
    }
}
