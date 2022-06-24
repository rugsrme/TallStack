<?php

namespace App\Http\Livewire;

use App\Models\Location;
use App\Models\Workorder;
use Livewire\Component;

class WorkorderTable extends Component
{
    public $workorder;
    public $workorders;
    public $task;
    public $desc;
    public $user_name;
    public $user_id;
    public $priority;
    public $status;
    public $assigned_to;
    public $locations;
    public $location_id;
    public $search;
    public $filterType;

    protected $rules = [
      'task' => 'required|min:3',
      'desc' => '',
      'user_name' => 'string|required',
      'priority' => '',
      'status' => '',
      'assigned_to' => '',
      'user_id' => 'required',
   ];
    public $confirmingOpenCreate = false;

    public function confirmOpenCreate(Workorder $workorder)
    {
        $this->workorder = $workorder;
        if (isset($this->workorder->id)) {
            $this->task = $workorder->task;
            $this->desc = $workorder->desc;
            $this->priority = $workorder->priority;
            $workorder->status != 'request' ? $this->status = $workorder->status : $this->status = 'request';
            $this->user_name = $workorder->user_name;
            $this->user_id = $workorder->user_id;
            $this->assigned_to = $workorder->assigned_to;
        }
        $this->confirmingOpenCreate = !$this->confirmingOpenCreate;
    }

    public function deleteWorkorder(Workorder $workorder)
    {
        $workorder->delete();
        session()->flash('flash.banner', 'Workorder deleted');
        session()->flash('flash.bannerStyle', 'danger');
        return redirect('/workorders');
    }
    public function mount()
    {
        $this->locations = Location::all();
    }
    public function submit()
    {
        $validatedData = $this->validate();
        if (isset($this->workorder->id)) {
            // $this->workorder->task = $this->task;
            // $this->workorder->desc = $this->desc;
            // $this->workorder->status = $this->status;
            // $this->workorder->priority = $this->priority;
            // $this->workorder->assigned_to = $this->assigned_to;
            $this->workorder->update($validatedData);
            $this->reset();
            $this->confirmingOpenCreate = false;
            session()->flash('flash.banner', 'Workorder updated');
            session()->flash('flash.bannerStyle', 'success');
            return redirect('/workorders-table');
        }
    }
    public function render()
    {
        if (strlen($this->search  > 0)) {
            $this->workorders = Workorder::where('task', 'like', '%' . $this->search . '%')->orWhere('desc', 'like', '%' . $this->search . '%')->orderBy('updated_at', 'desc')->get();
        } elseif ($this->filterType != null) {
            $this->workorders = Workorder::status($this->filterType)->orderBy('updated_at', 'desc')->get();
        } else {
            $this->workorders = Workorder::with(['comments', 'user'])->orderBy('updated_at', 'desc')->get();
        }
        return view('livewire.workorder-table', [
         'workorders' => $this->workorders,
      ]);
    }
}
