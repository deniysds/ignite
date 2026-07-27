<?php

    namespace App\Providers;

    use Illuminate\Support\Facades\File;
    use Illuminate\Support\ServiceProvider;

    class MenuServiceProvider extends ServiceProvider
    {
        protected $menus = [];

        public function boot()
        {
            $this->loadMenus();
            $this->shareMenusWithViews();
        }

        protected function loadMenus()
        {
            $modulesPath = base_path('Modules');
            $moduleDirs = File::directories($modulesPath);

            foreach ($moduleDirs as $moduleDir) {
                if(!is_module_active(basename($moduleDir)))
                {
                    continue;
                }
                $moduleJsonPath = $moduleDir . '/module.json';
                if (File::exists($moduleJsonPath)) {
                    $moduleConfig = json_decode(File::get($moduleJsonPath), true);
                    if (isset($moduleConfig['menu'])) {
                        $this->mergeMenus($moduleConfig['menu']);
                    }
                }
            }
        }

        protected function mergeMenus(array $menu)
        {
            foreach ($menu as $key => $items) {
                if (!isset($this->menus[$key])) {
                    $this->menus[$key] = [];
                }

                foreach ($items as $newItem) {
                    $existingItemKey = $this->findExistingMenuItem($this->menus[$key], $newItem);

                    if ($existingItemKey !== null) {
                        // Merge submenus if the item already exists
                        $this->menus[$key][$existingItemKey]['sub'] = array_merge(
                            $this->menus[$key][$existingItemKey]['sub'] ?? [],
                            $newItem['sub'] ?? []
                        );
                    } else {
                        // Add new item if it doesn't exist
                        $this->menus[$key][] = $newItem;
                    }
                }
            }
        }

        protected function findExistingMenuItem(array $menuItems, array $newItem)
        {
            foreach ($menuItems as $index => $item) {
                if ($item['title'] === $newItem['title'] || $item['path'] === $newItem['path']) {
                    return $index;
                }
            }
            return null;
        }

        protected function shareMenusWithViews()
        {
            view()->share('menus', $this->menus);
        }
    }
