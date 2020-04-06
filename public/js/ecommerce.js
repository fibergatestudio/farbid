function eCommerce() {
    this.exist = false;
    this.items = [];
    this.transaction = {};

    /**
     * Инициализация
     */
    this.init = function () {
        if (typeof (gtag) === 'function') this.exist = true;
    };

    this.event = function ($_event, $_data) {
        $_event = ($_event || null);
        $_data = ($_data || null);
        if (this.exist && $_event && $_data) gtag('event', $_event, $_data);
    };

    this.init();
}