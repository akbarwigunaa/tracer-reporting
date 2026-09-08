@extends('layouts.app')

@section('title', 'Detail Analisis')
@section('page-title', 'Detail Analisis')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('analysis.index') }}">Analisis</a></li>
<li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<p class="text-muted">Detail analisis 13 parameter akan diimplementasi di Tahap 23.</p>
@endsection
