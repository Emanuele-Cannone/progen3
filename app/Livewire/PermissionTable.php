<?php

namespace App\Livewire;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class PermissionTable extends PowerGridComponent
{
    use WithExport;

    public Role $role;


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
        return DB::table('permissions');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('active', function($item) {
                return $this->role->hasPermissionTo($item->name);
            })
            ->add('guard_name')
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->hidden(),

            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Guard name', 'guard_name')
                ->hidden()
                ->sortable()
                ->searchable(),

            Column::make('Active', 'active')
                ->toggleable(auth()->user()->can('edit-permission')),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->hidden()
                ->sortable(),

            Column::make('Created at', 'created_at')
                ->hidden()
                ->sortable()
                ->searchable(),

            Column::make('Updated at', 'updated_at_formatted', 'updated_at')
                ->hidden()
                ->sortable(),

            Column::make('Updated at', 'updated_at')
                ->hidden()
                ->sortable()
                ->searchable(),
        ];
    }

    public function filters(): array
    {
        return [
            //
        ];
    }

    public function onUpdatedToggleable(string|int $id, string $field, string $value): void
    {
        $permission = Permission::findById($id);

        if($value){
            $this->role->givePermissionTo($permission);
        } else {
            $this->role->revokePermissionTo($permission);
        }

        $this->skipRender();
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
