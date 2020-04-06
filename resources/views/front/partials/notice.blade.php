@if (session('notice'))
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function () {
            UIkit.notification("{!! session('notice.message') !!}", {
                status: '{{ session('notice.status', 'primary') }}',
                pos: 'top-center'
            });
        });
    </script>
@endif
@if (session('modal'))
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function () {
            UIkit.modal('<div class="uk-modal uk-flex-top" id="modal-alert"><div class="uk-modal-dialog uk-margin-auto-vertical uk-border-rounded alert-{{ session('modal.status') }}">' +
            '{!! session('modal.message') !!}</div></div>', {}).show();
        });
    </script>
@endif
@if (session('commands'))
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function () {
            setTimeout(function () {
                var $commands = <?= session('commands') ?>;
                for (var $i = 0; $i < $commands.length; ++$i) {
                    command_action($commands[$i]);
                }
            }, 500);
        });
    </script>
@endif