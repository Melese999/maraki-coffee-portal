<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuItemRequest;
use App\Http\Requests\UpdateMenuItemRequest;
use App\Models\MenuItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', MenuItem::class);

        return view('menu.index', [
            'items' => MenuItem::query()->ordered()->paginate(30),
            'printItems' => MenuItem::query()->ordered()->get(['id', 'name', 'price', 'unit', 'is_active']),
        ]);
    }

    public function print(Request $request): Response
    {
        $this->authorize('viewAny', MenuItem::class);

        $ids = collect($request->input('ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values();

        $items = MenuItem::query()
            ->ordered()
            ->when(
                $ids->isNotEmpty(),
                fn ($query) => $query->whereIn('id', $ids),
                fn ($query) => $query->active(),
            )
            ->get();

        $filename = 'maraki-menu-'.now()->format('Y-m-d').'.pdf';

        return Pdf::loadView('menu.print', [
            'items' => $items,
            'printedAt' => now(),
        ])
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'NotoEthiopic')
            ->download($filename);
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
