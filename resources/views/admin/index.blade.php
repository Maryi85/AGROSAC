@extends('admin.layout')

@section('header')
<h2 class="text-lg font-semibold text-emerald-700">Dashboard Administrador</h2>
@endsection

@section('content')
<div class="bg-white border rounded p-6">
    <p class="text-sm text-emerald-800/80">Bienvenido, {{ auth()->user()->name ?? 'Admin' }}.</p>
    <div class="grid grid-cols-2 gap-4 mt-4">
        <div class="border border-emerald-200 rounded p-4 bg-emerald-50">
            <div class="text-sm text-emerald-800/80">Usuarios</div>
            <div class="text-2xl font-semibold text-emerald-700">—</div>
        </div>
        <div class="border border-emerald-200 rounded p-4 bg-emerald-50">
            <div class="text-sm text-emerald-800/80">Lotes</div>
            <div class="text-2xl font-semibold text-emerald-700">—</div>
        </div>
    </div>
    <p class="text-xs text-emerald-800/70 mt-4">Esta es una pantalla inicial. Luego conectaremos estadísticas reales.</p>
  </div>
@endsection


