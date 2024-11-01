<?php

namespace App\Grids;

use Closure;
use Leantony\Grid\Grid;

class CouponsGrid extends Grid implements CouponsGridInterface
{
    /**
     * The name of the grid
     *
     * @var string
     */
    protected $name = 'Coupons';

    /**
     * List of buttons to be generated on the grid
     *
     * @var array
     */
    protected $buttonsToGenerate = [
        'create',
        // 'view',
        'delete',
        'refresh',
        // 'export'
    ];

    /**
     * Specify if the rows on the table should be clicked to navigate to the record
     *
     * @var bool
     */
    protected $linkableRows = false;

    /**
    * Set the columns to be displayed.
    *
    * @return void
    * @throws \Exception if an error occurs during parsing of the data
    */
    public function setColumns()
    {
        $this->columns = [
		    "name" => [
                "filter" => [
		            "enabled" => true,
                    "query" => function($query, $columnName, $nameInput) {
                        return $query->where("name", "like", "%" . $nameInput . "%");
                    }
		        ]
		    ],
            "code" => [
                "filter" => [
		            "enabled" => true,
                    "query" => function($query, $columnName, $nameInput) {
                        return $query->where("code", "like", "%" . $nameInput . "%");
                    }
		        ]
		    ],
            "discount" => [
                "label" => "Discount ($)",
                "filter" => [
		            "enabled" => true,
                    "query" => function($query, $columnName, $nameInput) {
                        return $query->where("discount", "like", "%" . $nameInput . "%");
                    }
		        ]
		    ],
            "using_count" => [
                "raw" => true,
                'presenter' => function ($columnData, $columnName) {
                    return '<a href="#" class="show-coupon-users" title="View Users" data-coupon_id="'.$columnData->id.'">' . $columnData->using_count . '</a>';
                },
                "label" => "Redemptions",
                "sort" => false
		    ]
		];
    }

    /**
     * Set the links/routes. This are referenced using named routes, for the sake of simplicity
     *
     * @return void
     */
    public function setRoutes()
    {
        // searching, sorting and filtering
        $this->setIndexRouteName('coupons');

        // crud support
        $this->setCreateRouteName('coupons.create');
        $this->setViewRouteName('coupons.edit');
        $this->setDeleteRouteName('coupons.delete');

        // default route parameter
        $this->setDefaultRouteParameter('id');
    }

    /**
    * Return a closure that is executed per row, to render a link that will be clicked on to execute an action
    *
    * @return Closure
    */
    public function getLinkableCallback(): Closure
    {
        return function ($gridName, $item) {
            return route($this->getViewRouteName(), [$gridName => $item->id]);
        };
    }

    /**
    * Configure rendered buttons, or add your own
    *
    * @return void
    */
    public function configureButtons()
    {
        $this->editRowButton('view', [
            'icon' => 'fa-pen-alt',
            'name' => 'Edit',
            'title' => 'Edit Coupon',
            'class' => 'btn btn-outline-info btn-sm',
            'url' => function($gridName, $gridItem) {
                return route('coupons.edit', ['coupon' => $gridItem->id]);
            },
        ]);
        $this->editRowButton('delete', [
            'class' => 'btn-remove-record data-remote grid-row-button btn btn-outline-danger btn-sm',
        ]);
        // call `addRowButton` to add a row button
        // call `addToolbarButton` to add a toolbar button
        // call `makeCustomButton` to do either of the above, but passing in the button properties as an array

        // call `editToolbarButton` to edit a toolbar button
        // call `editRowButton` to edit a row button
        // call `editButtonProperties` to do either of the above. All the edit functions accept the properties as an array
    }

    /**
    * Returns a closure that will be executed to apply a class for each row on the grid
    * The closure takes two arguments - `name` of grid, and `item` being iterated upon
    *
    * @return Closure
    */
    public function getRowCssStyle(): Closure
    {
        return function ($gridName, $item) {
            // e.g, to add a success class to specific table rows;
            // return $item->id % 2 === 0 ? 'table-success' : '';
            return "";
        };
    }
}
