<?php

namespace Wontonee\Shiprocket\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;
use Wontonee\Shiprocket\Models\ShiprocketOrder;
use Wontonee\Shiprocket\Repositories\ShiprocketRepository;
use Illuminate\Support\Collection;

class ShipmentDataGrid extends DataGrid
{
    /**
     * Primary Column.
     *
     * @var string
     */
    protected $primaryColumn = 'id';

    /**
     * Shipments collection from API
     * 
     * @var Collection
     */
    protected $shipments;

    /**
     * Create a new datagrid instance.
     * 
     * @param ShiprocketRepository $repository
     * @return void
     */    public function __construct(protected ShiprocketRepository $repository)
    {
        // Get shipments and sort them by created_at to ensure consistent ordering
        $this->shipments = $repository->getShipmentsWithOrderDetails()
            ->sortBy('created_at')
            ->values(); // Re-index the collection after sorting
    }
    /**
     * Prepare query builder.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function prepareQueryBuilder()
    {
        // Use a dummy query builder since we're using API data
        $queryBuilder = DB::table(DB::raw('(SELECT 1) as dummy'))
            ->whereRaw('0=1'); // This ensures an empty result set

        return $queryBuilder;
    }
    /**
     * Manually handle the data pagination and processing since we're using API data
     * 
     * @return array
     */
    public function process(): array
    {
        // Prepare the DataGrid with columns, actions, etc.
        $this->prepare();

        // Get pagination parameters from request
        $page = request()->get('page') ?: 1;
        $perPage = request()->get('per_page') ?: $this->itemsPerPage;

        // Manually paginate the collection
        $offset = ($page - 1) * $perPage;
        $items = $this->shipments->slice($offset, $perPage)->values();        // Transform the data to an appropriate format for the DataGrid
        $records = [];
        foreach ($items as $key => $item) {
            $record = (object) $item;

            // Add auto-increment ID field starting from 1
            // The ID will be based on the total collection position, not just the current page
            $record->id = $offset + $key + 1;

            // Add a special flag for pending status to help with filtering actions
            $record->is_pending = in_array(strtoupper($record->status), ['PENDING', 'READY TO SHIP', 'NEW', 'PROCESSING']);

            $records[] = $record;
        }

        $total = $this->shipments->count();

        // Get all actions
        $actions = $this->formatActions();

        // Process actions for each record
        $processedRecords = [];
        foreach ($records as $index => $record) {
            $recordActions = [];            // Filter actions for this specific record
            foreach ($actions as $action) {
                // Always include the view action
                if (isset($action['icon']) && ($action['icon'] === 'icon-eye' || $action['icon'] === 'icon-sort-right')) {
                    // Make a copy to avoid modifying the original
                    $actionCopy = $action;

                    // If URL is a closure, execute it with the current record
                    if (isset($actionCopy['url']) && is_callable($actionCopy['url'])) {
                        $actionCopy['url'] = $actionCopy['url']($record);
                    }

                    $recordActions[] = $actionCopy;
                }
                // Only include cancel action for pending shipments
                elseif (isset($action['icon']) && $action['icon'] === 'icon-cancel' && $record->is_pending) {
                    $recordActions[] = $action;
                }
            }

            // Add actions to the record
            $record->actions = $recordActions;
            $processedRecords[] = $record;
        }

        // Format the response to match what the DataGrid expects
        return [
            'records' => $processedRecords,
            'meta' => [
                'primary_column' => $this->primaryColumn,
                'page'      => (int) $page,
                'from'      => $offset + 1,
                'to'        => min($offset + $perPage, $total),
                'total'     => (int) $total,
                'per_page'  => (int) $perPage,
                'last_page' => ceil($total / $perPage),
                'current_page' => (int) $page,
            ],
            'columns' => $this->formatColumns(),
            'actions' => $actions,  // Include all possible actions
            'mass_actions' => [], // Important: use mass_actions, not massActions
        ];
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function prepareColumns()
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('shiprocket::app.admin.sales.shipments.datagrid.id'),
            'type'       => 'integer',
            'searchable' => false,
            'filterable' => false,
            'sortable'   => true,
        ]);
        $this->addColumn([
            'index'      => 'shiprocket_order_id',
            'label'      => trans('shiprocket::app.admin.sales.shipments.datagrid.shiprocket-order-id'),
            'type'       => 'string',
            'searchable' => true, // Disabled search
            'filterable' => true,
            'sortable'   => true,
        ]);
        $this->addColumn([
            'index'      => 'shiprocket_shipment_id',
            'label'      => trans('shiprocket::app.admin.sales.shipments.datagrid.shiprocket-shipment-id'),
            'type'       => 'string',
            'searchable' => false, // Disabled search
            'filterable' => true,
            'sortable'   => true,
        ]);
        $this->addColumn([
            'index'      => 'increment_id',
            'label'      => trans('shiprocket::app.admin.sales.shipments.datagrid.order-id'),
            'type'       => 'string',
            'searchable' => false, // Disabled search
            'filterable' => true,
            'sortable'   => true,
        ]);
        $this->addColumn([
            'index'      => 'status',
            'label'      => trans('shiprocket::app.admin.sales.shipments.datagrid.status'),
            'type'       => 'string',
            'searchable' => false, // Disabled search
            'filterable' => true,
            'sortable'   => true,
        ]);
        $this->addColumn([
            'index'      => 'tracking_number',
            'label'      => trans('shiprocket::app.admin.sales.shipments.datagrid.tracking-number'),
            'type'       => 'string',
            'searchable' => false, // Disabled search
            'filterable' => true,
            'sortable'   => true,
        ]);
        $this->addColumn([
            'index'      => 'courier_name',
            'label'      => trans('shiprocket::app.admin.sales.shipments.datagrid.courier'),
            'type'       => 'string',
            'searchable' => false, // Disabled search
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'customer_first_name',
            'label'      => trans('shiprocket::app.admin.sales.shipments.datagrid.customer'),
            'type'       => 'string',
            'searchable' => false,
            'filterable' => false,
            'sortable'   => false,
            'closure'    => function ($row) {
                return ($row->customer_first_name ?? '') . ' ' . ($row->customer_last_name ?? '');
            },
        ]);
        $this->addColumn([
            'index'      => 'grand_total',
            'label'      => trans('shiprocket::app.admin.sales.shipments.datagrid.amount'),
            'type'       => (strpos(core()->version(), '2.2') === 0 ? 'string' : 'decimal'),
            'searchable' => false, // Disabled search
            'filterable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                return core()->formatPrice($row->grand_total, core()->getBaseCurrencyCode());
            },
        ]);
        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('shiprocket::app.admin.sales.shipments.datagrid.created-at'),
            'type'       => 'datetime',
            'searchable' => false, // Disabled search
            'filterable' => true,
            'sortable'   => true,
        ]);
    }
    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        $this->addAction([
            'index'  => 'view',
            'icon'   => 'icon-sort-right',
            'title'  => 'View',
            'method' => 'GET',
            'url'    => function ($row) {
                return route('admin.shiprocket.shipment.view', $row->shiprocket_order_id);
            },
        ]);
    }

    /**
     * Prepare mass actions.
     *
     * @return void
     */
    public function prepareMassActions()
    {
        // No mass actions for this datagrid
    }

    /**
     * Get columns for DataGrid
     * 
     * @return array
     */
    public function getColumns(): array
    {
        $columns = [];

        foreach ($this->columns as $column) {
            $columns[] = [
                'index' => $column->getIndex(),
                'label' => $column->getLabel(),
                'type' => $column->getType(),
                'searchable' => $column->getSearchable(),
                'filterable' => $column->getFilterable(),
                'sortable' => $column->getSortable(),
                'visibility' => true,
                'closure' => $column->getClosure() ? true : false
            ];
        }

        return $columns;
    }
    /**
     * Get actions for DataGrid
     * 
     * @return array
     */
    public function getActions(): array
    {
        $actions = [];

        foreach ($this->actions as $action) {
            $actions[] = [
                'title' => $action->title,
                'method' => $action->method,
                'url' => $action->url,
                'icon' => $action->icon,
            ];
        }

        return $actions;
    }
    /**
     * Get mass actions for DataGrid
     * 
     * @return array
     */
    public function getMassActions(): array
    {
        // Always return empty array as we don't have mass actions
        return [];
    }

    /**
     * Format columns for DataGrid
     * 
     * @return array
     */
    protected function formatColumns(): array
    {
        return collect($this->columns)
            ->map(function ($column) {
                return [
                    'index' => $column->getIndex(),
                    'label' => $column->getLabel(),
                    'type' => $column->getType(),
                    'searchable' => $column->getSearchable(),
                    'filterable' => $column->getFilterable(),
                    'sortable' => $column->getSortable(),
                    'visibility' => true,
                ];
            })
            ->toArray();
    }
    /**
     * Format actions for DataGrid
     * 
     * @return array
     */    protected function formatActions(): array
    {
        $formattedActions = [];

        foreach ($this->actions as $action) {
            $formattedAction = [
                'title'  => $action->title,
                'method' => $action->method,
                'icon'   => $action->icon,
                'url'    => $action->url,
            ];

            // Add condition if available
            if (isset($action->condition)) {
                $formattedAction['condition'] = true;
            }

            // Don't use Vite for this action
            if ($action->icon === 'icon-cancel') {
                $formattedAction['useVite'] = false;
            }

            $formattedActions[] = $formattedAction;
        }

        return $formattedActions;
    }
}
