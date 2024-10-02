<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
class LogsController extends Controller
{
    // Show logs in a DataTable format
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Fetch logs with filters applied
            $logs = Logs::query()
                ->select(['id', 'action', 'user_name', 'action_time', 'payload', 'ip_address', 'location'])
                ->when($request->action, function ($query, $action) {
                    return $query->where('action', 'like', "%{$action}%");
                })
                ->when($request->user_name, function ($query, $user_name) {
                    return $query->where('user_name', 'like', "%{$user_name}%");
                })
                ->when($request->action_time, function ($query, $action_time) {
                    return $query->whereDate('action_time', $action_time);
                })
                ->when($request->ip_address, function ($query, $ip_address) {
                    return $query->where('ip_address', 'like', "%{$ip_address}%");
                })
                ->when($request->location, function ($query, $location) {
                    return $query->where('location', 'like', "%{$location}%");
                })
                ->orderBy('action_time', 'desc');

            return DataTables::of($logs)
                ->editColumn('action_time', function ($row) {
                    return Carbon::parse($row->action_time)->format('M d, Y h:i A');
                })
                ->editColumn('payload', function ($row) {
                    $payload = json_decode($row->payload, true);

                    if (is_array($payload)) {
                        // Use array_map to handle deeper arrays or objects
                        $flatPayload = [];
                        array_walk_recursive($payload, function ($value, $key) use (&$flatPayload) {
                            $flatPayload[] = "$key: $value";
                        });

                        return implode(', ', $flatPayload);
                    }

                    return 'No payload';
                })
               
                ->make(true);
        }

        return view('security.logs');
    }






}
