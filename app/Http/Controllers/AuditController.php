<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class AuditController extends Controller
{
    public function audit()
    {
        $audits = Audit::with('user')
            ->orderBy('created_at', 'desc')->get();
        return view('audit', compact('audits'));
    }
}
