<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * CODE STRUCTURE SUMMARY:
 * AnalyticsController
 */
class AnalyticsController extends Controller
{
    /* Display a listing of Analytics :: */
    public function index(Request $request): Response
    {
        return Inertia::render('backend/analytics/index');
    }
}
