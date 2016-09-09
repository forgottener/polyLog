@extends('admin.layouts.poly-log._template.master')

@section('content')
    <h1 class="page-header">各平台日志</h1>
    <div class="table-responsive">
        <table class="table table-condensed table-hover table-stats">
            <thead>
                <tr>
                    @foreach($headers as $key => $header)
                    <th class="{{ $key == 'channel' ? 'text-left' : 'text-center' }}">
                        @if ($key == 'channel')
                            <span class="label label-info">{{ $header }}</span>
                        @else
                            <span class="level level-{{ $key }}">
                                {!! log_styler()->icon($key) . ' ' . $header !!}
                            </span>
                        @endif
                    </th>
                    @endforeach
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $channel => $row)
                <tr>
                    @foreach($row as $key => $value)
                    <td class="{{ $key == 'channel' ? 'text-left' : 'text-center' }}">
                        @if ($key == 'channel')
                            <span class="label label-primary">{{ $value }}</span>
                        @elseif ($value == 0)
                            <span class="level level-empty">{{ $value }}</span>
                        @else
                            <a href="{{ route('admin.platform.logs.filter', [$channel, $key]) }}">
                                <span class="level level-{{ $key }}">{{ $value }}</span>
                            </a>
                        @endif
                    </td>
                    @endforeach
                    <td class="text-right">
                        <a href="{{ route('admin.platform.logs.show', [$channel]) }}" class="btn btn-xs btn-info">
                            <i class="fa fa-search"></i>
                        </a>
                        {{--<a href="{{ route('admin.platform.logs.download', [$channel]) }}" class="btn btn-xs btn-success">
                            <i class="fa fa-download"></i>
                        </a>
                        <a href="#delete-log-modal" class="btn btn-xs btn-danger" data-log-channel="{{ $channel }}">
                            <i class="fa fa-trash-o"></i>
                        </a>--}}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {!! $rows->render() !!}

    {{-- DELETE MODAL --}}
    <div id="delete-log-modal" class="modal fade">
        <div class="modal-dialog">
            <form id="delete-log-form" action="{{ route('admin.platform.logs.delete') }}" method="POST">
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="channel" value="">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">删除渠道日志</h4>
                    </div>
                    <div class="modal-body">
                        <p></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-default pull-left" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-sm btn-danger" data-loading-text="Loading&hellip;">删除</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(function () {
            var deleteLogModal = $('div#delete-log-modal'),
                deleteLogForm  = $('form#delete-log-form'),
                submitBtn      = deleteLogForm.find('button[type=submit]');

            $("a[href=#delete-log-modal]").click(function(event) {
                event.preventDefault();
                var channel = $(this).data('log-channel');
                deleteLogForm.find('input[name=channel]').val(channel);
                deleteLogModal.find('.modal-body p').html(
                    '你确定要<span class="label label-danger">删除</span> 这个渠道 <span class="label label-primary">' + channel + '</span> ?'
                );

                deleteLogModal.modal('show');
            });

            deleteLogForm.submit(function(event) {
                event.preventDefault();
                submitBtn.button('loading');

                $.ajax({
                    url:      $(this).attr('action'),
                    type:     $(this).attr('method'),
                    dataType: 'json',
                    data:     $(this).serialize(),
                    success: function(data) {
                        submitBtn.button('reset');
                        if (data.result === 'success') {
                            deleteLogModal.modal('hide');
                            location.reload();
                        }
                        else {
                            alert('AJAX ERROR ! Check the console !');
                            console.error(data);
                        }
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        alert('AJAX ERROR ! Check the console !');
                        console.error(errorThrown);
                        submitBtn.button('reset');
                    }
                });

                return false;
            });

            deleteLogModal.on('hidden.bs.modal', function(event) {
                deleteLogForm.find('input[name=channel]').val('');
                deleteLogModal.find('.modal-body p').html('');
            });
        });
    </script>
@stop
