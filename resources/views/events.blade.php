{{--
  Template Name: Events
--}}

@php

@endphp

@extends('layouts.app')

@section('content')
    @include('partials.page-header')
    <div class="container">
        <ul class="events-category">
            <li data-value="all">
                All
            </li>
            <li data-value="community">
                Community Event
            </li>
            <li data-value="conference">
                PCC Conference
            </li>
            <li data-value="pcc">
                PCC Event
            </li>
            <li data-value="icde">
                ICDE Event
            </li>
            <li data-value="course">
                Course
            </li>
        </ul>
    </div>
    <div id="pcc-event-template-unique-id" class="content">
        <div class="wp-block-group">
            <div class="cards cards--three-columns  events-container">
            </div>
            <nav class="navigation pagination" aria-label="Posts">
            </nav>
        </div>
    </div>
@endsection
