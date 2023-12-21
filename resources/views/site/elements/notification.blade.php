<div>
    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        @if(Session::has('alert-' . $msg))
            <div class="alert alert-dismissable mb20 alert-{{ $msg }}">
                <button aria-hidden="true" data-dismiss="alert" class="close close_alert_box" type="button">×</button>
                <span>{{ Session::get('alert-' . $msg) }}</span>
            </div>
        @endif
    @endforeach

    @if (count($errors) > 0)
        <div class="alert alert-dismissable mb20 alert-danger">
            <button aria-hidden="true" data-dismiss="alert" class="close close_alert_box" type="button">×</button>
            @foreach ($errors->all() as $error)
                <span>{{ $error }}</span><br />
            @endforeach
        </div>
    @endif
</div>