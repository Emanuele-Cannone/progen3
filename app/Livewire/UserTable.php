<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use Spatie\Permission\Models\Role;

final class UserTable extends PowerGridComponent
{
    use WithExport;

    public string $sortField = 'name';

    public string $sortDirection = 'asc';

    public array $role;

    public bool $showErrorBag = true;

    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return User::query()->with(['roles']);
    }

    public function relationSearch(): array
    {
        return [
            'roles' => [
                'name',
            ],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('email')
            ->add('role', fn($user) => e(implode(', ', $user->getRoleNames()->toArray())))
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->hidden(),

            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable(),

            Column::make('Role', 'role')
                ->searchable()
                ->editOnClick(auth()->user()->can('assign-role')),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable()
                ->hidden(),

            Column::make('Created at', 'created_at')
                ->sortable()
                ->searchable()
                ->hidden(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }


    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    #[\Livewire\Attributes\On('impersonate')]
    public function impersonate($userId): void
    {
        $this->redirectRoute('impersonate.user', $userId);
    }

    public function actions(User $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: ' . $row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id]),

            Button::add('impersonate')
                ->slot('Impersonate: ' . $row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('impersonate', ['userId' => $row->id])
        ];
    }

    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        $this->validate();

        User::query()->find($id)->syncRoles([]);

        collect(explode(',', $value))->each(fn($role) => User::query()
            ->find($id)
            ->assignRole(Str::replaceMatches('/(^\s+|\s+$)/u', '', $role))
        );

    }

    protected function rules(): array
    {
        return [
            'role.*' => [
                'nullable',
                'string',
            ],
        ];
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
