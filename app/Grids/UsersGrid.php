<?php

namespace App\Grids;

use Closure;
use Leantony\Grid\Grid;
use App\Models\Role;

class UsersGrid extends Grid implements UsersGridInterface
{
    /**
     * The name of the grid
     *
     * @var string
     */
    protected $name = 'Users';

    /**
     * List of buttons to be generated on the grid
     *
     * @var array
     */
    protected $buttonsToGenerate = [
        // 'create',
        'view',
        // 'delete',
        'refresh',
        //'export'
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
                'presenter' => function ($columnData, $columnName) {
                    return $columnData->full_name;
                },
                "filter" => [
                    "enabled" => true,
                    "query" => function($query, $columnName, $nameInput) {
                        return $query->where("name", "like", "%" . $nameInput . "%")
                            ->orWhere("lastname", "like", "%" . $nameInput . "%");
                    }
                ]
            ],
            "email" => [
                'presenter' => function ($columnData, $columnName) {
                    return $columnData->email;
                },
                "filter" => [
                    "enabled" => true,
                    "query" => function($query, $columnName, $nameInput) {
                        return $query->where("email", "like", "%" . $nameInput . "%");
                    }
                ]
            ],
            "email_verified_at" => [
                "label" => "Emai Verified",
                'presenter' => function ($columnData, $columnName) {
                    return $columnData->email_verified_at == null ? "" : "Yes";
                },
                "filter" => [
                    "enabled" => false,
                ]
            ],
            "role_id" => [
                "label" => "User Type",
                'presenter' => function ($columnData, $columnName) {
                    return $columnData->roles[0]->name;
                },
                'filter' => [
                    'enabled' => true,
                    'type' => 'select',
                    'data' => Role::query()->pluck('name', 'id'),
                ]
		    ],
		    "created_at" => [
                "label" => "Registration Date",
		        "date" => "true",
                'presenter' => function ($columnData, $columnName) {
                    return \Carbon\Carbon::parse($columnData->created_at)->format('m/d/Y');
                },
		        "filter" => [
		            "enabled" => true,
		            "type" => "date",
                    "query" => function($query, $columnName, $createdInput) {
                        return $query->whereDate("created_at", "like", "%" . \Carbon\Carbon::parse($createdInput)->format('Y-m-d') . "%");
                    }
		        ]
		    ],
		    "last_login_at" => [
                "label" => "Last Login Date",
		        "date" => "true",
                'presenter' => function ($columnData, $columnName) {
                    return $columnData->last_login_at ? \Carbon\Carbon::parse($columnData->last_login_at)->format('m/d/Y') : '';
                },
		        "filter" => [
		            "enabled" => true,
		            "type" => "date",
                    "query" => function($query, $columnName, $loggedInput) {
                        return $query->whereDate("last_login_at", "like", "%" . \Carbon\Carbon::parse($loggedInput)->format('Y-m-d') . "%");
                    }
		        ]
		    ],
            "units_count" => [
                "label" => "# Of units",
                "sort" => false
            ],
            "delete" => [
                "label" => "",
                "sort" => false,
                "raw" => true,
                'data' => function ($columnData, $columnName) {
                    return $columnData->id != \Auth::user()->id ? '<a class="btn btn-danger" href="'.route('delete-user',['user_id' => $columnData->id]).'">Delete</a>' : '';
                },
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
        $this->setIndexRouteName('users');

        // crud support
        // $this->setCreateRouteName('users.create');
        // $this->setViewRouteName('users.show');
        // $this->setDeleteRouteName('users.destroy');

        $this->setCreateRouteName('users');
        $this->setViewRouteName('users');
        $this->setDeleteRouteName('users');

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
            //'icon' => 'fa-sign-in-alt',
            'icon' => '',
            'name' => 'Login',
            'title' => 'Login as user',
            'class' => 'btn btn-info btn-login',
            'url' => function($gridName, $gridItem) {
                return $gridItem->id != \Auth::user()->id ? route('users', [$gridName => $gridItem->id]) : route('users');
            },
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
