<?php

namespace App\Livewire;

use App\Models\Group;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
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

final class GroupTable extends PowerGridComponent
{
    use WithExport;

    public array $active;

    public array $name;

    public bool $showErrorBag = true;

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

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
        return Group::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    protected function rules()
    {
        return [

            'active.*' => [
                'required',
                'boolean',
            ],

            'name.*' => [
                'required',
                'unique:groups,name'
            ]

        ];
    }

    protected function messages()
    {
        return [
            'name.*.unique' => 'Nome già utilizzato.',
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('description')
            ->add('active')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [

            Column::make('Id', 'id')
                ->visibleInExport(false)
                ->hidden(),

            Column::make('Name', 'name')
                ->sortable()
                ->searchable()
                ->editOnClick(auth()->user()->can('edit-group')),

            Column::make('Description', 'description')
                ->sortable()
                ->searchable()
                ->editOnClick(auth()->user()->can('edit-group')),

            Column::make('Active', 'active')
                ->sortable()
                ->searchable()
                ->toggleable(auth()->user()->can('publish-group')),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable()
                ->visibleInExport(false)
                ->hidden(),

            Column::make('Created at', 'created_at')
                ->sortable()
                ->hidden()
                ->visibleInExport(false)
                ->searchable(),

            Column::action('Action')
                ->visibleInExport(false)
                ->hidden(!auth()->user()->can('delete group'))
        ];
    }

    public function filters(): array
    {
        return [
            //
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions(Group $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: '.$row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id])
        ];
    }

    public function onUpdatedToggleable(string|int $id, string $field, string $value): void
    {
        $this->validate();

        throw_if(auth()->user()->cannot('publish group'), new Exception);

        try{
            DB::beginTransaction();

            Group::query()->find($id)->update([
                $field => e($value),
            ]);

            DB::commit();

        }catch (Exception $e) {

            DB::rollBack();
    }


        $this->skipRender();
    }

    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        $this->validate();

        if(auth()->user()->can('edit group')){

            if ($field === 'name' && Str::lower($value) !== 'nuovo gruppo') {

                Group::query()->find($id)->update([
                    $field => e($value),
                ]);

                if(auth()->user()->can('create group')){

                    Group::where($field, 'nuovo gruppo')->firstOrCreate([
                        'name' => 'nuovo gruppo',
                        'description' => 'descrizione nuovo gruppo',
                    ]);

                }

            }

            if ($field === 'description') {

                Group::query()->find($id)->update([
                    $field => e($value),
                ]);

            }

        }


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
