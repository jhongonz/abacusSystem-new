<?php

/**
 * @author Jhonny Andres Gonzalez <jhonnygonzalezf@gmail.com>
 * Date: 2024-05-27 23:42:53
 */

namespace App\Traits;

trait DataTablesTrait
{
    /**
     * @param array<string, mixed> $item
     */
    private function retrieveMenuOptionHtml(array $item, ?string $permission = null): string
    {
        return $this->viewFactory->make('components.menu-options-datatable')
            ->with('item', $item)
            ->with('permission', $permission)
            ->render();
    }
}
