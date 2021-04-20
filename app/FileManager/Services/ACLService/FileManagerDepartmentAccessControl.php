<?php

namespace App\FileManager\Services\ACLService;

use App\FileManager\Models\FileManagerAclRule;

class FileManagerDepartmentAccessControl implements ACLRepository
{

    /**
     * @inheritDoc
     */
    public function getUserDepartments()
    {
        // actually returning departments
        return array_column(\Auth::user()->departments()->get()->toArray(), 'id');
    }

    /**
     * @inheritDoc
     */
    public function getRules(): array
    {
        $rules = [];

        $filesAndFolders = FileManagerAclRule::query()
            ->whereHas('departments', function ($q) {
                return $q->whereNotIn('department_id', $this->getUserDepartments());
            })->get()->toArray();

        foreach ($filesAndFolders as $filesAndFolder) {
            if ($filesAndFolder['disk'] === 'public') {
                //if disk is `public`,then files and folders of public disk are displayed irrespective of department
                continue;
            }
            $rules[] = [
                "disk" => $filesAndFolder['disk'],
                "path" => $filesAndFolder['path'],
                "access" => 0 //files that does not belong to the department will be hidden
            ];
        }

        return $rules;
    }
}
