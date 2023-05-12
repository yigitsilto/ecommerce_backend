@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', "Filtreler")

@endcomponent

@component('admin::components.page.index_table')
    @slot('buttons', ['create'])
    @slot('resource', 'products')
    @slot('name', "Filtre")

    @slot('thead')


    @endslot
@endcomponent

