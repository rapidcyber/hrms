<?php

use App\Models\ActivityLog;

if (!function_exists('log_activity')) {
    function log_activity($action, $description = null, $subject = null, $properties = [])
    {
        ActivityLog::create([
            'user_id'     => auth()->id() ?? 1,
            'action'      => $action,
            'description' => $description,
            'subject_type'=> $subject ? get_class($subject) : null,
            'subject_id'  => $subject->id ?? null,
            'properties'  => $properties,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }
}
