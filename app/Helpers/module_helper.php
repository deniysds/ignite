<?php
    use Nwidart\Modules\Facades\Module;

    if (! function_exists('is_module_active')) {
        /**
         * Check if a Laravel module exists and is enabled.
         *
         * @param string $name
         * @return bool
         */
        function is_module_active(string $name): bool
        {
            return Module::has($name) && Module::isEnabled($name);
        }
    }
