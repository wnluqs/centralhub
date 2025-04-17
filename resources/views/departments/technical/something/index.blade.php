@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ route('departments.technical') }}" class="btn btn-secondary mb-3">
        ← Back to Technical Dashboard
    </a>

    <div class="container">
        <h2>Data Analytic</h2>
        <p>This dashboard shows data from the Control Center.</p>
        <h1>En Nik Haqim requires the Technical Datasets that will be displayed here. Need to get the link from En Fais
        </h1>
        <iframe width="100%" height="600"
            src="https://app.powerbi.com/view?r=eyJrIjoiNWZhMDMxMTUtYzc4YS00YjEyLTg1ZDQtMDEzNmVmNGI1NmFlIiwidCI6ImUwYWJhMzE2LTg2ZDQtNDM5Mi05ZGZiLTA0OWU1NGRhOThkYyIsImMiOjEwfQ%3D%3D"
            frameborder="0" allowFullScreen="true">
        </iframe>

    </div>
</div>
@endsection