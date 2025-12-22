@extends('layouts.app')

@section('title', 'Order Details')

@section('header', 'Order Details')

@section('content')
    @include('orders.show', ['order' => $order])
@endsection


