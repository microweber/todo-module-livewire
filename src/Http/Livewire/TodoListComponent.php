<?php

namespace MicroweberPackages\Modules\TodoModuleLivewire\Http\Livewire;

use Livewire\Component;
use MicroweberPackages\Modules\TodoModuleLivewire\Models\Todo;

class TodoListComponent extends Component
{
    public $objects = [];

    public $paginator = [];

    public $page = 1;

    public $items_per_page = 5;

    public $loading_message = "";

    public $listeners = [
        "load_list" => "loadList"
    ];

    public $filter = [
        "search" => "",
        "status" => "",
        "order_field" => "",
        "order_type" => "",
    ];

    protected $updatesQueryString = ['page'];

    public function mount(){
        $this->loadList();
    }

    public function loadList(){
        $this->loading_message = "Loading Todos...";

        $query = [];

        if(!empty($this->filter["status"])){
            $query["status"] = $this->filter["status"];
        }

        $objects = Todo::where($query);

        // Search
        if(!empty($this->filter["search"])){
            $filter = $this->filter;
            $objects = $objects->where(function ($query) use ($filter) {
                $query->where('title', 'LIKE', $this->filter['search'] . '%');
            });
        }

        // Ordering
        if(!empty($this->filter["order_field"])){
            $order_type = (!empty($this->filter["order_type"]))? $this->filter["order_type"]: 'ASC';
            $objects = $objects->orderBy($this->filter["order_field"], $order_type);
        }

        // Paginating
        $objects = $objects->paginate($this->items_per_page);


        $this->paginator = $objects->toArray();
        $this->objects = $objects->items();

    }

    // Pagination Method
    public function applyPagination($action, $value, $options=[]){

        if( $action == "previous_page" && $this->page > 1){
            $this->page-=1;
        }

        if( $action == "next_page" ){
            $this->page+=1;
        }

        if( $action == "page" ){
            $this->page=$value;
        }

        $this->loadList();
    }


    public function render()
    {
        return view('todo-module-livewire::admin.todo.list');
    }
}
