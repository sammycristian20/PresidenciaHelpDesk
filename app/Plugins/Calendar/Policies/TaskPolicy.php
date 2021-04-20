<?php

namespace App\Plugins\Calendar\Policies;

use App\Plugins\Calendar\Model\Task;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any tasks.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return (in_array($user->role, ['admin','agent']));
    }

    /**
     * Determine whether the user can view the task.
     *
     * @param User $user
     * @param Task $task
     * @return mixed
     */
    public function view(User $user, Task $task)
    {
       return $this->hasPermission($user, $task);
    }

    /**
     * Determine whether the user can create tasks.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return (in_array($user->role, ['admin','agent']));
    }

    /**
     * Determine whether the user can update the task.
     *
     * @param User $user
     * @param Task $task
     * @return mixed
     */
    public function update(User $user, Task $task)
    {
        return $this->hasPermission($user, $task);
    }

    /**
     * Determine whether the user can delete the task.
     *
     * @param User $user
     * @param  Task  $task
     * @return mixed
     */
    public function delete(User $user, Task $task)
    {
        return $this->hasPermission($user, $task);
    }

    /**
     * Verifies whether the user has permission to modify task.
     * @param User $user
     * @param Task $task
     * @return bool
     */
    private function hasPermission(User $user, Task $task)
    {
        if ($task->is_private) {
            return $user->id === $task->created_by->id;
        } else {
            $agents = $task->assignedTo->pluck('user_id')->toArray();
            return ($user->id === $task->created_by->id) || (in_array($user->id, $agents) || ($user->role === 'admin'));
        }
    }
}
