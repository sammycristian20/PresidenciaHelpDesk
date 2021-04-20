<?php

namespace App\Http\Controllers\Admin\helpdesk\Listener;

use App\Model\Listener\Listener;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\helpdesk\Listener\ListenerRequest;

class ListenerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listeners = Listener::select('id', 'name')->orderBy('order')->get()->toJson();
        return view('themes.default1.admin.helpdesk.manage.listener.index', compact('listeners'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('themes.default1.admin.helpdesk.manage.listener.create');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ListenerRequest $request)
    {
        $events    = checkArray('events', $request->all());
        $listeners = checkArray('listeners', $request->all());
        $rules     = checkArray('rules', $request->all());
        $actions   = checkArray('actions', $request->all());
        try {
            $listener = Listener::create($listeners);
            $listener->events()->createMany($events);
            if (is_array($rules)) {
                $listener->rules()->createMany($rules);
            }
            $listener->actions()->createMany($actions);
            $message = \Lang::get('lang.listener-success', ['action' => 'created']);
            $status  = 200;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $status  = 500;
        }
        return response()->json(['message' => $message], $status);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Listener\Listener  $listener
     * @return \Illuminate\Http\Response
     */
    public function show(Listener $listener)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Listener\Listener  $listener
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        return view('themes.default1.admin.helpdesk.manage.listener.edit');
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Listener\Listener  $listener
     * @return \Illuminate\Http\Response
     */
    public function update($id,ListenerRequest $request, Listener $listener)
    {
        try {
            $events    = checkArray('events', $request->all());
            $listeners = checkArray('listeners', $request->all());
            $rules     = checkArray('rules', $request->all());
            $actions   = checkArray('actions', $request->all());
            $listener = $listener->find($id);
            if (!$listener) {
                return errorResponse(\Lang::get('lang.not_found'), 404);
            }
            ($listeners) ? $listener->fill($listeners)->save() : '';
            $this->updateEvents($listener, $events);
            $this->updateRules($listener, $rules);
            $this->updateAction($listener, $actions);
            $message   = \Lang::get('lang.listener-success', ['action' => 'updated']);
            $status    = 200;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $status  = 500;
        }
        return response()->json(['message' => $message], $status);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Listener\Listener  $listener
     * @return \Illuminate\Http\Response
     */
    public function destroy(Listener $listener)
    {
        try {
            $listener->events()->delete();
            $listener->rules()->delete();
            $listener->actions()->delete();
            $listener->delete();
            $message = \Lang::get('lang.listener-success', ['action' => 'deleted']);
            $status  = 200;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $status  = 500;
        }
        return response()->json(['message' => $message], $status);
    }
    /**
     * Update the listener dependency events
     * @param Listener $listener
     * @param array $events
     */
    public function updateEvents($listener, $events)
    {
        $n = 0;
        $listener->events()->delete();
        if ($events) {
            while ($n < count($events)) {
                $listener->events()->create($events[$n]);
                $n++;
            }
        }
    }
    /**
     * Update the listener dependency rules
     * @param Listener $listener
     * @param array $rules
     */
    public function updateRules($listener, $rules)
    {
        $listener->rules()->delete();
        if (is_array($rules)) {
            $listener->rules()->createMany($rules);
        }
    }
    /**
     * Update the listener dependency actions
     * @param Listener $listener
     * @param array $actions
     */
    public function updateAction($listener, $actions)
    {
        $n = 0;
        $listener->actions()->delete();
        if ($actions) {
            while ($n < count($actions)) {
                $key = checkArray('key', $actions[$n]);
                if($key !== 'mail') {
                    $listener->actions()->updateOrCreate(['key' => $key], $actions[$n]);
                } else {
                    $listener->actions()->create($actions[$n]);
                }
                $n++;
            }
        }
    }
    /**
     * Reordering the list position
     *
     * @param Request $request
     * @return json
     */
    public function reorder(Request $request)
    {
        try {
            $lists = $request->all();
            if (count($lists) > 0) {
                foreach ($lists as $pos => $array) {
                    $id = $array['id'];
                    Listener::whereId($id)->update(['order' => $pos + 1]);
                }
            }
            $message = ['success' => 'Ordered successfully'];
            $status  = 200;
        } catch (\Exception $ex) {
            $message = ['error' => $ex->getMessage()];
            $status  = 500;
        }
        return response()->json($message, $status);
    }
}
