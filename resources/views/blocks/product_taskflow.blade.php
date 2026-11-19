@include('partials.ui-services')
@php $svc = app('ui.services')->firstWhere('slug', 'taskflow'); @endphp
@if ($svc)
    @include('partials.product-spotlight', ['service' => $svc, 'level' => 'flagship', 'heading' => $data['heading'] ?? null])
@endif
