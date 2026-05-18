@extends('layouts.admin')

@section('title', $title)

@section('content')
    <section class="hm-admin-page-head">
        <h1>{{ $title }}</h1>
        <p class="hm-admin-muted" style="font-size:14px">{{ $description ?? '' }}</p>
    </section>

    <section class="hm-admin-card hm-admin-panel">
        <div class="hm-admin-table-wrap">
            <table class="hm-admin-table">
                <thead>
                    <tr>
                        @foreach($columns as $column)
                            <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            @foreach($row as $cell)
                                <td>{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr><td colspan="{{ count($columns) }}"><div class="hm-admin-empty">No records found.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
