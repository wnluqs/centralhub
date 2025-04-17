<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function hr()
    {
        return view('departments.hr');
    }

    public function accounting()
    {
        return view('departments.accounting');
    }

    public function operations()
    {
        return view('departments.operations');
    }

    public function controlCenter()
    {
        return view('departments.control_center');
    }

    public function technical()
    {
        return view('departments.technical');
    }  //

    public function secretary()
    {
        return view('departments.secretary');
    }  //
}
