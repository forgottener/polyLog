<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="{{ route('admin.index') }}" class="navbar-brand">
                <i class="fa fa-fw fa-book"></i> polyLog
            </a>
        </div>
        <ul class="nav navbar-nav">
            <li class="{{ Route::is('admin.platform.index') ? 'active' : '' }}">
                <a href="{{ route('admin.platform.index') }}">
                    <i class="fa fa-dashboard"></i> Dashboard
                </a>
            </li>
            <li class="{{ Route::is('admin.platform.all') ? 'active' : '' }}">
                <a href="{{ route('admin.platform.all') }}">
                    <i class="fa fa-archive"></i> Statistics
                </a>
            </li>
            <li class="{{ Route::is('admin.platform.logs.list') ? 'active' : '' }}">
                <a href="{{ route('admin.platform.logs.list') }}">
                    <i class="fa fa-archive"></i> Logs
                </a>
            </li>
        </ul>
    </div>
</nav>
