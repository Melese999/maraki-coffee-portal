<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuItemRequest;
use App\Http\Requests\UpdateMenuItemRequest;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', MenuItem::class);

        return view('menu.index', [
            'items' => MenuItem::query()->ordered()->paginate(30),
        ]);
    }

    public function store(StoreMenuItemRequest $request): RedirectResponse
    {
        $this->authorize('create', MenuItem::class);

        MenuItem::query()->create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->integer('sort_order'),
        ]);

        return back()->with('success', __('Menu item added.'));
    }

    public function update(UpdateMenuItemRequest $request, MenuItem $menuItem): RedirectResponse
    {
        $this->authorize('update', $menuItem);

        $menuItem->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->integer('sort_order'),
        ]);

        return back()->with('success', __('Menu item updated.'));
    }

    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        $this->authorize('delete', $menuItem);
        $menuItem->delete();

        return back()->with('success', __('Menu item removed.'));
    }
}
