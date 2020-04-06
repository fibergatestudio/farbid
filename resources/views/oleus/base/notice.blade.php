@if (isset($errors) && count($errors))
    <script type="application/javascript">
        document.addEventListener("DOMContentLoaded", function () {
            UIkit.notification('<span uk-icon="icon: ui_close"></span> {{ trans('notice.errors') }}', {
                status: 'danger',
                pos: 'bottom-right'
            });
        });
    </script>
@endif
@if (session('notice'))
    <script type="application/javascript">
        document.addEventListener("DOMContentLoaded", function () {
            UIkit.notification("{!! session('notice.message') !!}", {
                status: '{{ session('notice.status', '') }}',
                pos: 'bottom-right'
            });
        });
    </script>
@endif