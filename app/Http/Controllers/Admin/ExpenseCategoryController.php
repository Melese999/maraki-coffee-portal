<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreExpenseCategoryRequest;
use App\Http\Requests\Admin\UpdateExpenseCategoryRequest;
use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()?->can('categories.manage'), 403);

        return view('admin.categories.index', [
            'categories' => ExpenseCategory::query()->orderBy('name')->paginate(30),
        ]);
    }

    public function store(StoreExpenseCategoryRequest $request): RedirectResponse
    {
        ExpenseCategory::query()->create([
            'name' => $request->validated('name'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', __('Category added.'));
    }

    public function update(UpdateExpenseCategoryRequest $request, ExpenseCategory $category): RedirectResponse
    {
        $category->update([
            'name' => $request->validated('name'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', __('Category updated.'));
    }

    public function destroy(Request $request, ExpenseCategory $category): RedirectResponse
    {
        abort_unless($request->user()?->can('categories.manage'), 403);
        $category->delete();

        return back()->with('success', __('Category deleted.'));
    }
}
