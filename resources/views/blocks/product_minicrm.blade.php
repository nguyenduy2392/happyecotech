@include('partials.ui-services')
@php $svc = app('ui.services')->firstWhere('slug', 'minicrm'); @endphp
@if ($svc)
    @include('partials.product-spotlight', ['service' => $svc, 'level' => 'second', 'heading' => $data['heading'] ?? null])
@endif
