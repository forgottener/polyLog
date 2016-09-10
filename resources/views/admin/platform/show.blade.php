@extends('admin.layouts.poly-log._template.master')

@section('content')
    <h1 class="page-header">Log [{{ !empty($log->channel) ? $log->channel : "All"}}]</h1>
    <form class="form-search pull-right">
        <label for="logId">log_id:</label>
        <input type="text" name="logId" class="search-query" value="{{ request()->input('logId') }}">
        <button type="submit" class="btn">Search</button>
    </form>
    <div class="row">
        <div class="col-md-1">
            @include('admin.layouts.poly-log._partials.menu')
        </div>
        <div class="col-md-11">
            <div class="panel panel-default">
                @if ($log->hasPages())
                    <div class="panel-heading">
                        {!! $log->render() !!}

                        <span class="label label-info pull-right">
                            Page {!! $log->currentPage() !!} of {!! $log->lastPage() !!}
                        </span>
                    </div>
                @endif

                <div class="table-responsive">
                    <table id="entries" class="table table-condensed">
                        <thead>
                            <tr>
                                <th>Channel</th>
                                <th style="width: 120px;">Level</th>
                                <th style="width: 65px;">Time</th>
                                <th>Message</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($log as $key => $entry)
                                <tr>
                                    <td>
                                        <a class="under" href="{{ route('admin.platform.logs.show', [$entry->channel]) }}">
                                            <span class="label label-env">{{ $entry->channel }}</span>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="level level-{{ $entry->level_name }}">
                                            {!! $log->menu[$entry->level_name]['icon'] !!} {{ $log->menu[$entry->level_name]['name'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="label label-default">
                                            {{ $entry->created_at->format('Y-m-d H:i:s') }}
                                        </span>
                                    </td>
                                    <td>
                                        <p>{{ $entry->message }}</p>
                                    </td>
                                    <td class="text-right">
                                        <a class="btn btn-xs btn-default" role="button" data-toggle="collapse" href="#log-detail-{{ $key }}" aria-expanded="false" aria-controls="log-stack-{{ $key }}">
                                            <i class="fa fa-toggle-on"></i> Details
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="stack">
                                        <div class="stack-content collapse" id="log-detail-{{ $key }}">
                                            {!! json_encode(json_decode($entry->data), JSON_PRETTY_PRINT) !!}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($log->hasPages())
                    <div class="panel-footer">
                        {!! $log->render() !!}

                        <span class="label label-info pull-right">
                            Page {!! $log->currentPage() !!} of {!! $log->lastPage() !!}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
    </script>
@endsection
