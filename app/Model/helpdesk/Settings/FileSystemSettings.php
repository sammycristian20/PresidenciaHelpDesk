<?php

namespace App\Model\helpdesk\Settings;

class FileSystemSettings extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'settings_filesystem';

    protected $fillable = ['disk', 'allowed_files','show_public_folder_with_default_disk','files_moved_from_old_private_disk'];
}
