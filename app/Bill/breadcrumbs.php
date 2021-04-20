<?php
	/**
     *
     * BREAD CRUMBS
     *
     */
    Breadcrumbs::register('bill.inbox', function ($breadcrumbs) {
        $breadcrumbs->parent('setting');
        $breadcrumbs->push(Lang::get('Bill::lang.packages'), route('bill.inbox'));
    });

    Breadcrumbs::register('bill.create', function ($breadcrumbs) {
        $breadcrumbs->parent('setting');
        $breadcrumbs->push(Lang::get('Bill::lang.packages'), route('bill.inbox'));
        $breadcrumbs->push(Lang::get('lang.create'), route('bill.create'));
    });

    Breadcrumbs::register('bill.edit', function ($breadcrumbs) {
        $breadcrumbs->parent('setting');
        $breadcrumbs->push(Lang::get('Bill::lang.packages'), route('bill.inbox'));
        $breadcrumbs->push(Lang::get('lang.edit'), url('bill/package/{id}/edit'));
    });

    Breadcrumbs::register('gatewaylit', function ($breadcrumbs) {
        $breadcrumbs->parent('setting');
        $breadcrumbs->push(Lang::get('Bill::lang.payment_gateway'), route('gatewaylit'));
    });

    Breadcrumbs::register('bill.setting', function ($breadcrumbs) {
        $breadcrumbs->parent('setting');
        $breadcrumbs->push(Lang::get('lang.bill'), route('bill.setting'));
    });