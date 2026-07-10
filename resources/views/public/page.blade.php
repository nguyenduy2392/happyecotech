@extends('layouts.public')

@section('content')
    @foreach ($page->blocks as $block)
        @includeIf('blocks.'.$block->block_type, ['block' => $block, 'data' => $block->data ?? []])
    @endforeach
@endsection
