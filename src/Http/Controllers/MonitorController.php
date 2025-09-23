<?php

namespace Emmanuelikeogu\DevGuard\Http\Controllers;

use Inertia\Inertia;

class MonitorController
{
  public function index()
  {
    return Inertia::render('Dashboard');
  }
}
