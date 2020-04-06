<div class="uk-margin-small-bottom">
    <nav class="uk-navbar uk-navbar-container uk-navbar-transparent uk-box-shadow-small">
        <div class="uk-navbar-left">
            <a class="uk-navbar-toggle uk-text-uppercase uk-text-bold"
               uk-toggle
               href="#left-sidebar">
                <span uk-navbar-toggle-icon></span>
            </a>
        </div>
        <div class="uk-navbar-right">
            {!! _l(trans('others.link_to_site'), '/', ['a' => ['target' => '_blank', 'class' => 'uk-button uk-button-secondary uk-margin-small-right uk-border-rounded']]) !!}
            <form action="{{ _r('logout') }}"
                  class="uk-form"
                  method="POST">
                {{ method_field('POST') }}
                {{ csrf_field() }}
                <div class="uk-form-rowuk-margin-remove">
                    <button type="submit"
                            title="@lang('others.link_logout_from_dashboard')"
                            class="uk-button uk-button-danger uk-margin-small-right uk-border-rounded">@lang('others.link_logout_from_dashboard')
                    </button>
                </div>
            </form>
        </div>
    </nav>
</div>