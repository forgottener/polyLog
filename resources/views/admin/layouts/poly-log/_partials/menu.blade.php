<div class="panel panel-default">
    <div class="panel-heading"><i class="fa fa-fw fa-flag"></i> Levels</div>
    <ul class="list-group">
        @foreach($log->menu as $level => $item)
            <a href="{{ $item['url'] }}" class="list-group-item {{ $level }}">
                <span class="level level-{{ $level }}">
                        {!! $item['icon'] !!} {{ $item['name'] }}
                    </span>
            </a>
        @endforeach
    </ul>
</div>
